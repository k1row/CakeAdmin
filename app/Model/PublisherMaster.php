<?php
class PublisherMaster extends AppModel
{
  var $name = "PublisherMaster";

  public $validate = array(
    'owner_name'=>array(
      array(
        'rule' => 'notEmpty',
        'message' => 'Please entry owner_name',
        ),
      array(
        'rule' => array('minlength', 2),
        'message' => 'It requires at least two characters',
        ),
      ),
    'owner_email_address'=>array(
      array(
        'rule' => 'notEmpty',
        'message' => 'Please entry owner_email_address',
        ),
      array(
        'rule' => array('minlength', 2),
        'message' => 'It requires at least two characters',
        ),
      array(
        'rule' => array('email'),
        'message' => 'It does not recognize email address',
        ),
      ),
    'owner_email_address'=>array(
      array(
        'rule' => array('minlength', 2),
        'message' => 'It requires at least two characters',
        ),
      array(
        'rule' => array('email'),
        'message' => 'It does not recognize email address',
        ),
      ),
    /*
    'url'=>array(
      array(
        'rule' => 'notEmpty',
        'message' => 'Please entry url',
        ),
      array(
        'rule' => array('url'),
        'message' => 'It does not recognize URL',
        ),
      )
      */
    );
}
