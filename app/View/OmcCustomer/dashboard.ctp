<div class="workplace">

    <div class="page-header">
        <h1>Dashboard <small></small></h1>
    </div>

    <div class="row-fluid">
        <div class="span6">
            <?php echo $this->element('omc_customer/customer_stock_board'); ?>
        </div>
        <div class="span6">
            <?php echo $this->element('omc_customer/cash_credit_summary'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <?php echo $this->element('omc_customer/total_sales'); ?>
        </div>
        <div class="span6">
            <?php echo $this->element('omc_customer/stock_calculation'); ?>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>
