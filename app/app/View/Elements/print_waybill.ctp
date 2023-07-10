<div class="row-fluid">
    <div class="span12">
        <div class="head clearfix">
            <div class="isw-grid"></div>
            <!--<h1><?php /*echo $table_title */?></h1>-->
        </div>
        <div class="block-fluid">
            <?php
                $way_bill_id =   $print_data['Waybill']['id'];
                $way_bill_date =    $this->App->covertDate($print_data['Waybill']['created'],'mysql_flip');
                $order_date =    $this->App->covertDate($print_data['Order']['order_date'],'mysql_flip');
                $loaded_date =  $this->App->covertDate($print_data['Order']['loaded_date'],'mysql_flip') ;
                $order_id =   $print_data['Order']['id'];
                $product = $print_data['Order']['ProductType']['name'];
                $loaded_quantity =   $print_data['Order']['loaded_quantity'];
                $truck_no =   $print_data['Order']['truck_no'];
                $endorse = array();
                $endorse[] = isset($print_data['Endorsement']['depot'])?$print_data['Endorsement']['depot']:false;
                $endorse[] = isset($print_data['Endorsement']['ceps_depot'])?$print_data['Endorsement']['ceps_depot']:false;
                $endorse[] = isset($print_data['Endorsement']['bdc'])?$print_data['Endorsement']['bdc']:false;
            ?>
            <style type="text/css">
                .tablep th, .tablep td {
                    border-top: 0px;
                }
            </style>

            <table cellpadding="0" cellspacing="0" class="tablep">
                <tbody>
                    <tr>
                        <td colspan="2"><strong>Way Bill Date :</strong><?php echo $way_bill_date ;?></td>
                        <td><strong>Way Bill No :</strong><?php echo "#".$way_bill_id ;?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Order Date :</strong><?php echo $order_date ;?></td>
                        <td><strong>Order No :</strong><?php echo "#".$order_id ;?></td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><strong>Product</strong></td>
                        <td><strong>Quantity</strong></td>
                        <td><strong>Truck Number</strong></td>
                    </tr>
                    <tr>
                        <td><?php echo $product ;?></td>
                        <td><?php echo $this->App->formatNumber($loaded_quantity,'money',0) ;?></td>
                        <td><?php echo $truck_no ;?></td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <?php
                            foreach($endorse as $dt){
                                if($dt){
                                    $from = $dt['from'];
                                    $endorsed_by = $dt['endorsed_by'];
                                    $sign_date = $this->App->covertDate($dt['date'],'mysql_flip');
                                    echo "<td><strong>Approved :</strong><br />";
                                    echo $from."<br />".$endorsed_by."<br />".$sign_date."</td>";
                                }
                            }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>