
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
php /usr/local/nginx/cakeAdmin/app/Console/cake.php Copy /usr/local/nginx/cakeAdmin/app

シェル名の後に任意のメソッドを指定できる
php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeCampaign test /usr/local/nginx/cakeAdmin/app

php /usr/local/nginx/cakeAdmin/app/Console/cake.php AnalyzeCampaign specifiedDate 2013-03-10 -app /usr/local/nginx/cakeAdmin/app
パラメータを渡すことも可能
*/


App::uses('Shell', 'Console');

class CopyShell extends Shell
{
  var $uses = array('Click', 'Conversion');
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
	//$this->getClicks ();
	$this->getConversions ();
  }

  function getClicks ()
  {
    $datas = $this->Click->find ('all');
    foreach ($datas as $data)
    {
	  $this->updateClick ($data);
    }
  }

  function updateClick ($data)
  {
	$this->Click->create ();
	
	$field = array ('id' => $data['Click']['id'],
					'dpidraw' => $data['Click']['dpidraw'],
					);
	$this->Click->set ($field);
	$this->Click->save ();
  }

  function getConversions ()
  {
    $datas = $this->Conversion->find ('all');
    foreach ($datas as $data)
    {
	  $this->updateConversion ($data);
    }
  }

  function updateConversion ($data)
  {
	$this->Conversion->create ();
	
	$field = array ('id' => $data['Conversion']['id'],
					'dpidraw' => $data['Conversion']['dpidraw'],
					);
	$this->Conversion->set ($field);
	$this->Conversion->save ();
  }

  function sqlDump ($dbConfig = 'default')
  {
    ConnectionManager::getDataSource ($dbConfig)->showLog ()."\n";
  }
}
