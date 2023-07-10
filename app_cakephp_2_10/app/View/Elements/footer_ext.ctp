<div id="select_more_add_modal" class="modal hide fade" style="display: none">
    <form class="form form-horizontal" id="select_more_form" method="post" action="" enctype="" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h5>Add Custom Volume</h5>
        </div>
        <div class="modal-body">

            <div class="control-group">
                <label class="control-label" for="password"> New Volume:</label>
                <div class="controls">
                    <input id="vol" name="vol" type="text" value="" required="required" class="input input-large numbersOnly" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="add_more_btn" class="btn btn-primary">Add New Volume</button>
            <a href="#select_more_add_modal" id="close_more_btn" class="btn" data-toggle="modal">Close</a>
        </div>
    </form>
</div>

<div id="export_modal" class="modal hide fade" style="width: 300px; margin:-250px 0 0 -150px ; display: none">
    <form action="" id="form-export" target="ExportWindow" method="post" accept-charset="utf-8">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h5>Export Data</h5>
        </div>
        <div class="modal-body">

            <div class="control-group">
                <label class="control-label"  style="float: left; width: 140px;">Order Start Date:</label>
                <div class="controls">
                    <input name="export_startdt" id="export_startdt" class="datepicker span3 date-masking validate[required] hasDatepicker" placeholder="dd-mm-yyyy" type="text" value="<?php echo date('d-m-Y');?>">
                   <!-- <span>(dd-mm-yyyy)</span>-->
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" style="float: left; width: 140px;">Order End Date:</label>
                <div class="controls">
                    <input name="export_enddt" id="export_enddt" class="datepicker span3 date-masking validate[required] hasDatepicker" placeholder="dd-mm-yyyy" type="text" value="<?php echo date('d-m-Y');?>">
                    <!--<span>(dd-mm-yyyy)</span>-->
                </div>
            </div>
            <div class="modal-filter-elements"></div>
        </div>
        <div class="modal-footer">
            <button type="button" id="export-window-btn" class="btn btn-primary">Export Now</button>
            <a href="#export_modal" id="close_export_btn" class="btn" data-toggle="modal">Close</a>
        </div>
    </form>
    <div class="export-window-form" style="display: none">
        <form action="" id="export-window-form" target="ExportWindow" method="post" accept-charset="utf-8">
        </form>
    </div>
</div>
<?php   echo $this->Html->script('custom_export.js'); ?>