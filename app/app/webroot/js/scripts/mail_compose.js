/*
 * @name Mail.js
 */
var MailCompose = {
    /* Function init
     * @param void
     * @return void
     * @access public
     * */

   // selected_users:{},

    init:function () {
        var self = this;
        /*Sortable Table */

        $('#send-btn').click(function () {
            self.sendMessage();
        });

        $("#form-new-message").validationEngine();
        self.bindAddressBookContact();

        $('#navPopMessages').hide();
        //var visible = $('#navPopMessages').is(':visible');
        /*if(!visible){//bind scrollable
            console.log('hidden')
            $('#navPopMessages .scrollBox .scroll').mCustomScrollbar("update");
        }
        else{
            console.log("can't see")
        }*/
    },

    bindAddressBookContact:function () {
        var self = this;
        $('#navPopMessages .scrollBox .scroll div.item').click(function () {
            var item = $(this);
            var username = item.attr('data-user');
            var temp = $("#to").val();
            var temp_to_arr = [];

            if(temp){
                temp_to_arr = temp.split(',');
                if(!inArray(username,temp_to_arr)){
                    temp_to_arr.push(username)
                }
                var tos = temp_to_arr.join(',');
                $("#to").val(tos)
            }
            else{
                $("#to").val(username);
            }
        });
    },

    sendMessage:function () {
        var self = this;
        /* Validate the form*/
        var validationStatus = $('#form-new-message').validationEngine({returnIsValid:true});
        /* When the Validation Status is true meaning that the data input are correct then the data can be processed. */
        if (validationStatus) {

            var query = $('#form-new-message').serialize();
            var url = $("#form-new-message").attr('action');
            /* Send the data to the server and handle the server response */

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
                    if (response.code === 0) {
                        jLib.message('Message', txt, 'success');
                    }
                    //* When there are Errors *//*
                    else if (response.code === 1) {
                        jLib.message('Message', txt, 'error');
                    }
                },
                error:function (xhr) {
                    console.log(xhr.responseText);
                    jLib.serverError();
                }
            });
        }
    }
};

/* when the page is loaded */
$(document).ready(function () {
    MailCompose.init();
});
    