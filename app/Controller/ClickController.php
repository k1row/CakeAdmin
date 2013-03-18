<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class ClickController extends AppController {

	var $name = 'Click';
	public $uses = array('Click');

	public function index() {
		$this -> autoRender = false;
		$params = $this->params['url'];
		
		// set time
		$now = date('Y-m-d h:i:s', time());
		
		// load models
		$this->loadModel('CampaignMaster');
		$this->loadModel('PublisherMaster');
		
		// error checking
		$check = 1;
		
		// set default variables
		$campaign_id = "";
		$publisher_id = "";
		$udid = "";
		$openudid = "";
		$idfa = "";
		$android_id = "";
		$imei = "";
		$macaddr = "";
		$ua = "";
		$created = "";
		$modified = "";
		$url = "";
		
		// check campaign id
		if(isset($this->request->query['campaign_id'])) {
			$data = $this->CampaignMaster->find('all', $params = array(
				'conditions' => array(
					'id' => $this->request->query['campaign_id']
				)));
			if(strtotime($data[0]["CampaignMaster"]["begin_time"]) < strtotime($now) && strtotime($data[0]["CampaignMaster"]["end_time"]) > strtotime($now)) {
				
				if(strlen($this->request->query['campaign_id']) == 9 && is_numeric($this->request->query['campaign_id'])) {
					$campaign_id = $this->request->query['campaign_id'];
				} else {
					$check = 0; // campaign id is incorrect
				}
			} else {
				$check = 0; //campaign isn't running
			}
		} else {
			$check = 0; // no campaign id
		}
		
		// check publisher id
		if(isset($this->request->query['publisher_id'])) {
			$data = $this->PublisherMaster->find('all', $params = array(
				'conditions' => array(
					'id' => $this->request->query['publisher_id']
				)));
			if(isset($data[0]["PublisherMaster"]["id"])) {
			
				if(strlen($this->request->query['publisher_id']) == 9 && is_numeric($this->request->query['publisher_id'])) {
					$publisher_id = $this->request->query['publisher_id'];
				} else {
					$check = 0; // publisher id is incorrect
				}
			} else {
				$check = 0; // publisher doesn't exist
			}
		} else {
			$check = 0; // no publisher id
		}
		
		// check udid
		if(isset($this->request->query['udid'])) {
			if(strlen($this->request->query['udid']) == 40) {
				$udid = $this->request->query['udid'];
			} else {
				$check = 0; // incorrect udid length
				$this->header( 'HTTP/1.1 200 ERROR reason: incorrect udid length' );
			}
		}
		
		// check openudid
		if(isset($this->request->query['openudid'])) {
			if(strlen($this->request->query['openudid']) == 40) {
				$udid = $this->request->query['openudid'];
			} else {
				$check = 0; // incorrect openudid length
			}
		}
		
		// check idfa
		if(isset($this->request->query['idfa'])) {
			$idfa = $this->request->query['idfa'];
		}
		
		// check android id
		if(isset($this->request->query['android_id'])) {
			$android_id = $this->request->query['android_id'];
		}
		
		// check imei
		if(isset($this->request->query['imei'])) {
			$android_id = $this->request->query['imei'];
		}
		
		// check mac address
		if(isset($this->request->query['macaddr'])) {
			if(strlen($this->request->query['macaddr']) >= 10 && strlen($this->request->query['macaddr']) <= 40) {
				$macaddr = $this->request->query['macaddr'];
			} else {
				$check = 0; // incorrect mac address length
			}
		}
		
		// check user agent
		if(isset($this->request->query['ua'])) {
			$ua = $this->request->query['ua'];
		}
		
		// set the time variables to the current time
		$created = $now;
		$modified = $now;
		
		// check for errors
		if($check != 0) {
			// save the click in the database
			$this->Click->set(array(
			            'campaign_id' => $campaign_id,
			            'publisher_id' => $publisher_id,
			            'udid' => $udid,
			            'openudid' => $openudid,
			            'idfa' => $idfa,
			            'android_id' => $android_id,
			            'imei' => $imei,
			            'macaddr' => $macaddr,
			            'ua' => $ua,
			            'created' => $created,
			            'modified' => $modified
			        ));
			$this->Click->save();
			
			$data = $this->PublisherMaster->find('all', $params = array(
				'conditions' => array(
					'id' => $this->request->query['publisher_id']
				)));
			// redirect to store
			if($data[0]["PublisherMaster"]["redirect"] == 1) {
				$data = $this->CampaignMaster->find('all', $params = array(
					'conditions' => array(
						'id' => $this->request->query['campaign_id']
					)));
				$url = $data[0]["CampaignMaster"]["url"];
				header("Location: $url");
			}
		
		}
	}
}
