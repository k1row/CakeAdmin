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
class ConversionController extends AppController {

	var $name = 'Conversion';
	public $uses = array('Conversion');

	public function index() {
		$this -> autoRender = false;
		$params = $this->params['url'];
		
		// set current time and time zone
		
		$now = date('Y-m-d h:i:s', time());
		
		// load models
		$this->loadModel('CampaignMaster');
		$this->loadModel('PublisherMaster');
		$this->loadModel('Click');
		$this->loadModel('PublisherDetail');
		
		// error checking
		$check = 1;
		
		// set default variables
		$campaign_id = "";
		$udid = "";
		$openudid = "";
		$idfa = "";
		$android_id = "";
		$imei = "";
		$macaddr = "";
		$ua = "";
		$created = "";
		$modified = "";
		$publisher_app_id = "";
		$click_id = "";
		
		// check campaign id
		if(isset($this->request->query['campaign_id'])) {
			$data = $this->CampaignMaster->find('all', $params = array(
				'id' => $this->request->query['campaign_id']
				));
			if(strtotime($data[0]["CampaignMaster"]["begin_time"]) < strtotime($now) && strtotime($data[0]["CampaignMaster"]["end_time"]) > strtotime($now)) {
				$campaign_start = strtotime($data[0]["CampaignMaster"]["begin_time"]);
				$campaign_end = strtotime($data[0]["CampaignMaster"]["end_time"]);
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
		
		// check udid
		if(isset($this->request->query['udid'])) {
			if(strlen($this->request->query['udid']) == 40) {
				$udid = $this->request->query['udid'];
			} else {
				$check = 0; // incorrect udid length
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
			if(strlen($this->request->query['macaddr']) >= 10 && strlen($this->request->query['macaddr']) <= 65) {
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
		
		// save data
		if($check != 0) {
			// save the install to the database
			$this->Conversion->set(array(
			            'campaign_id' => $campaign_id,
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
			$this->Conversion->save();
			
			// check install against clicks
			$postback = 0;
			if($udid != "") {
				$find_id = $this->Click->find('first', $params = array(
					'conditions' => array(
					                'udid' => $udid,
					                'campaign_id' => $campaign_id
					)));
				if(isset($find_id["Click"]["created"])){
					if($campaign_start < strtotime($find_id["Click"]["created"]) && $campaign_end > strtotime($find_id["Click"]["created"])) {
						$postback = 1;
					}
				}
			} elseif($openudid != "") {
				$find_id = $this->Click->find('first', $params = array(
					'conditions' => array(
					                'openudid' => $openudid,
					                'campaign_id' => $campaign_id
					)));
				if(isset($find_id["Click"]["created"])){
					if($campaign_start < strtotime($find_id["Click"]["created"]) && $campaign_end > strtotime($find_id["Click"]["created"])) {
						$postback = 1;
					}
				}
			} elseif($idfa != "") {
				$find_id = $this->Click->find('first', $params = array(
					'conditions' => array(
					                'idfa' => $idfa,
					                'campaign_id' => $campaign_id
					)));
				if(isset($find_id["Click"]["created"])){
					if($campaign_start < strtotime($find_id["Click"]["created"]) && $campaign_end > strtotime($find_id["Click"]["created"])) {
						$postback = 1;
					}
				}
			} elseif($android_id != "") {
				$find_id = $this->Click->find('first', $params = array(
					'conditions' => array(
					                'android_id' => $android_id,
					                'campaign_id' => $campaign_id
					)));
				if(isset($find_id["Click"]["created"])){
					if($campaign_start < strtotime($find_id["Click"]["created"]) && $campaign_end > strtotime($find_id["Click"]["created"])) {
						$postback = 1;
					}
				}
			} elseif($imei != "") {
				$find_id = $this->Click->find('first', $params = array(
					'conditions' => array(
					                'imei' => $imei,
					                'campaign_id' => $campaign_id
					)));
				if(isset($find_id["Click"]["created"])){
					if($campaign_start < strtotime($find_id["Click"]["created"]) && $campaign_end > strtotime($find_id["Click"]["created"])) {
						$postback = 1;
					}
				}
			} elseif($macaddr != "") {
				$find_id = $this->Click->find('first', $params = array(
					'conditions' => array(
					                'macaddr' => $macaddr,
					                'campaign_id' => $campaign_id
					)));
				if(isset($find_id["Click"]["created"])){
					if($campaign_start < strtotime($find_id["Click"]["created"]) && $campaign_end > strtotime($find_id["Click"]["created"])) {
						$postback = 1;
					}
				}
			}
			
			// check if there is a publisher ID set in the click and if there should be a postback
			if($postback = 1 && isset($find_id["Click"]["publisher_id"])) {
				// check database for the publisher
				$data = $this->PublisherMaster->find('all', $params = array(
					'conditions' => array(
						'id' => $find_id["Click"]["publisher_id"]
					)));
				// check if the publisher ID exists, the publisher is set to enabled, and that there should be a postback
				if(isset($data[0]["PublisherMaster"]["id"]) && $data[0]["PublisherMaster"]["postback"] == 1 && $data[0]["PublisherMaster"]["enable"] == 1) {
					
					// get publisher app id
					$temp = $this->PublisherDetail->find('all', $params = array(
						'conditions' => array(
							'id' => $find_id["Click"]["publisher_id"],
							'campaign_id' => $this->request->query['campaign_id']
						)));
						
					if(isset($temp[0]["PublisherDetail"]["id"])) {
						$publisher_app_id = $temp[0]["PublisherDetail"]["app_id"];
					}
					
					// set the postback url
					$postback_url = $data[0]["PublisherMaster"]["url"];
					
					// check if publisher click id is set
					if($click_id == "") {
						
						/*if($find_id["Click"]["publisher_click_id"] != NULL) {
							$publisher_click_id = $find_id["Click"]["publisher_click_id"];
							echo($publisher_click_id);
						}*/
					}
					
					// find and replace values in the postback url
					$postback_url = str_replace("UUDDIIDD", $udid, $postback_url); // udid
					$postback_url = str_replace("UDIDOPEN", $openudid, $postback_url); // openudid
					$postback_url = str_replace("IIMMEEII", $imei, $postback_url); // imei
					$postback_url = str_replace("AADDIIDD", $android_id, $postback_url); // android id
					$postback_url = str_replace("IIDDFFAA", $idfa, $postback_url); // idfa
					$postback_url = str_replace("MMAACCAA", $macaddr, $postback_url); // mac address
					$postback_url = str_replace("CCAAMMPP", $publisher_app_id, $postback_url); // publisher campaign/app id
					$postback_url = str_replace("SSUUBBID", $click_id, $postback_url); // publisher click/sub id
					$postback_url = str_replace("UUUUAAAA", $ua, $postback_url); // user agent
					
					// get request for postback
					$result = file_get_contents($postback_url);
					
				}
			}
		}
	}
}
