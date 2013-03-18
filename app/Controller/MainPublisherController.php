<?php

App::uses('Controller', 'Controller');

class MainPublisherController extends Controller
{
  public $name = 'MainPublisher';

  public function index()
  {
    $this->loadModel('PublisherMaster');
    $this->today = date ("Y-m-d", strtotime ("now"));

    $this->paginate = array (
      'limit' => 50,
      );

    $this->set('datas', $this->paginate('PublisherMaster'));
  }
}
