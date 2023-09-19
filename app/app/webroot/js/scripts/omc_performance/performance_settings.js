var OmcTrucks = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        var user_group = jLib.user_group;
        var columns = Array();

        columns.push({display:'ID', name:'id', width:20, sortable:true, align:'left', hide:true}),
        columns.push({display:'Station Name', name:'omc_customer_id', width:200, sortable:true, align:'left', hide:false,  editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:customerLists}}),
        columns.push({display:'Product Type', name:'product_type_id', width:200, sortable:true, align:'left', hide:false,  editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:products}}),
        columns.push({display:'Manager Name', name:'manager_name', width:120, sortable:true, align:'left', hide:false,  editable:{form:'text', validate:'empty', defval:''}}),
        columns.push({display:'Daily Target', name:'daily_target', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}}),
        columns.push({display:'Teritory', name:'teritory', width:200, sortable:true, align:'left', hide:false,  editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:teritory_name}})
       


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
        if(inArray('PX',permissions)){
            btn_actions.push({type:'select',name: 'Filter Customer', id: 'filter_customer',bclass: 'filter',onchange:self.handleGridEvent,options:customerLists});
            btn_actions.push({separator:true});
            btn_actions.push({type:'select',name: 'Product Type', id: 'filter_product',bclass: 'filter',onchange:self.handleGridEvent,options:products});
            btn_actions.push({separator:true});
        }

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:columns,
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
        else if (com == 'Filter Customer' || com == 'Product Type') {
            OmcTrucks.filterGrid(json);
        }

    },

    filterGrid:function(json){
        var customer_filter = $("#filter_customer").val();
        var filter_product = $("#filter_product").val();
        $(OmcTrucks.objGrid).flexOptions({
            params: [
                {name: 'filter_customer', value: customer_filter},
                {name: 'filter_product', value: filter_product}
            ]
        }).flexReload();
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