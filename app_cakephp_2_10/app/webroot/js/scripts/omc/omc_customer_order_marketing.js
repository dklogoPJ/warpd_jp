var Order = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        var user_group = jLib.user_group;
        var columns = Array();
        columns.push({display:'Order Id', name:'id', width:70, sortable:false, align:'left', hide:false});
        columns.push({display:'Order Date', name:'order_date', width:80, sortable:false, align:'left', hide:false});
        columns.push({display:'Customer', name:'omc_customer_id', width:110, sortable:false, align:'left', hide:false});
        columns.push({display:'Credit Limit', name:'credit_limit', width:80, sortable:false, align:'left', hide:false});
        columns.push({display:'Product Type', name:'product_type_id', width:100, sortable:true, align:'left', hide:false});
        columns.push({display:'Order Quantity', name:'order_quantity', width:100, sortable:true, align:'left', hide:false});
        columns.push({display:'Delivery Priority', name:'delivery_priority', width:100, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'', options:omc_dealer_marketing_feedback}});
        columns.push({display:'Finance Approval', name:'finance_approval', width:120, sortable:true, align:'left', hide:false});
        columns.push({display:'Intended Delivery Location', name:'intended_delivery_location', width:170, sortable:true, align:'left', hide:false});

        var btn_actions = [];
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
        btn_actions.push({type:'select',name: 'Filter Customer', id: 'filter_customer',bclass: 'filter',onchange:self.handleGridEvent,options:customers});
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
                edit:inArray('E',permissions)
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

        $("#form-export").validationEngine();
        $("#export-btn").click(function () {
            var validationStatus = $('#form-export').validationEngine({returnIsValid:true});
            if (validationStatus) {
                $("#form-export").attr('action', $("#export_url").val());
                window.open('', "ExportWindow", "menubar=yes, width=300, height=200,location=no,status=no,scrollbars=yes,resizable=yes");
                $("#form-export").submit();
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            Order.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = jLib.getSelectedRows(grid);
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
        else if (com == 'Filter Customer' || com == 'Order Status') {
            Order.filterGrid(json);
        }
    },

    filterGrid:function(json){
        var customer_filter = $("#filter_customer").val();
        var filter_status = $("#filter_status").val();
        $(Order.objGrid).flexOptions({
            params: [
                {name: 'filter_customer', value: customer_filter},
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