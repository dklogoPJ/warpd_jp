<?php

/**
 * @name BdcOrdersController.php
 */
App::import('Controller', 'BdcApp');

class BdcStockController extends BdcAppController
{
    # Controller name

    var $name = 'BdcStock';
    # set the model to use
    var $uses = array('Bdc','BdcDistribution','Order','Depot','ProductType','BdcInitialStockStartup','BdcStockUpdate','BdcStockHistory','StockTrading');

    # Set the layout to use
    var $layout = 'bdc_layout';

    # Bdc ids this user will work with only
    var $user_bdc_ids = array();

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter(array('bdc_user_types'=>array('Allow All')));
        //If Trading, check if there are new products added to depots, force them to initialise it
        $comp = $this->global_company;
        $StockTrading = ClassRegistry::init('StockTrading');
        $force_new_products_added_to_depot = false;
        if($StockTrading->isTrading($comp['id'])){
            $new_data = $this->get_new_added_depot_products();
            $depots_products = $new_data['depots_products'];
            if(!empty($depots_products)){
                $action = $this->params['action'];
                $controller = $this->params['controller'];
                if(in_array($action,array('initial_startup_stocks'))){
                    //Allow
                    $force_new_products_added_to_depot = false;
                }
                else{
                    //Block all none required pages
                    $force_new_products_added_to_depot = true;
                }
            }
        }
        $this->set(compact('force_new_products_added_to_depot'));
    }


    function index()
    {
        $this->redirect('initial_startup_stocks');
    }

    function initial_startup_stocks(){
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $today = date('Y-m-d');
            $save = array();
            $empty_value = false;
            foreach($this->request->data['BdcInitialStockStartup'] as $k => $dt){
                $quantity_ltrs = trim($dt['quantity_ltrs']);
                if(empty($quantity_ltrs)){
                    $empty_value = true;
                }
                else{
                    $dt['bdc_id'] = $company_profile['id'];
                    $dt['created_by'] = $authUser['id'];
                    $dt['modified_by'] = $authUser['id'];
                    //this will be used in as stock history record
                    $dt['initial_quantity']= $dt['quantity_ltrs'];
                    $dt['stock_date']= $today;
                    $dt['status']= 'Open';

                    $save[]=$dt;
                }
            }

            if($empty_value){
                $this->Session->setFlash('Data Error, Please initialize all stock values.');
                $this->Session->write('process_error', 'yes');
                $this->redirect(array('action' => 'initial_startup_stocks'));
            }

            /*if(empty($save)){
                $this->Session->setFlash('Data Error, At least one initial stock value has to be set.');
                $this->Session->write('process_error', 'yes');
                $this->redirect(array('action' => 'initial_startup_stocks'));
            }*/

            $res = $this->BdcInitialStockStartup->saveAll($this->sanitize($save));
            if ($res) {
                $this->BdcStockHistory->saveAll($this->sanitize($save));
                //Check with stock trading if these products are trading there.
                $this->StockTrading->isTradingAllProducts($company_profile['id'],$save);
                $this->Session->setFlash('Stock has been initialize !');
                $this->Session->write('process_error', 'no');
            }
            else {
                $this->Session->setFlash('Sorry, Stock initialization failed.');
                $this->Session->write('process_error', 'yes');
            }

            $this->redirect(array('action' => 'initial_startup_stocks'));
        }

        $new_data = $this->get_new_added_depot_products();
        $depots_products = $new_data['depots_products'];
        $grid_data =$new_data['grid_data'];
        $controller = $this;
        $this->set(compact('controller', 'depots_products','grid_data'));
    }


    function stock_update(){
        $authUser = $this->Auth->user();
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $this->request->data['BdcStockUpdate']['created_by'] = $authUser['id'];
            $this->request->data['BdcStockUpdate']['modified_by'] = $authUser['id'];
            $this->request->data['BdcStockUpdate']['bdc_id'] = $company_profile['id'];

            $res = $this->BdcStockUpdate->save($this->sanitize($this->request->data['BdcStockUpdate']));
            if ($res) {
                $this->Session->setFlash('Stock has been update !');
                $this->Session->write('process_error', 'no');
            }
            else {
                $this->Session->setFlash('Sorry, Stock update failed.');
                $this->Session->write('process_error', 'yes');
            }

            $this->redirect(array('action' => 'stock_update'));
        }

        $products = $this->get_products();
        $product_options = array();
        foreach($products as $p){
            $product_options[$p['id']]= $p['name'];
        }
        $depots = $this->get_depot_list();
        $depot_options = array();
        foreach($depots as $dp){
            $depot_options[$dp['id']]= $dp['name'];
        }
        $depots_to_products = $this->Bdc->getDepotToProduct($company_profile['id']);
        $controller = $this;
        $this->set(compact('controller', 'depots_to_products','depot_options','product_options'));
    }


    function stock_history(){

    }

    function getDailyStockVariance(){
        $today_dt = date('Y-m-d');
        //$status = 'Closed';
        $status = null;
        $depot = '0';//All
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            $depot = $this->request->data['Query']['depot'];
        }
        $grid = $this->BdcStockHistory->getDailyStockVariance($company_profile['id'],$today_dt,$depot);
        $controller = $this;
        $my_depots = $this->get_depot_list();
        $my_depots_opts = array('0'=>'All');
        foreach($my_depots as $dpt){
            $my_depots_opts[$dpt['id']] = $dpt['name'];
        }
        $this->set(compact('controller', 'grid','my_depots_opts','depot'));
    }


    function getStockHistories(){
        $start_dt = date('Y-m-1');
        $end_dt = date('Y-m-t');
        $depot = '0';//All
        $status = null;
        $company_profile = $this->global_company;
        if($this->request->is('post')){
            //$start_dt = $this->covertDate($this->request->data['Query']['start_dt'],'mysql');
            $start_dt = $this->request->data['Query']['start_dt'];
            //$end_dt = $this->covertDate($this->request->data['Query']['end_dt'],'mysql');
            $end_dt = $this->request->data['Query']['end_dt'];
            $depot = $this->request->data['Query']['depot'];
        }
        $grid = $this->BdcStockHistory->getStockHistories($company_profile['id'],$start_dt,$end_dt,$depot,$status);

        $my_depots = $this->get_depot_list();
        $my_depots_opts = array('0'=>'All');
        foreach($my_depots as $dpt){
            $my_depots_opts[$dpt['id']] = $dpt['name'];
        }

        $controller = $this;
        $this->set(compact('controller', 'grid','start_dt','end_dt','my_depots_opts','depot'));
    }
}