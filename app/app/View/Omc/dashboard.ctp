<div class="workplace">

    <div class="page-header">
        <h1>Dashboard <small></small></h1>
    </div>

    <?php echo $this->element('omc/daily_distribution'); ?>
    <div class="dr"><span></span></div>
    <div class="row-fluid">
        <div class="span6">
            <?php echo $this->element('loading_board'); ?>
        </div>
        <div class="span6">
            <?php echo $this->element('loaded_board'); ?>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>
