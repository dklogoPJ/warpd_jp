var MailAddressBook = {
    /* Server script url   */
    selected_row_id:null,

    /* dataTable Object */
    objGrid:null,

    /* Function init
     * @param void
     * @return void
     * @access public
     * */
    init:function () {
        var self = this;
        /*Sortable Table */
        var oTable = null;
        var dataUrl = $('#table-url').val();
        self.objGrid = $('#flex').flexigrid({
            url:dataUrl,
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Contact ID', name:'contact_username', width:240, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Contact Name', name:'contact_name', width:240, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                {display:'Date Created', name:'created', width:200, sortable:false, align:'left', hide:false}
            ],
            formFields:[
                {type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent}
            ],
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:true,
                edit:true,
                confirmSave:false
            },
            searchitems:[
                {display:'Contact ID', name:'contact_username', isdefault:true},
                {display:'Contact Name', name:'contact_name', isdefault:true}
            ],
            checkboxSelection:true,
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

        $('#preview_mail').on('hidden', function () {
            $("#mail_from").html(' ');
            $("#mail_to").html(' ');
            $("#mail_attach").html(' ');
            $("#mail_body").html(' ');
        });
    },

    handleGridEvent:function (com, grid) {
        if (com == 'New') {
            MailAddressBook.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            if (FlexObject.rowSelectedCheck(MailAddressBook.objGrid,grid,1)) {
                var row = FlexObject.getSelectedRows(grid);
                MailAddressBook.objGrid.flexBeginEdit(row[0]);
            }
        }
        else if (com == 'Save') {
            MailAddressBook.objGrid.flexSaveChanges();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(MailAddressBook.objGrid,grid)) {
                MailAddressBook.delete_(grid);
            }
        }
    },


    delete_:function (grid) {

        jConfirm('Are you sure you want to continue ?', 'Confirm', function (confirmation) {
            if (confirmation) {
                var self = this;
                var row_ids = FlexObject.getSelectedRowIds(grid);
                var data_ids = '';
                if (typeof row_ids != null) {
                    for (var x in row_ids) {
                        data_ids += '' + row_ids[x] + ','
                    }
                }
                var url = $('#table-delete-url').val();
                var query = 'data_ids=' + data_ids;

                $.ajax({
                    url:url,
                    data:query,
                    dataType:'json',
                    type:'POST',
                    success:function (response) {
                        var txt = '';
                        if (typeof response.mesg == 'object') {
                            for (megTxt in response.mesg) {
                                txt += response.mesg[megTxt] + '<br />';
                            }
                        }
                        else {
                            txt = response.mesg
                        }
                        //* When everything went on smoothly on the server redirect the user to the appropriate page.*//*
                        if (response.code === 0) {
                            var data = response.data;

                            FlexObject.removeGridRows(grid);
                            jLib.message('Message Deletion', response.mesg, 'success');

                            self.selected_row_id = null;
                        }
                        //* When there are Errors *//*
                        else if (response.code === 1) {
                            jLib.serverError(txt);
                        }
                    },
                    error:function (xhr) {
                        console.log(xhr.responseText);
                        jLib.serverError();
                    }
                });
            }
        });
    }

};

/* when the page is loaded */
$(document).ready(function () {
    MailAddressBook.init();
});
    