<?php

App::uses('Controller', 'Controller');

class IndividualpublisherController extends Controller
{
  public $name = 'Individualpublisher';
  public function index()
  {
    $this->loadModel ('AdminAnalyzePublisher');

    $this->paginate = array (
      'conditions' => array ('AdminAnalyzePublisher.publisher_id' => $this->params['url']['pid']),
      'limit' => 50,
      'order' => array('AdminAnalyzeCampaign.target_date' => 'DESC'),
      );

    $this->set ('publisher_id', $this->params['url']['pid']);
    $this->set ('publisher_name', $this->params['url']['name']);
    $this->set ('datas', $this->paginate ('AdminAnalyzePublisher'));
  }
}
