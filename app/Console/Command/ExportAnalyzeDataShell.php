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
php /usr/local/nginx/cakeAdmin/app/Console/cake.php ExportAnalyzeData /usr/local/nginx/cakeAdmin/app

シェル名の後に任意のメソッドを指定できる
php /usr/local/nginx/cakeAdmin/app/Console/cake.php ExportAnalyzeData test /usr/local/nginx/cakeAdmin/app

php /usr/local/nginx/cakeAdmin/app/Console/cake.php ExportAnalyzeData specifiedDate 2013-03-10 -app /usr/local/nginx/cakeAdmin/app
パラメータを渡すことも可能
*/



/*

CREATE TABLE `admin_analyze_campaign_per_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advertiser_id` int(11) NOT NULL,
  `appsigid` varchar(255) NOT NULL DEFAULT '',
  `target_date` datetime NOT NULL,
  `click_num` smallint(6) NOT NULL,
  `install_num` smallint(6) NOT NULL,
  `cvr` double(4,2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_index` (`appsigid`,`target_date`),
  KEY `appsigid` (`appsigid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
*/

App::uses('Shell', 'Console');

class ExportAnalyzeDataShell extends Shell
{
  var $uses = array('CampaignMaster', 'Click', 'Conversion');

  var $target_date;
  var $file_name;
  var $begin_time;
  var $end_time;

  // オーバーライドして、Welcome to CakePHP･･･のメッセージを出さないようにする。
  function startup ()
  {
    $this->log (Configure::version(), LOG_DEBUG);

    Configure::write ('debug', 2);
    //$debug = Configure::read ('debug'); // 設定ファイルを読むこともできる

    $this->target_date = date ("Y-m-d");
    $this->file_name = sprintf ("%s.tsv", date ("Y-m-d-H", strtotime ("-1 hour")));

    $this->begin_time = date ("Y-m-d H:00:00", strtotime ("-1 hour"));
    $this->end_time = date ("Y-m-d H:59:59", strtotime ("-1 hour"));
    $this->User = ClassRegistry::init ('User');
  }

  public function main ()
  {
    $datas = $this->getExistCampaign ();
    //$this->log ($datas, LOG_DEBUG);

    $this->writeFile ($datas);
  }

  function writeFile ($datas)
  {
    $fp = fopen ($this->file_name, "w");
    $title = "target_date_hour\tadvertiser_id\tappsigid\tclick_num\tinstall_num\n";
    fwrite ($fp, $title);
    foreach ($datas as $data)
    {
      $l = $this->begin_time."\t".$data['advertiser_id']."\t".$data['appsigid']."\t".$data['click_num']."\t".$data['install_num']."\n";
      fwrite ($fp, $l);
    }
    fclose ($fp);
  }

  function getExistCampaign ()
  {
    //$one_day_before = date ("Y-m-d", strtotime ("$this->target_date -1 day"));
    $datas = $this->CampaignMaster->find ('all', array (
      //'conditions' => array (array ('LEFT (CampaignMaster.end_time, 10) >=' => $one_day_before))));
      'conditions' => array (array ('LEFT (CampaignMaster.end_time, 10) >=' => $this->target_date))));

    //echo $this->sqlDump ();
    //$this->log ($datas, LOG_DEBUG);
    $result = array ();
    foreach ($datas as $data)
    {
      if ($data['CampaignMaster']['advertiser_id'] === '100000000') { continue; }
      if ($data['CampaignMaster']['id'] === '2045444883139e79c4246183595c2df2613d6192') { continue; }
      if ($data['CampaignMaster']['id'] === '40b247a5c58ea510c773942a6ba0aa3a7467cc35') { continue; }

      $insert_data;
      $insert_data['advertiser_id'] = $data['CampaignMaster']['advertiser_id'];
      $insert_data['appsigid'] = $data['CampaignMaster']['id'];
      $insert_data['begin_time'] = $data['CampaignMaster']['begin_time'];
      $insert_data['end_time'] = $data['CampaignMaster']['end_time'];
      $insert_data['click_num'] = $this->getClicks ($data);
      $insert_data['install_num'] = $this->getConversions ($data);

      array_push ($result, $insert_data);
    }
    return $result;
  }
  function getClicks ($data)
  {
    $datas = $this->Click->find ('all', array (
      'conditions' => array ('Click.appsigid' => $data['CampaignMaster']['id'],
                             array ('Click.created >=' => $this->begin_time),
                             array ('Click.created <=' => $this->end_time))));

    //echo $this->sqlDump ();
    return $this->getActualRecordNum ('Click', $datas);
  }

  function getConversions ($data)
  {
    $datas = $this->Conversion->find ('all', array (
      'conditions' => array ('Conversion.appsigid' => $data['CampaignMaster']['id'],
                             array ('Conversion.created >=' => $this->begin_time),
                             array ('Conversion.created <=' => $this->end_time))));

    //echo $this->sqlDump ();
    return $this->getActualRecordNum ('Conversion', $datas);
  }

  function getActualRecordNum ($table_name, $datas)
  {
    $result = array ();
    foreach ($datas as $data)
    {
      //$this->log ($data, LOG_DEBUG);
      $key = 0;

      if ($data["$table_name"]['dpidraw'])
      {
        if (array_key_exists ($data["$table_name"]['dpidraw'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['dpidraw']; }
      }
      if ($data["$table_name"]['dpidmd5'])
      {
        if (array_key_exists ($data["$table_name"]['dpidmd5'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['dpidmd5']; }
      }
      if ($data["$table_name"]['dpidsha1'])
      {
        if (array_key_exists ($data["$table_name"]['dpidsha1'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['dpidsha1']; }
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
        if (array_key_exists ($data["$table_name"]['idfa'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfa']; }
      }
      if ($data["$table_name"]['idfamd5'])
      {
        if (array_key_exists ($data["$table_name"]['idfamd5'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfamd5']; }
      }
      if ($data["$table_name"]['idfasha1'])
      {
        if (array_key_exists ($data["$table_name"]['idfasha1'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfasha1']; }
      }
      if ($data["$table_name"]['macaddr'])
      {
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
  function sqlDump ($dbConfig = 'default')
  {
    ConnectionManager::getDataSource ($dbConfig)->showLog ()."\n";
  }
}
