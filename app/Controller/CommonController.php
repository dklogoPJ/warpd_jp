<?php

/**
 * @name OtherSetupController.php
 */
App::import('Controller', 'App');

class CommonController extends AppController
{
    # Controller name

    var $name = 'Common';
    # set the model to use
    var $uses = array('Volume');
    # Set the layout to use
    var $layout = 'other_setup_layout';

    public function beforeFilter($param_array = null)
    {
        parent::beforeFilter();
    }

    public function index() {
        //$this->redirect('users');
    }


    function set_volume()
    {
        $this->autoRender = false;
        $this->autoLayout = false;
        $authUser = $this->Auth->user();
        //check if username does not exist for in this company
        $data = array('Volume' => $_POST);
        $data['Volume']['created_by'] = $this->Auth->user('id');

        if ($this->Volume->save($this->sanitize($data))) {
            return json_encode(array('code' => 0, 'msg' => 'Data Saved.'));
        } else {
            echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
        }
    }
}