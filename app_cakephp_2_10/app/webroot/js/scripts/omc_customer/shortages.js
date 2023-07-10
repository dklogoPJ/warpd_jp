var Order = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        var btn_actions = [];
       /* if(inArray('A',permissions)){
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
        }*/
       // btn_actions.push({type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:order_filter});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'Order Id', name:'id', width:60, sortable:false, align:'left', hide:false},
                {display:'Order Date', name:'order_date', width:80, sortable:false, align:'left', hide:false},
				{display:'Product Type', name:'product_type_id', width:100, sortable:true, align:'left', hide:false},
                {display:'Order Quantity', name:'order_quantity', width:100, sortable:true, align:'left', hide:false},
                {display:'Delivery Quantity', name:'delivery_quantity', width:120, sortable:true, align:'left', hide:false},
                {display:'Received Quantity', name:'received_quantity', width:130, sortable:true, align:'left', hide:false},
                {display:'Quantity Short', name:'shortage_quantity', width:110, sortable:true, align:'left', hide:false},
                {display:'Shortage Cost', name:'shortage_cost', width:110, sortable:true, align:'left', hide:false},
                {display:'Delivery Truck', name:'truck_number', width:110, sortable:true, align:'left', hide:false},
                {display:'Driver', name:'driver', width:100, sortable:true, align:'left', hide:false},
                {display:'Comments', name:'comments', width:170, sortable:true, align:'left', hide:false}
                //{display:'Delivery Quantity', name:'delivery_quantity', width:130, sortable:true, align:'left', hide:false}

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
            height:300,
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
    }
};

/* when the page is loaded */
$(document).ready(function () {
    Order.init();
});