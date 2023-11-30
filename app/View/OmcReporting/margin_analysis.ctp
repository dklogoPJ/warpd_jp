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
        <h1>Margin Analysis  <small> Report</small></h1>
    </div>


    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Report Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query','inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span2" style="width: 7%">Type:</div>
                    <div class="span3">
                        <?php echo $this->Form->input('report_type', array('id'=>'report_type', 'class' => '','options'=>$report_types, 'default'=>$report_type, 'div' => false, 'label' => false,)); ?>
                    </div>
                    <div class="span1" style="">Month:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('month', array('id'=>'month', 'class' => '', 'options'=>$month_list, 'required', 'default'=>$month, 'div' => false, 'label' => false)); ?>
                    </div>
                    <div class="span1" style="">End Date:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('year', array('type'=>'text','id'=>'year', 'class' => '', 'value'=>$year, 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                    <!--<div class="span2" style="width: 80px;">&nbsp;</div>-->
                    <div class="span2" style="width: 10%">
                        <!-- --><?php /*echo $this->Form->input('indicator', array('id'=>'indicator', 'class' => '','default'=>$indicator,'options'=>array('all'=>'All','red'=>'Red','yellow_red'=>'Yellow & Red'), 'div' => false, 'label' => false,)); */?>
                        <button class="btn" type="submit" id="query-btn">Get Report </button>
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
                <h1><?php echo $table_title; ?></h1>
                <ul class="buttons">
                    <?php
                    if(in_array('PX',$permissions)){
                        ?>
                        <li><button class="btn btn-success" type="button" id="print-btn">Print </button></li>
                        <li><button class="btn btn-success" type="button" id="export-btn">Export </button></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="block-fluid">
                <?php echo $this->element('omc/reporting/'.$report_type); ?>
            </div>
        </div>

    </div>

    <div class="dr"><span></span></div>

    <form id="export_margin_analysis_form" method="post" action="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'print_export_margin_analysis')); ?>" target="ExportWindow">
        <input type="hidden" name="data_report_type" id="data_report_type" value="" />
        <input type="hidden" name="data_month" id="data_month"  value="" />
        <input type="hidden" name="data_year" id="data_year"  value="" />
        <input type="hidden" name="data_type" id="data_type"  value="print" />
    </form>

</div>


<!-- URLs -->
<input type="hidden" id="load-record-url" value="<?php echo $this->Html->url(array('controller' => 'OmcReporting', 'action' => 'margin_analysis')); ?>" />
<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/report_margin_analysis.js');
    if(in_array('PX',$permissions)){
        echo $this->Html->script('highcharts/exporting.js');
    }
?>
