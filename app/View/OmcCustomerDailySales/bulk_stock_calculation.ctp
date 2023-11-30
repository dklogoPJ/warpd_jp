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
        padding: 2px  !important;
    }
    .table{
        /*width: 80%;*/
    }

</style>

<div class="workplace">

    <div class="page-header">
        <h1>Bulk Stock Calculation : <?php echo date('l jS F Y');?> <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <!--<h1>Sales Record Sheet</h1>-->
                <ul class="buttons">
                    <?php
                    if(in_array('E',$permissions)){
                        ?>
                        <li><a href="javascript:void(0);" id="edit_row_btn" class="isw-edit"> &nbsp;  &nbsp; Edit Row</a></li>
                        <li><a href="javascript:void(0);" id="cancel_row_btn" class="isw-cancel"> &nbsp;  &nbsp; Cancel</a></li>
                        <li><a href="javascript:void(0);" id="save_row_btn" class="isw-ok"> &nbsp;  &nbsp; Save</a></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="block-fluid">
                <div class="block-fluid" style="overflow-y: auto; border: none">
                    <table  id="" class=" form-table table table-bordered">
                        <thead>
                            <tr>
                                <?php
                                    foreach($table_setup as $row){
                                    ?>
                                        <th><?php echo $row['header'].' '.$row['unit'] ;?></th>
                                    <?php
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if($form_data){
                                    foreach($form_data as $row){
                                        $row_id = $row['OmcBulkStockCalculation']['id'];
                                        ?>
                                        <tr data-id="<?php echo $row_id ;?>">
                                            <?php
                                            foreach($table_setup as $tr_row){
                                                $field = $tr_row['field'];
                                                $format = $tr_row['format'];
                                                $editable = $tr_row['editable'];
                                                $cell_value = $field_value = $row['OmcBulkStockCalculation'][$field];
                                                if(is_numeric($field_value)){
                                                    $format_type = $format;
                                                    $decimal_places = 0;
                                                    if($format == 'float'){
                                                        $decimal_places = 2;
                                                        $format_type = 'money';
                                                    }
                                                    if($format_type !=''){
                                                        $cell_value = $this->App->formatNumber($cell_value,$format_type,$decimal_places);
                                                    }
                                                }
                                            ?>
                                                <td data-editable="<?php echo $editable ;?>" data-field="<?php echo $field ;?>" data-value="<?php echo $field_value ;?>"><?php echo $cell_value ;?></td>
                                            <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="dr"><span></span></div>

</div>


<!-- URLs -->
<input type="hidden" id="form-save-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'bulk_stock_calculation')); ?>" />

<!-- Le Script -->
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var table_setup = <?php echo json_encode($table_setup); ?>;
    var stock_position_data = <?php echo json_encode($stock_position_data); ?>;
</script>
<?php
    echo $this->Html->script('scripts/omc_customer/rule_actions.js');
    echo $this->Html->script('scripts/omc_customer/dsrp_common.js');
    echo $this->Html->script('scripts/omc_customer/bulk_stock_calculation.js');
?>
