<style>
    .buttons li a{
        width: 100%;
        color: #fff;
        text-decoration: none;
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

</style>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $menu_title.' : '.date('l jS F Y',strtotime($sales_report_date));?> <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">
            <div class="head clearfix">
                <div class="isw-list"></div>
                <h1>Report Sheet</h1>
                <ul class="buttons">
                    <li>
                        <label for="sales-sheet-dates" class="label-override">Report Dates:</label>
                        <select class="sales-sheet-dates-class" name="sales-sheet-dates" id="sales-sheet-dates">
                            <?php
                            foreach($last7days as $key => $opt){
                                ?>
                                <option value="<?php echo $key; ?>" <?php echo $key == $sales_report_date ? 'selected':''  ?>><?php echo $opt; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </li>

                    <li class="spacer">
                        &nbsp;
                    </li>

                </ul>
            </div>
            <div class="block-fluid" id="form_tabs">
                <div style="padding: 10px 10px 0px;">
                    <div class="row-fluid">
                        <?php
                        if($report_records) {
                            ?>
                            <div class="span12">
                                <div style="height: 550px; overflow-x: auto; overflow-y: auto;">
                                    <?php
                                    echo $this->TableForm->renderDailySalesReport($report_records);
                                    ?>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="span12">
                                <div class="row-fluid">
                                    <div class="span12" style="text-align: center; margin-bottom: 20px;">
                                        <h5><?php echo $menu_title." has no sales sheet on ".date('l jS F Y',strtotime($sales_report_date))."." ?> </h5>
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
    <input type="hidden" id="dsrp-report-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySalesReport', 'action' => 'index')); ?>" />

    <!-- Le Script -->
    <script type="text/javascript">
        var permissions = <?php echo json_encode($permissions); ?>;
        var report_key = <?php echo json_encode($report_key); ?>;
    </script>
    <?php
    echo $this->Html->script('scripts/omc_customer/dsrp_report.js');
    ?>
