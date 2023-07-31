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

</style>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $menu_title.' : '.date('l jS F Y');?> <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Sales Record Sheet</h1>
                <ul class="buttons">
                    <?php
                    if($current_day_records) {
                    ?>
                        <?php
                        if (in_array("D", $permissions)) {
                        ?>
                        <li><a href="javascript:void(0);" id="delete_sales_sheet_btn" class="grid_menu"><i class="isw-delete"></i> Delete Sales Sheet</a></li>
                        <?php
                        }
                        ?>

                        <?php
                        if (in_array("E", $permissions)) {
                            ?>
                            <li><a href="javascript:void(0);" id="edit_row_btn" class="grid_menu"><i class="isw-edit"></i>Edit Row</a></li>
                            <li><a href="javascript:void(0);" id="cancel_row_btn" class="grid_menu"><i class="isw-cancel"></i>Cancel Editing</a></li>
                            <li><a href="javascript:void(0);" id="save_row_btn" class="grid_menu"><i class="isw-ok"></i>Save Row</a></li>
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
                                    <h5><?php echo $menu_title." has no sales sheet for today. Please click on the button below to create the sales sheet." ?> </h5>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span12" style="text-align: center; margin-bottom: 20px;">
                                    <button type="button" class="btn" id="new_sales_sheet_btn"><i class="isw-empty_document"></i> Creat Sales Sheet</button>
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

</div>


<!-- URLs -->
<input type="hidden" id="form-sales-sheet-id" value="<?php echo $sales_sheet_id; ?>" />
<input type="hidden" id="form-save-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'index')); ?>" />

<!-- Le Script -->
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
   // var form_field_rendered = <?php // echo json_encode($form_field_rendered); ?>;
    var price_change_data = <?php echo json_encode($price_change_data); ?>;
    var previous_day_records = <?php echo json_encode($previous_day_records); ?>;
    var current_day_records =  <?php echo json_encode($current_day_records); ?>;
    var form_key = <?php echo json_encode($form_key); ?>;
</script>
<?php
echo $this->Html->script('scripts/omc_customer/event_actions.js');
echo $this->Html->script('scripts/omc_customer/daily_sales.js');
?>
