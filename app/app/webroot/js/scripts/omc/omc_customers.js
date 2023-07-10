var OmcCustomers = {

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
        /*if(inArray('PX',permissions)){
            btn_actions.push({type:'buttom', name:'Export All', bclass:'export', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }*/

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Customer Name', name:'name', width:300, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                /*{display:'Region', name:'region_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'',bclass:'region-class', options:region}},
                {display:'District', name:'district_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:district}},*/
                {display:'Address', name:'address', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Telephone', name:'telephone', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Default Admin', name:'admin_username', width:110, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Admin Password', name:'admin_pass', width:110, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}}
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
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
            }
        });


        $(".region-class").live('change',function () {
            var sel = $(this);
            var value = sel.val();
            var row_id = sel.parent().parent().parent().attr('data-id');
            if(typeof glbl_region_district[value] == "undefined"){
                return;
            }
            var d_options = glbl_region_district[value];
            //console.log(customer_credit_data)
            var select = document.getElementById('district_id_'+row_id);
            select.options.length = 0;
            for(nx in d_options){
                var opt = document.createElement('option');
                opt.value = nx;
                opt.text = d_options[nx];
                try{ //Standard
                    select.add(opt,null) ;
                }
                catch(error){ //IE Only
                    select.add(opt) ;
                }
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            OmcCustomers.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = jLib.getSelectedRows(grid);
            OmcCustomers.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            OmcCustomers.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            OmcCustomers.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (jLib.rowSelectedCheck(OmcCustomers.objGrid,grid)) {
                OmcCustomers.delete_(grid);
            }
        }
        else if (com == 'Export All') {
            var url = $("#export_url").val();
            window.open(url, "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
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
    OmcCustomers.init();
});