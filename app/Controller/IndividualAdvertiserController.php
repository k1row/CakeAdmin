<?php

App::uses('Controller', 'Controller');

class IndividualAdvertiserController extends Controller
{
  public $name = 'IndividualAdvertiser';
  public function index() {

    $this->loadModel('CampaignMaster');

    $this->paginate = array(
      'conditions' => array('CampaignMaster.advertiser_id' => $this->params['url']['aid']),
      'limit' => 50,
      );

    $this->set('datas', $this->paginate('CampaignMaster'));
    $this->set('advertiser_name', $this->params['url']['name']);
  }
}
