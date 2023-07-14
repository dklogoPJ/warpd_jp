var Order = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        var btn_actions = [];
        if(inArray('A',permissions)){
            btn_actions.push({type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('A',permissions) || inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Attachment', bclass:'attach', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
       // btn_actions.push({type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:order_filter});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'Id', name:'id', width:70, sortable:false, align:'left', hide:false},
                {display:'Order Date', name:'order_date', width:100, sortable:false, align:'left', hide:false},
                {display:'Product Type', name:'product_type_id', width:100, sortable:true, align:'left', hide:false},
                {display:'Station Name', name:'station_name', width:140, sortable:false, align:'left', hide:false},
                {display:'Order Quantity', name:'order_quantity', width:100, sortable:true, align:'left', hide:false},
                {display:'Loaded Quantity', name:'loaded_quantity', width:120, sortable:true, align:'left', hide:false},
                {display:'Truck Arrival Date', name:'truck_arrival_date', width:120, sortable:false},
                {display:'Transporter', name:'transporter', width:100, sortable:false, align:'left', hide:false},
                {display:'Tank No', name:'tank_no', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Product Density Depot', name:'product_density_depot', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Product Temp-Depot', name:'product_temp_depot', width:130, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Product Density Station', name:'product_density_station', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Product Temp-Station', name:'product_temp_station', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Dipping Pre-Discharge (ltr)', name:'dipping_pre_discharge', width:170, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
               // {display:'TM Approval', name:'tm_approval', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'empty,numeric',bclass:'quantity-class',defval:'',options:git_status}},
                {display: 'TM Approval', name: 'tm_approval', width: 140, sortable: true, align: 'left', hide: false, editable:{form:'select', validate:'', defval:'', options:list_tm}},
                {display:'TM Comments', name:'tm_comments', width:170, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}}
            ],
            formFields:btn_actions,
            searchitems:[
                {display:'Order Id', name:'id', isdefault:true}
            ],
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions),
                confirmSave:false
               // confirmSaveText:"If this order gets processed by te OMC, you can't change it afterwords. \n Are you sure the information you entered is correct ?"
            },
            columnControl:true,
            sortname:"id",
            sortorder:"desc",
            usepager:true,
            useRp:true,
            rp:15,
            showTableToggleBtn:false,
            height:370,
            subgrid:{
                use:false
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
            }
        });

        $('input.datepicker').live('focus', function(){
            if (false == $(this).hasClass('hasDatepicker')) {
                $(this).datepicker({
                    inline: true,
                    changeMonth: true,
                    changeYear: true
                });
                $(this).datepicker( "option", "dateFormat", 'dd-mm-yy' );
            }
        });

        $("#form-export").validationEngine();
        $("#export-btn").click(function () {
            var validationStatus = $('#form-export').validationEngine({returnIsValid:true});
            if (validationStatus) {
                $("#form-export").attr('action', $("#export_url").val());
                window.open('', "ExportWindow", "menubar=yes, width=300, height=200,location=no,status=no,scrollbars=yes,resizable=yes");
                $("#form-export").submit();
            }
        });

        $('.quantity-class').live('focus', function(){
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            Order.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            Order.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            Order.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            Order.objGrid.flexCancel();
        }
        else if (com == 'Attachment') {
            if (FlexObject.rowSelectedCheck(Order.objGrid,grid,1)) {
                Order.attach_file(grid);
            }
        }
        else if (com == 'Filter BDC' || com == 'Order Status') {
            Order.filterGrid(json);
        }
    },

    filterGrid:function(json){
        //var bdc_filter = $("#filter_bdc").val();
        var filter_status = $("#filter_status").val();
        $(Order.objGrid).flexOptions({
            params: [
                //{name: 'filter', value: bdc_filter},
                {name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    },

    attach_file:function(grid){
        var row_ids = FlexObject.getSelectedRowIds(grid);
        var item_id = row_ids[0];
        document.getElementById('fileupload').reset();
        var attachment_type = 'Customer Order';
        var log_activity_type = 'Order';
        $("#fileupload #type_id").val(item_id);
        $("#fileupload #type").val(attachment_type);//
        $("#fileupload #log_activity_type").val(log_activity_type);
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#get_attachments_url').val()+'/'+item_id+'/'+attachment_type,
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                $('#ajax_upload_table tbody').html('');
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});

                $('#attachment_modal').modal({
                    backdrop: 'static',
                    show: true,
                    keyboard: true
                });
            });

    }
};

/* when the page is loaded */
$(document).ready(function () {
    Order.init();
});