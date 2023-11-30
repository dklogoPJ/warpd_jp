<div class="row-fluid">
    <div class="span12">
        <div class="head clearfix">
            <div class="isw-grid"></div>
            <!--<h1><?php /*echo $table_title */?></h1>-->
        </div>
        <div class="block-fluid">
            <table cellpadding="0" cellspacing="0" width="100%" class="tablep table-bordered">
                <thead>
                <tr>
                    <th>OMC</th>
                    <th>Quantity</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($list_data as $data){
                ?>
                    <tr>
                        <td><?php echo $data[0] ;?></td>
                        <td><?php echo  $controller->formatNumber($data[1],'money',0).' ltrs' ;?></td>
                    </tr>
                <?php
                 }
                ?>
                </tbody>
            </table>
        </div>

    </div>
</div>