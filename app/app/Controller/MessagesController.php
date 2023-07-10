<?php
/**
 * This is the administration_controller class file.
 * @author Amissah Gideon<kuulmek@yahoo.com>
 * @access public
 * @version 1.0
 */

class MessagesController extends AppController
{
    # Controller Name to be used
    var $name = 'Messages';

    var $uses = array(
        'User',
        'Message',
        'OmcCustomer',
        'BdcOmc',
        'MessageReciever',
        'MessageAddressBook'
    );
    # Set the layout to use
    var $layout = 'messages_layout';

    # Check the authenticity of the user
    function  beforeFilter($param_array = null)
    {
        parent::beforeFilter(false);
        $this->Auth->allow('checkMail');
    }


    function compose(){
        $user_id = $this->Auth->user('id');
        $address_list = $this->MessageAddressBook->getContacts($user_id);

        $this->set(compact('address_list'));
    }


    # Controller actions
    /**
     * This function displays all the users in the system
     * @name index
     * @param void
     * @return Array of data.
     * @access public
     */
    function index()
    {
        $loggedUser = $this->Auth->user();
        $user_id = $loggedUser['id'];

        if ($this->request->is('ajax')) {
            # disable the rendering of the layout
            $this->autoRender = false;
            $this->autoLayout = false;

            $action = isset($_POST['action']) ? $_POST['action'] : 'Get';

            switch ($action) {
                case 'Get' :
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

                    $condition_array = array('MessageReciever.user_id'=>$user_id,'MessageReciever.trash' => 'n');
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

                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->MessageReciever->find('all', array('conditions' => $condition_array, 'order' => "MessageReciever.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 2));
                    $data_table_count = $this->MessageReciever->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            if($obj['MessageReciever']['msg_status'] == 'unread'){
                                $return_arr[] = array(
                                    'id' => $obj['MessageReciever']['id'],
                                    'cell' => array(
                                        "<strong>".$obj['MessageReciever']['id']."</strong>",
                                        "<strong>".$obj['Message']['User']['username']."</strong>",
                                        "<strong>".$obj['Message']['title']."</strong>",
                                        "<strong>".$this->formatMailDate($obj['Message']['created'])."</strong>"
                                    )
                                );
                            }
                            else{
                                $return_arr[] = array(
                                    'id' => $obj['MessageReciever']['id'],
                                    'cell' => array(
                                        $obj['MessageReciever']['id'],
                                        $obj['Message']['User']['username'],
                                        $obj['Message']['title'],
                                        $this->formatMailDate($obj['Message']['created'])
                                    )
                                );
                            }
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }
                    break;
                case 'View':
                    /** Loading */
                    $data_id = $_POST['data-id'];
                    $message = $this->MessageReciever->find('first',array(
                        'conditions'=>array('MessageReciever.id'=>$data_id),
                        'contain'=>array(
                            'User' => array('fields'=>array('User.id', 'User.username', 'User.fname', 'User.lname')),
                            'Message'=>array(
                                'User' => array('fields'=>array('User.id', 'User.username', 'User.fname', 'User.lname'))
                            )
                        ),
                        'recursive'=>2
                    ));

                    if ($message) {
                        //Mark as read
                        if($message['MessageReciever']['msg_status'] == 'unread'){
                            $this->MessageReciever->id = $data_id;
                            $data['MessageReciever'] = array(
                                'msg_status' => 'read'
                            );
                            # save the data
                            $s=$this->MessageReciever->save($this->sanitize($data));
                        }
                        return json_encode(array('code' => 0, 'data' => $message, 'mesg' => __('Data Found')));
                    }
                    else {
                        return json_encode(array('code' => 1, 'data' => array(), 'mesg' => __('No Record Found')));
                    }
                    break;

                case 'Delete':
                    $ids = explode(',', $_POST['data_ids']);
                    /*$data['MessageReciever'] = array(
                        'trash' => 'y'
                    );*/
                    # save the data
                    $result=$this->MessageReciever->updateAll(
                        $this->sanitize(array('MessageReciever.trash' => "'y'")),
                        $this->sanitize(array('MessageReciever.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'mesg' => __('Selected message(s) have been successfully deleted')));
                    }
                    else {
                        echo json_encode(array('code' => 1, 'mesg' => __('Selected message(s) cannot be deleted')));
                    }

                    break;
            }
        }

        $page_title = 'Messages';
        $this->set(compact('page_title'));

    }


    function outbox(){
        $loggedUser = $this->Auth->user();
        $user_id = $loggedUser['id'];

        if ($this->request->is('ajax')) {
            # disable the rendering of the layout
            $this->autoRender = false;
            $this->autoLayout = false;

            $action = isset($_POST['action']) ? $_POST['action'] : 'Get';

            switch ($action) {
                case 'Get' :
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

                    $condition_array = array('Message.user_id'=>$user_id,'Message.msg_type'=>'user','Message.trash' => 'n');
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

                    $data_table = $this->Message->find('all', array('conditions' => $condition_array, 'order' => "Message.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => 2));
                    $data_table_count = $this->Message->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $usr = '';
                            foreach($obj['MessageReciever'] as $u){
                               $usr .= $u['User']['fname'].' '.$u['User']['lname']." < ".$u['User']['username']." >,";
                            }

                            $return_arr[] = array(
                                'id' => $obj['Message']['id'],
                                'cell' => array(
                                    $obj['Message']['id'],
                                    $usr,
                                    $obj['Message']['title'],
                                    $this->formatMailDate($obj['Message']['created'])
                                )
                            );
                        }
                        return json_encode(array('success' => true, 'total' => $total_records, 'page' => $page, 'rows' => $return_arr));
                    }
                    else {
                        return json_encode(array('success' => false, 'total' => $total_records, 'page' => $page, 'rows' => array()));
                    }
                    break;
                case 'View':
                    /** Loading */
                    $data_id = $_POST['data-id'];
                    $message = $this->Message->find('first',array(
                        'conditions'=>array('Message.id'=>$data_id),
                        'contain'=>array(
                            'User' => array('fields'=>array('User.id', 'User.username', 'User.fname', 'User.lname')),
                            'MessageReciever'=>array(
                                'User' => array('fields'=>array('User.id', 'User.username', 'User.fname', 'User.lname'))
                            )
                        ),
                        'recursive'=>2
                    ));

                    if ($message) {
                        return json_encode(array('code' => 0, 'data' => $message, 'mesg' => __('Data Found')));
                    }
                    else {
                        return json_encode(array('code' => 1, 'data' => array(), 'mesg' => __('No Record Found')));
                    }
                    break;

                case 'Delete':
                    $ids = explode(',', $_POST['data_ids']);
                    # save the data
                    $result=$this->Message->updateAll(
                        $this->sanitize(array('Message.trash' => "'y'")),
                        $this->sanitize(array('Message.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'mesg' => __('Selected message(s) have been successfully deleted')));
                    }
                    else {
                        echo json_encode(array('code' => 1, 'mesg' => __('Selected message(s) cannot be deleted')));
                    }

                    break;
            }
        }

        $page_title = 'Messages';
        $this->set(compact('page_title'));
    }

    function trash(){

    }


    function sendMessage(){
        $loggedUser = $this->Auth->user();
        $user_id = $loggedUser['id'];

        if($this->request->is('ajax')){
            # disable the rendering of the layout
            $this->autoRender = false;
            $this->autoLayout = false;

            $return = $this->send_message($this->request->data);
            return json_encode($return);
        }
    }


    function address_book($action = 'get')
    {
        $loggedUser = $this->Auth->user();
        $user_id = $loggedUser['id'];

        if ($this->request->is('ajax')) {
            # disable the rendering of the layout
            $this->autoRender = false;
            $this->autoLayout = false;

            switch ($action) {
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

                    $condition_array = array('MessageAddressBook.user_id'=>$user_id,'MessageAddressBook.deleted' => 'n');
                    if (!empty($search_query)) {
                        if ($qtype == 'contact_username') {
                            $condition_array['MessageAddressBook.contact_username']=$search_query;
                        }
                        else {
                            $condition_array['MessageAddressBook.contact_name LIKE']=$search_query . '%';
                        }
                    }

                    // $fields = array('User.id', 'User.username', 'User.first_name', 'User.last_name', 'User.group_id', 'User.active');
                    $data_table = $this->MessageAddressBook->find('all', array('conditions' => $condition_array, 'order' => "MessageAddressBook.$sortname $sortorder", 'limit' => $start . ',' . $limit, 'recursive' => -1));
                    $data_table_count = $this->MessageAddressBook->find('count', array('conditions' => $condition_array, 'recursive' => -1));

                    $total_records = $data_table_count;

                    if ($data_table) {
                        $return_arr = array();
                        foreach ($data_table as $obj) {
                            $return_arr[] = array(
                                'id' => $obj['MessageAddressBook']['id'],
                                'cell' => array(
                                    $obj['MessageAddressBook']['id'],
                                    $obj['MessageAddressBook']['contact_username'],
                                    $obj['MessageAddressBook']['contact_name'],
                                    $this->formatMailDate($obj['MessageAddressBook']['created'])
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
                    $data = array('MessageAddressBook' => $_POST);
                    $data['MessageAddressBook']['user_id'] = $this->Auth->user('id');
                    if ($this->MessageAddressBook->save($this->sanitize($data))) {

                        if($_POST['id'] > 0){
                            return json_encode(array('code' => 0, 'msg' => 'Contact Updated!'));
                        }
                        else{
                            return json_encode(array('code' => 0, 'msg' => 'Contact Saved', 'id'=>$this->MessageAddressBook->id));
                        }
                    } else {
                        echo json_encode(array('code' => 1, 'msg' => 'Some errors occurred.'));
                    }

                    break;

                case 'delete':
                    $ids = explode(',', $_POST['data_ids']);
                    # save the data
                    $result=$this->MessageAddressBook->updateAll(
                        $this->sanitize(array('MessageAddressBook.deleted' => "'y'")),
                        $this->sanitize(array('MessageAddressBook.id' => $ids))
                    );
                    if ($result) {
                        echo json_encode(array('code' => 0, 'mesg' => __('Selected contact(s) have been successfully deleted')));
                    }
                    else {
                        echo json_encode(array('code' => 1, 'mesg' => __('Selected contact(s) cannot be deleted')));
                    }

                    break;
            }
        }

        $page_title = 'Address Book';
        $this->set(compact('page_title'));

    }


    function formatMailDate($date){
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $day = date('d',strtotime($date));
        $this_year = date('Y');
        $this_month = date('m');
        $this_day = date('d');
        $return = '';
        if(($year == $this_year) && ($month == $this_month) && ($day == $this_day)){
            $return = date('H:i a',strtotime($date));
        }
        elseif(($year == $this_year) && ($month == $this_month) && ($day != $this_day)){
            $return = date('M j',strtotime($date));
        }
        elseif(($year == $this_year) && ($month != $this_month) && ($day != $this_day)){
            $return = date('M j',strtotime($date));
        }
        elseif(($year != $this_year) && ($month != $this_month) && ($day != $this_day)){
            $return = date('M j, Y',strtotime($date));
        }
        else{
            $return = $date;
        }

        return $return;
    }

    function messages(){

        $loggedUser = $this->Auth->user();
        $user_id = $loggedUser['id'];

        if($this->request->is('ajax')){
            # disable the rendering of the layout
            $this->autoRender = false;
            $this->autoLayout = false;

            $action = $_POST['action'];

            switch ($action) {
                case 'add' :
                    // debug($this->request->data);
                    //exit;

                    # save the data
                    if ($this->Message->saveAll($this->sanitize($this->request->data))) {
                        return json_encode(array('code' => 0, 'mesg' => __('Message Sent')));
                    }
                    else {
                        return json_encode(array('code' => 1, 'mesg' => __('Message Not Sent')));
                    }
                    break;
                case 'load' :
                    $msg_id = $_POST['data-id'];
                    $msg_type = $_POST['msg-type'];
                    $msg_status = $_POST['msg-status'];

                    $msg = $this->Message->find('first',array(
                        'conditions'=>array('Message.id'=>$msg_id),
                        'contain'=>array('User','MessageReciever'=>array('User')),
                        'recursive'=>2,
                    ));

                    //Mark as read if it's the first time the user is accessing this message.
                    if($msg_type == 'inbox' && $msg_status == 'unread'){
                        $reciever_id = null;
                        foreach($msg['MessageReciever'] as $reciever){
                            if($reciever['user_id'] == $user_id){
                                $reciever_id = $reciever['id'];
                                break;
                            }
                        }
                        $this->MessageReciever->id = $reciever_id;
                        $data['MessageReciever'] = array(
                            'msg_status' => 'read'
                        );
                        # save the data
                        $s=$this->MessageReciever->save($this->sanitize($data));
                    }

                    return json_encode(array('data' => $msg));

                    break;
                case 'delete' :
                    $delete_id = $_POST['data-id'];
                    $msg_type = $_POST['delete-type'];
                    $delete_action = $_POST['delete-action'];
                    if($delete_action == 'trash'){
                        if($msg_type == 'inbox'){
                            $this->MessageReciever->id = $delete_id;
                            $data['MessageReciever'] = array(
                                'trash' => 'y'
                            );
                            # save the data
                            $s=$this->MessageReciever->save($this->sanitize($data));
                        }
                        else{
                            $this->Message->id = $delete_id;
                            $data['Message'] = array(
                                'trash' => 'y'
                            );
                            # save the data
                            $s=$this->Message->save($this->sanitize($data));
                        }
                    }
                    else{//Empty trash
                        if($msg_type == 'inbox'){
                            $this->MessageReciever->id = $delete_id;
                            $data['MessageReciever'] = array(
                                'trash' => 'y',
                                'deleted' => 'y'
                            );
                            # save the data
                            $s=$this->MessageReciever->save($this->sanitize($data));
                        }
                        else{
                            $this->Message->id = $delete_id;
                            $data['Message'] = array(
                                'trash' => 'y',
                                'deleted' => 'y'
                            );
                            # save the data
                            $s=$this->Message->save($this->sanitize($data));
                        }
                    }

                    if ($s) {
                        return json_encode(array('code' => 0, 'mesg' => __('Message deleted!')));
                    }
                    else {
                        return json_encode(array('code' => 1, 'mesg' => 'Cannot delete message'));
                    }

                    break;
            }

        }
    }


    function checkMail(){
        # disable the rendering of the layout
        $this->autoRender = false;
        $this->autoLayout = false;

        $loggedUser = $this->Auth->user();
        if(!$loggedUser){
            return json_encode(array('code' => 1, 'total_count2' => 0));
        }
        $user_id = $loggedUser['id'];

        if($this->request->is('ajax')){
            $condition_array = array('MessageReciever.user_id'=>$user_id, 'MessageReciever.msg_status'=>'unread','MessageReciever.trash' => 'n');
            $fields = array('MessageReciever.id');
            $data_mesg = $this->MessageReciever->find('all', array(
                'fields'=>$fields,
                'conditions' => $condition_array,
                'contain'=>array('Message'=>array('fields'=>array('Message.id','Message.title'))),
                'recursive' => 1
            ));
            $count = 0;
            $mesg_arr = array();
            $mesg_str = "";
            foreach($data_mesg as $d){
                $count++;
                if(isset($mesg_arr[$d['Message']['title']])){
                    $mesg_arr[$d['Message']['title']] = $mesg_arr[$d['Message']['title']] + 1;
                }
                else{
                    $mesg_arr[$d['Message']['title']] = 1;
                }
            }
            foreach($mesg_arr as $title => $ct){
                $mesg_str .= $title." (".$ct.") <br />";
            }

            if ($count > 0) {
                return json_encode(array('code' => 0, 'total_count' => $count, 'data'=>$mesg_str));
            }
            else {
                return json_encode(array('code' => 1, 'total_count' => $count));
            }
        }
    }

}