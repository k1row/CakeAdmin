<?php

App::uses('Controller', 'Controller');

class AddpublisherController extends Controller
{
  public $name = 'Addpublisher';
  var $uses = array('PublisherMaster');

  public function index ()
  {
  }
  public function add ()
  {
    // If the request is POST
    if ($this->request->is ('post'))
    {
      // If data was saved
      if ($this->PublisherMaster->save ($this->request->data))
      {
        $this->Session->setFlash ('Saved data');

        $this->redirect(array('controller' => 'Mainpublisher', 'action' => 'index'));
      }
      else
      {
        // If data was NOT saved
        $this->Session->setFlash ('Sorry. To save PublisherMaster was failed. Please try again.');
      }
    }
  }
  public function edit ($id = null)
  {
    $this->PublisherMaster->id = $id;

    // Set data
    $this->set ('id', $id);

    if($this->request->is ('post') || $this->request->is ('put'))
    {
      // When data was saved
      if ($this->PublisherMaster->save ($this->request->data))
      {
        $this->Session->setFlash ('Updated data');

        $param = "index?pid=$id&name=".$this->request->data['PublisherMaster']['owner_name'];
        debug($param);
        $this->redirect (array ('controller' => 'Individualpublisher', 'action' => $param));
      }
      else
      {
        // When data was NOT saved
        $this->Session->setFlash ('Sorry. To update PublisherMaster was failed. Please try again.');
      }
    }
    else
    {
      $this->request->data = $this->PublisherMaster->read (null, $id);
    }
  }
}
