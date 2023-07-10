<style>
    table th, .table td {
        padding: 2px;
        white-space: nowrap !important;
    }
</style>
<?php
if($g_data){
    $table_setup = $g_data['header'];
    $form_data = $g_data['data'];
    ?>
    <table id="" class="table table-bordered">
        <thead>
        <tr>
            <?php
            foreach($table_setup as $row){
                ?>
                <th><?php echo $row['header'].' '.$row['unit'] ;?></th>
            <?php
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if($form_data){
            foreach($form_data as $row){
                $row_id = $row['OmcDailySalesProduct']['id'];
                ?>
                <tr data-id="<?php echo $row_id ;?>">
                    <?php
                    foreach($table_setup as $tr_row){
                        $field = $tr_row['field'];
                        $format = $tr_row['format'];
                        $editable = $tr_row['editable'];
                        $cell_value = $field_value = $row['OmcDailySalesProduct'][$field];
                        if(is_numeric($field_value)){
                            $format_type = $format;
                            $decimal_places = 0;
                            if($format == 'float'){
                                $decimal_places = 2;
                                $format_type = 'money';
                            }
                            if($format_type !=''){
                                $cell_value = $this->App->formatNumber($cell_value,$format_type,$decimal_places);
                            }
                        }
                        ?>
                        <td data-editable="<?php echo $editable ;?>" data-field="<?php echo $field ;?>" data-value="<?php echo $field_value ;?>"><?php echo $cell_value ;?></td>
                    <?php
                    }
                    ?>
                </tr>
            <?php
            }
        }
        ?>
        </tbody>
    </table>
<?php
}
else{
    ?>

<?php
}
?>
