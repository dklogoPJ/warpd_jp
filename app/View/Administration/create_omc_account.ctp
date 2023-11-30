<div class="span3">
    <?php
    echo $this->element('admin_bdc_nav_left');
    ?>
</div>
<div class="span9">

    <div class="form-actions" style="margin-top: 0px; font-weight: bold">
        OMC Accounts
    </div>

    <table id="dg" title="" style="height: 400px;" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true" url="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account/get')); ?>" >
        <thead>
        <tr>
            <th field="name"width="50" sortable="false" >Omc Name</th>
            <th field="number_of_users"width="50" sortable="false" >Number of Users</th>
        </tr>
        </thead>
    </table>
    <div class="row-fluid" id="toolbar">
        <div class="span12">
            <div class="btn-group">
                <a href="javascript: void(0);" id="new_btn" class="btn" iconCls="icon-add" plain="true" ><i class="icon-plus"></i> New</a>
                <!--<a href="javascript: void(0);" class="btn" iconCls="icon-remove" plain="true" onclick="javascript:$('#dg').edatagrid('destroyRow')"><i class="icon-trash"></i> Destroy</a>-->
                <!--<a href="javascript: void(0);" class="btn" iconCls="icon-save" plain="true" onclick="javascript:$('#dg').edatagrid('saveRow')"><i class="icon-hdd"></i> Save</a>
                <a href="javascript: void(0);" class="btn" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg').edatagrid('cancelRow')"><i class="icon-ban-circle"></i> Cancel</a>-->
            </div>
        </div>
        <!--<div class="span6">
            <div class="input-append">
                <label>Search On:</label>
                <select class="span2">
                    <option>Date</option>
                    <option>Waybill No.</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
                <input class="span4" id="appendedInputButton" size="16" type="text">
                <button class="btn" type="button">Search</button>
            </div>
        </div>-->
    </div>


    <!-- Colorbox pop up-->
    <div style="display: none;">
        <div id="customers-form-window" style="margin: 20px 20px 5px 20px;">
            <div class="customers-prompt" ></div>
            <?php echo $this->Form->create('Omc', array('default' => false, 'id' => 'form', 'action' => 'add','inputDefaults' => array('label' => false,'div' => false)));?>
            <fieldset>
                <legend> Omc Account</legend>

                <table cellspacing="10">
                    <tr>
                        <td><?php echo __('Omc Name:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('omclist_id', array('div' => false, 'label' => false, 'id'=>'omclist_id' ,'class' => 'validate[required] ','options'=>$omclist)); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('Location:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('location', array('div' => false, 'label' => false, 'id'=>'location' ,'class' => 'validate[required]', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('Address:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('address', array('div' => false, 'label' => false, 'id'=>'address' ,'class' => 'validate[required] date-picker', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('Telephone:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('telephone', array('div' => false, 'label' => false, 'id'=>'telephone' ,'class' => 'validate[required]', 'value'=>'')); ?>

                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset>
                <legend> Admin Info</legend>

                <table cellspacing="10">
                    <tr>
                        <td><?php echo __('Title:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('title', array('div' => false, 'label' => false, 'id'=>'title' ,'class' => 'validate[required] ', 'value'=>'')); ?>
                        </td>
                    </tr>
                    <!-- <tr>
                    <td><?php /* echo __('Customer Type:') ;*/?></td>
                    <td>
                        <?php /*echo $this->Form->input('customer_type', array('div' => false, 'label' => false, 'id'=>'customer_type' ,'options'=>$customer_types_opt)); */?>
                    </td>
                </tr>-->
                    <tr>
                        <td><?php  echo __('* First Name:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('fname', array('div' => false, 'label' => false, 'id'=>'fname' ,'class' => 'validate[required]', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('* Last Name:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('lname', array('div' => false, 'label' => false, 'id'=>'lname' ,'class' => 'validate[required] date-picker', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('* Username:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('username', array('div' => false, 'label' => false, 'id'=>'username' ,'class' => 'validate[required]', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('* Password:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('password', array('div' => false, 'label' => false, 'id'=>'password' ,'class' => 'validate[required]', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td><?php  echo __('* Email:') ;?> </td>
                        <td>
                            <?php echo $this->Form->input('email', array('div' => false, 'label' => false, 'id'=>'email' ,'class' => 'validate[required]', 'value'=>'')); ?>

                        </td>
                    </tr>
                    <input name="user_type" id="user_type" type="hidden" value="omc">
                    <input name="user_level" id="user_level" type="hidden" value="admin">
                </table>
            </fieldset>
            <table cellspacing="10">
                <tr>
                    <td>

                    </td>
                    <td>
                        <button type="button" class="btn" id="save-btn">&nbsp;Save</button>
                        <button type="reset" class="btn" id="cancel-btn">&nbsp;Cancel</button>
                    </td>
                </tr>
            </table>

            <?php echo $this->Form->end();?>
        </div>
    </div>

</div>

<?php
echo $this->Html->css('editable_grid/gray/easyui.css');
echo $this->Html->css('editable_grid/icon');

echo $this->Html->script('editable_grid/jquery.easyui.min.js');
//echo $this->Html->script('editable_grid/jquery.edatagrid.js');
echo $this->Html->script('scripts/omc_account.js');
?>

<input type="hidden" id="grid_get_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account/get')); ?>" />
<input type="hidden" id="grid_save_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account/save')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_omc_account/delete')); ?>" />