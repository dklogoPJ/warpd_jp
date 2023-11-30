<?php
    $t_head = $g_data['table']['t_head'];
    $t_body_data = $g_data['table']['t_body'];
?>
<div class="block users scrollBox" style="border: none; background: none;">
    <div class="scroll" style="height: 650px;">
        <table cellpadding="0" cellspacing="0" width="100%" class="table">
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
            if($t_body_data){
                foreach($t_body_data as $tbd_arr){
                    ?>
                    <tr>
                        <?php
                        foreach($tbd_arr as $key => $v){
                            ?>
                            <td><?php echo $v ;?></td>
                        <?php
                        }
                        ?>
                    </tr>
                <?php
                }
            }
            else{
                ?>
                <tr><td colspan="<?php echo count($t_head); ?>" style="text-align: center;"> No Records Found.</td></tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>