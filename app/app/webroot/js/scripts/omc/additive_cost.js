var OmcTrucks = {

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
        if(inArray('D',permissions)){
            btn_actions.push({type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('A',permissions) || inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        /*if(inArray('PX',permissions)){
            btn_actions.push({type:'buttom', name:'Export All', bclass:'export', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }*/

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                /**{display:'ID', name:'id', width:20, sortable:true, align:'left', hide:true},
                {display:'Additive Name', name:'additive_setup_id', width:300, sortable:true, align:'left', hide:false,  editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:additives}},
                {display:'Doping Name', name:'drum_name', width:100, sortable:true, align:'left', hide:false,  editable:{form:'text', validate:'empty', defval:''}},
                {display:'No. of Ltrs', name:'ltr', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:'', on_key_up:'{"action":"division", "sources":["ltr","product_qty"], "targets":["doping_ratio"]}'}},
                {display:'Product Qty - Ltrs', name:'product_qty', width:130, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:'', on_key_up:'{"action":"division", "sources":["ltr","product_qty"], "targets":["doping_ratio"]}'}},
                {display:'Doping Ratio', name:'doping_ratio', width:180, sortable:true, align:'left', format_number: false, hide:false, editable:{form:'text',readonly:'readonly', validate:'empty', defval:''}}*/

                {display:'ID', name:'id', width:20, sortable:true, align:'left', hide:true},
                {display:'Order Id', name:'order_id', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Order Date', name:'order_date', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}},
                {display:'Customer Name', name:'omc_customer_id', width:180, sortable:true, align:'left', hide:false,  editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:customerLists}},
                {display:'Loading Depot', name:'depot_id', width:170, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:depotLists}},
                {display:'Product Type', name:'product_type_id', width:180, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:products}}, 
                {display:'Truck No.', name:'truck_no', width:120, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:numbers}}, 
                {display:'Loading Quantity', name:'loading_quantity', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}}, 
                {display:'Loading Date', name:'loading_date', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}},
                {display:'Additive Used', name:'additive_setup_id', width:300, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:additives}},
                {display:'Doping Ratio', name:'doping_ratio', width:120, sortable:true, align:'left',format_number: false, hide:false, editable:{form:'text', validate:'empty', defval:'',on_key_up:'{"action":"multiply", "sources":["loading_quantity","doping_ratio"], "targets":["additive_quantity"]}'}},
                {display:'Additive Quantity (Ltrs)', name:'additive_quantity', width:150, sortable:true, align:'left', hide:false, editable:{form:'text',readonly:'readonly', validate:'empty', defval:''}}, 
                {display:'Additive Cost Per Ltr', name:'additive_cost_ltr', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:'', on_key_up:'{"action":"multiply", "sources":["additive_cost_ltr","additive_quantity"], "targets":["invoice_additive_cost"]}'}},
                {display:'Invoice Additive Cost', name:'invoice_additive_cost', width:150, sortable:true, align:'left', hide:false, editable:{form:'text',readonly:'readonly', validate:'empty', defval:''}}
            ],
            formFields:btn_actions,
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions),
                confirmSave:true,
                confirmSaveText:"Are you sure the information you entered is correct ?"
            },
            columnControl:false,
            sortname:"id",
            sortorder:"asc",
            usepager:true,
            useRp:true,
            rp:15,
            showTableToggleBtn:false,
            height:250,
            subgrid:{
                use:false
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
            }
        });

    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            OmcTrucks.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            //var row = jLib.getSelectedRows(grid);
            var row = FlexObject.getSelectedRows(grid);
            OmcTrucks.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            OmcTrucks.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            OmcTrucks.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(OmcTrucks.objGrid,grid,1)) {
                OmcTrucks.delete_(grid);
            }
        }
        else if (com == 'Export All') {
            var url = $("#export_url").val();
            window.open(url, "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        }

    },

    delete_:function (grid) {
        var self = this;
        var url = $('#grid_delete_url').val();
        jLib.do_delete(url, grid);
    }
};

/* when the page is loaded */
$(document).ready(function () {
    OmcTrucks.init();
});