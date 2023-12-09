<style>
    .row-form {
        border-bottom: none;
        border-top: none;
        padding: 16px 10px;
    }
    .buttons li a{
        width: 100%;
        color: #fff;
        text-decoration: none;
    }
    .isw-edit {
        background: url('../img/icons/ws/ic_edit.png') 10% 50% no-repeat transparent;
    }
    .isw-delete {
        background: url('../img/icons/ws/ic_delete.png') 10% 50% no-repeat transparent;
    }
    .isw-picture {
        background: url('../img/icons/ws/ic_picture.png') 4% 50% no-repeat transparent;
    }
    .selected td{
        color: #486B91;
        font-weight: bolder;
        background-color: #D1E0F0 !important;
    }
    tr:hover{
        cursor: pointer;
    }
    th,td{
        white-space: nowrap !important;
    }

</style>

<div class="workplace">

    <div class="page-header">
        <h1>Sales Form Templates <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Form Templates</h1>
            </div>
            <div class="block-fluid tabs">

                <ul>
                    <li><a href="#tabs-1"><strong>Sales Forms</strong></a></li>
                    <li><a href="#tabs-3" id="form_fields_tab"><strong>Sales Form Fields</strong></a></li>
                    <li><a href="#tabs-2" id="form_primary_field_tab"><strong>Sales Form Primary Field's Options</strong></a></li>

                </ul>

                <div id="tabs-1">
                    <div style="padding: 40px 10px 0px;">
                        <div class="row-fluid">
                            <div class="span4">
                                <div class="head clearfix">
                                    <!--<div class="isw-ok"></div>-->
                                    <h1>Create New or Update Sales Forms</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="sales-forms" method="" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_form_templates')); ?>">

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Form Title:</div>
                                            <div class="span8"><input type="text" name="form_name" id="form_name" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Description:</div>
                                            <div class="span8"><textarea name="form_description" id="form_description"></textarea></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Primary Field:</div>
                                            <div class="span8"><input type="text" name="form_primary_field" id="form_primary_field" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Form Order:</div>
                                            <div class="span8"><input type="text" name="form_order" id="form_order" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Customers:</div>
                                            <div class="span8">
                                                <select name="form_omc_customer_list[]" id="form_omc_customer_list" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Report Link:</div>
                                            <div class="span8">
                                                <select name="form_omc_sales_report_id" id="form_omc_sales_report_id" style="width: 100%;">
                                                    <?php
                                                    foreach($all_reports as $opt){
                                                        ?>
                                                        <option value="<?php echo $opt['id']; ?>"><?php echo $opt['name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="hidden"  name="form_action_type" id="form_action_type" value="form_save" >
                                            <input type="hidden"  name="form_id" id="form_id" value="0" >
                                            <input type="hidden"  name="omc_id" id="omc_id" value="<?php echo $company_profile['id']; ?>" >
                                            <input type="hidden"  name="menu_id" id="menu_id" value="" >

                                            <button type="button" class="btn" id="form_delete_btn"><i class="isw-delete"></i> Delete</button>
                                            <button type="button" class="btn" id="form_reset"><i class="isw-refresh"></i> Reset</button>
                                            <button type="submit" class="btn" id="form_save"><i class="isw-ok"></i>Save</button>
                                        </div>

                                    </form>
                                </div>

                            </div>

                            <div class="span8">
                                <div class="head clearfix">
                                    <!--<div class="isw-grid"></div>-->
                                    <h1>All Sales Forms</h1>
                                    <ul class="buttons">
                                        <li><a href="javascript:void(0);" id="form_preview_btn" class="isw-picture"> &nbsp;  &nbsp; Preview Form</a></li>
                                    </ul>
                                </div>
                                <div class="block-fluid">
                                    <div style="height: 415px; overflow-x: auto; overflow-y: auto;">
                                        <table id="sales_form_list" cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                            <tr>
                                                <th>Form Name</th>
                                                <th>Form Description</th>
                                                <th>Primary Field</th>
                                                <th>Form Order</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                foreach($sale_forms as $val_arr){
                                                    $val = $val_arr['OmcSalesForm'];
                                            ?>
                                                    <tr data-form_id="<?php echo $val['id']; ?>">
                                                        <td><?php echo $val['form_name']; ?></td>
                                                        <td><?php echo $val['description']; ?></td>
                                                        <td><?php echo $val['primary_field']; ?></td>
                                                        <td><?php echo $val['order']; ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div id="tabs-2">
                    <div style="padding: 40px 10px 0px;">
                        <div class="row-fluid">
                            <div class="span6">
                                <div class="head clearfix">
                                    <!--<div class="isw-ok"></div>-->
                                    <h1>Sales Form Primary Field's Option</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="sales-form-primary-field-option" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_form_templates')); ?>">

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Form Type:</div>
                                            <div class="span8">
                                                <select name="pf_omc_sales_form_id" id="pf_omc_sales_form_id" class="" required>
                                                    <?php
                                                    foreach($sale_form_options as $key => $opt){
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Primary Field:</div>
                                            <div class="span8">
                                                <div id="primary_field_html"><b></b></div>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Option Name:</div>
                                            <div class="span8"><input type="text" name="pf_option_name" id="pf_option_name" value="" required class="" /></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Option Link Type:</div>
                                            <div class="span8">
                                                <select name="pf_option_link_type" id="pf_option_link_type">
                                                    <?php
                                                    foreach($all_option_link_types as $opt){
                                                        ?>
                                                        <option value="<?php echo $opt['id']; ?>"><?php echo $opt['name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Option Link Source: <span id="option_link_id_label"></span></div>
                                            <div class="span8">
                                                <select name="pf_option_link_id" id="pf_option_link_id"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Order:</div>
                                            <div class="span8"><input type="text" name="pf_order" id="pf_order" value="" required class="" /></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Is Total:</div>
                                            <div class="span8">
                                                <label class="checkbox inline">
                                                    <input type="radio" name="pf_is_total_radio" id="pf_is_total_no" value="no" checked="checked" /> No
                                                </label>
                                                <label class="checkbox inline">
                                                    <input type="radio" name="pf_is_total_radio" id="pf_is_total_yes" value="yes" /> Yes
                                                </label>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix pf_total_options_and_fields_wrapper" id="" style="border-top-width: 0px; display:none">
                                            <div class="span4">Total Options List:</div>
                                            <div class="span8">
                                                <select name="pf_total_option_list[]" id="pf_total_option_list" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix pf_total_options_and_fields_wrapper" id="" style="border-top-width: 0px; display:none">
                                            <div class="span4">Total Fields List:</div>
                                            <div class="span8">
                                                <select name="pf_total_field_list[]" id="pf_total_field_list" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="hidden"  name="pf_option_id" id="pf_option_id" value="0" >
                                            <input type="hidden"  name="pf_option_is_total" id="pf_option_is_total" value="no" >
                                            <input type="hidden"  name="pf_option_action_type" id="pf_option_action_type" value="option_save" >

                                            <button type="button" class="btn" id="pf_option_delete_btn"><i class="isw-delete"></i> Delete</button>
                                            <button type="button" class="btn" id="pf_option_reset"><i class="isw-refresh"></i> Reset</button>
                                            <button type="submit" class="btn" id="pf_option_save"><i class="isw-ok"></i>Save</button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                            <div class="span6">
                                <div class="head clearfix">
                                    <!--<div class="isw-grid"></div>-->
                                    <h1>Primary Fields Options</h1>
                                    <!--<ul class="buttons">
                                        <li><a href="javascript:void(0);" id="form_field_preview_btn" class="isw-picture"> &nbsp;  &nbsp; Preview Form</a></li>
                                    </ul>-->
                                </div>
                                <div class="block-fluid">
                                    <div style="height: 415px; overflow-x: auto; overflow-y: auto;">
                                        <table id="primary-field-option_list_table" cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                            <tr>
                                                <th>Option Name</th>
                                                <th>Is Total</th>
                                                <th>Order</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div id="tabs-3">
                    <div style="padding: 40px 10px 0px;">
                        <div class="row-fluid">
                            <div class="span4">
                                <div class="head clearfix">
                                    <!--<div class="isw-ok"></div>-->
                                    <h1>Sales Forms Fields</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="sales-form-fields" method="" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_form_templates')); ?>">

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Form Type:</div>
                                            <div class="span8">
                                                <select name="omc_sales_form_id" id="omc_sales_form_id" class="" required>
                                                    <?php
                                                    foreach($sale_form_options as $key => $opt){
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Name:</div>
                                            <div class="span8"><input type="text" name="field_name" id="field_name" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Type:</div>
                                            <div class="span8">
                                                <select name="field_type" id="field_type" class="" required>
                                                    <option value="Text">Text</option>
                                                    <option value="Drop Down">Drop Down</option>
                                                    <option value="File Upload">File Upload</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Order:</div>
                                            <div class="span8"><input type="text" name="field_order" id="field_order" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" id="drop_down_options" style="border-top-width: 0px; display:none">
                                            <div class="span4">Drop Down Options:</div>
                                            <div class="span8">
                                                <input type="text" class="tags" id="field_type_values" value=""/>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Required:</div>
                                            <div class="span8">
                                                <select name="field_required" id="field_required" class="span4" required>
                                                    <option value="No">No</option>
                                                    <option value="Yes">Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Disabled:</div>
                                            <div class="span8">
                                                <select name="field_disabled" id="field_disabled" class="span4" required>
                                                    <option value="n">No</option>
                                                    <option value="y">Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Event:</div>
                                            <div class="span8">
                                                <select name="field_event" id="field_event">
                                                    <?php
                                                    foreach($sale_form_element_events as $key => $opt){
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix field_event_wrapper" style="border-top-width: 0px; display:none">
                                            <div class="span4">Field Action:</div>
                                            <div class="span8">
                                                <select name="field_action" id="field_action">
                                                    <?php
                                                    foreach($sale_form_element_actions as $key => $opt){
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix field_dsrp_wrapper" style="border-top-width: 0px; display: none">
                                            <div class="span4">DSRP Form:</div>
                                            <div class="span8">
                                                <select name="dsrp_form" id="dsrp_form" class="" required>
                                                    <?php
                                                    foreach($sale_form_options as $key => $opt){
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!--<div class="row-form clearfix field_dsrp_wrapper" style="border-top-width: 0px; display: none">
                                            <div class="span4">DSRP Primary Field Options:</div>
                                            <div class="span8">
                                                <select name="dsrp_form_primary_field_options[]" id="dsrp_form_primary_field_options" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>-->

                                        <div class="row-form clearfix field_dsrp_wrapper" style="border-top-width: 0px; display: none">
                                            <div class="span4">DSRP Fields:</div>
                                            <div class="span8">
                                                <select name="dsrp_form_fields" id="dsrp_form_fields" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix field_dsrp_wrapper" style="border-top-width: 0px; display: none">
                                            <div class="span4">DSRP Operands:</div>
                                            <div class="span8">
                                                <select name="operands" id="operands" class="">
                                                    <?php
                                                    foreach($sale_form_element_operands as $key => $opt){
                                                        ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix field_event_wrapper" id="field_action_source_column_wrapper" style="border-top-width: 0px; display:none">
                                            <div class="span4">Field Action Source Column:</div>
                                            <div class="span8">
                                                <select name="field_action_source_column" id="field_action_source_column"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix field_event_wrapper" id="field_action_sources_wrapper" style="border-top-width: 0px; display:none">
                                            <div class="span4">Field Action Sources:</div>
                                            <div class="span8">
                                                <select name="field_action_sources[]" id="field_action_sources" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix field_event_wrapper" style="border-top-width: 0px; display:none">
                                            <div class="span4">Field Action Targets:</div>
                                            <div class="span8">
                                                <select name="field_action_targets[]" id="field_action_targets" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="hidden"  name="field_id" id="field_id" value="0" >
                                           <!-- <input type="hidden"  name="field_type" id="field_type" value="Text" >-->
                                            <input type="hidden"  name="field_action_type" id="field_action_type" value="field_save" >

                                            <button type="button" class="btn" id="field_delete_btn"><i class="isw-delete"></i> Delete</button>
                                            <button type="button" class="btn" id="field_reset"><i class="isw-refresh"></i> Reset</button>
                                            <button type="submit" class="btn" id="field_save"><i class="isw-ok"></i>Save</button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        <div class="span8">
                                <div class="head clearfix">
                                    <!--<div class="isw-grid"></div>-->
                                    <h1>Form Fields</h1>
                                    <ul class="buttons">
                                        <li><a href="javascript:void(0);" id="form_field_preview_btn" class="isw-picture"> &nbsp;  &nbsp; Preview Form</a></li>
                                    </ul>
                                </div>
                                <div class="block-fluid">
                                    <div style="height: 415px; overflow-x: auto; overflow-y: auto;">
                                        <table id="form_field_list" cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                            <tr>
                                                <!--<th>Form Name</th>-->
                                                <th>Field Name</th>
                                                <th>Field Type</th>
                                                <th>Field Order</th>
                                                <th>Required</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- Form Preview -->
<div style="display: none;">
    <div id="preview-form-window" style="width: 900px; overflow: auto">
        <div class="preview-content" style="padding: 20px;"></div>
    </div>
</div>


<!-- URLs -->
<input type="hidden" id="form-save-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_form_templates')); ?>" />

<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var $forms_fields = <?php echo json_encode($forms_fields); ?>;
    var $sale_form_options = <?php echo json_encode($sale_form_options); ?>;
    var $sale_form_element_events = <?php echo json_encode($sale_form_element_events); ?>;
    var $sale_form_element_actions = <?php echo json_encode($sale_form_element_actions); ?>;
    var $customers = <?php echo json_encode($customers); ?>;
    var $all_option_link_types = <?php echo json_encode($all_option_link_types); ?>;
    var $all_modules_link_types = <?php echo json_encode($all_modules_link_types); ?>;
</script>
<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/sales_form_template.js');
?>
