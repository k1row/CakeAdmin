<?php

App::uses('Controller', 'Controller');

class IndividualAppCSVDownloadController extends Controller
{
  public $name = 'IndividualAppCSVDownload';

  var $helpers = array('Html', 'Form', 'Csv'); //CSVƒwƒ‹ƒp[‚ğİ’è‚µ‚Ü‚·

  public function index ()
  {
    //Configure::write('debug', 0); // Œx‚ğo‚³‚È‚¢
    $this->layout = false;

    $this->loadModel ('CampaignMaster');
    $this->loadModel ('Conversion');

    $target_date = $this->params['url']['tdate'];
    if (!$this->isValidDate ($target_date))
    {
      exit;
    }
    $target_date = date ("Y-m-d", strtotime ($this->params['url']['tdate']));

    $filename = 'EXP_' . $this->params['url']['cid'] . '_' . date('YmdHis'); // File Name

    // The sheet first row
    $th = array('id', 'campaign_id', 'udid', 'openudid', 'idfa', 'android_id', 'imei', 'macaddr', 'ua', 'created', 'modified');

    $begin_time;
    $end_time;
    $this->getCorrectBeginEndTime ($begin_time, $end_time);

    // Get contents
    $td = $this->Conversion->find ('all', array('fields' => $th,
                                                'conditions' => array ('Conversion.campaign_id' => $this->params['url']['cid'],
                                                                       array ('Conversion.created >=' => $begin_time),
                                                                       array ('Conversion.created <=' => $end_time))));

    $this->set (compact ('filename', 'genders', 'th', 'td'));
  }
  function getCorrectBeginEndTime (&$begin_time, &$end_time)
  {
    $datas = $this->CampaignMaster->find ('all', array (
      'conditions' => array ('CampaignMaster.id' => $this->params['url']['cid'])));

    $begin_time = $datas[0]['CampaignMaster']['begin_time'];
    if (strtotime ("$this->target_date 00:00:00") >= strtotime ($datas[0]['CampaignMaster']['begin_time']))
      $begin_time = "$this->target_date 00:00:00";

    $end_time = $datas[0]['CampaignMaster']['end_time'];
    if (strtotime ("$this->target_date 23:59:59") <= strtotime ($datas[0]['CampaignMaster']['end_time']))
      $end_time = "$this->target_date 23:59:59";
  }
  function getExceptDuplicateRecord ($table_name, $datas)
  {
    $result = array ();
    $dup = array ();
    foreach ($datas as $data)
    {
      $key = 0;

      if ($data["$table_name"]['udid'])
      {
        if (array_key_exists ($data["$table_name"]['udid'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['udid']; }
      }
      if ($data["$table_name"]['openudid'])
      {
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
      if ($data["$table_name"]['android_id'])
      {
        if (array_key_exists ($data["$table_name"]['android_id'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['android_id']; }
      }
      if ($data["$table_name"]['imei'])
      {
        if (array_key_exists ($data["$table_name"]['imei'], $result))
          continue;

        if ($key == 0) { $key = $data["$table_name"]['imei']; }
      }
      if ($data["$table_name"]['macaddr'])
      {
        if (array_key_exists ($data["$table_name"]['macaddr'], $result))
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
    $date_format = 'Y-m-d';
    $input = trim($input);
    $time = strtotime($input);

    $is_valid = date($date_format, $time) == $input;

    //print "Valid [$input] ? ".($is_valid ? 'yes' : 'no')."\n";
    return $is_valid ? true : false;
  }
}
