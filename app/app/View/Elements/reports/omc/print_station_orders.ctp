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
                    foreach($g_data['table']['thead'] as $h){
                        ?>
                        <th><?php echo $h ;?></th>
                    <?php
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($g_data['table']['tbody'] as $tbd_arr){
                    ?>
                    <tr>
                        <?php
                        foreach($tbd_arr as $key => $v){
                            if($key == 0){
                                ?>
                                <td><?php echo $v ;?></td>
                            <?php
                            }
                            else{
                                ?>
                                <td><?php echo $this->App->formatNumber($v,'money',0).' ltrs' ;?></td>
                            <?php
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