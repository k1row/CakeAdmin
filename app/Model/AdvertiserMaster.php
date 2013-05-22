<?php
class AdvertiserMaster extends AppModel
{
  var $name = "AdvertiserMaster";

  public $validate = array(
    'company_name' => array(
      array(
        'rule' => 'notEmpty',
        'message' => 'Please entry company_name',
        ),
      array(
        'rule' => array('minlength', 2),
        'message' => 'It requires at least two characters',
        ),
      ),
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
    );
}
