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
        <h1>Sales Report Templates <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Report Templates</h1>
            </div>
            <div class="block-fluid tabs">

                <ul>
                    <li><a href="#tabs-1"><strong>Sales Report</strong></a></li>
                    <li><a href="#tabs-2" id="report_primary_field_tab"><strong>Sales Report Primary Field's Options</strong></a></li>
                    <li><a href="#tabs-3" id="report_fields_tab"><strong>Sales Report Fields</strong></a></li>
                </ul>

                <div id="tabs-1">
                    <div style="padding: 40px 10px 0px;">
                        <div class="row-fluid">
                            <div class="span4">
                                <div class="head clearfix">
                                    <!--<div class="isw-ok"></div>-->
                                    <h1>Create New or Update Sales Report</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="sales-reports" method="" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_report_templates')); ?>">

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Report Title:</div>
                                            <div class="span8"><input type="text" name="report_name" id="report_name" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Description:</div>
                                            <div class="span8"><textarea name="report_description" id="report_description"></textarea></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Primary Field:</div>
                                            <div class="span8"><input type="text" name="report_primary_field" id="report_primary_field" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Report Order:</div>
                                            <div class="span8"><input type="text" name="report_order" id="report_order" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Customers:</div>
                                            <div class="span8">
                                                <select name="report_omc_customer_list[]" id="report_omc_customer_list" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="hidden"  name="report_action_type" id="report_action_type" value="report_save" >
                                            <input type="hidden"  name="report_id" id="report_id" value="0" >
                                            <input type="hidden"  name="omc_id" id="omc_id" value="<?php echo $company_profile['id']; ?>" >
                                            <input type="hidden"  name="menu_id" id="menu_id" value="" >

                                            <button type="button" class="btn" id="report_delete_btn"><i class="isw-delete"></i> Delete</button>
                                            <button type="button" class="btn" id="report_reset"><i class="isw-refresh"></i> Reset</button>
                                            <button type="submit" class="btn" id="report_save"><i class="isw-ok"></i>Save</button>
                                        </div>

                                    </form>
                                </div>

                            </div>

                            <div class="span8">
                                <div class="head clearfix">
                                    <!--<div class="isw-grid"></div>-->
                                    <h1>All Sales Reports</h1>
                                    <ul class="buttons">
                                        <li><a href="javascript:void(0);" id="report_preview_btn" class="isw-picture"> &nbsp;  &nbsp; Preview Report</a></li>
                                    </ul>
                                </div>
                                <div class="block-fluid">
                                    <div style="height: 415px; overflow-x: auto; overflow-y: auto;">
                                        <table id="sales_report_list" cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                            <tr>
                                                <th>Report Name</th>
                                                <th>Report Description</th>
                                                <th>Primary Field</th>
                                                <th>Report Order</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                foreach($sale_reports as $val_arr){
                                                    $val = $val_arr['OmcSalesReport'];
                                            ?>
                                                    <tr data-report_id="<?php echo $val['id']; ?>">
                                                        <td><?php echo $val['report_name']; ?></td>
                                                        <td><?php echo $val['report_description']; ?></td>
                                                        <td><?php echo $val['report_primary_field']; ?></td>
                                                        <td><?php echo $val['report_order']; ?></td>
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
                                    <h1>Sales Report Primary Field's Option</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="sales-report-primary-field-option" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_report_templates')); ?>">

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Report Type:</div>
                                            <div class="span8">
                                                <select name="pf_omc_sales_report_id" id="pf_omc_sales_report_id" class="" required>
                                                    <?php
                                                    foreach($sale_report_options as $key => $opt){
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
                                            <div class="span8"><input type="text" name="report_pf_option_name" id="report_pf_option_name" value="" required class="" /></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Option Link Type:</div>
                                            <div class="span8">
                                                <select name="report_pf_option_link_type" id="report_pf_option_link_type">
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
                                                <select name="report_pf_option_link_id" id="report_pf_option_link_id"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Order:</div>
                                            <div class="span8"><input type="text" name="report_pf_option_order" id="report_pf_option_order" value="" required class="" /></div>
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
                                                <select name="report_pf_total_option_list[]" id="report_pf_total_option_list" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix pf_total_options_and_fields_wrapper" id="" style="border-top-width: 0px; display:none">
                                            <div class="span4">Total Fields List:</div>
                                            <div class="span8">
                                                <select name="report_pf_total_field_list[]" id="report_pf_total_field_list" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="hidden"  name="report_pf_option_id" id="report_pf_option_id" value="0" >
                                            <input type="hidden"  name="report_pf_option_is_total" id="report_pf_option_is_total" value="no" >
                                            <input type="hidden"  name="report_pf_option_action_type" id="report_pf_option_action_type" value="report_option_save" >
                                            <button type="button" class="btn" id="report_pf_option_delete_btn"><i class="isw-delete"></i> Delete</button>
                                            <button type="button" class="btn" id="report_pf_option_reset"><i class="isw-refresh"></i> Reset</button>
                                            <button type="submit" class="btn" id="report_pf_option_save"><i class="isw-ok"></i>Save</button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                            <div class="span6">
                                <div class="head clearfix">
                                    <!--<div class="isw-grid"></div>-->
                                    <h1>Primary Fields Options</h1>
                                    <!--<ul class="buttons">
                                        <li><a href="javascript:void(0);" id="report_field_preview_btn" class="isw-picture"> &nbsp;  &nbsp; Preview Report</a></li>
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
                                    <h1>Sales Reports Fields</h1>
                                </div>
                                <div class="block-fluid">
                                    <form id="sales-report-fields" method="" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_report_templates')); ?>">

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Report Type:</div>
                                            <div class="span8">
                                                <select name="omc_sales_report_id" id="omc_sales_report_id" class="" required>
                                                    <?php
                                                    foreach($sale_report_options as $key => $opt){
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
                                            <div class="span8"><input type="text" name="report_field_name" id="report_field_name" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Order:</div>
                                            <div class="span8"><input type="text" name="report_field_order" id="report_field_order" value="" required class=""></div>
                                        </div>

                                        <div class="row-form clearfix field_dsrp_wrapper" style="border-top-width: 0px;">
                                            <div class="span4">DSRP Form:</div>
                                            <div class="span8">
                                                <select name="report_dsrp_form" id="report_dsrp_form" class="" required>
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
                                            <div class="span4">DSRP Fields:</div>
                                            <div class="span8">
                                                <select name="report_dsrp_fields[]" id="report_dsrp_fields" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="row-form clearfix" style="border-top-width: 0px;">
                                            <div class="span4">Field Action Targets:</div>
                                            <div class="span8">
                                                <select name="report_field_action_targets[]" id="report_field_action_targets" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>

                                        <div class="footer tar">
                                            <input type="hidden"  name="report_field_id" id="report_field_id" value="0" >
                                            <input type="hidden"  name="report_field_action_type" id="report_field_action_type" value="report_field_save" >
                                            <button type="button" class="btn" id="report_field_delete_btn"><i class="isw-delete"></i> Delete</button>
                                            <button type="button" class="btn" id="report_field_reset"><i class="isw-refresh"></i> Reset</button>
                                            <button type="submit" class="btn" id="report_field_save"><i class="isw-ok"></i>Save</button>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        <div class="span8">
                                <div class="head clearfix">
                                    <!--<div class="isw-grid"></div>-->
                                    <h1>Report Fields</h1>
                                    <ul class="buttons">
                                        <li><a href="javascript:void(0);" id="report_field_preview_btn" class="isw-picture"> &nbsp;  &nbsp; Preview Report</a></li>
                                    </ul>
                                </div>
                                <div class="block-fluid">
                                    <div style="height: 415px; overflow-x: auto; overflow-y: auto;">
                                        <table id="report_field_list" cellpadding="0" cellspacing="0" width="100%" class="table">
                                            <thead>
                                            <tr>
                                                <!--<th>Report Name</th>-->
                                                <th>Field Name</th>
                                                <th>Field Type</th>
                                                <th>Field Order</th>
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

<!-- Report Preview -->
<div style="display: none;">
    <div id="preview-form-window" style="width: 900px; overflow: auto">
        <div class="preview-content" style="padding: 20px;"></div>
    </div>
</div>


<!-- URLs -->
<input type="hidden" id="report-save-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'sales_report_templates')); ?>" />

<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var $reports_fields = <?php echo json_encode($reports_fields); ?>;
    var $forms_fields = <?php echo json_encode($forms_fields); ?>;
    var $sale_report_options = <?php echo json_encode($sale_report_options); ?>;
    var $customers = <?php echo json_encode($customers); ?>;
    var $all_option_link_types = <?php echo json_encode($all_option_link_types); ?>;
</script>
<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/sales_report_template.js');
?>
