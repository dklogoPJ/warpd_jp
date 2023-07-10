<div class="workplace">
<?php //debug($authUser); ?>
    <div class="page-header">
        <h1>Daily Sales Record Product <small> </small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span2" style="width: 50px;">Date:</div>
                    <div class="span4" style="">
                        <?php echo $this->Form->input('day', array('id'=>'day', 'class' => '','default'=>$default_day,'options'=>$day_list, 'style' => 'width:50px;display:inline;' ,'div' => false, 'label' => false,)); ?>
                        <?php echo $this->Form->input('month', array('id'=>'month', 'class' => '','default'=>$default_month,'options'=>$month_list, 'style' => 'width:100px;display:inline;','div' => false, 'label' => false,)); ?>
                        <?php echo $this->Form->input('year', array('id'=>'year', 'class' => '','default'=>$default_year,'options'=>$year_list, 'style' => 'width:80px;display:inline;','div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span2" style="width: 80px;">Report Type:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('dsrp_opt', array('id'=>'dsrp_opt', 'class' => '','default'=>$default_dsrp,'options'=>$dsrp_list, 'div' => false, 'label' => false,)); ?>
                    </div>

                    <!--<div class="span2" style="width: 80px;">Customer:</div>-->
                    <!--<div class="span2">-->
                    <?php echo $this->Form->input('customer', array('type'=>'hidden','id'=>'customer','value'=>$default_customer)); ?>
                    <!--</div>-->
                </div>

                <div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Get Report </button>
                </div>
                <?php echo $this->Form->end();?>
            </div>
         </div>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1><?php echo $table_title ?></h1>
                <ul class="buttons">
                    <?php
                    if(in_array('PX',$permissions)){
                        ?>
                        <!--<button class="btn btn-success" type="button" id="print-btn">Print </button>-->
                        <button class="btn btn-success" type="button" id="export-btn">Export </button>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="block-fluid" style="padding: 20px;">
                <div class="block-fluid" style="overflow-y: auto; border: none">
                    <?php
                    if($default_dsrp == 'bsp'){
                        echo $this->element('omc/reporting/bulk_stock_position');
                    }
                    elseif($default_dsrp == 'bsc'){
                        echo $this->element('omc/reporting/bulk_stock_calculation');
                    }
                    elseif($default_dsrp == 'dsp'){
                        echo $this->element('omc/reporting/daily_sales_product');
                    }
                    elseif($default_dsrp == 'ccs'){
                        echo $this->element('omc/reporting/cash_credit_summary');
                    }
                    elseif($default_dsrp == 'opc'){
                        echo $this->element('omc/reporting/operators_credit');
                    }
                    elseif($default_dsrp == 'cmc'){
                        echo $this->element('omc/reporting/customer_credit');
                    }
                    elseif($default_dsrp == 'lbp'){
                        echo $this->element('omc/reporting/lubricant_position');
                    }
                    ?>
                </div>

            </div>

        </div>
    </div>

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'export_dsrp')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_dsrp_type" id="data_dsrp_type" value="" />
        <input type="hidden" name="data_customer" id="data_customer" value="" />
        <input type="hidden" name="data_month" id="data_month"  value="" />
        <input type="hidden" name="data_year" id="data_year"  value="" />
        <input type="hidden" name="data_day" id="data_day"  value="" />
        <input type="hidden" name="data_doc_type" id="data_doc_type"  value="" />
    </form>

    <div class="dr"><span></span></div>
</div>

<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/omc/report_dsrp.js');
?>
