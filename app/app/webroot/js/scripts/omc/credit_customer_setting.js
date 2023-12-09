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
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Customer Name', name:'name', width:150, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Business Type', name:'business_type', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Station Name', name:'omc_customer_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:station_list}},
                {display:'Territory', name:'territory', width:110, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Credit Limit', name:'credit_limit', width:110, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Credit Days', name:'credit_days', width:110, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Agreement Sign', name:'agreement_sign', width:100, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:yes_no}},
                {display:'Risk Rating', name:'risk_rating', width:120, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:risk_rate}}
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