<div class="head clearfix">
    <div class="isw-archive"></div>
    <h1>Cash & Credit Summary : <?php echo $format_date;?></h1>
    <ul class="buttons">
        <li>
            <a href="<?php echo $this->Html->url(array('controller' =>  $this->params['controller'], 'action' =>  'dashboard')); ?>" class="isw-refresh"></a>
        </li>
    </ul>
</div>
<div class="block-fluid">
    <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
        <tbody >
    <?php
        foreach($widget_data_cash_credit_summary as $update){
            $header = $update['header'];
            $value = $update['value'];
            ?>
                <tr>
                    <td style="width: 50%;"><span class=""><?php echo $header; ?></span></td>
                    <td><span class=""><?php echo "GHS "; ?></span></td>
                    <td><span class="date"><?php echo $value; ?></span></td>
                </tr>
            <?php
        }
    ?>
        </tbody>
    </table>
</div>

