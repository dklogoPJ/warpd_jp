/*
 * @name Mail.js
 */
var MailInbox = {
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
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'From', name:'from', width:240, sortable:false, align:'left', hide:false},
                {display:'Title', name:'title', width:240, sortable:false, align:'left', hide:false},
                {display:'Date', name:'to', width:200, sortable:false, align:'left', hide:false}
            ],
            formFields:[
                {type:'buttom', name:'View', bclass:'view', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent}
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
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
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
        if (com == 'View') {
            if (FlexObject.rowSelectedCheck(MailInbox.objGrid,grid, 1)) {
                MailInbox.view(FlexObject.getSelectedRowIds(grid));
            }
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(MailInbox.objGrid,grid)) {
                MailInbox.delete_(grid);
            }
        }
    },

    view:function (row_ids) {

        var self = this;
        var dataID = row_ids[0];
        var url = $('#table-url').val();
        var query = 'action=View&data-id=' + dataID;

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

                    $("#mail_from").html(data.Message.User.fname + ' ' + data.Message.User.lname + ' < ' + data.Message.User.username + ' >');
                    $("#mail_to").html(data.User.fname + ' ' + data.User.lname + ' < ' + data.User.username + ' >');
                    $("#mail_title").html(data.Message.title);
                    $("#mail_body").html(data.Message.content);

                    $('#preview_mail').modal('show');
                    self.selected_row_id = dataID;
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
                var url = $('#table-url').val();
                var query = 'action=Delete&data_ids=' + data_ids;

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
                            //jLib.message('Message Deletion', response.mesg, 'success');

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
    MailInbox.init();
});
    