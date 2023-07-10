<?php

?>
<div class="block-fluid form_tabs">
    <ul id="sales-form-tabs">
        <?php
        foreach($forms_n_fields as $f){
            $form_id = $f['id'];
            $form_name = $f['name'];
            $tab_ref_id = "#tabs-".$form_id;
            ?>
            <li><a href="<?php echo $tab_ref_id; ?>" data-form_id="<?php echo $form_id; ?>"  data-form_table_id="#form-table-<?php echo $form_id; ?>"><strong><?php echo $form_name; ?></strong></a></li>
        <?php
        }
        ?>
    </ul>
    <?php
    $form_field_rendered = array();
    foreach($forms_n_fields as $f){
        $form_id = $f['id'];
        $tab_id = "tabs-".$form_id;
        $table_id = "form-table-".$form_id;
        ?>
        <div id="<?php echo $tab_id; ?>">
            <div style="padding: 40px 10px 0px;">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="block-fluid">
                            <div style="height: 415px; overflow-x: auto; overflow-y: auto;">
                                <?php
                                $table_n_fields = $this->TableForm->render($table_id,$f['fields'],$f['values']);
                                $form_field_rendered[$form_id] = $table_n_fields['fields'];
                                echo $table_n_fields['table'];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>