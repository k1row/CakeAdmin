<?php

App::uses('Controller', 'Controller');

class IndividualadvertiserController extends Controller
{
  public $name = 'Individualadvertiser';
  public function index() {

    $this->loadModel('AdvertiserMaster');
    $this->loadModel('CampaignMaster');

    $advertiser = $this->AdvertiserMaster->find ('all', array (
      'conditions' => array ('AdvertiserMaster.id' => $this->params['url']['aid'])));

    $this->paginate = array(
      'conditions' => array('CampaignMaster.advertiser_id' => $this->params['url']['aid']),
      'limit' => 50,
      );

    $this->set('datas', $this->paginate('CampaignMaster'));
    $this->set('advertiser', $advertiser);
  }
}
