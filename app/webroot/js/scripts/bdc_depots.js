var Depots = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Depot Name', name:'name', width:300, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Short Name', name:'short_name', width:150, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}}
            ],
            formFields:[
                {type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent},
                {separator:true}
            ],
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val()
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
            Depots.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            Depots.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            Depots.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            Depots.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(Depots.objGrid,grid)) {
                Depots.delete_(grid);
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
    Depots.init();
});