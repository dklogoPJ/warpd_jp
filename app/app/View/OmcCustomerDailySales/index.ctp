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
    .isw-empty_document {
        background-position: 10% 50% ;
    }
    .isw-edit {
        background-position: 10% 50% ;
    }
    .isw-cancel {
        background-position: 10% 50% ;
    }
    .isw-ok {
        background-position: 10% 50% ;
    }
    .grid_menu {
        line-height: 37px;
        padding: 0 10px 0 10px;
        margin: 10px;
    }
    .grid_menu i {
        padding: 11px 0px !important;
    }

    .selected td{
        color: #486B91;
        font-weight: bolder;
        background-color: #D1E0F0 !important;
    }
    tr:hover{
        cursor: pointer;
    }
    .error_span{
        color: #e9322d;
        font-style: italic;
        font-size: 11px;
    }

    td input,td select{
        margin: 0px !important;
        padding: 1px !important;
    }

    th,td{
        white-space: nowrap !important;
    }

    .label-override {
        display: inline;
        margin-top: 4px;
        color: white;
    }

    .sales-sheet-dates-class {
        margin-bottom: 0px;
        margin-top: 4px;
        width: 150px;
    }
    ul.buttons li  {
        display: flex;
        align-items: center;
        height: 100%;
    }
    ul.buttons li.spacer  {
        width: 50px;
    }
    .linked_report_content {
        display: flex;
        justify-content: center;
    }
    #linked_report_loader {
        color: #131313;
        font-style: italic;
        font-size: 16px;
    }

</style>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $menu_title.' : '.date('l jS F Y',strtotime($sales_sheet_date));?> <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Sales Record Sheet</h1>
                <ul class="buttons">
                    <li>
                        <label for="sales-sheet-dates" class="label-override">Sales Sheets Dates:</label>
                        <select class="sales-sheet-dates-class" name="sales-sheet-dates" id="sales-sheet-dates">
                            <?php
                            foreach($sales_sheet_date_range as $key => $opt){
                                ?>
                                <option value="<?php echo $key; ?>" <?php echo $key == $sales_sheet_date ? 'selected':''  ?>><?php echo $opt; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>

                    <li class="spacer">&nbsp</li>

                    <?php
                    if($current_day_records) {
                    ?>
                        <?php
                        if (in_array("D", $permissions)) {
                        ?>
                        <li><button class="btn btn-danger" type="button" id="delete_sales_sheet_btn">Delete Sales Sheet</button> <!--<a href="javascript:void(0);" id="delete_sales_sheet_btn" class="grid_menu"><i class="isw-delete"></i> Delete Sales Sheet</a>--></li>
                        <?php
                        }
                        ?>
						<li class="spacer">&nbsp</li>

                        <?php
                        if (in_array("E", $permissions)) {
                            ?>
                            <li><button class="btn btn-info" type="button" id="edit_row_btn">Edit Row</button><!--<a href="javascript:void(0);" id="edit_row_btn" class="grid_menu"><i class="isw-edit"></i>Edit Row</a>--></li>
                            <li><button class="btn btn-inverse" type="button" id="cancel_row_btn">Cancel Editing</button><!--<a href="javascript:void(0);" id="cancel_row_btn" class="grid_menu"><i class="isw-cancel"></i>Cancel Editing</a></li>-->
                            <li><button class="btn btn-success" type="button" id="save_row_btn">Save Row</button><!--<a href="javascript:void(0);" id="save_row_btn" class="grid_menu"><i class="isw-ok"></i>Save Row</a></li>-->
                            <?php
                        }
                        ?>
                    <?php
                    }
                    ?>

                </ul>
            </div>
            <div class="block-fluid" id="form_tabs">
                <div style="padding: 10px 10px 0px;">
                    <div class="row-fluid">
                        <?php
                        if($current_day_records) {
                        ?>
                        <div class="span12">
                            <div style="height: 550px; overflow-x: auto; overflow-y: auto;">
                                <?php
                                echo $this->TableForm->renderDailySalesTableForm($current_day_records);
                                ?>
                            </div>
                        </div>
                        <?php
                        } else {
                        ?>
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="span12" style="text-align: center; margin-bottom: 20px;">
                                    <h5><?php echo $menu_title." has no sales sheet on ".date('l jS F Y',strtotime($sales_sheet_date)).". Please click on the button below to create the sales sheet." ?> </h5>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span12" style="text-align: center; margin-bottom: 20px;">
                                    <button type="button" class="btn" id="new_sales_sheet_btn"><i class="isw-empty_document"></i> Creat Sales Sheet for <?php echo date('l jS F Y',strtotime($sales_sheet_date)) ?></button>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="dr"><span></span></div>

    <?php
        if($report_title) {
     ?>
         <div class="row-fluid" id="linked_report_container">
                <div class="span12">
                    <div class="head clearfix">
                        <div class="isw-list"></div>
                        <h1><span id="linked_report_title"><?php echo $report_title ?></span></h1>
                        <ul class="buttons">
                            <li>
                                <label for="sales-sheet-dates" class="label-override">Report Dates:</label>
                                <select class="sales-sheet-dates-class" name="linked_report_date" id="linked_report_date">
                                    <?php
                                    foreach($sales_sheet_date_range as $key => $opt){
                                        ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key == $sales_sheet_date ? 'selected':''  ?>><?php echo $opt; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </li>

                            <li class="spacer">
                                &nbsp;
                            </li>

                            <li><a href="javascript:void(0);" id="linked_report_refresh" class="grid_menu"><i class="isw-refresh"></i>Refresh</a></li>

                        </ul>
                    </div>
                    <div class="block-fluid" >
                        <div style="padding: 10px 10px 0px;">
                            <div class="row-fluid">
                                <div class="span12 linked_report_content">
                                    <div id="linked_report_loader"></div>
                                    <div id="linked_report_html"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    <?php
        }
    ?>

</div>


<!-- URLs -->
<input type="hidden" id="form-sales-sheet-id" value="<?php echo $sales_sheet_id; ?>" />
<input type="hidden" id="form-save-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'index')); ?>" />
<input type="hidden" id="linked_report_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'get_dsrp_report')); ?>" />

<!-- This URL will be used by Ajax upload -->
<input type="hidden" id="get_attachments_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'get_attachments')); ?>" />
<input type="hidden" id="ajax_upload_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'attach_files')); ?>" />
<?php echo $this->element('ajax_upload');?>

<!-- Le Script -->
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
   // var form_field_rendered = <?php // echo json_encode($form_field_rendered); ?>;
    var all_external_data_sources = <?php echo json_encode($all_external_data_sources); ?>;
    var previous_day_records = <?php echo json_encode($previous_day_records); ?>;
    var current_day_records =  <?php echo json_encode($current_day_records); ?>;
    var form_key = <?php echo json_encode($form_key); ?>;
</script>
<?php
echo $this->Html->script('scripts/omc_customer/event_actions.js');
echo $this->Html->script('scripts/omc_customer/daily_sales.js');
?>
