<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Today's Loaded Trucks</h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid accordion">
    <?php
        foreach($loaded_board as $depot){
            $ct = count($depot['data']);
            ?>
            <h3><?php echo $depot['info']['depot'].": &nbsp; $ct trucks to loaded. ";?></h3>
            <div class="scrollBox">
                <div class="scroll" style="height: 170px;">
                    <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                        <thead>
                        <tr>
                            <th>Order No</th><th>Product</th><th>Quantity</th><th>Truck No.</th>
                        </tr>
                        </thead>
                        <tbody >
                        <?php
                        foreach($depot['data'] as $load){
                        ?>
                            <tr>
                                <td><span class="date"><?php echo $load['order_id']?></span></td>
                                <td><?php echo $load['loading_product']?></td>
                                <td><span class="price"><?php echo $load['loading_quantity']?></span></td>
                                <td><span class="date"><?php echo $load['truck_no']?></span></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php
        }
    ?>
</div>

