<?php

    $table = $this->TableForm->render_preview('prev_table_form',$from_data['fields']);
    echo "<p><strong><u>".$from_data['form']['name']." Preview</u></strong></p> <br />";
    echo "<div style='width: auto; padding: 10px;'>";
    echo $table;
    echo "</div>";
?>