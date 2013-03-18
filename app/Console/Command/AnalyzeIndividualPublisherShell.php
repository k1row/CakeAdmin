<?php

/*
実際にTerminalから実行する場合は、
/usr/bin/php /CakePHPのパス/app/Console/cake.php Hoge -app /CakePHPのパス/app/

1) /usr/bin/php                        ･･･ phpまでのパス
2) /CakePHPのパス/app/Console/cake.php ･･･ cake.phpまでのパス(固定）
3) Hoge                                ･･･ Shell.phpを除いたシェル名
4) -app                                ･･･ appコマンド
5) /CakePHPのパス/app/                 ･･･ appまでのパス(固定）

メソッド名を指定しない場合自動出来に、main()メソッドが呼び出される
php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeIndividualPublisher /usr/local/nginx/cakeAdmin/app

シェル名の後に任意のメソッドを指定できる
php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeIndividualPublisher test /usr/local/nginx/cakeAdmin/app

php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeIndividualPublisher main 2013-03-10 -app /usr/local/nginx/cakeAdmin/app
パラメータを渡すことも可能
*/



/*

  CREATE TABLE `admin_analyze_publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) NOT NULL,
  `advertiser_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `campaign_name` varchar(255) NOT NULL,
  `expense` smallint(6) NOT NULL,
  `cpi` double(4,2) NOT NULL,
  `install_num` smallint(6) NOT NULL,
  `ios` tinyint(1) DEFAULT '0',
  `android` tinyint(1) DEFAULT '0',
  `incentivized` tinyint(1) DEFAULT '0',
  `non_incentivized` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_index` (`publisher_id`, `advertiser_id`, `campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


*/

App::uses('Shell', 'Console');

class AnalyzeIndividualPublisherShell extends Shell
{
  var $uses = array('PublisherMaster', 'CampaignMaster', 'Click', 'Conversion', 'AdminAnalyzePublisher');
  var $target_date;

  // オーバーライドして、Welcome to CakePHP･･･のメッセージを出さないようにする。
  function startup ()
  {
    $this->log (Configure::version(), LOG_DEBUG);

    Configure::write ('debug', 2);
    //$debug = Configure::read ('debug'); // 設定ファイルを読むこともできる
    $this->target_date = date ("Y-m-d", strtotime ("-1 day"));
    $this->User = ClassRegistry::init ('User');
  }

  public function main ()
  {
    $this->insertAnalyzeData ($this->getExistCampaign ());
  }

  function getExistCampaign ()
  {
    $datas = $this->CampaignMaster->find ('all', array (
      'conditions' => array (array ('CampaignMaster.begin_time <=' => "$this->target_date"),
                             array ('CampaignMaster.end_time >=' => "$this->target_date"))));

    $result = array ();
    foreach ($datas as $data)
    {
      //$this->log ($data, LOG_DEBUG);
      $insert_data;
      $insert_data['campaign_id'] = $data['CampaignMaster']['id'];
      $insert_data['advertiser_id'] = $data['CampaignMaster']['advertiser_id'];
      $insert_data['campaign_name'] = $data['CampaignMaster']['name'];
      $insert_data['expense'] = $data['CampaignMaster']['expense'];

      $this->getCampaignResult ($data['CampaignMaster']['id'], $result, $insert_data);
    }
    //$this->log ($result, LOG_DEBUG);
    return $result;
  }

  function getCampaignResult ($campaign_id, &$result, $insert_data)
  {
    $datas = $this->Click->find ('all', array (
      'fields' => array ('DISTINCT publisher_id'),
      'conditions' => array ('Click.campaign_id' => $campaign_id)));

    foreach ($datas as $data)
    {
      $insert_data['publisher_id'] = $data['Click']['publisher_id'];

      $this->getPublisherInfo ($data['Click']['publisher_id'], $insert_data);
      $insert_data['install_num'] = $this->compareClick2Conversion ($campaign_id, $data['Click']['publisher_id']);
      $insert_data['cpi'] = round ($data['CampaignMaster']['expense'] / $data['CampaignMaster']['insert_data'], 2);
      array_push ($result, $insert_data);
    }

    //$this->log ($result, LOG_DEBUG);
  }

  function compareClick2Conversion ($campaign_id, $publisher_id)
  {
    //$this->log ('getConversions campaign_id ='.$campaign_id, LOG_DEBUG);

    $datas = $this->Conversion->find ('all', array (
      'conditions' => array ('Conversion.campaign_id' => $campaign_id)));

    $processed_record = array ();
    $install_num = 0;
    foreach ($datas as $data)
    {
      if ($this->isDuplicateRecord ("Conversion", $data, $processed_record))
        continue;

      // check udid
      if ($this->doUdid ($campaign_id, $publisher_id, $data)) { $install_num++; continue; }

      // check openudid
      if ($this->doOpenUdid ($campaign_id, $publisher_id, $data)) { $install_num++; continue; }

      // check idfa
      if ($this->doIdfa ($campaign_id, $publisher_id, $data)) { $install_num++; continue; }

      // check android_id
      if ($this->doAndroidId ($campaign_id, $publisher_id, $data)) { $install_num++; continue; }

      // check imei
      if ($this->doImei ($campaign_id, $publisher_id, $data)) { $install_num++; continue; }

      // check macaddr
      if ($this->doMacaddr ($campaign_id, $publisher_id, $data)) { $install_num++; continue; }

      /*
      // It's error because there are not any devices datas in this record.
      {
        $this->log ("No device data found publisher_id = $publisher_id", LOG_DEBUG);
        $this->log ($data, LOG_DEBUG);
      }
        */
    }

    return $install_num;
  }

  function isDuplicateRecord ($table_name, $data, &$processed_record)
  {
    $key = 0;

    if ($data["$table_name"]['udid'])
    {
      //$this->log ('udid ='.$data["$table_name"]['udid'], LOG_DEBUG);
      if (array_key_exists ($data["$table_name"]['udid'], $processed_record))
        return 1;

      if ($key == 0) { $key = $data["$table_name"]['udid']; }
    }
    if ($data["$table_name"]['openudid'])
    {
      //$this->log ('openudid ='.$data["$table_name"]['openudid'], LOG_DEBUG);
      if (array_key_exists ($data["$table_name"]['openudid'], $processed_record))
        return 1;

      if ($key == 0) { $key = $data["$table_name"]['openudid']; }
    }
    if ($data["$table_name"]['idfa'])
    {
      //$this->log ('idfa ='.$data["$table_name"]['idfa'], LOG_DEBUG);
      if (array_key_exists ($data["$table_name"]['idfa'], $processed_record))
        return 1;

      if ($key == 0) { $key = $data["$table_name"]['idfa']; }
    }
    if ($data["$table_name"]['android_id'])
    {
      //$this->log ('android_id ='.$data["$table_name"]['android_id'], LOG_DEBUG);
      if (array_key_exists ($data["$table_name"]['android_id'], $processed_record))
        return 1;

      if ($key == 0) { $key = $data["$table_name"]['android_id']; }
    }
    if ($data["$table_name"]['imei'])
    {
      //$this->log ('imei ='.$data["$table_name"]['imei'], LOG_DEBUG);
      if (array_key_exists ($data["$table_name"]['imei'], $processed_record))
        return 1;

      if ($key == 0) { $key = $data["$table_name"]['imei']; }
    }
    if ($data["$table_name"]['macaddr'])
    {
      //$this->log ('macaddr ='.$data["$table_name"]['macaddr'], LOG_DEBUG);
      if (array_key_exists ($data["$table_name"]['macaddr'], $processed_record))
        return 1;

      if ($key == 0) { $key = $data["$table_name"]['macaddr']; }
    }

    $processed_record{$key} = '1';
    return 0;
  }

  function doUdid ($campaign_id, $publisher_id, $data)
  {
    if ($this->isExistsField ($data['Conversion']['udid']))
    {
      //$this->log ('found udid = '.$data['Conversion']['udid'], LOG_DEBUG);
      return $this->isInstalledTargetPublisher (array ('Click.campaign_id' => $campaign_id,
                                                       'Click.publisher_id' => $publisher_id,
                                                       'Click.udid' => $data['Conversion']['udid']));
    }

    return 0;
  }
  function doOpenUdid ($campaign_id, $publisher_id, $data)
  {
    if ($this->isExistsField ($data['Conversion']['openudid']))
    {
      //$this->log ('found udid = '.$data['Conversion']['openudid'], LOG_DEBUG);
      return $this->isInstalledTargetPublisher (array ('Click.campaign_id' => $campaign_id,
                                                       'Click.publisher_id' => $publisher_id,
                                                       'Click.openudid' => $data['Conversion']['openudid']));
    }

    return 0;
  }
  function doIdfa ($campaign_id, $publisher_id, $data)
  {
    if ($this->isExistsField ($data['Conversion']['idfa']))
    {
      //$this->log ('found idfa = '.$data['Conversion']['idfa'], LOG_DEBUG);
      return $this->isInstalledTargetPublisher (array ('Click.campaign_id' => $campaign_id,
                                                       'Click.publisher_id' => $publisher_id,
                                                       'Click.idfa' => $data['Conversion']['idfa']));
    }

    return 0;
  }
  function doAndroidId ($campaign_id, $publisher_id, $data)
  {
    if ($this->isExistsField ($data['Conversion']['android_id']))
    {
      //$this->log ('found android_id = '.$data['Conversion']['android_id'], LOG_DEBUG);
      return $this->isInstalledTargetPublisher (array ('Click.campaign_id' => $campaign_id,
                                                       'Click.publisher_id' => $publisher_id,
                                                       'Click.android_id' => $data['Conversion']['android_id']));
    }

    return 0;
  }
  function doImei ($campaign_id, $publisher_id, $data)
  {
    if ($this->isExistsField ($data['Conversion']['imei']))
    {
      //$this->log ('found imei = '.$data['Conversion']['imei'], LOG_DEBUG);
      return $this->isInstalledTargetPublisher (array ('Click.campaign_id' => $campaign_id,
                                                       'Click.publisher_id' => $publisher_id,
                                                       'Click.imei' => $data['Conversion']['imei']));
    }

    return 0;
  }
  function doMacaddr ($campaign_id, $publisher_id, $data)
  {
    if ($this->isExistsField ($data['Conversion']['macaddr']))
    {
      //$this->log ('found macaddr = '.$data['Conversion']['macaddr'], LOG_DEBUG);
      return $this->isInstalledTargetPublisher (array ('Click.campaign_id' => $campaign_id,
                                                       'Click.publisher_id' => $publisher_id,
                                                       'Click.macaddr' => $data['Conversion']['macaddr']));
    }

    return 0;
  }

  function isInstalledTargetPublisher ($conditions)
  {
    $datas = $this->Click->find ('count', array (
      'conditions' => $conditions));

    if ($datas > 1)
    {
      // It's erro because it found more than 1 record. It's illegal.
      // For now it returns 1;
      $this->log ("Found more than 1 click datas", LOG_DEBUG);
      return 1;
    }

    return $datas;
  }

  function getPublisherInfo ($publisher_id, &$insert_data)
  {
    $datas = $this->PublisherMaster->find ('all', array (
      'conditions' => array ('PublisherMaster.id' => $publisher_id)));

    if (empty ($datas))
      return 0;

    $insert_data['ios'] = $datas[0]['PublisherMaster']['ios'];
    $insert_data['android'] = $datas[0]['PublisherMaster']['android'];
    $insert_data['incentivized'] = $datas[0]['PublisherMaster']['incentivized'];
    $insert_data['non_incentivized'] = $datas[0]['PublisherMaster']['non_incentivized'];
  }

  function insertAnalyzeData ($result)
  {
    foreach ($result as $ret)
    {
      $this->AdminAnalyzePublisher->create ();

      $field = array (
        'publisher_id' => $ret['publisher_id'],
        'advertiser_id' => $ret['advertiser_id'],
        'campaign_id' => $ret['campaign_id'],
        'campaign_name' => $ret['campaign_name'],
        'expense' => $ret['expense'],
        'cpi' => $ret['cpi'],
        'install_num' => $ret['install_num'],
        'ios' => $ret['ios'],
        'android' => $ret['android'],
        'incentivized' => $ret['incentivized'],
        'non_incentivized' => $ret['non_incentivized'],
        );

      $already_data = $this->isExistsAnalyzeData ($ret);
      if ($already_data)
      {
        //$this->log ($already_data, LOG_DEBUG);
        $field['id'] =$already_data['AdminAnalyzePublisher']['id'];
        //array_push ($field, $add);
      }

      //$this->log ("field", LOG_DEBUG);
      $this->log ($field, LOG_DEBUG);

      $this->AdminAnalyzePublisher->set ($field);
      $this->AdminAnalyzePublisher->save ();
      //echo $this->sqlDump ();
    }
  }

  function isExistsAnalyzeData ($ret)
  {
    $datas = $this->AdminAnalyzePublisher->find ('all', array (
      'conditions' => array ('AdminAnalyzePublisher.publisher_id' => $ret['publisher_id'],
                             'AdminAnalyzePublisher.advertiser_id' => $ret['advertiser_id'],
                             'AdminAnalyzePublisher.campaign_id' => $ret['campaign_id'])));
    return count ($datas) >= 1 ? $datas[0] : 0;
  }

  function isExistsField ($filed_data)
  {
    if (is_null ($filed_data))
      return 0;

    if (!isset ($filed_data))
      return 0;

    if (empty ($filed_data))
      return 0;

    return 1;
  }

  function sqlDump ($dbConfig = 'default')
  {
    ConnectionManager::getDataSource ($dbConfig)->showLog ();
  }
}
