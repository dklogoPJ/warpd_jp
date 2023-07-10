var WayBill = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        var columns = Array();
        columns.push({display:'Waybill Id', name:'id', width:60, sortable:false, align:'left', hide:false});
        columns.push({display:'Waybill Date', name:'created', width:80, sortable:false, align:'center', hide:false});
        columns.push({display:'Order Id', name:'order_id', width:80, sortable:false, align:'center', hide:false});
        columns.push({display:'Order Date', name:'created', width:80, sortable:false, align:'center', hide:false});
        columns.push({display:'Product Type', name:'product_type_id', width:160, sortable:true, align:'center', hide:false});
        columns.push({display:'Quantity', name:'order_quantity', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Truck No.', name:'truck_no', width:80, sortable:true, align:'center', hide:false});
        columns.push({display:'Approve', name:'approve', width:130, sortable:false, align:'center', hide:false});
        columns.push({display:'Depot Approval', name:'depot_id', width:230, sortable:false, align:'center', hide:false});
        columns.push({display:'CEPS Approval', name:'cep_id', width:230, sortable:true, align:'center', hide:false});

        var btn_actions = [];
        if(inArray('E',permissions)){
            //btn_actions.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
           // btn_actions.push({separator:true});
        }
        if(inArray('E',permissions)){
           // btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
           // btn_actions.push({separator:true});
           // btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
          //  btn_actions.push({separator:true});
        }
        if(inArray('PX',permissions)){
             btn_actions.push({type:'buttom', name:'Print', bclass:'print', onpress:self.handleGridEvent});
              btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Attachment', bclass:'attach', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }

        btn_actions.push({type:'select',name: 'Filter Depot', id: 'filter_depot' ,bclass: 'filter',onchange:self.handleGridEvent,options:depot_filter});
        btn_actions.push({separator:true});
        btn_actions.push({type:'select',name: 'Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:waybill_filter});
        btn_actions.push({separator:true});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:columns,
            formFields:btn_actions,
            searchitems:[
                {display:'Waybill Id', name:'id', isdefault:true},
                {display:'Order Id', name:'order_id'}
             ],
            checkboxSelection:true,
            editablegrid:{
                use:false
               /* url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions),
                confirmSave:true,
                confirmSaveText:"If truck is loaded, you cannot change it again. \n Are you sure the information you entered is correct ?"*/
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
            }
        });


        $('button.approve-waybill').live('click', function(){
            var waybill_id = $(this).attr('data-id');
            var order_id = $(this).attr('data-order-id');
            var div = $(this).parent();
            self.approveWaybill(waybill_id,order_id,div);
        });
    },

   handleGridEvent:function (com, grid, json) {
        if (com == 'Print') {
            if (FlexObject.rowSelectedCheck(WayBill.objGrid,grid,1)) {
                var row_ids = FlexObject.getSelectedRowIds(grid);
                WayBill.printWaybill(row_ids[0]);
            }
        }
        else if (com == 'Attachment') {
            if (FlexObject.rowSelectedCheck(WayBill.objGrid,grid,1)) {
                WayBill.attach_file(grid);
            }
        }
        else if (com == 'Filter Depot' || com == 'Status') {
            WayBill.filterGrid(json);
        }
    },

    filterGrid:function(json){
        var filter = $("#filter_depot").val();
        var filter_status = $("#filter_status").val();
        $(WayBill.objGrid).flexOptions({
            params: [
                {name: 'filter', value: filter},
                {name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    },


    printWaybill:function(id){
        var url = $("#print_waybill_url").val()+'/'+id;
        window.open(url, "PrintWindow", "menubar=yes, width=700, height=400,location=no,status=no,scrollbars=yes,resizable=yes");
    },

    approveWaybill:function(id,order_id,div){
        var self = this;
        var url = $("#table-editable-url").val();
        var query = "id="+id+"&order_id="+order_id;
        jConfirm('Are you sure you want to continue ?', 'Confirm', function(confirmation) {
            if(confirmation){
                $.ajax({
                    type: 'post',
                    url: url,
                    data: query,
                    dataType: 'json',
                    success: function (response) {
                        var txt = '';
                        if (typeof response.msg == 'object') {
                            var messages = response.msg;
                            var len = messages.length;
                            for (var x = 0; x < len; x++) {
                                txt += messages[x] + '<br />';
                            }
                        }
                        else {
                            txt = response.msg
                        }
                        if (response.code == 0) {
                            //div.html('Approved');
                            self.objGrid.flexReload();
                            //jLib.message('Way Bill Approval',txt, 'success');
                        }
                        else {
                            jLib.serverError(txt);
                        }
                    },
                    error: function () {
                        jLib.serverError();
                    }
                });
            }
        });

    },


    attach_file:function(grid){
        var row_ids = FlexObject.getSelectedRowIds(grid);
        var item_id = row_ids[0];
        document.getElementById('fileupload').reset();
        var attachment_type = 'Waybill';
        var log_activity_type = 'Way Bill';
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
    WayBill.init();
});