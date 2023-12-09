<div class="workplace">

    <div class="page-header">
        <h1>Dashboard <small></small></h1>
    </div>

	<div class="row-fluid">
		<div class="span4">
			<?php echo $this->element('omc/total_day_sales_liters'); ?>
		</div>
		<div class="span4">
			<?php echo $this->element('omc/total_day_sales_cedis'); ?>
		</div>
		<div class="span4">
			<?php echo $this->element('omc/todays_yesterdays_loading'); ?>
		</div>
	</div>
	<div class="row-fluid">

		<div class="span6">
			<?php /*echo $this->element('omc_customer/total_sales'); */?>
		</div>
	</div>

    <?php //echo $this->element('omc/daily_distribution', array('is_connected_to_bdc'=> $is_connected_to_bdc)); ?>
    <div class="dr"><span></span></div>
    <?php
    if($is_connected_to_bdc) {
    ?>
        <div class="row-fluid">
            <div class="span6">
                <?php //echo $this->element('loading_board'); ?>
            </div>
            <div class="span6">
                <?php //echo $this->element('loaded_board'); ?>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="dr"><span></span></div>

</div>
