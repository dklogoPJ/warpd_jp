<div class="workplace">

    <div class="page-header">
        <h1>Dashboard <small></small></h1>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <?php echo $this->element('omc_customer/total_day_sales_liters'); ?>
        </div>
        <div class="span6">
            <?php echo $this->element('omc_customer/total_day_sales_cedis'); ?>
        </div>
    </div>
    <div class="row-fluid">
		<div class="span6">
			<?php echo $this->element('omc_customer/stock_calculation'); ?>
		</div>
        <div class="span6">
            <?php echo $this->element('omc_customer/total_sales'); ?>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>
