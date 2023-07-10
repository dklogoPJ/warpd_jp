/* 
 * @name login.js
 * @author : Amissah Gideon<kuulmek@yahoo.com>
 * @version 1.0
 */

var Login = {
    formLogin:null,

    init:function () {
        var self = this;
        //self.formLogin = $('#form-login');

        /*$("#username, #password").keypress(function(e) {
         var code = (e.keyCode ? e.keyCode : e.which);
         if(code == 13) { //Enter keycode
         self.submitForm();
         }
         });

         $("#submit_btn").click(function(e) {
         self.submitForm();
         });
         */
        /*self.formLogin.submit(function(){
         self.submitForm();
         return false;
         });*/

       /* $("#terms_conds").click(function(){
            $.colorbox({
                inline:true,
                scrolling:false,
                overlayClose:false,
                escKey:false,
                top:'5%',
                title:'Term and Conditions',
                href:"#terms-conds-window"
            });
        });*/
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
    }
};

/* when the page is loaded */
$(document).ready(function () {
    Login.init();
});