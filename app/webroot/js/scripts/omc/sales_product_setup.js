var Obj = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,

    init:function () {
        var self = this;

        var btn_actions_sub = [];
        if(inArray('A',permissions)){
            btn_actions_sub.push({type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent});
            btn_actions_sub.push({separator:true});
        }
        if(inArray('E',permissions)){
            btn_actions_sub.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
            btn_actions_sub.push({separator:true});
        }

        if(inArray('A',permissions) || inArray('E',permissions)){
            btn_actions_sub.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions_sub.push({separator:true});
            btn_actions_sub.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions_sub.push({separator:true});
        }
        if(inArray('D',permissions)){
            btn_actions_sub.push({type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent});
            btn_actions_sub.push({separator:true});
        }

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Product / Service', name:'name', width:300, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'Super', defval:''}}
            ],
            formFields:btn_actions_sub,
            searchitems:[
             {display:'Product / Service', name:'name', isdefault:true}
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
            Obj.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            Obj.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            Obj.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            Obj.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(Obj.objGrid,grid)) {
                Obj.delete_(grid);
            }
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
    Obj.init();
});