var OmcTrucks2 = {

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

        self.objGrid = $('#flex2').flexigrid({
            url:$('#table2-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:true, align:'left', hide:true},
                {display:'Additive Name', name:'additive_setup_id', width:300, sortable:true, align:'left', hide:false,  editable:{form:'select', validate:'', defval:'', bclass:'product_type_id-class', options:additives}},
                {display:'Drum Size (Ltrs)', name:'drum_size', width:130, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Cost per drum', name:'drum_cost', width:130, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:'', on_key_up:'{"action":"division", "sources":["drum_cost","drum_size"], "targets":["cost_per_ltr"]}'}},
                {display:'Cost per Ltr', name:'cost_per_ltr', width:130, sortable:true, align:'left', hide:false, editable:{form:'text',readonly:'readonly', validate:'empty', defval:''}},
                {display:'Total no. of Drum', name:'total_no_dum_inv', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:'', on_key_up:'{"action":"multiply", "sources":["total_no_dum_inv","drum_size"], "targets":["total_no_ltr"]}'}},
                {display:'Total no. of Ltrs', name:'total_no_ltr', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', readonly:'readonly', validate:'empty', defval:'', on_focus:'{"action":"multiply", "sources":["total_no_dum_inv","drum_cost"], "targets":["total_stock_cost"]}'}},
                {display:'Total Stock Cost', name:'total_stock_cost', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:'', on_focus:'{"action":"multiply", "sources":["total_no_dum_inv","drum_cost"], "targets":["total_stock_cost"]}'}}
            ],
            formFields:btn_actions,
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table2-editable-url').val(),
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
            OmcTrucks2.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            //var row = jLib.getSelectedRows(grid);
            var row = FlexObject.getSelectedRows(grid);
            OmcTrucks2.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            OmcTrucks2.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            OmcTrucks2.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(OmcTrucks2.objGrid,grid,1)) {
                OmcTrucks2.delete_(grid);
            }
        }
        else if (com == 'Export All') {
            var url = $("#export2_url").val();
            window.open(url, "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        }

    },

    delete_:function (grid) {
        var self = this;
        var url = $('#grid2_delete_url').val();
        jLib.do_delete(url, grid);
    }
};

/* when the page is loaded */
$(document).ready(function () {
    OmcTrucks2.init();
});