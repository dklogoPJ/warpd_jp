<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Current Stock level</h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
    <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
        <thead>
        <tr>
            <th>Product</th><th>Current Stock Level (ltr)</th><th>Last Updated</th>
        </tr>
        </thead>
        <tbody >
        <?php
        foreach($last_stock_updates as $update){
            $stock = $update['omc_customer_stocks'];
            $tank = $update['omc_customer_tanks'];
            $stck_qty = $this->App->formatNumber(preg_replace('/,/','',$stock['quantity']),'money',0);
        ?>
            <tr>
                <td><span class=""><?php echo $tank['name']; ?></span></td>
                <td><span class="date"><?php echo $stck_qty; ?></span></td>
                <td><span class=""><?php echo $this->App->covertDate($stock['created'],'mysql_flip');  ?></span></td>
            </tr>
        <?php
         }
        ?>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>

