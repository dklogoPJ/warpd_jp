<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;"><?php echo $omc_name; ?> UPPF <small>Returns</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-brush"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span2" style="width: 80px;">Start Date:</div>
                    <div class="span2" style="width: 8%; margin-left: 3px;">
                        <?php echo $this->Form->input('start_dt', array('id'=>'start_dt', 'class' => 'span2 date-masking validate[required]','default'=>$start_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span2" style="width: 80px;">End Date:</div>
                    <div class="span2" style="width: 8%; margin-left: 3px;">
                        <?php echo $this->Form->input('end_dt', array('id'=>'end_dt', 'class' => 'span2 date-masking validate[required]','default'=>$end_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span2" style="width: 80px;">Product:</div>
                    <div class="span2" style=" margin-left: 3px;">
                        <?php echo $this->Form->input('product_group', array('id'=>'product_group', 'class' => '','default'=>$default_product_group,'options'=>$product_group_list, 'div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span1" style="">OMC:</div>
                    <div class="span3" style=" margin-left: 3px;">
                        <?php echo $this->Form->input('omc', array('id'=>'omc', 'class' => '','default'=>$omc,'options'=>$omc_list, 'div' => false, 'label' => false,)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Get UPPF Report </button>
                    <?php echo $this->Form->input('product_group_name', array('type'=>'hidden','id'=>'product_group_name', 'value'=>'')); ?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1><?php echo $table_title ?> (ltrs)</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success" type="button" id="print-btn">Print </button>
                            <button class="btn btn-success" type="button" id="export-btn">Export </button>
                        <?php
                        }
                        ?>
                        <!--<a href="#" class="isw-text_document"> Export</a>-->
                    </li>
                </ul>
            </div>
            <div class="block-fluid">
                <style type="text/css">
                    .table th, .table td {
                        padding: 2px;
                    }
                    [class*="block"] .table tr th, [class*="block"] .table tr td {
                        border-right: 1px solid #8A8484;
                    }
                    .table th, .table td {
                        border-top: 1px solid #8A8484;
                    }
                    .table td {
                        font-size: 11px;
                    }
                </style>
                <table cellpadding="0" cellspacing="0" width="100%" class="table">
                    <thead>
                    <tr>
                        <?php
                        foreach($t_head as $h){
                            ?>
                            <th><?php echo $h ;?></th>
                        <?php
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($t_body_data as $tbd_arr){
                        ?>
                        <tr>
                            <?php
                            foreach($tbd_arr as $key => $v){
                                if($key == 0){
                                    ?>
                                    <td><?php echo $v ;?></td>
                                <?php
                                }
                                else{
                                    ?>
                                    <td><?php echo $controller->formatNumber($v,'money',0).'' ;?></td>
                                <?php
                                }
                            }
                            ?>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'NpaReport', 'action' => 'print_export_omc_uppf')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_type" id="data_type" value="" />
        <input type="hidden" name="data_start_dt" id="data_start_dt"  value="" />
        <input type="hidden" name="data_end_dt" id="data_end_dt"  value="" />
        <input type="hidden" name="data_product_group" id="data_product_group"  value="" />
        <input type="hidden" name="data_product_group_name" id="data_product_group_name"  value="" />
        <input type="hidden" name="data_omc" id="data_omc" value="" />
        <input type="hidden" name="data_omc_name" id="data_omc_name" value="" />
    </form>


    <div class="dr"><span></span></div>

</div>
<!-- Le Script -->
<?php
echo $this->Html->script('scripts/npa_uppf.js');
?>