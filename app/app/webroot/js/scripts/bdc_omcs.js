var BdcOmcs = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Omc Name', name:'omc_id', width:300, sortable:false, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:omclist_arr}}
            ],
            formFields:[
                {type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent},
                /*{separator:true},
                {type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent},*/
                {separator:true},
                {type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent},
                {separator:true}/*,
                 {type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent},
                 {separator:true}*/
            ],
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                callback:function (server_response) {
                    if (server_response.code == 0) {
                        if (server_response.data) {
                            var name = server_response.data.name;
                            var username = server_response.data.username;
                            var pass = server_response.data.default_pass;
                            jLib.message('Status', name + " successfully added. The default Admin credentials are: username is '" + username + "' and default password is '" + pass + "' ", 'success');
                        }
                        else{//Editing
                            jLib.message('Data Status', server_response.msg, 'success');
                        }
                    }
                    else {
                        jLib.message('Data Status', server_response.msg, 'error');
                    }
                }
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
            BdcOmcs.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = jLib.getSelectedRows(grid);
            BdcOmcs.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            BdcOmcs.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            BdcOmcs.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (jLib.rowSelectedCheck(BdcOmcs.objGrid,grid)) {
                BdcOmcs.delete_(grid);
            }
        }
    },

    delete_:function (grid) {
        var self = this;
        var url = $('#delete_url').val();
        jLib.do_delete(url, grid);
    }


    /**
     * Sets up UI elements for the Credit Account Modal
     */
    /* configModals: function() {
     var self = this;
     $('#creat_omc_modal').modal({
     backdrop: 'static',
     show: false,
     keyboard: true
     });
     },

     save: function(url){
     var self = this;
     var query = $('#form-bdc-omc').serialize();
     $.ajax({
     url:url,
     data:query,
     dataType:'json',
     type:'POST',
     success:function (response) {
     var txt = '';
     if (typeof response.msg == 'object') {
     for (var megTxt in response.msg) {
     txt += response.msg[megTxt] + '<br />';
     }
     }
     else {
     txt = response.msg
     }
     /*//* When everything went on smoothly on the server redirect the user to the appropriate page.*//**//*
     if (response.success === 0) {
     var name = response.data.name;
     var username = response.data.username;
     var pass = response.data.default_pass;
     jLib.message('Status',name+" successfully added. The default username is '"+username+"' and default password is '"+pass+"' ",'success');
     //jLib.message('Status',txt,'success');
     document.getElementById('form-bdc-omc').reset();
     $('#creat_omc_modal').modal('hide');
     $('#dg').edatagrid('reload');

     }
     /*//* When there are Errors *//**//*
     else if (response.success === 1) {
     jLib.message('Status',txt,'error');
     }
     },
     error:function (xhr) {
     console.log(xhr.responseText);
     jLib.message('Error','Please contact an administrator.','error');
     }
     });
     //
     }*/
};

/* when the page is loaded */
$(document).ready(function () {
    BdcOmcs.init();
});