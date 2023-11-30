<div class="block-fluid form_tabs">
    <div class="row-fluid">
        <?php
        if($sales_records) {
            ?>
            <div class="span12">
                <div style="height: 550px; overflow-x: auto; overflow-y: auto;">
                    <?php
                    echo $this->TableForm->renderDailySalesReport($sales_records);
                    ?>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="span12">
                <div class="row-fluid">
                    <div class="span12" style="text-align: center; margin-bottom: 20px;">
                        <h5><?php echo "No records found!." ?> </h5>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>