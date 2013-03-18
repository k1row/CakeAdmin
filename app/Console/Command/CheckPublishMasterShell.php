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
php /usr/local/nginx/cakeAdmin/app/Console/cake.php CheckPublishMaster /usr/local/nginx/cakeAdmin/app

*/




App::uses('Shell', 'Console');
App::uses('CakeEmail', 'Network/Email');


class CheckPublishMasterShell extends Shell
{
  var $uses = array('PublisherMaster');
  var $target_date;

  // オーバーライドして、Welcome to CakePHP･･･のメッセージを出さないようにする。
  function startup ()
  {
    $this->log (Configure::version(), LOG_DEBUG);

    Configure::write ('debug', 2);
    //$debug = Configure::read ('debug'); // 設定ファイルを読むこともできる
    $this->User = ClassRegistry::init ('User'); 
  }

  public function main ()
  {
    $msg = "This program found illegal data in publisher_masters\n";
    $ret1 = $illegalNull = $this->getIllegalDataNull ($msg);
    $ret2 = $illegal0 = $this->getIllegalData0 ($msg);
    $ret3 = $illegal1 = $this->getIllegalData1 ($msg);

    if ($ret1 == 0 && $ret2 == 0 && $ret3 == 0)
      exit;

    $this->sendMail ($msg);
  }

  function getIllegalDataNull (&$msg)
  {
    // Finding ios = 0 and android = 0
    $datas = $this->PublisherMaster->find ('all', array (
      'conditions' => array ('PublisherMaster.enable' => '1',
                             'PublisherMaster.ios' => NULL,
                             'PublisherMaster.android' => NULL)));

    if (empty ($datas))
      return 0;

    $msg .= "\nFinding ios = NULL and android = NULL ... \n";
    foreach ($datas as $data)
    {
      $msg .= $data['PublisherMaster']['id'] . "(". $data['PublisherMaster']['owner_name']. ")\n";
    }
    return 1;
  }

  function getIllegalData0 (&$msg)
  {
    // Finding ios = 0 and android = 0
    $datas = $this->PublisherMaster->find ('all', array (
      'conditions' => array ('PublisherMaster.enable' => '1',
                             'PublisherMaster.ios' => '0',
                             'PublisherMaster.android' => '0')));

    if (empty ($datas))
      return 0;

    $msg .= "\nFinding ios = 0 and android = 0 ... \n";
    foreach ($datas as $data)
    {
      $msg .= $data['PublisherMaster']['id'] . "(". $data['PublisherMaster']['owner_name']. ")\n";
    }
    return 1;
  }

  function getIllegalData1 (&$msg)
  {
    // Finding ios = 1 and android = 1
    $datas = $this->PublisherMaster->find ('all', array (
      'conditions' => array ('PublisherMaster.enable' => '1',
                             'PublisherMaster.ios' => '1',
                             'PublisherMaster.android' => '1')));

    if (empty ($datas))
      return 0;

    $msg .= "\nFinding ios = 1 and android = 1 ... \n";
    foreach ($datas as $data)
    {
      $msg .= $data['PublisherMaster']['id'] . "(". $data['PublisherMaster']['owner_name']. ")\n";
    }
    return 1;
  }

  function sendMail ($msg)
  {
    $this->log ("sendMail", LOG_DEBUG);
    $this->log ($msg, LOG_DEBUG);

    $email = new CakeEmail ('gmail');
    $email->from (array ('alert@usad.amoad.net' => 'alert@usad.amoad.net'));
    $email->to (array ('j.armfield@amoad.com' => 'j.armfield@amoad.com',
                       'k.lassen@cyberagentamerica.com' => 'k.lassen@cyberagentamerica.com',
                       'k.nagashima@cyberagentamerica.com' => 'k.nagashima@cyberagentamerica.com'))
    $email->subject ('CheckPublishMasterShell Alert');
    $result = $email->send ($msg);
  }

  function sqlDump ($dbConfig = 'default')
  {
    ConnectionManager::getDataSource ($dbConfig)->showLog ();
  }
}
