/* 
 * @name login.js
 * @author : Amissah Gideon<kuulmek@yahoo.com>
 * @version 1.0
 */

var SignUp = {
    formLogin:null,

    init:function () {
        var self = this;
        self.formLogin = $('#form-login');

        //$('#wizard').smartWizard({transitionEffect:'slideleft',enableFinishButton:false});

        $("#username, #password").keypress(function (e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code == 13) { //Enter keycode
                self.submitForm();
            }
        });

        self.formLogin.submit(function () {
            self.submitForm();
            return false;
        });
    },

    /*
     * AJAX login
     * This function will handle the login process through AJAX
     */
    submitForm:function () {
        var self = this;
        self.hideMsg();

        if (!self.validate()) {
            self.displayMsg('Required', 'Please enter username/password', 'error');
            return;
        }
        else {
            // Show progress
            self.displayMsg('Checking', 'Please wait checking credentials...', 'warning');

            var query = self.formLogin.serialize();
            var url = $('#login-url').val();
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
                    /* When everything went on smoothly on the server redirect the user to the appropriate page.*/
                    if (response.code === 0) {
                        // Show progress
                        self.displayMsg('Login Success!', 'Please wait redirecting to homepage...', 'success');
                        window.location = $('#after-login-url').val();
                    }
                    /* When there are Errors */
                    else if (response.code === 1) {
                        self.displayMsg('Error', txt, 'error');
                    }
                },
                error:function (xhr) {
                    console.log(xhr.responseText);
                    self.displayMsg('Error', 'Network while contacting server, please try again', 'error');
                }
            });
        }
    },


    validate:function () {
        var self = this;
        $("#username").removeClass('error_textfield');
        $("#password").removeClass('error_textfield');

        var errorflag = '';
        if ($("#username").val().length == 0) {
            errorflag += "#username,";
        }
        if ($("#password").val().length == 0) {
            errorflag += "#password,";
        }

        var fields = errorflag.split(",");
        if (errorflag.length == 0) {
            return true
        }
        else {
            for (var x = 0; x < fields.length; x++) {
                $('' + fields[x] + '').addClass('error_textfield');
            }
            return false
        }
    },

    /**
     * Function to display error messages
     * @param string message the error to display
     */
    displayMsg:function (title, message, type) {
        var self = this;
        var $class = {'success':'alert-success', 'error':'alert-error', 'warning':'alert-block', 'info':'alert-info'};
        $("#loginmsg div.alert h4").html(title);
        $("#loginmsg div.alert span").html(message);
        if ($("#loginmsg div.alert").hasClass('alert-success')) {
            $("#loginmsg div.alert").removeClass('alert-success');
        }
        if ($("#loginmsg div.alert").hasClass('alert-error')) {
            $("#loginmsg div.alert").removeClass('alert-error');
        }
        if ($("#loginmsg div.alert").hasClass('alert-block')) {
            $("#loginmsg div.alert").removeClass('alert-block');
        }
        if ($("#loginmsg div.alert").hasClass('alert-info')) {
            $("#loginmsg div.alert").removeClass('alert-info');
        }
        //$("#loginmsg div.alert").removeClass('alert-success','alert-error','alert-block','alert-info');
        $("#loginmsg div.alert").addClass($class[type]);
        $("#loginmsg").show();
    },

    hideMsg:function (title, message, type) {
        var self = this;
        $("#loginmsg").hide();
    }
};

/* when the page is loaded */
$(document).ready(function () {
    SignUp.init();
});