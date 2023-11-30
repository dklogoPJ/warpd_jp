<?php
class DashboardController extends AppController {


    function index(){
        $this->autoRender = false;
        $this->autoLayout = false;
        $this->redirect('Users/login');
    }
}