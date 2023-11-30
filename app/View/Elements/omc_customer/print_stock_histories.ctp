<div class="row-fluid">
    <div class="span12">
        <div class="head clearfix">
            <div class="isw-grid"></div>
            <!--<h1><?php /*echo $table_title */?></h1>-->
        </div>
        <div class="block-fluid">
            <table cellpadding="0" cellspacing="0" class="tablep table-bordered">
                <thead>
                <tr>
                    <?php
                    foreach($t_head as $h){
                        ?>
                        <th><?php echo $h ;?></th>
                    <?php
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($t_body_data as $tbd_arr){
                    ?>
                    <tr>
                        <?php
                        foreach($tbd_arr as $key => $v){
                            if(!is_int($key)){
                                ?>
                                <td><?php echo $v ;?></td>
                            <?php
                            }
                            else{
                                if($v == '-'){
                                    ?>
                                    <td><?php echo $v ;?></td>
                                <?php
                                }
                                else{
                                    ?>
                                    <td><?php echo $controller->formatNumber($v,'money',0).' ltrs' ;?></td>
                                <?php
                                }
                            }
                        }
                        ?>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>

    </div>
</div>