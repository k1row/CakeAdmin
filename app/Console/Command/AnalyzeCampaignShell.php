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
php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeCampaign /usr/local/nginx/cakeAdmin/app

シェル名の後に任意のメソッドを指定できる
php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeCampaign test /usr/local/nginx/cakeAdmin/app

php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeCampaign specifiedDate 2013-03-10 -app /usr/local/nginx/cakeAdmin/app
パラメータを渡すことも可能
*/



/*

CREATE TABLE `admin_analyze_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advertiser_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `target_date` date NOT NULL,
  `click_num` smallint(6) NOT NULL,
  `install_num` smallint(6) NOT NULL,
  `cvr` double(4,2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_index` (`campaign_id`,`target_date`),
  KEY `advertiser_id` (`advertiser_id`,`campaign_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

*/

App::uses('Shell', 'Console');

class AnalyzeCampaignShell extends Shell
{
  var $uses = array('AdvertiserMaster', 'CampaignMaster', 'Click', 'Conversion', 'AdminAnalyzeCampaign');
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
    $campaign_result = $this->getExistCampaign ();
    $this->insertAnalyzeData ($campaign_result);
  }
  public function specifiedDate ()
  {
    $this->log ($this->args[0], LOG_DEBUG);

    // 引数が正当な日付で合った場合集計データ日を変更
    if (!empty ($this->args[0]))
    {
      if (!$this->isValidDate ($this->args[0]))
      {
        echo 'Invalid arg = '.$this->args[0]."\n";
        echo 'Usage : php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeCampaign specifiedDate 2013-03-10 -app /usr/local/nginx/cakeAdmin/app'."\n";
        exit;
      }

      $this->target_date = date ("Y-m-d", strtotime ($this->args[0]));
      $this->log ("target_date has changed = $this->target_date", LOG_DEBUG);
    }

    //$this->log ($this->target_date, LOG_DEBUG);

    $campaign_result = $this->getExistCampaign ();
    $this->insertAnalyzeData ($campaign_result);
  }

  function isValidDate ($input)
  {
    $date_format = 'Y-m-d';
    $input = trim ($input);
    $time = strtotime ($input);

    $is_valid = date ($date_format, $time) == $input;

    //print "Valid [$input] ? ".($is_valid ? 'yes' : 'no')."\n";
    return $is_valid ? true : false;
  }

  function getExistCampaign ()
  {
    /*
    $datas = $this->CampaignMaster->find ('all', array (
      'conditions' => array (array ('LEFT (CampaignMaster.begin_time, 10) >=' => "$this->target_date"),
                             array ('LEFT (CampaignMaster.end_time, 10) <=' => "$this->target_date"))));
     */

    $one_day_before = date ("Y-m-d", strtotime ("$this->target_date -1 day"));
    $datas = $this->CampaignMaster->find ('all', array (
      'conditions' => array (array ('LEFT (CampaignMaster.end_time, 10) >=' => $one_day_before))));

    //echo $this->sqlDump ();
    //$this->log ($datas, LOG_DEBUG);
    $result = array ();
    foreach ($datas as $data)
    {
      $this->log (strtotime (substr ($data['CampaignMaster']['begin_time'], 0, 10)), LOG_DEBUG);
      $this->log (strtotime ("$this->target_date"), LOG_DEBUG);

      if (strtotime (substr ($data['CampaignMaster']['begin_time'], 0, 10)) > strtotime ("$this->target_date"))
        continue;

      $insert_data;
      $insert_data['target_date'] = $this->target_date;
      $insert_data['advertiser_id'] = $data['CampaignMaster']['advertiser_id'];
      $insert_data['campaign_id'] = $data['CampaignMaster']['id'];
      $insert_data['click_num'] = $this->getClicks ($data);
      $insert_data['install_num'] = $this->getConversions ($data);
      $insert_data['cvr'] = round ($insert_data['install_num'] / $insert_data['click_num'], 2);

      array_push ($result, $insert_data);
    }
    return $result;
  }

  function getClicks ($data)
  {
    $begin_time = $data['CampaignMaster']['begin_time'];
    if (strtotime ("$this->target_date 00:00:00") >= strtotime ($data['CampaignMaster']['begin_time']))
      $begin_time = "$this->target_date 00:00:00";

    $end_time = $data['CampaignMaster']['end_time'];
    if (strtotime ("$this->target_date 23:59:59") <= strtotime ($data['CampaignMaster']['end_time']))
      $end_time = "$this->target_date 23:59:59";

    $datas = $this->Click->find ('all', array (
      'conditions' => array ('Click.campaign_id' => $data['CampaignMaster']['id'],
                             array ('Click.created >=' => $begin_time),
                             array ('Click.created <=' => $end_time))));

    echo $this->sqlDump ();
    return $this->getActualRecordNum ('Click', $datas);
  }

  function getConversions ($data)
  {
    $begin_time = $data['CampaignMaster']['begin_time'];
    if (strtotime ("$this->target_date 00:00:00") >= strtotime ($data['CampaignMaster']['begin_time']))
      $begin_time = "$this->target_date 00:00:00";

    $end_time = $data['CampaignMaster']['end_time'];
    if (strtotime ("$this->target_date 23:59:59") <= strtotime ($data['CampaignMaster']['end_time']))
      $end_time = "$this->target_date 23:59:59";

    $datas = $this->Conversion->find ('all', array (
      'conditions' => array ('Conversion.campaign_id' => $data['CampaignMaster']['id'],
                             array ('Conversion.created >=' => $begin_time),
                             array ('Conversion.created <=' => $end_time))));

    echo $this->sqlDump ();
    return $this->getActualRecordNum ('Conversion', $datas);
  }

  function getActualRecordNum ($table_name, $datas)
  {
    $result = array ();
    foreach ($datas as $data)
    {
      //$this->log ($data, LOG_DEBUG);
      $key = 0;

      if ($data["$table_name"]['udid'])
      {
        //$this->log ('udid ='.$data["$table_name"]['udid'], LOG_DEBUG);
        if (array_key_exists ($data["$table_name"]['udid'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['udid']; }
      }
      if ($data["$table_name"]['openudid'])
      {
        //$this->log ('openudid ='.$data["$table_name"]['openudid'], LOG_DEBUG);
        if (array_key_exists ($data["$table_name"]['openudid'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['openudid']; }
      }
      if ($data["$table_name"]['idfa'])
      {
        //$this->log ('idfa ='.$data["$table_name"]['idfa'], LOG_DEBUG);
        if (array_key_exists ($data["$table_name"]['idfa'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfa']; }
      }
      if ($data["$table_name"]['android_id'])
      {
        //$this->log ('android_id ='.$data["$table_name"]['android_id'], LOG_DEBUG);
        if (array_key_exists ($data["$table_name"]['android_id'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['android_id']; }
      }
      if ($data["$table_name"]['imei'])
      {
        //$this->log ('imei ='.$data["$table_name"]['imei'], LOG_DEBUG);
        if (array_key_exists ($data["$table_name"]['imei'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['imei']; }
      }
      if ($data["$table_name"]['macaddr'])
      {
        //$this->log ('macaddr ='.$data["$table_name"]['macaddr'], LOG_DEBUG);
        if (array_key_exists ($data["$table_name"]['macaddr'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['macaddr']; }
      }

      $result{$key} = '1';
    }

    //$this->log ($result, LOG_DEBUG);
    //$this->log (count ($result), LOG_DEBUG);
    return count ($result);
  }

  function insertAnalyzeData ($campaign_result)
  {
    $this->log ('insertAnalyzeData', LOG_DEBUG);
    foreach ($campaign_result as $result)
    {
      $this->log ($result, LOG_DEBUG);

      $this->AdminAnalyzeCampaign->create ();
      $this->AdminAnalyzeCampaign->set (array (
        'target_date' => $result['target_date'],
        'advertiser_id' => $result['advertiser_id'],
        'campaign_id' => $result['campaign_id'],
        'click_num' => $result['click_num'],
        'install_num' => $result['install_num'],
        'cvr' => $result['cvr'],
        ));
      $this->AdminAnalyzeCampaign->save ();
      //echo $this->sqlDump ();
    }
  }

  function sqlDump ($dbConfig = 'default')
  {
    ConnectionManager::getDataSource ($dbConfig)->showLog ()."\n";
  }
}
