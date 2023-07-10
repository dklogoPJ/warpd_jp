var BdcOrder = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        var columns = Array();
        columns.push({display:'Order Id', name:'id', width:50, sortable:false, align:'left', hide:false});
        columns.push({display:'Order Date', name:'order_date', width:80, sortable:false, align:'center', hide:false});
        columns.push({display:'Time Elapsed', name:'time_elapsed', width:85, sortable:false, align:'left', hide:false});
        columns.push({display:'Customer', name:'omc_customer_id', width:150, sortable:false, align:'center', hide:false});
        columns.push({display:'OMC', name:'omc_id', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Loading Depot', name:'depot_id', width:120, sortable:true, align:'center', hide:false});
        columns.push({display:'Product Type', name:'product_type_id', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Order Quantity', name:'order_quantity', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Delivery Priority', name:'delivery_priority', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Approve', name:'bdc_feedback', width:100, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'bdc_feedback-class', options:ops_feedback}});
        columns.push({display:'Finance Approval', name:'finance_approval', width:110, sortable:true, align:'center', hide:false});
        columns.push({display:'Approved Quantity', name:'approved_quantity', width:120, sortable:true, align:'center', hide:false});
        columns.push({display:'CEPS Approval', name:'ceps_approval', width:100, sortable:true, align:'center', hide:false});

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

        btn_actions.push({type:'select',name: 'Filter OMC', id: 'filter_omc' ,bclass: 'filter',onchange:self.handleGridEvent,options:omc_lists});
        btn_actions.push({separator:true});
        btn_actions.push({type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:order_filter});
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
                confirmSaveText:"If CEPS approves this order, you cannot change it again. \n Are you sure the information you entered is correct ?"

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

        $(".bdc_feedback-class").live('change',function () {
            var sel = $(this);
            var value = sel.val();
            var parent_tr = sel.parent().parent().parent();
            var parent_td = sel.parent().parent();
            var order_quantity = parent_td.prev().prev().find('div').html();
            var fna_feed_td = parent_td.next();
            var quantity_td = parent_td.next().next();
            var ex_dt = parent_tr.attr('extra-data');
            var ex_dt_arr_str = ex_dt.split(',');
            var ex_dt_arr = {};
            for(var k in ex_dt_arr_str){
                var key_value = ex_dt_arr_str[k].split('=>');
                ex_dt_arr[key_value[0]]= key_value[1];
            }
            var fin = ex_dt_arr['fna_feedback'];
            //console.log(ex_dt_arr);
            if(fin == 'Approved' || fin == 'Ok'){

            }
            else if(fin == 'Not Approved'){ //Can't approve while Finance says No
                sel.val(ex_dt_arr['ops_feedback']);
                return false;
            }
           /* else{ //N/A for finance
                fna_feed_td.find('div').html('Ok');
                quantity_td.find('div').html(order_quantity);
            }*/
            else{
                //fna_feed_td.find('div').html(ex_dt_arr['fna_feedback']);
                fna_feed_td.find('div').html('Ok');
                quantity_td.find('div').html(jLib.formatNumber(ex_dt_arr['order_quantity'],'money',0));
               /* if(value == 'Finance Required' || 'Not Approved'){
                    quantity_td.find('div').html('');
                }
                else{

                }*/
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            BdcOrder.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            BdcOrder.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            BdcOrder.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            BdcOrder.objGrid.flexCancel();
        }
        else if (com == 'Attachment') {
            if (FlexObject.rowSelectedCheck(BdcOrder.objGrid,grid,1)) {
                BdcOrder.attach_file(grid);
            }
        }
        else if (com == 'Filter OMC' || com == 'Order Status') {
            BdcOrder.filterGrid(json);
        }
    },

    filterGrid:function(json){
        var omc_filter = $("#filter_omc").val();
        var filter_status = $("#filter_status").val();
        $(BdcOrder.objGrid).flexOptions({
            params: [
                {name: 'filter', value: omc_filter},
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
    BdcOrder.init();
});