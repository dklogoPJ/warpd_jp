var SignupPricingPlan = {
    form:null,

    init:function () {
        var self = this;
        self.form = $('#form');

        $('#submit-btn').click(function () {
            self.form.submit();
        });

        self.initSelectedPlan();

        self.form.submit(function (e) {
            $("#package_error").hide();
            if (!self.validate()) {
                $("#package_error").show();
                return false
            }
            else {
                if (!self.submit) {
                    $("#package_error").show();
                    return false;
                }
            }
        });

        $("#form a.package_type_btn").click(function () {
            var $this = $(this);
            var id = $this.attr('data-id');
            $("#form #package_id").val(id);
            if ($("#form a.package_type_btn").hasClass('btn-secondary')) {
                $("#form a.package_type_btn").removeClass('btn-secondary')
                $("#form a.package_type_btn").html('Select');
            }
            $this.addClass('btn-secondary');
            $this.html('Selected');
        });

    },

    initSelectedPlan:function () {
        var id = parseInt($("#form #package_id").val());
        if (id > 0) {
            $("#form a.package_type_btn").each(function () {
                if (parseInt($(this).attr('data-id')) == id) {
                    $(this).addClass('btn-secondary');
                }
            })
        }
    },


    validate:function () {
        var self = this;
        var id = parseInt($("#form #package_id").val());
        if (id > 0) {
            self.submit = true;
            return true;
        }
        else {
            self.submit = false;
            return false;
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
    SignupPricingPlan.init();
});