<?php

App::uses('Controller', 'Controller');

class MainadvertiserController extends Controller
{
  public $name = 'Mainadvertiser';

  public function index ()
  {
    $this->loadModel('AdvertiserMaster');
    $this->loadModel('CampaignMaster');
    $today = date ("Y-m-d", strtotime ("now"));

    $this->paginate = array(
      'fields' => array ('AdvertiserMaster.*'),
      'limit' => 50,
      );

    $datas = $this->paginate('AdvertiserMaster');
    $result = array ();
    foreach ($datas as $data)
    {
      $insert_data;
      $insert_data['id'] = $data['AdvertiserMaster']['id'];
      $insert_data['company_name'] = $data['AdvertiserMaster']['company_name'];
      $insert_data['owner_name'] = $data['AdvertiserMaster']['owner_name'];
      $insert_data['owner_email_address'] = $data['AdvertiserMaster']['owner_email_address'];
      $insert_data['created'] = $data['AdvertiserMaster']['created'];
      $insert_data['modified'] = $data['AdvertiserMaster']['modified'];
      $insert_data['enable_campaign_num'] = $this->getCampaignEnable ($data['AdvertiserMaster']['id'], $today);
      array_push ($result, $insert_data);
    }
    $this->set('datas', $result);
  }
  function getCampaignEnable ($advertiser_id, $today)
  {
    $datas = $this->CampaignMaster->find ('count', array (
      'conditions' => array ('CampaignMaster.advertiser_id' => $advertiser_id,
                             array ('CampaignMaster.end_time >' => "$today 23:59:59"))));

    return $datas;
  }
  
  /*
  public function index()
  {
    $this->loadModel('AdvertiserMaster');
    $this->loadModel('CampaignMaster');
    $this->today = date ("Y-m-d", strtotime ("now"));

    $this->paginate = array(
      'fields' => array ('AdvertiserMaster.*', 'cm.id AS cmid'),
      'joins' => array (
        array(
          'type'=>'LEFT',
          'table'=>'campaign_masters',
          'alias'=>'cm',
          'conditions' => array ('cm.advertiser_id = AdvertiserMaster.id',
                                 array ('cm.end_time <=' => "$this->today 23:59:59")))),
      'limit' => 50,
      );

    $this->set('datas', $this->paginate('AdvertiserMaster'));
  }
    */
}
