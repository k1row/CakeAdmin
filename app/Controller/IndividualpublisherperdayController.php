<?php

App::uses('Controller', 'Controller');

class IndividualpublisherperdayController extends Controller
{
  public $name = 'Individualpublisherperday';
  public function index()
  {
    $this->loadModel ('AdminAnalyzePublisherPerDay');
    $this->loadModel('CampaignMaster');

    $this->paginate = array (
      'conditions' => array ('AdminAnalyzePublisherPerDay.publisher_id' => $this->params['url']['pid'],
                             'AdminAnalyzePublisherPerDay.appsigid' => $this->params['url']['cid']),
      'limit' => 50,
      'order' => array('AdminAnalyzePublisherPerDay.target_date' => 'DESC'),
      );

    $this->set ('datas', $this->paginate ('AdminAnalyzePublisherPerDay'));

    $campaignmaster = $this->CampaignMaster->find ('all', array (
      'conditions' => array ('CampaignMaster.id' => $this->params['url']['cid'])));

    $this->set ('campaignmaster', $campaignmaster);

    $this->set ('publisher_id', $this->params['url']['pid']);
    $this->set ('publisher_name', $this->params['url']['name']);
  }
}
