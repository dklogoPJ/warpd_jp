<?php

/**
 * @name OmcController.php
 */
App::import('Controller', 'OmcApp');

class OmcUppfController extends OmcAppController
{
    # Controller name

    var $name = 'OmcUppf';
    # set the model to use
    var $uses = array('BdcDistribution','OmcBdcDistribution', 'OmcCustomer','BdcUser','OmcUser', 'BdcOmc', 'User', 'Depot', 'District', 'ProductType', 'Region','Bdc','Order','FreightRate','DeliveryLocation','FreightRateCategory','DeliveryLoc','FreightRateTemp','Omc');

    # Set the layout to use
    var $layout = 'omc_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('omc_user_types'=>array('Allow All')));
    }


    function index()
    {
        $this->redirect('uppf');
    }


    function uppf() {
        $company_profile = $this->global_company;
        $start_dt = date('Y-m-01');
        $end_dt = date('Y-m-t');
        $product_group = 'Premix';
        $default_product_group = 'Premix';
        $product_group_name = 'On Premix';

        if($this->request->is('post')){
            //$start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $start_dt = $this->request->data['Query']['start_dt'];
            //$end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            $end_dt = $this->request->data['Query']['end_dt'];
            if($this->request->data['Query']['product_group'] != 'all'){
                $product_group = $this->request->data['Query']['product_group'];
                $default_product_group = $this->request->data['Query']['product_group'];
                $product_group_name = 'On '.$this->request->data['Query']['product_group_name'];
            }
        }

        $g_data = $this->getUppf($this->covertDate($start_dt,'mysql'),$this->covertDate($end_dt,'mysql'),$product_group);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $table_title = 'UPPF Returns '.$product_group_name;

        //Get all product group
        $product_group_data = $this->get_product_group();
        //$product_group_list = array('all'=>'All Product Type');
        $product_group_list = array();
        foreach($product_group_data as $arr){
            $product_group_list[$arr] = $arr;
        }

        $controller = $this;
        $this->set(compact('controller','table_title','t_head','t_body_data','product_group_list','end_dt','start_dt','default_product_group'));
    }


    function print_export_uppf(){
        $download = false;
        $company_profile = $this->global_company;

        $media_type = $_POST['data_type'];
        $start_dt = $this->covertDate($_POST['data_start_dt'],'mysql');
        $end_dt = $this->covertDate($_POST['data_end_dt'],'mysql');
        $product_group = $_POST['data_product_group'];
        $product_group_name = 'On '.$_POST['data_product_group_name'];
        if($product_group == 'all'){
            $product_group = null;
            $product_group_name = '';
        }
        $g_data = $this->getUppf($start_dt,$end_dt,$product_group);
        $t_head = $g_data['t_head'];
        $t_body_data = $g_data['t_body_data'];

        $table_title = $export_title = 'UPPF Returns '.$product_group_name;

        $list_data = $t_body_data;

        $this->autoLayout = false;

        if($media_type == 'export'){
            if(empty($list_data)){
                $download = false;
            }
            else{
                $download = true;
                $list_headers = $t_head;
                $filename = $export_title;
                $res = $this->convertToExcel($list_headers,$list_data,$filename);
                $objPHPExcel = $res['excel_obj'];
                $filename = $res['filename'];
            }
        }
        elseif($media_type == 'print'){
            $this->autoLayout = true;
            $this->layout = 'print_layout';
        }
        $print_title = $table_title;
        $controller = $this;

        $this->set(compact('controller','company_profile','print_title','table_title','t_head','t_body_data','graph_title','objPHPExcel', 'download', 'filename','media_type','grid_data'));
    }



    function distances($type = 'get')
    {
        $company_profile = $this->global_company;
        $permissions = $this->action_permission;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** @var $filter  */
                    $filter_depot =   isset($_POST['filter_depot']) ? $_POST['filter_depot'] : 0 ;
                    $filter_region =   isset($_POST['filter_region']) ? $_POST['filter_region'] : 0 ;
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('DeliveryLocation.deleted' => 'n');
                    if($filter_depot != 0){
                        $condition_array['DeliveryLocation.depot_id'] = $filter_depot;
                    }
                    if($filter_region != 0){
                        $condition_array['DeliveryLocation.region_id'] = $filter_region;
                    }

                    if (!empty($search_query)) {
                        if ($qtype == 'username') {
                            /*$condition_array = array(
                                'User.username' => $search_query,
                                'User.deleted' => 'n'
                            );*/
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
                        'Region'=>array('fields' => array('Region.id', 'Region.name'))
                    );

                    $data_table = $this->DeliveryLocation->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "DeliveryLocation.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->DeliveryLocation->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['DeliveryLocation']['id'],
                                'cell' => array(
                                    $obj['DeliveryLocation']['id'],
                                    $obj['Depot']['name'],
                                    $obj['DeliveryLocation']['name'],
                                    $obj['Region']['name'],
                                    $obj['DeliveryLocation']['distance'],
                                    $obj['DeliveryLocation']['alternate_route']
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    if ($_POST['id'] == 0) {//Mew
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    //check if username does not exist for in this company
                    $data = array('DeliveryLocation' => $_POST);
                    $data['DeliveryLocation']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['DeliveryLocation']['created_by'] = $this->Auth->user('id');
                    }


                    if ($this->DeliveryLocation->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifyDeliveryLocation')." (Location: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $new_user = $_POST['name'];
                            $log_description = $this->getLogMessage('CreateDeliveryLocation')." (Location: ".$new_user.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->DeliveryLocation->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved!', 'data' => $dt));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }

                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('DeliveryLocation');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('DeliveryLocation.deleted' => "'y'",'DeliveryLocation.modified' => "$now",'DeliveryLocation.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('DeliveryLocation.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $depot_lists = $this->get_depot_list();
        $places_data = $this->get_region_district();
        $glbl_region_district = $places_data['region_district'];
        $regions_lists = $places_data['region'];

        $filter_depots =array(array('name'=>'All','value'=>0));
        foreach($depot_lists as $arr){
            $filter_depots[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }

        $filter_region =array(array('name'=>'All','value'=>0));
        foreach($regions_lists as $arr){
            $filter_region[] = array('name'=>$arr['name'],'value'=>$arr['id']);
        }

        $controller = $this;
        $this->set(compact('controller', 'depot_lists','filter_depots','regions_lists','filter_region'));
    }


    function export_distances($filter_depot =0 , $filter_region = 0){
        $download = false;
        $condition_array = array('DeliveryLocation.deleted' => 'n');
        if($filter_depot != 0){
            $condition_array['DeliveryLocation.depot_id'] = $filter_depot;
        }
        if($filter_region != 0){
            $condition_array['DeliveryLocation.region_id'] = $filter_region;
        }

        $contain = array(
            'Depot'=>array('fields' => array('Depot.id', 'Depot.name')),
            'Region'=>array('fields' => array('Region.id', 'Region.name'))
        );
        $export_data = $this->DeliveryLocation->find('all', array(
            'conditions' => $condition_array,
            'contain'=>$contain,
            'order' => array("DeliveryLocation.id"=>'asc'),
            'recursive' => 1
        ));

        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['DeliveryLocation']['id'],
                    $obj['Depot']['id'],
                    $obj['Depot']['name'],
                    $obj['DeliveryLocation']['name'],
                    $obj['Region']['id'],
                    $obj['Region']['name'],
                    $obj['DeliveryLocation']['distance'],
                    $obj['DeliveryLocation']['alternate_route']
                );
            }
            $list_headers = array('Id','Depot Id','Depot','Location','Region Id','Region','Distance','Alternate Route');
            $filename = "UPPF Distances";
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }

        $this->autoLayout = false;
        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


    function import_distances(){
        $mesg = false;
        $this->layout = 'import_layout';
        if($this->request->is('post')){
            $upload_file = false;
            $save_raw = $save = $this->request->data;
            if(isset($save_raw['Import']['attach'])){
                if(!empty($save_raw['Import']['attach']['name'])){
                    $upload_file = true;
                }
            }
            $upload_success = false;
            if($upload_file){
                $upload_info = array(
                    'save_path'=>'files/distances/',
                    'folder'=>'distances/',
                    'file_name'=>$save_raw['Import']['attach']['name'],//$_FILES['uploadfile']['name']
                    'file_type'=>$save_raw['Import']['attach']['type'],//$_FILES['uploadfile']['type'];
                    'file_tmp_name'=>$save_raw['Import']['attach']['tmp_name'],//$_FILES['uploadfile']['tmp_name']
                    'file_size'=>$save_raw['Import']['attach']['size'],//$_FILES['uploadfile']['size']
                    'file_name_prefix'=>time(),
                    'check_file_type'=>array('application/vnd.ms-excel')
                );
                $result = $this->uploadFile($upload_info);
                if($result['status']){
                    $result2 = $this->do_distance_import($result['file_name'],$result['file_path']);
                    $mesg = $result2['mesg'];
                   // $mesg = 'File Uploaded';
                    $log_description = $this->getLogMessage('ImportedDeliveryLocation')." (File: ".$result['file_name'].")";
                    $this->logActivity('Administration',$log_description);
                }
                else{
                    $mesg  = $result['msg'];
                }
            }

            /*if($upload_success){
                //debug($result);

            }*/
        }
        $this->set(compact('mesg'));
    }


    function do_distance_import($filename,$file_path){
        ini_set("memory_limit", "512M") ;
        set_time_limit('1200');
        # folder path structure
        $folder = "files/";
        # setup directory pathname
        $folderAbsPath = WWW_ROOT . $folder;
        //do excel import
        $save = array();
        $status = true;
        $sheet_name = 'Page_1';

        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '8MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $objReader = PHPExcel_IOFactory::createReader('CSV');//Excel5 for Excel and CSV For csv
        /**  Advise the Reader of which WorkSheets we want to load  **/
        //$objReader->setLoadSheetsOnly($sheet_name); //To read CSV COMMENT OUT
        /** Advise the Reader that we only want to load cell data, not formatting **/
        //$objReader->setReadDataOnly(true);//To read CSV COMMENT OUT
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($folderAbsPath.$file_path);
        /**  Advise the Reader of which WorkSheets we want to load  **/

        $worksheetTitle     = $objPHPExcel->getActiveSheet()->getTitle();
        $highestRow         = $objPHPExcel->getActiveSheet()->getHighestRow();  // e.g. 10
        $highestColumn      = $objPHPExcel->getActiveSheet()->getHighestColumn(); // e.g 'F'

        //validate the documents and see if it matches standards, get the preferred columns
        $Id   = trim($objPHPExcel->getActiveSheet()->getCell("A1")->getValue());
        $Depot_Id = trim($objPHPExcel->getActiveSheet()->getCell("B1")->getValue());
        $Depot = trim($objPHPExcel->getActiveSheet()->getCell("C1")->getValue());
        $Location = trim($objPHPExcel->getActiveSheet()->getCell("D1")->getValue());
        $Region_Id = trim($objPHPExcel->getActiveSheet()->getCell("E1")->getValue());
        $Region = trim($objPHPExcel->getActiveSheet()->getCell("F1")->getValue());
        $Distance = trim($objPHPExcel->getActiveSheet()->getCell("G1")->getValue());
        $Alternate_Route = trim($objPHPExcel->getActiveSheet()->getCell("H1")->getValue());

        if($Id != 'Id'){
            return array('status'=>false,'mesg'=>'1-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Depot_Id != 'Depot Id'){
            return array('status'=>false,'mesg'=>'2-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Depot != 'Depot'){
            return array('status'=>false,'mesg'=>'3-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Location != 'Location'){
            return array('status'=>false,'mesg'=>'4-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Region_Id != 'Region Id'){
            return array('status'=>false,'mesg'=>'5-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Region != 'Region'){
            return array('status'=>false,'mesg'=>'6-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Distance != 'Distance'){
            return array('status'=>false,'mesg'=>'7-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Alternate_Route != 'Alternate Route'){
            return array('status'=>false,'mesg'=>'8-Invalid template. Please make sure you are using the right template for the upload.');
        }

        $save = array();

        for ($row =2; $row < ($highestRow + 1); ++$row){
            $id   = trim($objPHPExcel->getActiveSheet()->getCell("A".$row)->getValue());
            $depot_id = trim($objPHPExcel->getActiveSheet()->getCell("B".$row)->getValue());
            $location = trim($objPHPExcel->getActiveSheet()->getCell("D".$row)->getValue());
            $region_id = trim($objPHPExcel->getActiveSheet()->getCell("E".$row)->getValue());
            $distance = trim($objPHPExcel->getActiveSheet()->getCell("G".$row)->getValue());
            $alt_route = trim($objPHPExcel->getActiveSheet()->getCell("H".$row)->getValue());
            if(($id == 0 || $id > 0) && !empty($depot_id) && !empty($location) && !empty($region_id) && !empty($distance)){
                $save[]=array(
                    'row'=>$row,
                    'id'=>$id,
                    'depot_id'=>$depot_id,
                    'name'=>$location,
                    'distance'=>$distance,
                    'region_id'=>$region_id,
                    'alternate_route'=>$alt_route,
                    'created_by'=>$this->Auth->user('id')
                );
            }
        }

        # save the data

        if ($this->DeliveryLocation->saveAll($this->sanitize($save))) {
            return array('status'=>true,'mesg'=>"The file was successfully imported!");
        } else {
            return array('status'=>false,'mesg'=>'The file could not be imported.');
        }
    }


    function rates_category($type = 'get')
    {
        $company_profile = $this->global_company;
        $permissions = $this->action_permission;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();

            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;


                    $condition_array = array('FreightRateCategory.deleted' => 'n');

                    if (!empty($search_query)) {
                        if ($qtype == 'name') {
                            /*$condition_array = array(
                                'User.username' => $search_query,
                                'User.deleted' => 'n'
                            );*/
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $data_table = $this->FreightRateCategory->find('all', array('conditions' => $condition_array,'order' => "FreightRateCategory.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => -1));
                    $data_table_count = $this->FreightRateCategory->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['FreightRateCategory']['id'],
                                'cell' => array(
                                    $obj['FreightRateCategory']['id'],
                                    $obj['FreightRateCategory']['name']
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    if ($_POST['id'] == 0) {//Mew
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    $data = array('FreightRateCategory' => $_POST);
                    $data['FreightRateCategory']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['FreightRateCategory']['created_by'] = $this->Auth->user('id');
                    }

                    if ($this->FreightRateCategory->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $rate = $_POST['name'];
                            $log_description = $this->getLogMessage('ModifyRateCategory')." (Rate: ".$rate.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $rate = $_POST['name'];
                            $log_description = $this->getLogMessage('CreateRateCategory')." (Rate: ".$rate.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->FreightRateCategory->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved!', 'data' => $dt));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                    break;

                case 'delete':

                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }

                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('FreightRateCategory');
                    $result = $modObj->updateAll(
                        $this->sanitize(array('FreightRateCategory.deleted' => "'y'")),
                        $this->sanitize(array('FreightRateCategory.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }

        $controller = $this;
        $this->set(compact('controller'));

    }


    function rates($type = 'get')
    {
        $company_profile = $this->global_company;
        $permissions = $this->action_permission;
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->autoLayout = false;
            $authUser = $this->Auth->user();
            switch ($type) {
                case 'get' :
                    /**  Get posted data */
                    $page = isset($_POST['page']) ? $_POST['page'] : 1;
                    /** The current page */
                    $sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'id';
                    /** Sort column */
                    $sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
                    /** Sort order */
                    $qtype = isset($_POST['qtype']) ? $_POST['qtype'] : '';
                    /** @var $filter  */
                    $filter_category =   isset($_POST['filter_category']) ? $_POST['filter_category'] : 1 ;
                    /** Search column */
                    $search_query = isset($_POST['query']) ? $_POST['query'] : '';
                    /** Search string */
                    $rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
                    $limit = $rp;
                    $start = ($page - 1) * $rp;

                    $condition_array = array('FreightRate.freight_rate_category_id'=>$filter_category,'FreightRate.deleted' => 'n');

                    if (!empty($search_query)) {
                        if ($qtype == 'username') {
                            /*$condition_array = array(
                                'User.username' => $search_query,
                                'User.deleted' => 'n'
                            );*/
                        }
                        else {
                            /* $condition_array = array(
                                 "User.$qtype LIKE" => $search_query . '%',
                                 'User.deleted' => 'n'
                             );*/
                        }
                    }

                    $contain = array(
                        'FreightRateCategory'=>array('fields' => array('FreightRateCategory.id', 'FreightRateCategory.name'))
                    );

                    $data_table = $this->FreightRate->find('all', array('conditions' => $condition_array, 'contain'=>$contain,'order' => "FreightRate.$sortname $sortorder", 'page' => $page  , 'limit'=> $limit, 'recursive' => 1));
                    $data_table_count = $this->FreightRate->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['FreightRate']['id'],
                                'cell' => array(
                                    $obj['FreightRate']['id'],
                                    $obj['FreightRateCategory']['name'],
                                    $obj['FreightRate']['distance'],
                                    $obj['FreightRate']['rate']
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }

                    break;

                case 'save' :
                    if ($_POST['id'] == 0) {//Mew
                        if(!in_array('A',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    else{
                        if(!in_array('E',$permissions)){
                            return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                        }
                    }
                    //check if username does not exist for in this company
                    $data = array('FreightRate' => $_POST);
                    $data['FreightRate']['modified_by'] = $this->Auth->user('id');
                    if ($_POST['id'] == 0) {
                        $data['FreightRate']['created_by'] = $this->Auth->user('id');
                    }


                    if ($this->FreightRate->save($this->sanitize($data))) {
                        if($_POST['id'] > 0){
                            $rate = $_POST['rate'];
                            $distance = $_POST['distance'];
                            $log_description = $this->getLogMessage('ModifyRate')." (Distance: ".$distance.", Rate: ".$rate.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved'));
                        }
                        else{ //If new pass back extra data if needed.
                            $rate = $_POST['rate'];
                            $distance = $_POST['distance'];
                            $log_description = $this->getLogMessage('CreateRate')." (Distance: ".$distance.", Rate: ".$rate.")";
                            $this->logActivity('Administration',$log_description);

                            return json_encode(array('code' => 0, 'msg' => 'Data Saved.', 'id'=>$this->FreightRate->id));
                        }
                        //echo json_encode(array('success' => 0, 'msg' => 'Data Saved!', 'data' => $dt));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                    break;

                case 'delete':
                    if(!in_array('D',$permissions)){
                        return json_encode(array('code' => 1, 'msg' => 'Access Denied.'));
                    }

                    $ids = $_POST['ids'];
                    $modObj = ClassRegistry::init('FreightRate');
                    $now = "'".date('Y-m-d H:i:s')."'";
                    $result = $modObj->updateAll(
                        $this->sanitize(array('FreightRate.deleted' => "'y'",'FreightRate.modified' => "$now",'FreightRate.modified_by' => $this->Auth->user('id'))),
                        $this->sanitize(array('FreightRate.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'msg' => 'Data Deleted!'));
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Data cannot be deleted'));
                    }
                    break;
            }
        }


        $rate_cats = $this->FreightRateCategory->getCategories();

        $filter_rate_cats =array();
        $rate_cat_options =array();
        foreach($rate_cats as $arr){
            $filter_rate_cats[] = array('name'=>$arr['FreightRateCategory']['name'],'value'=>$arr['FreightRateCategory']['id']);
            $rate_cat_options[]= array('name'=>$arr['FreightRateCategory']['name'],'id'=>$arr['FreightRateCategory']['id']);
        }

        $controller = $this;
        $this->set(compact('controller', 'filter_rate_cats','rate_cat_options'));
    }


    function export_rates($filter_category =1){
        $download = false;
        $condition_array = array('FreightRate.freight_rate_category_id'=>$filter_category,'FreightRate.deleted' => 'n');

        $contain = array(
            'FreightRateCategory'=>array('fields' => array('FreightRateCategory.id', 'FreightRateCategory.name'))
        );
        $export_data = $this->FreightRate->find('all', array(
            'conditions' => $condition_array,
            'contain'=>$contain,
            'order' => array("FreightRate.id"=>'asc'),
            'recursive' => 1
        ));

        if ($export_data) {
            $download = true;
            $list_data = array();
            foreach ($export_data as $obj) {
                $list_data[] = array(
                    $obj['FreightRate']['id'],
                    $obj['FreightRateCategory']['id'],
                    $obj['FreightRateCategory']['name'],
                    $obj['FreightRate']['distance'],
                    $obj['FreightRate']['rate']
                );
            }
            $list_headers = array('Id','Rate Category Id','Rate Category','Distance','Rate');
            $filename = "UPPF Rates";
            $res = $this->convertToExcel($list_headers,$list_data,$filename);
            $objPHPExcel = $res['excel_obj'];
            $filename = $res['filename'];
        }

        $this->autoLayout = false;
        $this->set(compact('objPHPExcel', 'download', 'filename'));
    }


    function import_rates(){
        $mesg = false;
        $this->layout = 'import_layout';
        if($this->request->is('post')){
            $upload_file = false;
            $save_raw = $save = $this->request->data;
            if(isset($save_raw['Import']['attach'])){
                if(!empty($save_raw['Import']['attach']['name'])){
                    $upload_file = true;
                }
            }
            $upload_success = false;
            if($upload_file){
                $upload_info = array(
                    'save_path'=>'files/rates/',
                    'folder'=>'rates/',
                    'file_name'=>$save_raw['Import']['attach']['name'],//$_FILES['uploadfile']['name']
                    'file_type'=>$save_raw['Import']['attach']['type'],//$_FILES['uploadfile']['type'];
                    'file_tmp_name'=>$save_raw['Import']['attach']['tmp_name'],//$_FILES['uploadfile']['tmp_name']
                    'file_size'=>$save_raw['Import']['attach']['size'],//$_FILES['uploadfile']['size']
                    'file_name_prefix'=>time(),
                    'check_file_type'=>array('application/vnd.ms-excel')
                );
                $result = $this->uploadFile($upload_info);
                if($result['status']){
                    $result2 = $this->do_rate_import($result['file_name'],$result['file_path']);

                    $log_description = $this->getLogMessage('ImportedRates')." (File: ".$result['file_name'].")";
                    $this->logActivity('Administration',$log_description);

                    $mesg = $result2['mesg'];
                    // $mesg = 'File Uploaded';
                }
                else{
                    $mesg  = $result['msg'];
                }
            }
        }
        $this->set(compact('mesg'));
    }


    function do_rate_import($filename,$file_path){
        ini_set("memory_limit", "512M") ;
        set_time_limit('1200');
        # folder path structure
        $folder = "files/";
        # setup directory pathname
        $folderAbsPath = WWW_ROOT . $folder;
        //do excel import
        $save = array();
        $status = true;
        $sheet_name = 'Page_1';

        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '8MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $objReader = PHPExcel_IOFactory::createReader('CSV');//Excel5 for Excel and CSV For csv
        /**  Advise the Reader of which WorkSheets we want to load  **/
        //$objReader->setLoadSheetsOnly($sheet_name); //To read CSV COMMENT OUT
        /** Advise the Reader that we only want to load cell data, not formatting **/
        //$objReader->setReadDataOnly(true);//To read CSV COMMENT OUT
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($folderAbsPath.$file_path);
        /**  Advise the Reader of which WorkSheets we want to load  **/

        $worksheetTitle     = $objPHPExcel->getActiveSheet(0)->getTitle();
        $highestRow         = $objPHPExcel->getActiveSheet(0)->getHighestRow();  // e.g. 10
        $highestColumn      = $objPHPExcel->getActiveSheet(0)->getHighestColumn(); // e.g 'F'

        //validate the documents and see if it matches standards, get the preferred columns
        $Id   = trim($objPHPExcel->getActiveSheet(0)->getCell("A1")->getValue());
        $Rate_Cat_Id = trim($objPHPExcel->getActiveSheet(0)->getCell("B1")->getValue());
        $Rate_Category = trim($objPHPExcel->getActiveSheet(0)->getCell("C1")->getValue());
        $Distance = trim($objPHPExcel->getActiveSheet(0)->getCell("D1")->getValue());
        $Rate = trim($objPHPExcel->getActiveSheet(0)->getCell("E1")->getValue());

        if($Id != 'Id'){
            return array('status'=>false,'mesg'=>'1-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Rate_Cat_Id != 'Rate Category Id'){
            return array('status'=>false,'mesg'=>'2-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Rate_Category != 'Rate Category'){
            return array('status'=>false,'mesg'=>'3-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Distance != 'Distance'){
            return array('status'=>false,'mesg'=>'4-Invalid template. Please make sure you are using the right template for the upload.');
        }
        if($Rate != 'Rate'){
            return array('status'=>false,'mesg'=>'5-Invalid template. Please make sure you are using the right template for the upload.');
        }

        $save = array();

        for ($row =2; $row < ($highestRow + 1); ++$row){
            $id   = trim($objPHPExcel->getActiveSheet(0)->getCell("A".$row)->getValue());
            $rate_cat_id = trim($objPHPExcel->getActiveSheet(0)->getCell("B".$row)->getValue());
            $distance = trim($objPHPExcel->getActiveSheet(0)->getCell("D".$row)->getValue());
            $rate = trim($objPHPExcel->getActiveSheet(0)->getCell("E".$row)->getValue());

            if(($id == 0 || $id > 0) && !empty($rate_cat_id) && !empty($distance) && !empty($rate)){
                $save[]=array(
                    'row'=>$row,
                    'id'=>$id,
                    'freight_rate_category_id'=>$rate_cat_id,
                    'distance'=>$distance,
                    'rate'=>$rate,
                    'created_by'=>$this->Auth->user('id')
                );
            }
        }
        # save the data

        if ($this->FreightRate->saveAll($this->sanitize($save))) {
            return array('status'=>true,'mesg'=>"The file was successfully imported!");
        } else {
            return array('status'=>false,'mesg'=>'The file could not be imported.');
        }
    }

}
