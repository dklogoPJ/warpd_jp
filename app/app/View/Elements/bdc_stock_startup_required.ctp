<?php
   $show_modal =  isset($show_initial_stock_required_modal) ? $show_initial_stock_required_modal : false;
   $force_new_initialize =  isset($force_new_products_added_to_depot) ? $force_new_products_added_to_depot : false;
?>
<div style="display: none;">
    <div id="required-start-up-stock-window" style="width: 500px;">
        <div class="content" style="padding: 20px; margin: 0px;">
            <fieldset style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                <legend style="font-size: 14px; font-weight: bolder; width:34%; margin: 0px; border-bottom: none">Required Settings </legend>
                <p>
                    <span style="font-size: 14px; font-family: Bookman Old Style">
                        Hello, if you are seeing this screen, it means your Administrator has not configured the initial startup stocks.
                        Please contact your administrator.
                    </span>
                </p>
                <p>
                    <span style="font-size: 14px; font-family: Bookman Old Style">
                        If you are the Administrator, you can click the following links below to configure your initial start up stocks.
                        Note if you are not the Administrator these links won't work for you.<br /><br />

                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots')); ?>"> Setup Depots.</a><br />
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_products')); ?>"> Setup Products.</a><br />
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots_to_products')); ?>"> Setup Matching Depots and Products.</a><br />
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'initial_startup_stocks')); ?>"> Enter Initial Stock For Each Product At Each Depot.</a>
                    </span>
                </p>
            </fieldset>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="initialise-newly-added-products-window" style="width: 500px;">
        <div class="content" style="padding: 20px; margin: 0px;">
            <fieldset style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                <legend style="font-size: 14px; font-weight: bolder; width:34%; margin: 0px; border-bottom: none">Required Settings </legend>
                <p>
                    <span style="font-size: 14px; font-family: Bookman Old Style">
                        Hello, if you are seeing this screen, it means your company has added new products to depots. Please initialise the start up stock quantity for the newly added products.
                    </span>
                </p>
                <p>
                    <span style="font-size: 14px; font-family: Bookman Old Style">
                        If you are the Administrator, you can click the following links below to configure your initial start up stocks quantity.
                        Note if you are not the Administrator these links won't work for you.<br /><br />

                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots')); ?>"> Setup Depots.</a><br />
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_products')); ?>"> Setup Products.</a><br />
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots_to_products')); ?>"> Setup Matching Depots and Products.</a><br />
                        <a href="<?php echo $this->Html->url(array('controller' => 'BdcStock', 'action' => 'initial_startup_stocks')); ?>"> Enter Initial Stock For Each Product At Each Depot.</a>
                    </span>
                </p>
            </fieldset>
        </div>
    </div>
</div>

<script type="text/javascript">
    var show_modal = <?php echo json_encode($show_modal); ?>;
    var force_new_initialize = <?php echo json_encode($force_new_initialize); ?>;
    $(document).ready(function () {
        if(show_modal){
            $.colorbox({
                inline:true,
                scrolling:false,
                overlayClose:false,
                escKey:false,
                top:'15%',
                title:'Start Up Stock Required',
                href:"#required-start-up-stock-window"
            });
            $("#cboxClose").hide();
        }

        if(force_new_initialize){
            $.colorbox({
                inline:true,
                scrolling:false,
                overlayClose:false,
                escKey:false,
                top:'15%',
                title:'Initialise Newly Added Products to Depot',
                href:"#initialise-newly-added-products-window"
            });
            $("#cboxClose").hide();
        }
    });
</script>