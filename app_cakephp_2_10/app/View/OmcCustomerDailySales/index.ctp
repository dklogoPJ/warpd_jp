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
        <h1>Daily Sales Record : <?php echo date('l jS F Y');?> <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Sales Record Sheet</h1>
                <ul class="buttons">
                    <li><a href="javascript:void(0);" id="add_row_btn" class="isw-empty_document"> &nbsp;  &nbsp; Add Row</a></li>
                    <li><a href="javascript:void(0);" id="edit_row_btn" class="isw-edit"> &nbsp;  &nbsp; Edit Row</a></li>
                    <li><a href="javascript:void(0);" id="cancel_row_btn" class="isw-cancel"> &nbsp;  &nbsp; Cancel</a></li>
                    <li><a href="javascript:void(0);" id="save_row_btn" class="isw-ok"> &nbsp;  &nbsp; Save</a></li>
                </ul>
            </div>
            <div class="block-fluid" id="form_tabs">
                <ul id="sales-form-tabs">
                    <?php
                    foreach($forms_n_fields as $f){
                        $form_id = $f['id'];
                        $form_name = $f['name'];
                        $render_type = $f['render_type'];
                        $tab_ref_id = "#tabs-".$form_id;
                    ?>
                        <li><a href="<?php echo $tab_ref_id; ?>" data-render_type="<?php echo $render_type; ?>"  data-form_id="<?php echo $form_id; ?>"  data-form_table_id="#form-table-<?php echo $form_id; ?>"><strong><?php echo $form_name; ?></strong></a></li>
                    <?php
                    }
                    ?>
                </ul>
                <?php
                $form_field_rendered = array();
                foreach($forms_n_fields as $f){
                    $form_id = $f['id'];
                    $tab_id = "tabs-".$form_id;
                    $table_id = "form-table-".$form_id;
                ?>
                    <div id="<?php echo $tab_id; ?>">
                        <div style="padding: 40px 10px 0px;">
                            <div class="row-fluid">
                                <div class="span12">
                                    <div class="block-fluid">
                                        <div style="height: 550px; overflow-x: auto; overflow-y: auto;">
                                            <?php
                                            $table_n_fields = $this->TableForm->render($table_id,$f['fields'],$f['values']);
                                            $form_field_rendered[$form_id] = $table_n_fields['fields'];
                                            echo $table_n_fields['table'];
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

    </div>

    <div class="dr"><span></span></div>

</div>


<!-- URLs -->
<input type="hidden" id="form-save-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'index')); ?>" />

<!-- Le Script -->
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var forms_n_fields = <?php echo json_encode($forms_n_fields); ?>;
    var form_field_rendered = <?php echo json_encode($form_field_rendered); ?>;
    var price_change_data = <?php echo json_encode($price_change_data); ?>;
    var previous_day_records = <?php echo json_encode($previous_day_records); ?>;
    var current_day_records =  <?php echo json_encode($current_day_records); ?>;
</script>
<?php
echo $this->Html->script('scripts/omc_customer/daily_sales.js');
echo $this->Html->script('scripts/omc_customer/form_rules2.js');
?>
