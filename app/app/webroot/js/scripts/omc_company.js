var OmcCompany = {

    grid:null,

    init:function () {
        var self = this;
        document.querySelector('form-omc').onkeypress = self.preventEnterSubmit;
    },

    preventEnterSubmit:function (e) {
        if (e.which == 13) {
            var $targ = $(e.target);

            if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
                var focusNext = false;
                $(this).find(":input:visible:not([disabled],[readonly]), a").each(function(){
                    if (this === e.target) {
                        focusNext = true;
                    }
                    else if (focusNext){
                        $(this).focus();
                        return false;
                    }
                });
                return false;
            }
        }
    }
};

/* when the page is loaded */
$(document).ready(function () {
    OmcCompany.init();
});