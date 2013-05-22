<?php

App::uses('Controller', 'Controller');

class IndividualappcsvdownloadController extends Controller
{
  public $name = 'Individualappcsvdownload';

  var $helpers = array('Html', 'Form', 'Csv'); //CSVヘルパーを設定します

  public function index ()
  {
    //Configure::write('debug', 0); // 警告を出さない

    $this->layout = false;
    $this->autoRender = false;

    $this->loadModel ('CampaignMaster');
    $this->loadModel ('Conversion');

    $begin_time = sprintf ("%04d", $this->params['url']['start_year']) . '-' . sprintf ("%02d", $this->params['url']['start_month']) . '-' .  sprintf ("%02d", $this->params['url']['start_day']);

    if ($this->params['url']['dl_type'] === 'Timeline')
      $begin_time .= sprintf (" %02d:00:00", $this->params['url']['time']);
    else
      $begin_time .= ' 00:00:00';

    //debug ($begin_time);
    if (!$this->isValidDate ($begin_time))
    {
      debug ("invalid begin_time");
      exit;
    }
    $end_time = sprintf ("%04d", $this->params['url']['end_year']) . '-' . sprintf ("%02d", $this->params['url']['end_month']) . '-' .  sprintf ("%02d", $this->params['url']['end_day']);

    if ($this->params['url']['dl_type'] === 'Timeline')
      $end_time .= sprintf (" %02d:59:59", $this->params['url']['time']);
    else
      $end_time .= ' 23:59:59';

    //debug ($end_time);
    if (!$this->isValidDate ($end_time))
    {
      debug ("invalid end_time");
      exit;
    }

    $filename = 'EXP_' . $this->params['url']['cid'] . '_' . $begin_time . '_' . $end_time;

    // The sheet first row
    $th = array('id', 'appsigid', 'idfa', 'idfamd5', 'idfasha1', 'dpidraw', 'dpidmd5', 'dpidsha1', 'openudid', 'macaddr', 'created', 'modified');

    // Get contents
    $datas = $this->exceptDuplicateRecord ("Conversion",
                                           $this->Conversion->find (
                                             'all',
                                             array('fields' => $th,
                                                   'conditions' => array ('Conversion.appsigid' => $this->params['url']['cid'],
                                                                          array ('Conversion.created >=' => $begin_time),
                                                                          array ('Conversion.created <=' => $end_time)))));

    $delimiter = ',';
    $enclosure = '"';

    $fp = fopen ('php://temp','r+');
    fputcsv ($fp, $th);
    foreach ($datas as $data)
    {
      fputcsv ($fp, $data['Conversion'], $delimiter, $enclosure);
    }

    rewind ($fp);

    $csv = stream_get_contents ($fp);
    $csv = mb_convert_encoding ($csv, 'SJIS', mb_internal_encoding ());
    fclose ($fp);

    //渡されたファイル名の拡張子やパスを切り落とす
    $filename = basename ($filename);

    header ('Content-Disposition:attachment; filename="' . $filename . '.csv"');
    header ('Content-Type:application/octet-stream');
    echo $csv;
    exit;
  }

  function exceptDuplicateRecord ($table_name, $datas)
  {
    $result = array ();
    $dup = array ();
    foreach ($datas as $data)
    {
      $key = 0;

      if ($data["$table_name"]['dpidraw'])
      {
        if (array_key_exists ($data["$table_name"]['dpidraw'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['dpidraw']; }
      }
      if ($data["$table_name"]['dpidmd5'])
      {
        if (array_key_exists ($data["$table_name"]['dpidmd5'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['dpidmd5']; }
      }
      if ($data["$table_name"]['dpidsha1'])
      {
        if (array_key_exists ($data["$table_name"]['dpidsha1'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['dpidsha1']; }
      }
      if ($data["$table_name"]['openudid'])
      {
        if (array_key_exists ($data["$table_name"]['openudid'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['openudid']; }
      }
      if ($data["$table_name"]['idfa'])
      {
        if (array_key_exists ($data["$table_name"]['idfa'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfa']; }
      }
      if ($data["$table_name"]['idfamd5'])
      {
        if (array_key_exists ($data["$table_name"]['idfamd5'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfamd5']; }
      }
      if ($data["$table_name"]['idfasha1'])
      {
        if (array_key_exists ($data["$table_name"]['idfasha1'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['idfasha1']; }
      }
      if ($data["$table_name"]['macaddr'])
      {
        if (array_key_exists ($data["$table_name"]['macaddr'], $dup))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['macaddr']; }
      }

      $dup{$key} = $data;
      array_push ($result, $data);
    }

    return $result;
  }
  function isValidDate ($input)
  {
    //$date_format = 'Y-m-d';
    $date_format = 'Y-m-d H:i:s';
    $input = trim ($input);
    $time = strtotime ($input);

    $is_valid = date ($date_format, $time) == $input;

    //print "Valid [$input] ? ".($is_valid ? 'yes' : 'no')."\n";
    return $is_valid ? true : false;
  }
}
