var JSObj = {

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
        if(inArray('A',permissions) || inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('D',permissions)){
            btn_actions.push({type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Name', name:'name', width:250, sortable:false, align:'center', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Group Depot', name:'group_depot', width:250, sortable:false, align:'center', hide:false, editable:{form:'select', validate:'', defval:'',options:my_depots}}
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
                edit:inArray('E',permissions)
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
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            JSObj.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            if (FlexObject.rowSelectedCheck(grid,1)) {
                var row = FlexObject.getSelectedRows(grid);
                JSObj.objGrid.flexBeginEdit(row[0]);
            }
        }
        else if (com == 'Save') {
            JSObj.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            JSObj.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(JSObj.objGrid,grid)) {
                JSObj.delete_(grid);
            }
        }
    },

    delete_:function (grid) {
        var self = this;
        var url = $('#delete_url').val();
        jLib.do_delete(url, grid);
    }
};

/* when the page is loaded */
$(document).ready(function () {
    JSObj.init();
});