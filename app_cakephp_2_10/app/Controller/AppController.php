<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	# Set the various components and helpers to use.
	public $helpers = array(
		'Js', 'Html', 'Form', 'Session', 'Select', 'Message','TableForm'
	);

	var $uses = array('Endorsement','ActivityLog','User','Attachment');

	public $components = array(
		'RequestHandler', 'Session','Cookie',
		'Auth' => array(
			'loginAction' => array('controller' => 'Users', 'action' => 'login'),
			'loginRedirect' => array('controller' => 'Router', 'action' => 'index'),
			'logoutRedirect' => array('controller' => 'Users', 'action' => 'login')
		),
		'ExcelPHP','Upload'
	);


	# Finance FeedBack
	var $waybill_feedback = array(
		'Not Yet Approved'=>'Not Yet Approved',
		'Approved'=>'Approved'
	);

	# Finance FeedBack
	var $depot_feedback = array(
		'Loaded'=>'Loaded',
		'Not Loaded'=>'Not Loaded'
	);

	# Finance FeedBack
	var $ceps_feedback = array(
		'Approved'=>'Approved',
		'Not Approved'=>'Not Approved'
	);

	# Operations FeedBack
	var $ops_feedback = array(
		'Approved'=>'Approved - CEPS Approval Required',
		'Finance Required'=>'Finance approval required - please approve.',
		'Not Approved'=>'Not approved - please contact us.'
	);
	# Finance FeedBack
	var $fna_feedback = array(
		'Ok'=>'Ok',
		'Approved'=>'Approved',
		'Not Approved'=>'Not approved - please contact us.'
	);
	# Marketing  FeedBack
	var $mkt_feedback = array(
		'High'=>'High',
		'Medium'=>'Medium',
		'Normal'=>'Normal'
	);

	# Operations FeedBack
	var $omc_dealer_feedback = array(
		//'N/A'=>'N/A',
		//'Processing'=>'Processing',
		'Approved'=>'Approved',
		'Not Approved'=>'Not approved - please contact us.'
	);

	var $omc_dealer_marketing_feedback = array(
		//'N/A'=>'N/A',
		'High Priority'=>'High priority please supply',
		'Medium Priority'=>'Medium priority please supply',
		'Low Priority'=>'Low priority please supply',
		'No Supply'=>'Do not supply'
	);

	# Filter Options For Orders
	var $order_filter = array(
		array('name'=>'Incomplete Orders','value'=>'incomplete_orders'),
		array('name'=>'Complete Orders','value'=>'complete_orders')
	);

	# Omc Customer tank status
	var $omc_customer_tank_status = array(
		'Operational'=>'Operational',
		'Maintenance'=>'Maintenance',
		'Out of Service'=>'Out of Service'
	);

	public $price_change = array();

	public $global_company = array();

	var $company_modules = array();


	function beforeFilter($validate_access_control = true)
	{
		$this->processUserCookie();
		$this->updateUserLoginTime();

		$User = ClassRegistry::init('User');
		$Bdc = ClassRegistry::init('Bdc');
		$Omc = ClassRegistry::init('Omc');
		$OmcCustomer = ClassRegistry::init('OmcCustomer');
		$Org = ClassRegistry::init('Org');
		$Depot = ClassRegistry::init('Depot');
		$Cep = ClassRegistry::init('Cep');

		$authUser = $user_auth = $this->Auth->user();
		$action = $this->params['action'];
		if($action != 'logout'){//If we are not logging out then continue
			$fresh_user_data = $User->getUserById($authUser['id'],-1);
			$company_modules = array();
			if($authUser){
				$user_type = $authUser['user_type'];
				//if ses_entity then use it rather than querying from db.
				if($this->Session->check('ses_entity')){
					$this->global_company = $this->Session->read('ses_entity');
				}
				else{
					if ($user_type == 'bdc') {
						$company_profile = $Bdc->getBdcById($authUser['bdc_id']);
						$this->global_company = $company_profile['Bdc'];
					}
					elseif ($user_type == 'omc') {
						$company_profile = $Omc->getOmcById($authUser['omc_id']);
						$this->global_company = $company_profile['Omc'];
					}
					elseif ($user_type == 'omc_customer') {
						$company_profile = $OmcCustomer->getCustomerById($authUser['omc_customer_id']);
						$this->global_company = $company_profile['OmcCustomer'];
					}
					elseif ($user_type == 'depot') {
						$company_profile = $Depot->getDepotById($authUser['depot_id']);
						$this->global_company = $company_profile['Depot'];
					}
					elseif ($user_type == 'ceps_central' || $user_type == 'ceps_depot') {
						$company_profile = $Cep->getCepById($authUser['cep_id']);
						$this->global_company = $company_profile['Cep'];
					}
					elseif ($user_type == 'org') {
						$company_profile = $Org->getOrgById($authUser['org_id']);
						$this->global_company = $company_profile['Org'];
					}
					else{ //Else if the user type does not much any, log the person out
						$this->Auth->logout();
					}

					//We have to cache the company info so we don't worry mysql, this makes it faster
					$this->Session->write('ses_entity',$this->global_company);
				}
				//Trading for BDC only
				if($user_type == 'bdc'){
					$comp = $this->global_company;
					$StockTrading = ClassRegistry::init('StockTrading');
					if($StockTrading->isTrading($comp['id'])){
						//Continue
						$StockTrading->isTradingOver($comp['id']);
					}
					else{
						/** Before we start trading we have to make sure that all initial stock are set*/
						$this->processTrading($comp['id']);
					}
				}

				$this->getMenus($user_type,$authUser['group_id'],$this->global_company['id'],$validate_access_control);
			}

			$this->getPriceChange();
			$company_profile = $this->global_company;
			$user_type = $authUser['user_type'];
			if($authUser){
				if($user_type == 'omc' || $user_type == 'bdc') {
					$this->getTodayAndYesterday($company_profile['id'], $user_type);
				}
			}
			$this->getMessageCount();
			if($fresh_user_data){
				$auth_user_view = array(
					'shown_welcome'=>$fresh_user_data['User']['shown_welcome'],
					'user_type'=>$fresh_user_data['User']['user_type'],
				);
			}

			$controller = $this->params['controller'];

			$this->set(compact('authUser','auth_user_view','company_profile','company_modules','controller'));
		}
	}


	function logActivity($activity,$description)
	{
		$authUser = $this->Auth->user();
		if($authUser){
			$Bdc = ClassRegistry::init('Bdc');
			$Omc = ClassRegistry::init('Omc');
			$OmcCustomer = ClassRegistry::init('OmcCustomer');
			$Org = ClassRegistry::init('Org');
			$Depot = ClassRegistry::init('Depot');
			$Cep = ClassRegistry::init('Cep');

			$user_type = $authUser['user_type'];
			if ($user_type == 'bdc') {
				$from_id = $authUser['bdc_id'];
				$company_profile = $Bdc->getBdcById($from_id);
				$from = $company_profile['Bdc'];
			}
			elseif ($user_type == 'omc') {
				$from_id = $authUser['omc_id'];
				$company_profile = $Omc->getOmcById($from_id);
				$from = $company_profile['Omc'];
			}
			elseif ($user_type == 'omc_customer') {
				$from_id = $authUser['omc_customer_id'];
				$company_profile = $OmcCustomer->getCustomerById($from_id);
				$from = $company_profile['OmcCustomer'];
			}
			elseif ($user_type == 'depot') {
				$from_id = $authUser['depot_id'];
				$company_profile = $Depot->getDepotById($from_id);
				$from = $company_profile['Depot'];
			}
			elseif ($user_type == 'ceps_central' || $user_type == 'ceps_depot') {
				$from_id = $authUser['cep_id'];
				$company_profile = $Cep->getCepById($from_id);
				$from = $company_profile['Cep'];
			}
			elseif ($user_type == 'org') {
				$from_id = $authUser['org_id'];
				$company_profile = $Org->getOrgById($from_id);
				$from = $company_profile['Org'];
			}
			$group_id = $authUser['group_id'];
			$group_name = $authUser['Group']['name'];
			$data = array(
				'type' => $user_type,
				'entity_id' => $from_id,
				'entity_name' => $from['name'],
				'user_id'=>$authUser['id'],
				'user_full_name'=>$authUser['fname'].' '.$authUser['mname'].' '.$authUser['lname'],
				'user_group_id'=>$group_id,
				'user_group_name'=>$group_name,
				'activity'=>$activity,
				'description'=>$description
			);

			$r  = $this->ActivityLog->logActivity($data);

			return ;

		}
		return false;
	}


	function getLog($user_id, $activity = null,$group_id = null)
	{
		$authUser = $this->Auth->user();
		if($authUser){
			$user_type = $authUser['user_type'];
			if ($user_type == 'bdc') {
				$from = $authUser['bdc_id'];
			}
			elseif ($user_type == 'omc') {
				$from = $authUser['omc_id'];
			}
			elseif ($user_type == 'omc_customer') {
				$from = $authUser['omc_customer_id'];
			}
			elseif ($user_type == 'depot') {
				$from = $authUser['depot_id'];
			}
			elseif ($user_type == 'ceps_central' || $user_type == 'ceps_depot') {
				$from = $authUser['cep_id'];
			}
			elseif ($user_type == 'org') {
				$from = $authUser['org_id'];
			}

			return $this->ActivityLog->getLog($user_type,$from,$user_id,$activity,$group_id);
		}
		return false;
	}

	function getLogTypes(){
		return $this->ActivityLog->getLogTypes();
	}

	function getLogMessage($key){
		return Configure::read($key);
	}

	function  getUserTypesEntities(){
		return $this->User->userTypesEntity();
	}


	function processTrading($bdc_id){
		$action = $this->params['action'];
		$controller = $this->params['controller'];
		$BdcInitialStockStartup = ClassRegistry::init('BdcInitialStockStartup');
		$initial_product_stocks = $BdcInitialStockStartup->getStockStartUp($bdc_id);
		if($initial_product_stocks){
			$StockTrading = ClassRegistry::init('StockTrading');
			$StockTrading->startTrading($bdc_id);
			$show_initial_stock_required_modal = false;
		}
		else{
			if(in_array($controller,array('BdcAdmin','BdcStock','Dashboard')) && in_array($action,array('access_control','admin_depots','admin_products','admin_depots_to_products','initial_startup_stocks'))){
				//Allow
				$show_initial_stock_required_modal = false;
			}
			else{
				//Block all none required pages
				$show_initial_stock_required_modal = true;
			}
		}

		$this->set(compact('show_initial_stock_required_modal'));
	}

	function getMenus($type,$group_id,$comp_id,$validate_access_control)
	{
		$MenuGroup = ClassRegistry::init('MenuGroup');
		$user_menus = $MenuGroup->getGroupMenus($type,$group_id,$comp_id);
		//debug($user_menus);
		if($validate_access_control){
			$this->accessControlValidation($user_menus);
		}
		/* else{
			 debug("Don't validate");
			 exit;
		 }*/
		$permissions = $this->action_permission;
		$this->set(compact('user_menus','permissions'));
	}

	function accessControlValidation($user_menus){
		$action = $this->params['action'];
		$controller = $this->params['controller'];
		$is_allowed = false;
		if(stristr($controller, 'Simulator') !== false) {//Skip for Simulator
			return true;
		}
		if(stristr($controller, 'Messages') !== false) {//Skip for Messages
			return true;
		}
		if(stristr($controller, 'Common') !== false) {//Skip for Common
			return true;
		}
		if(stristr($action, 'print_export') !== false) {//Skip for print and export functions
			return true;
		}
		if(stristr($action, 'export') !== false) {//Skip for export functions
			return true;
		}
		if(stristr($action, 'import') !== false) {//Skip for export functions
			return true;
		}
		if(stristr($action, 'print') !== false) {//Skip for print functions
			return true;
		}
		if(stristr($action, 'attach_files') !== false) {//Skip for file Attachment
			return true;
		}
		if(stristr($action, 'get_attachments') !== false){
			return true;
		}
		if(stristr($action, 'add_dsrp_options') !== false){
			return true;
		}
		if(stristr($action, 'view') !== false){
			return true;
		}

		//Allow All dashboards
		$con_arr = array('Bdc','Omc','Ceps','Depot','OmcCustomer','Npa');
		$act_arr = array('dashboard');
		if(in_array($controller,$con_arr) && in_array($action,$act_arr)){
			return true;
		}

		foreach($user_menus as $menu){
			if($is_allowed){
				break;
			}
			if(isset($menu['sub'])){
				foreach($menu['sub'] as $inner_um){
					if($controller == $inner_um['controller'] && $action == $inner_um['action']){
						$is_allowed = true;
						$this->action_permission = explode(',',$inner_um['permission']);

						break;
					}
				}
			}
			else{
				if($controller == $menu['controller'] && $action == $menu['action']){
					$is_allowed = true;
					$this->action_permission = explode(',',$menu['permission']);
				}
			}
		}

		if(!$is_allowed){//if not allowed, redirect to Router
			$this->redirect(array('controller' => 'Router', 'action' => 'index'));
		}
	}


	function getPriceChange()
	{
		$PriceChange = ClassRegistry::init('PriceChange');
		$price_change = $PriceChange->getPriceQuotes();
		$this->price_change = $price_change;
		$this->set(compact('price_change'));
	}

	function getPriceChangeData()
	{
		$PriceChange = ClassRegistry::init('PriceChange');
		$price_change = $PriceChange->getPriceQuotesData();
		return $price_change;
	}

	function cleanInput($input) {
		$search = array(
			'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
		);
		$output = preg_replace($search, '', $input);
		return $output;
	}

	function sanitize($input) {
		//debug($input);
		if (is_array($input)) {
			$output = array();
			foreach($input as $var=>$val) {
				$output[$var] = $this->sanitize($val);
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$input = stripslashes($input);
			}
			$input  = $this->cleanInput($input);
			$new_enough_php = function_exists("mysql_real_escape_string");//i.e. PHP >= v4.3.0
			if($new_enough_php)
			{//PHP v4.3.0 or higher
				//undo any magic quote effects so mysql_real_escape_string can do the work
				// $output = mysql_real_escape_string($input);
			}
			$output = $input;
		}
		return $output;
	}

	function endorse($document_type,$document_id)
	{
		$authUser = $user_auth = $this->Auth->user();
		if($authUser){
			$User = ClassRegistry::init('User');
			$Bdc = ClassRegistry::init('Bdc');
			$Omc = ClassRegistry::init('Omc');
			$OmcCustomer = ClassRegistry::init('OmcCustomer');
			$Org = ClassRegistry::init('Org');
			$Depot = ClassRegistry::init('Depot');
			$Cep = ClassRegistry::init('Cep');

			$user_type = $authUser['user_type'];
			if ($user_type == 'bdc') {
				$from_id = $authUser['bdc_id'];
				$company_profile = $Bdc->getBdcById($from_id);
				$from = $company_profile['Bdc'];
			}
			elseif ($user_type == 'omc') {
				$from_id = $authUser['omc_id'];
				$company_profile = $Omc->getOmcById($from_id);
				$from = $company_profile['Omc'];
			}
			elseif ($user_type == 'omc_customer') {
				$from_id = $authUser['omc_customer_id'];
				$company_profile = $OmcCustomer->getCustomerById($from_id);
				$from = $company_profile['OmcCustomer'];
			}
			elseif ($user_type == 'depot') {
				$from_id = $authUser['depot_id'];
				$company_profile = $Depot->getDepotById($from_id);
				$from = $company_profile['Depot'];
			}
			elseif ($user_type == 'ceps_central' || $user_type == 'ceps_depot') {
				$from_id = $authUser['cep_id'];
				$company_profile = $Cep->getCepById($from_id);
				$from = $company_profile['Cep'];
			}
			elseif ($user_type == 'org') {
				$from_id = $authUser['org_id'];
				$company_profile = $Org->getOrgById($from_id);
				$from = $company_profile['Org'];
			}

			$data = array(
				'type' => $user_type,
				'from_id' => $from_id,
				'from' => $from['name'],
				'document_type' => $document_type,
				'document_id' => $document_id,
				'endorsed_by_id'=>$authUser['id'],
				'endorsed_by_name'=>$authUser['fname'].' '.$authUser['mname'].' '.$authUser['lname']
			);
			return $this->Endorsement->endorse($data);
		}
		return false;
	}


	function getEndorsement($document_type,$document_id)
	{
		$authUser = $user_auth = $this->Auth->user();
		if($authUser){
			$user_type = $authUser['user_type'];
			if ($user_type == 'bdc') {
				$from = $authUser['bdc_id'];
			}
			elseif ($user_type == 'omc') {
				$from = $authUser['omc_id'];
			}
			elseif ($user_type == 'omc_customer') {
				$from = $authUser['omc_customer_id'];
			}
			elseif ($user_type == 'depot') {
				$from = $authUser['depot_id'];
			}
			elseif ($user_type == 'ceps_central' || $user_type == 'ceps_depot') {
				$from = $authUser['cep_id'];
			}
			elseif ($user_type == 'org') {
				$from = $authUser['org_id'];
			}

			return $this->Endorsement->getEndorsement($user_type,$from,$document_type,$document_id);
		}
		return false;
	}


	function getSignatories($document_type,$document_id)
	{
		return $this->Endorsement->getSignatories($document_type,$document_id);
	}


	function get_product_group(){
		//$lists = array('White Products','LPG','Premix','MGO','Kero','RFO','Naphtha');
		$lists = array('White Products','LPG','Premix','MGO','Kero');
		return $lists;
	}

	function updateUserLoginTime()
	{
		$authUser = $this->Auth->user();
		if($authUser){
			$User = ClassRegistry::init('User');
			$User->updateAll(
				$this->sanitize(array('User.login_update_time' => "'".date('Y-m-d H:i:s')."'")),
				$this->sanitize(array('User.id' => $authUser['id']))
			);
		}
	}

	function processUserCookie()
	{
		if($this->Auth->user()){}
		else{//If the session has expired, then read from the cookie and clear the login session and time from the db
			$user_id = $this->Cookie->read('c_user_id');
			if($user_id != null){
				$User = ClassRegistry::init('User');
				$User->updateAll(
				//array('User.login_session' => "''",'User.login_update_time' => "''"),
					$this->sanitize(array('User.login_session' => "''")),
					$this->sanitize(array('User.id' => $user_id))
				);
				$this->Cookie->delete('c_user_id');
			}
		}
	}


	protected  function convertToExcel($list_headers,$param ,$filename)
	{
		ini_set("memory_limit", "512M") ;
		set_time_limit('1200');

		$company_profile = $this->global_company;

		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("WARP-D_".$company_profile['name'])
			->setLastModifiedBy("WARP-D_".$company_profile['name'])
			->setTitle("Office 2007 XLSX WARP-D ".$company_profile['name']." Document")
			->setSubject("Office 2007 XLSX WARP-D ".$company_profile['name']." Document")
			->setDescription($company_profile['name']." document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory($company_profile['name']." result file");

		// Set default font
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

		$count_h = 1;
		foreach($list_headers as $header){
			$letter_h = $this->getAlphabetCharacter($count_h);
			$objPHPExcel->getActiveSheet()->setCellValue($letter_h.'1', $header);
			$count_h++;
		}
		//Bold the headers
		$letter_h = $this->getAlphabetCharacter($count_h -1);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$letter_h.'1')->getFont()->setBold(true);

		$it = 2;
		foreach ($param as $item) {
			$count_inner = 1;
			foreach($item as $itm){
				$letter_h = $this->getAlphabetCharacter($count_inner);
				$objPHPExcel->getActiveSheet()->setCellValue($letter_h.$it, strip_tags($itm));
				$count_inner++;
			}
			$it++;
		}

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Page_1');

		$objPHPExcel->setActiveSheetIndex(0);

		return array(
			'excel_obj' => $objPHPExcel,
			'filename' => $filename
		);
	}


	protected  function convertToExcelBook($params ,$filename)
	{
		ini_set("memory_limit", "512M") ;
		set_time_limit('1200');

		$company_profile = $this->global_company;

		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("WARP-D_".$company_profile['name'])
			->setLastModifiedBy("WARP-D_".$company_profile['name'])
			->setTitle("Office 2007 XLSX WARP-D ".$company_profile['name']." Document")
			->setSubject("Office 2007 XLSX WARP-D ".$company_profile['name']." Document")
			->setDescription($company_profile['name']." document for Office 2007 XLSX, generated using PHP classes.")
			->setKeywords("office 2007 openxml php")
			->setCategory($company_profile['name']." result file");

		// Set default font
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

		/*$export_data = array(
			   array('header'=>array('A','B','C'),'data'=>array(array('1','2','3'),array('4','5','6')),'sheet_name'=>'Sheet A'),
			   array('header'=>array('E','F','G'),'data'=>array(array('7','8','9'),array('10','11','12')),'sheet_name'=>'Sheet B'),
			   array('header'=>array('H','I','J'),'data'=>array(array('13','14','15'),array('17','18','19')),'sheet_name'=>'Sheet C')
		   );*/

		$sheet_count = 0;
		foreach($params as $excel_arr){
			$list_headers = $excel_arr['header'];
			$sheet_data = $excel_arr['data'];
			$sheet_name = $excel_arr['sheet_name'];

			// create another sheet for upload Details
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($sheet_count);

			$count_h = 1;
			foreach($list_headers as $header){
				$letter_h = $this->getAlphabetCharacter($count_h);
				$objPHPExcel->getActiveSheet()->setCellValue($letter_h.'1', $header);
				$count_h++;
			}
			//Bold the headers
			$letter_h = $this->getAlphabetCharacter($count_h -1);
			$objPHPExcel->getActiveSheet()->getStyle('A1:'.$letter_h.'1')->getFont()->setBold(true);

			$it = 2;
			foreach ($sheet_data as $item) {
				$count_inner = 1;
				foreach($item as $itm){
					$letter_h = $this->getAlphabetCharacter($count_inner);
					$objPHPExcel->getActiveSheet()->setCellValue($letter_h.$it, strip_tags($itm));
					$count_inner++;
				}
				$it++;
			}

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle($sheet_name);

			$sheet_count++;
		}

		$objPHPExcel->setActiveSheetIndex(0);

		return array(
			'excel_obj' => $objPHPExcel,
			'filename' => $filename
		);
	}


	function uploadFile($params)
	{  # Get the user session data
		# folder path structure
		$folder = $params['save_path'];//'files/tickets_attachments/';
		$folder_db = $params['folder'];//'tickets_attachments/';
		$file_size =  $params['file_size'];
		$prefix =  $params['file_name_prefix'];

		# setup directory pathname
		$folderAbsPath = WWW_ROOT . $folder;
		$folderRelPath = $folder;

		# create folders if it does not exist
		if (!is_dir($folderAbsPath)) {
			mkdir($folderAbsPath, 0777, true);
		}

		# get the file details
		$rawFile = $folderAbsPath . $prefix.basename($params['file_name']);//($_FILES['uploadfile']['name']);
		$rawFile_rel = $folder_db . $prefix. basename($params['file_name']); //($_FILES['uploadfile']['name']);

		// replace spaces with underscores
		$raw_file_name = str_replace(' ', '_', $prefix.$params['file_name']);
		$fileName = str_replace(' ', '_', $rawFile);
		$fileName_rel = str_replace(' ', '_', $rawFile_rel);
		$fileType = $params['file_type'];//$_FILES['uploadfile']['type'];
		$typeOk = false;
		if(!empty($params['check_file_type'])){
			foreach ($params['check_file_type'] as $type) {
				//echo $type .'=='. $fileType.'<br />';
				if ($type == $fileType) {
					$typeOk = true;
					break;
				}
			}
		}
		else{
			$typeOk = true;
		}

		# list of permitted file types, this is only images but documents can be added
		// $permitted = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');

		# if typeOk upload the file
		if ($typeOk) {
			# check whether the file already exists or not
			if (!file_exists($fileName)) {
				# upload the file
				if (move_uploaded_file($params['file_tmp_name'], $fileName)) {//$_FILES['uploadfile']['tmp_name']
					return array('status'=>true, 'file_name'=>$raw_file_name, 'file_path'=>$fileName_rel,'msg'=>'File Uploaded.');
				} else {
					return array('status'=>false,'msg'=>'File Cannot Be Uploaded.');
				}
			} else {
				return array('status'=>false,'msg'=>'File Already Exist.');
			}
		} else {
			return array('status'=>false,'msg'=>'Invalid File Format.');
		}
	}



	function getAlphabetCharacter($index)
	{
		$alphabet = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G',8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P'
		,17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z');
		return $alphabet[$index];
	}

	function randomString($length)
	{
		$original_string = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'),array(time()),array('_','!','?','=','-','[',']','+','$','#','^','&'));
		$original_string = implode("", $original_string);
		return substr(str_shuffle($original_string), 0, $length);
	}

	function covertDate($date = null, $type = 'mysql')
	{
		$res = '';
		if ($type == 'mysql') {
			$res = date("Y-m-d", strtotime($date));
		}
		elseif ($type == 'mysql_flip') {
			$res = date("d-m-Y", strtotime($date));
		}
		elseif ($type == 'ui') {
			$res = date("F d, Y", strtotime($date));
		}
		elseif($type == 'formal'){
			$res = date("m/d/Y", strtotime($date));
		}
		return $res;
	}


	function getDays()
	{
		$start = 1;
		$end = 31;
		$days_arr = array();
		for($start; $start<=$end; $start++){
			$days_arr[$start] = $start;
		}
		return $days_arr;
	}

	function getMonths($index = null)
	{
		$months = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
		if($index){
			return $months[$index];
		}
		else{
			return $months;
		}
	}

	function getYears($start = 2012, $end = 'CURRENT YEAR')
	{
		if($end == 'CURRENT YEAR' ){
			$end = date('Y');
		}
		$start = (int)$start;
		$end = (int)$end;
		$years_arr = array();
		for($start; $start<=$end; $start++){
			$years_arr[$start] = $start;
		}

		return $years_arr;
	}

	function getTanksProductTypes($index = null)
	{
		$pro = array('AGO'=>'AGO','PMS'=>'PMS','Kerosene'=>'Kerosene','Premix'=>'Premix','MGO'=>'MGO','LPG'=>'LPG','RFO'=>'RFO');
		if($index){
			return $pro[$index];
		}
		else{
			return $pro;
		}
	}

	function get_product_list($product_ids = null){
		$products_type = $this->ProductType->getProductList($product_ids);
		return $products_type;
	}


	function count_time_between_dates($smaller_date, $bigger_date,$count_type='months')
	{
		$d1 = strtotime($smaller_date); //Smaller
		$d2 = strtotime($bigger_date); //Bigger
		$min_date = min($d1, $d2);
		$max_date = max($d1, $d2);
		$i = 0;
		$type = "+1 month";
		if($count_type == 'hours'){
			$type = "+1 hours";
		}
		elseif($count_type == 'days'){
			$type = "+1 day";
		}
		elseif($count_type == 'weeks'){
			$type = "+1 week";
		}
		elseif($count_type == 'years'){
			$type = "+1 year";
		}
		while (($min_date = strtotime($type, $min_date)) <= $max_date) {
			$i++;
		}
		return $i;
	}


	function formatNumber($value = 0, $type = 'number', $decimal_place = 2)
	{
		$value = floatval($value);
		if ($type == 'money') {
			$num = number_format($value, $decimal_place, '.', ',');
		} else {
			$num = number_format($value, $decimal_place, '.', '');
		}

		return $num;
	}


	function __add_ordinal_suffix($num)
	{
		$last = substr($num, -1);
		if (strlen($num) < 2) {
			$next_to_last = 0;
		} else {
			$next_to_last = substr($num, -2);
		}
		if ($next_to_last >= 10 && $next_to_last < 20) {
			$suff = "th";
		} else
			if ($last == 1) {
				$suff = "st";
			} else
				if ($last == 2) {
					$suff = "nd";
				} else
					if ($last == 3) {
						$suff = "rd";
					} else {
						$suff = "th";
					}

		return number_format($num) . $suff;
	}

	function getMessageCount()
	{
		$authUser = $this->Auth->user();
		$MessageReciever = ClassRegistry::init('MessageReciever');
		$user_inbox = $MessageReciever->messageCount($authUser['id']);
		$this->set(compact('user_inbox'));
		return $user_inbox;
	}


	function getTodayAndYesterday($id, $type = 'bdc')
	{
		$BdcDistribution = ClassRegistry::init('BdcDistribution');
		$result = $BdcDistribution->getTodayAndYesterday($id, $type);
		$totals['today'] = $this->formatNumber($result['today'], 'money');
		$totals['yesterday'] = $this->formatNumber($result['yesterday'], 'money');
		$today_yesterday_totals = $totals;
		$this->set(compact('today_yesterday_totals'));

		return $totals;
	}

	function getTodayConsolidated($id, $type = 'bdc')
	{
		$BdcDistribution = ClassRegistry::init('BdcDistribution');
		return $BdcDistribution->getTodayConsolidated($id, $type);

	}


	function getWeekDates()
	{
		$today = date('Y-m-d');
		//w Numeric representation of the day of the week 0 (for Sunday) through 6 (for Saturday)
		$dates_arr = array();
		$day_of_week = intval(date("w"));
		$dates_arr[$day_of_week] = $today;
		$start = $day_of_week - 1;
		$reduce = 1;
		for ($x = $start; $x >= 0; $x--) {
			$today = date('Y-m-d', strtotime("-$reduce day"));
			$dates_arr[$x] = $today;
			$reduce++;
		}
		return $dates_arr;
	}

	function getBarGraphData($id, $type = 'bdc')
	{
		$BdcDistribution = ClassRegistry::init('BdcDistribution');
		$dates_arr = $this->getWeekDates();
		return $BdcDistribution->getBarGraphData($id, $type,$dates_arr);
	}


	function getOrders($type = 'bdc',$id, $start_dt,$end_dt,$group_by,$filter_bdc,$filter_omc)
	{
		$Order = ClassRegistry::init('Order');
		return $Order->getOrders($type,$id, $start_dt,$end_dt,$group_by,$filter_bdc,$filter_omc);

	}


	function send_message($params,$plist=null){
		$loggedUser = $this->Auth->user();
		$user_id = $loggedUser['id'];

		# disable the rendering of the layout
		$this->autoRender = false;
		$this->autoLayout = false;

		$User = ClassRegistry::init('User');
		$Message = ClassRegistry::init('Message');
		$list = array();
		//Build the Message receivers if any
		if(isset($params['Message']['to'])){
			$tos = $params['Message']['to'];
			$usernames = explode(',',$tos);
			$find_list = array();
			foreach($usernames as $usr){
				$find_list[] = trim($usr);
			}
			$find_list = array_unique($find_list);
			$list = $User->find('list', array(
				'fields'=>array('User.id'),
				'conditions' => array('User.username' => $find_list),
				'recursive' => -1
			));
		}
		elseif($plist){
			$list = $plist;
		}

		if(!$list){
			return array('code' => 1, 'mesg' => __('Reciepient(s) Not Found'));
		}

		$receivers = array('MessageReciever'=>array());
		foreach($list as $user){
			$receivers['MessageReciever'][] = array(
				'user_id'=>$user
			);
		}
		$params['MessageReciever'] = $receivers['MessageReciever'];

		$save = $Message->saveAll($this->sanitize($params));

		# save the data
		if ($save) {
			//Save to address book
			return array('code' => 0, 'mesg' => __('Message Sent'));
		}
		else {
			return array('code' => 1, 'mesg' => __('Message Not Sent'));
		}
	}

	function sendMessage($params){
		if($params['sender_type'] == 'blast'){
			$all_users = $this->getMessageAddressList($params['entity'],$params['entity_id'],$params['include_this_entity'],$params['sender']);
		}
		elseif($params['sender_type'] == 'internal'){
			$all_users = $this->getMessageAddressList(null,null,true,$params['sender']);
		}
		else{
			$all_users = $params['all_users'];
		}

		$message['Message']=array(
			'title'=>$params['title'],
			'content'=>$params['content'],
			'user_id'=>$params['sender'],
			'msg_type'=>$params['msg_type']
		);
		return $this->send_message($message,$all_users);
	}

	function getMessageAddressList($entity,$entity_id,$include_this_entity,$sender)
	{
		$user_comp = array(
			'bdc'=>'bdc_id',
			'omc'=>'omc_id',
			'omc_customer'=>'omc_customer_id',
			'org'=>'org_id',
			'ceps_depot'=>'cep_id',
			'ceps_central'=>'cep_id',
			'depot'=>'depot_id'
		);
		$entity_users = array();
		if($entity != null && $entity_id !=null){
			$find_id = $user_comp[$entity];
			$conditions_entity = array('User.'.$find_id => $entity_id,'User.deleted'=>'n');
			$entity_users = $this->User->find('list', array(
				'fields'=>array('User.id'),
				'conditions' => $conditions_entity,
				'recursive' => -1
			));
		}
		$my_entity_users = array();
		if($include_this_entity){
			$authUser = $user_auth = $this->Auth->user();
			$user_type = $authUser['user_type'];

			$company_profile = $this->global_company;
			$find_id = $user_comp[$user_type];
			$conditions_entity = array('User.'.$find_id => $company_profile['id'] ,'User.deleted'=>'n');
			$my_entity_users = $this->User->find('list', array(
				'fields'=>array('User.id'),
				'conditions' => $conditions_entity,
				'recursive' => -1
			));
		}

		$all_users = array_merge($entity_users,$my_entity_users);
		return $all_users;
	}


	function get_region_district(){
		$Region = ClassRegistry::init('Region');
		$company_profile = $this->global_company;
		return $Region->getRegionDistrict();
	}

	function get_loading_board($depot=0){
		$Order = ClassRegistry::init('Order');
		$company_profile = $this->global_company;
		$authUser = $user_auth = $this->Auth->user();
		$user_type = $authUser['user_type'];

		return $Order->loadingToday($user_type,$company_profile['id'],$depot);
	}

	function get_loaded_board($depot=0){
		$Order = ClassRegistry::init('Order');
		$company_profile = $this->global_company;
		$authUser = $user_auth = $this->Auth->user();
		$user_type = $authUser['user_type'];

		return $Order->loadedToday($user_type,$company_profile['id'],$depot);
	}


	function getEntityUsers()
	{
		$user_comp = array(
			'bdc'=>'bdc_id',
			'omc'=>'omc_id',
			'omc_customer'=>'omc_customer_id',
			'org'=>'org_id',
			'ceps_depot'=>'cep_id',
			'ceps_central'=>'cep_id',
			'depot'=>'depot_id'
		);

		$my_entity_users = array();
		$authUser = $user_auth = $this->Auth->user();
		$user_type = $authUser['user_type'];

		$company_profile = $this->global_company;
		$find_id = $user_comp[$user_type];
		$conditions_entity = array('User.'.$find_id => $company_profile['id'] ,'User.deleted'=>'n');
		$my_entity_users = $this->User->find('all', array(
			'fields'=>array('User.id','User.username','User.fname','User.mname','User.lname'),
			'conditions' => $conditions_entity,
			'recursive' => -1
		));

		return $my_entity_users;
	}

	function attachment_get_upload_dir(){
		$company_profile = $this->global_company;
		$type_id = $_POST['type_id'];
		$comp =   $company_profile['comp_type'].'/'.$company_profile['id'].'/';
		$path = $comp.$type_id;
		$type = $_POST['type'];
		$save_to = 'Orders/'.$path;
		if($type == 'Waybill'){
			$save_to = 'Waybills/'.$path;
		}
		elseif($type == 'Customer Order'){
			$save_to = 'CustomerOrders/'.$path;
		}
		return $save_to;
	}

	function attachment($print_response){
		$save_to = $this->attachment_get_upload_dir();
		if($print_response){
			$this->Upload->upload(array('save_to'=>$save_to));
		}
		else{
			return $this->Upload->upload(array('save_to'=>$save_to),false);
		}
	}

	function attachment_fire_response($content){
		$this->Upload->fire_response($content, true);
	}

	function get_webroot_url() {
		$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
		return
			($https ? 'https://' : 'http://').
			(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
			(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
				($https && $_SERVER['SERVER_PORT'] === 443 ||
				$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
			substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
	}

	function __get_attachments($attachment_type,$attachment_type_id = null){
		$this->autoRender = false;
		$company_profile = $this->global_company;
		$attachment_data = $this->Attachment->get_attachments($attachment_type_id,$attachment_type,$company_profile['id']);
		$result = array('file'=>array());
		$webroot_url = $this->get_webroot_url();
		$thumbnail = "thumbnail";
		foreach($attachment_data as $rec){
			$upload_by = $rec['Attachment']['upload_by'];
			$upload_from = $rec['Attachment']['upload_from'];
			$path = $rec['Attachment']['path'];
			$file_name = $rec['Attachment']['file_name'];
			$file_size = $rec['Attachment']['file_size'];
			$file_url = $webroot_url.'/'.$path.'/'.$file_name;
			$thumbnailUrl = $webroot_url.'/'.$path.'/'.$thumbnail.'/'.$file_name;
			$result['files'][]=array(
				"deleteType"=> "DELETE",
				"deleteUrl"=> $thumbnailUrl,
				"name"=> $file_name,
				"size"=> intval($file_size),
				"thumbnailUrl"=> $thumbnailUrl,
				"upload_by"=> $upload_by,
				"upload_from"=>$upload_from,
				"url"=> $file_url
			);
		}

		return $result;
	}


	function __attach_files(){
		$this->autoRender = false;
		$type_id = $_POST['type_id'];
		$type = $_POST['type'];
		$log_activity_type = $_POST['log_activity_type'];
		$upload_by = $_POST['upload_by'];
		$upload_by_id = $_POST['upload_by_id'];
		$upload_from = $_POST['upload_from'];
		$upload_from_id = $_POST['upload_from_id'];
		$print_response = false;
		$save_to = $this->attachment_get_upload_dir();
		$upload_data = $this->attachment($print_response);
		$save_files_to_db  = array();
		foreach($upload_data['files'] as $key => $file){
			$upload_data['files'][$key]['upload_by'] = $upload_by;
			$upload_data['files'][$key]['upload_from'] = $upload_from;
			if(!isset($file['error'])){
				$save_files_to_db[]= array(
					'type'=>$type,
					'type_id'=>$type_id,
					'file_name'=>$file['name'],
					"file_size"=> $file['size'],
					'path'=>"files/".$save_to,
					'upload_by'=>$upload_by,
					'upload_by_id'=>$upload_by_id,
					'upload_from'=>$upload_from,
					'upload_from_id'=>$upload_from_id
				);
				//Activity Log
				$to_low = strtolower($type);
				$log_description = $this->getLogMessage('Attachment')." (".$file['name'].") to the $to_low #".$type_id;
				$this->logActivity($log_activity_type,$log_description);
			}
		}
		$this->Attachment->saveAll($save_files_to_db);
		return $upload_data;
	}


	function getDSRPoptions($index = null){
		$opt = array(
			'bsp'=>"Bulk Stock Position",
			'bsc'=>"Bulk Stock Calculation",
			'dsp'=>"Daily Sales Report",
			'ccs'=>"Cash & Credit Summary",
			'opc'=>"Operator's Credit",
			'cmc'=>"Customers Credit",
			'lbp'=>"Lubricants Position",
		);

		if($index){
			$r = isset($opt[$index]) ? $opt[$index] : '';
			return $r;
		}
		else{
			return $opt;
		}
	}

}
