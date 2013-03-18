<?php

App::uses('Controller', 'Controller');

class IndividualAppController extends Controller
{
  public $name = 'IndividualApp';

  /*
  // Reference
  // http://shie-plusplus.seesaa.net/article/322310531.html

  var $paginate = array(
    'page' => 1,
    'limit' => 25,
    'recursive' => 0,
    'conditions' => null,
    'fields' => null,
    'order' => null,
    );

  public function index()
  {
    $this->loadModel('Conversion');
    $this->paginate['conditions'] = array('conversions.campaign_id' => $this->params['url']['cid']);
    $this->set('datas', $this->paginate('Conversion'));
  }
    */

  public function index()
  {
    $this->loadModel ('AdminAnalyzeCampaign');

    $this->paginate = array (
      'conditions' => array ('AdminAnalyzeCampaign.campaign_id' => $this->params['url']['cid']),
      'limit' => 50,
      'order' => array('AdminAnalyzeCampaign.target_date' => 'DESC'),
      );

    $this->set ('campaign_id', $this->params['url']['cid']);
    $this->set ('datas', $this->paginate ('AdminAnalyzeCampaign'));
  }
}
