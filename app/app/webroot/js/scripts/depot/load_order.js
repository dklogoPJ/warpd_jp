var ApproveOrder = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        var columns = Array();
        columns.push({display:'Order Id', name:'id', width:50, sortable:false, align:'left', hide:false});
        columns.push({display:'Order Date', name:'order_date', width:80, sortable:false, align:'center', hide:false});
        columns.push({display:'BDC', name:'bdc_id', width:130, sortable:false, align:'center', hide:false});
        columns.push({display:'OMC', name:'omc_id', width:130, sortable:true, align:'center', hide:false});
        columns.push({display:'CEPS', name:'cep_id', width:140, sortable:true, align:'center', hide:false});
        columns.push({display:'Product Type', name:'product_type_id', width:110, sortable:true, align:'center', hide:false});
        columns.push({display:'Quantity', name:'order_quantity', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Truck No.', name:'truck_no', width:80, sortable:true, align:'center', hide:false});
        columns.push({display:'Status', name:'depot_loadding_approval', width:90, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'empty',bclass:'approval-class', defval:'', options:depot_feedback}});
        columns.push({display:'Quantity Loaded', name:'loaded_quantity', width:110, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'empty',bclass:'quantity-class', defval:'', options:volumes}});
        columns.push({display:'Date Loaded', name:'loaded_date', width:100, sortable:true, align:'center', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}});

        var btn_actions = [];
        if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Attachment', bclass:'attach', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }

        //btn_actions.push({type:'select',name: 'Filter OMC', id: 'filter_omc' ,bclass: 'filter',onchange:self.handleGridEvent,options:omc_lists});
        //btn_actions.push({separator:true});
        btn_actions.push({type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:depot_filter});
        btn_actions.push({separator:true});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:columns,
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
                confirmSave:true,
                confirmSaveText:"If truck is loaded, you cannot change it again. \n Are you sure the information you entered is correct ?"
            },
            columnControl:true,
            sortname:"id",
            sortorder:"desc",
            usepager:true,
            useRp:true,
            rp:15,
            showTableToggleBtn:false,
            height:300,
            subgrid:{
                use:false
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
            }
        });


        $(".approval-class").live('change', function () {
            var value = $(this).val();
            var parent_tr = $(this).parent().parent().parent();
            var row_id = parent_tr.attr('data-id');
            var ex_dt = parent_tr.attr('extra-data');
            var ex_dt_arr_str = ex_dt.split(',');
            var ex_dt_arr = {};
            for(var k in ex_dt_arr_str){
                var key_value = ex_dt_arr_str[k].split('=>');
                ex_dt_arr[key_value[0]]= key_value[1];
            }
            var order_quantity = ex_dt_arr['order_quantity'];
            var freez = false;
            if(value == 'Loaded' ){
                freez = false;
            }
            else{
                freez = true;
            }

            if(freez){
                $('#loaded_quantity_' + row_id).hide();
                $('#loaded_date_' + row_id).hide();
            }
            else{
                $('#loaded_quantity_' + row_id).val(order_quantity).show();
                $('#loaded_date_' + row_id).show();
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

        $('.quantity-class').live('focus', function(){
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            ApproveOrder.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            ApproveOrder.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            ApproveOrder.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            ApproveOrder.objGrid.flexCancel();
        }
        else if (com == 'Attachment') {
            if (FlexObject.rowSelectedCheck(ApproveOrder.objGrid,grid,1)) {
                ApproveOrder.attach_file(grid);
            }
        }
        else if (com == 'Filter OMC' || com == 'Order Status') {
            ApproveOrder.filterGrid(json);
        }
    },

    filterGrid:function(json){
       // var omc_filter = $("#filter_omc").val();
        var filter_status = $("#filter_status").val();
        $(ApproveOrder.objGrid).flexOptions({
            params: [
                //{name: 'filter', value: omc_filter},
                {name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    },


    attach_file:function(grid){
        var row_ids = FlexObject.getSelectedRowIds(grid);
        var item_id = row_ids[0];
        document.getElementById('fileupload').reset();
        var attachment_type = 'Order';
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
    ApproveOrder.init();
});