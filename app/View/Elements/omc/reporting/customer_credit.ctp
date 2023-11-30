<style>
    table th, .table td {
        padding: 2px;
        white-space: nowrap !important;
    }
    .table{
        width: 40%;
    }
</style>
<?php
if($g_data){
    $table_setup = $g_data['header'];
    $form_data = $g_data['data'];
    ?>
    <table id="" class="table table-bordered">
        <tbody>
        <?php
        $id = $form_data[0]['OmcCustomersCredit']['id'];
        foreach($table_setup as $row){
            $header = $row['header'];
            $field = $row['field'];
            $format = $row['format'];
            $editable = $row['editable'];
            $row_id = $id;
            $cell_value = $field_value = $form_data[0]['OmcCustomersCredit'][$field];
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
            <tr data-id="<?php echo $row_id ;?>">
                <td data-editable="no"><?php echo $header ;?></td>
                <td data-editable="<?php echo $editable ;?>" data-field="<?php echo $field ;?>" data-value="<?php echo $field_value ;?>" width="50%"><?php echo $cell_value ;?></td>

            </tr>
        <?php
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
