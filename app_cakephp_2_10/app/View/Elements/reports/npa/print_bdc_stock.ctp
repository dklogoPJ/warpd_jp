<div class="row-fluid">
    <div class="span12">
        <div class="head clearfix">
            <div class="isw-grid"></div>
            <!--<h1><?php /*echo $table_title */?></h1>-->
        </div>
        <div class="block-fluid">
            <?php
            if($grid){
                ?>
                <table cellpadding="0" cellspacing="0" width="100%" class="tablep table-bordered">
                    <!--<thead>
                    <tr>
                        <?php
                        /*foreach($g_data['t_head'] as $h){
                            */?>
                            <th><?php /*echo $h ;*/?></th>
                        <?php
                        /*}
                        */?>
                    </tr>
                    </thead>
                    <tbody>-->
                    <?php
                    $style_td_1 = "style='width:20%; ;vertical-align: middle; font-style: italic;font-family: bookman old style;font-size: 22px; font-weight:bolder;background: #F9F9F9'";
                    $style_td_2 = "style='vertical-align: middle;font-weight:bolder; background:#E8EBF0;'";
                    $style_td_3 = "style='font-size: 10.5px;'";
                    $final_str = "";
                    $headers = $grid['headers'];
                    $t_heads_str = "<tr><th><strong>Depot</strong></th><th><strong>Product</strong></th><th></th>";
                    //create headers first
                    foreach($headers as $h){
                        $t_heads_str .= "<th><strong>$h</strong></th>";
                    }
                    $t_heads_str .= "</tr>";
                    $final_str .= $t_heads_str;
                    foreach($grid['data'] as $depot){
                        $tr_str = "<tr>";
                        $row_span = count($depot['products']) * 4;
                        $tr_str .= "<td rowspan='$row_span' $style_td_1>".$depot['name']."</td>";
                        //treat the first product special
                        $first_pro = array_shift($depot['products']);
                        $tr_str .= "<td rowspan='4' $style_td_2>".$first_pro['name']."</td>";
                        //Initial
                        $tr_str .= "<td $style_td_3>Initial</td>";
                        foreach($headers as $h){
                            $m = isset($first_pro['data'][$h])? $first_pro['data'][$h]['initial_quantity']:0;
                            $init = $controller->formatNumber($m,'money',0);
                            $tr_str .= "<td $style_td_3>$init</td>";
                        }
                        $tr_str .= "</tr>";
                        //Recievings
                        $tr_str .= "<tr><td $style_td_3>Receipts</td>";
                        foreach($headers as $h){
                            $m = isset($first_pro['data'][$h])?$first_pro['data'][$h]['stock_update_quantity']:0;
                            $recv = $controller->formatNumber($m,'money',0);
                            $tr_str .= "<td $style_td_3>$recv</td>";
                        }
                        $tr_str .= "</tr>";
                        //Lifting
                        $tr_str .= "<tr><td $style_td_3>Lifting</td>";
                        foreach($headers as $h){
                            $m = isset($first_pro['data'][$h])?$first_pro['data'][$h]['lifting_quantity']:0;
                            $lift = $controller->formatNumber($m,'money',0);
                            $tr_str .= "<td $style_td_3>$lift</td>";
                        }
                        $tr_str .= "</tr>";
                        //Closing
                        $tr_str .= "<tr><td $style_td_3>Closing</td>";
                        foreach($headers as $h){
                            $m = isset($first_pro['data'][$h])? $first_pro['data'][$h]['closing_quantity']:0;
                            $cls = $controller->formatNumber($m,'money',0);
                            $tr_str .= "<td $style_td_3>$cls</td>";
                        }
                        $tr_str .= "</tr>";

                        //now process the rest of the products
                        foreach($depot['products'] as $v_arr){
                            $tr_str .= "</tr><td rowspan='4' $style_td_2>".$v_arr['name']."</td>";
                            //Initial
                            $tr_str .= "<td $style_td_3>Initial</td>";
                            foreach($headers as $h){
                                $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['initial_quantity']:0;
                                $init = $controller->formatNumber($m,'money',0);
                                $tr_str .= "<td $style_td_3>$init</td>";
                            }
                            $tr_str .= "</tr>";
                            //Recievings
                            $tr_str .= "<tr><td $style_td_3>Receipts</td>";
                            foreach($headers as $h){
                                $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['stock_update_quantity']:0;
                                $recv = $controller->formatNumber($m,'money',0);
                                $tr_str .= "<td $style_td_3>$recv</td>";
                            }
                            $tr_str .= "</tr>";
                            //Lifting
                            $tr_str .= "<tr><td $style_td_3>Lifting</td>";
                            foreach($headers as $h){
                                $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['lifting_quantity']:0;
                                $lift = $controller->formatNumber($m,'money',0);
                                $tr_str .= "<td $style_td_3>$lift</td>";
                            }
                            $tr_str .= "</tr>";
                            //Closing
                            $tr_str .= "<tr><td $style_td_3>Closing</td>";
                            foreach($headers as $h){
                                $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['closing_quantity']:0;
                                $cls = $controller->formatNumber($m,'money',0);
                                $tr_str .= "<td $style_td_3>$cls</td>";
                            }
                            $tr_str .= "</tr>";
                            //
                        }
                        $final_str .= $tr_str;
                    }
                    echo $final_str;
                    ?>
                    <!-- </tbody>-->
                </table>
            <?php
            }
            else{

            }
            ?>
        </div>

    </div>
</div>