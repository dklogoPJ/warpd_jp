var SignUpReview = {
    form:null,

    init:function () {
        var self = this;
        self.form = $('#form');

        $('#submit-btn').click(function () {
            self.form.submit();
        });
    }
};

/* when the page is loaded */
$(document).ready(function () {
    SignUpReview.init();
});