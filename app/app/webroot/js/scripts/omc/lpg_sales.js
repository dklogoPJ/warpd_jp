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
                {display:'ID', name:'id', width:20, sortable:true, align:'left', hide:true},
                {display:'LPG Types', name:'name', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Unit Volume (kg)', name:'unit_volume', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Unit Price (GHs)', name:'unit_price', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:'', on_key_up:'{"action":"division", "sources":["unit_price","unit_volume"], "targets":["price_per_kg"]}'}},
                {display:'Price Per KG (GHs)', name:'price_per_kg', width:120, sortable:true, align:'left', format_number: false, hide:false, editable:{form:'text', validate:'empty', defval:'',readonly:'readonly'}}
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
            height:400,
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
            //test
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