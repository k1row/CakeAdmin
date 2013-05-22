<?php

App::uses('Controller', 'Controller');

class IndividualappController extends Controller
{
  public $name = 'Individualapp';

  public function index()
  {
    $this->loadModel ('AdminAnalyzeCampaign');
    $this->loadModel ('AdminAnalyzeCampaignPerDay');
    $this->loadModel('CampaignMaster');

    $appsigid = $this->params['url']['cid'];
    $this->set ('appsigid', $appsigid);

    $datas = $this->AdminAnalyzeCampaign->find ('all', array (
      'conditions' => array ('AdminAnalyzeCampaign.appsigid' => $appsigid)));

    $this->set ('datas', $datas);

    $this->paginate = array (
      'conditions' => array ('AdminAnalyzeCampaignPerDay.appsigid' => $appsigid),
      'limit' => 50,
      'order' => array('AdminAnalyzeCampaignPerDay.target_date' => 'DESC'),
      );

    $this->set ('dailydatas', $this->paginate ('AdminAnalyzeCampaignPerDay'));

    $campaignmaster = $this->CampaignMaster->find ('all', array (
      'conditions' => array ('CampaignMaster.id' => $appsigid)));

    $this->set ('campaignmaster', $campaignmaster);
  }
}
