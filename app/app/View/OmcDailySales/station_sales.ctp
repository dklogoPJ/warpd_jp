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
    .isw-download{
        background-position: 4% 50%;
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
<script type="text/javascript">
   var permissions = <?php echo json_encode($permissions); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1>Daily Station Sales  <small> </small></h1>
    </div>


    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query','inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span1" style="">Station:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('station', array('id'=>'station', 'class' => '','options'=>$station_opt, 'div' => false, 'label' => false,)); ?>
                    </div>
                    <div class="span1" style="">Form:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('sales_form_id', array('id'=>'sales_form_id', 'class' => '','options'=>$form_sales_opt, 'div' => false, 'label' => false,)); ?>
                    </div>
                    <div class="span2" style="">Record Date:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('record_dt', array('type'=>'text','id'=>'record_dt', 'class' => 'datepicker', 'required', 'div' => false, 'label' => false)); ?>
                    </div>

                    <!--<div class="span2" style="width: 80px;">&nbsp;</div>-->
                    <div class="span2">
                        <!-- --><?php /*echo $this->Form->input('indicator', array('id'=>'indicator', 'class' => '','default'=>$indicator,'options'=>array('all'=>'All','red'=>'Red','yellow_red'=>'Yellow & Red'), 'div' => false, 'label' => false,)); */?>
                        <button class="btn" type="submit" id="query-btn">Get Sales Record </button>
                    </div>
                </div>
                <!--<div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Get Stock Variance </button>
                </div>-->
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>



    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Station Sales</h1>
                <ul class="buttons">
                    <li><a href="javascript:void(0);" id="export_sales_btn" class="isw-download"> &nbsp;  &nbsp; Export Data</a></li>
                </ul>
            </div>
            <div class="block-fluid" id="tab-content">

            </div>
        </div>

    </div>

    <div class="dr"><span></span></div>

    <form id="export-daily-sales-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'export_sale_data')); ?>" target="ExportWindow">
        <input type="hidden" name="data_station" id="data_station" value="" />
        <input type="hidden" name="data_sales_form_id" id="data_sales_form_id"  value="" />
        <input type="hidden" name="data_record_dt" id="data_record_dt"  value="" />
    </form>

</div>


<!-- URLs -->
<input type="hidden" id="load-record-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'station_sales')); ?>" />
<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/station_sales.js');
?>
