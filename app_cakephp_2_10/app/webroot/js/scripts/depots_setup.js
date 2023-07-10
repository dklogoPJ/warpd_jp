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
       // btn_actions.push({type:'select',name: 'Filter Rate Category', id: 'filter_category' ,bclass: 'filter',onchange:self.handleGridEvent,options:filter_rate_cats});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:false,
            reload_after_edit:false,
            dataType:'json',
            colModel:[
                {display:'Id', name:'id', width:50, sortable:true, align:'left', hide:true},
                {display:'Name', name:'name', width:250, sortable:true, align:'center', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Short Name', name:'short_name', width:200, sortable:true, align:'center', hide:false, editable:{form:'text', validate:'empty', defval:''}}
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
            columnControl:true,
            sortname:"id",
            sortorder:"asc",
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

        $("#import-btn").click(function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            window.open(url, "Import", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            JSObj.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = jLib.getSelectedRows(grid);
            JSObj.objGrid.flexBeginEdit(row[0]);
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
        else if (com == 'Filter Rate Category') {
            JSObj.filterGrid(json);
        }
        else if (com == 'Export') {
            var filter_category = $("#filter_category").val();
            var url = $("#table-export-url").val()+"/"+filter_category;
            window.open(url, "ExportWindow", "menubar=yes, width=400, height=300,location=no,status=no,scrollbars=yes,resizable=yes");
        }

    },

    delete_:function (grid) {
        var self = this;
        var url = $('#grid_delete_url').val();
        jLib.do_delete(url, grid);
    },

    filterGrid:function(json){
        var filter_category = $("#filter_category").val();
        $(JSObj.objGrid).flexOptions({
            params: [
                {name: 'filter_category', value: filter_category}
            ]
        }).flexReload();
    }
};

/* when the page is loaded */
$(document).ready(function () {
    JSObj.init();
});