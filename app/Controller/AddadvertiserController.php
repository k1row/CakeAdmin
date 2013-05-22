<?php

App::uses('Controller', 'Controller');

class AddadvertiserController extends Controller
{
  public $name = 'Addadvertiser';
  var $uses = array('AdvertiserMaster');

  public function index ()
  {
  }
  public function add ()
  {
    if ($this->request->is ('post'))
    {
      // When data was saved
      if ($this->AdvertiserMaster->save ($this->request->data))
      {
        $this->Session->setFlash ('Saved data');

        $this->redirect (array ('controller' => 'Mainadvertiser', 'action' => 'index'));
      }
      else
      {
        // When data was NOT saved
        $this->Session->setFlash ('Sorry. To save AdvertiserMaster was failed. Please try again.');
      }
    }
  }
  public function edit ($id = null)
  {
    $this->AdvertiserMaster->id = $id;

    // Set data
    $this->set ('id', $id);

    if($this->request->is ('post') || $this->request->is ('put'))
    {
      // When data was saved
      if ($this->AdvertiserMaster->save ($this->request->data))
      {
        $this->Session->setFlash ('Updated data');

        $param = "index?aid=$id";
        $this->redirect (array ('controller' => 'Individualadvertiser', 'action' => $param));
      }
      else
      {
        // When data was NOT saved
        $this->Session->setFlash ('Sorry. To update AdvertiserMaster was failed. Please try again.');
      }
    }
    else
    {
      $this->request->data = $this->AdvertiserMaster->read (null, $id);
    }
  }
}
