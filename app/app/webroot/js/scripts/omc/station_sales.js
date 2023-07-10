/**
 * Created by kuulmek on 6/8/14.
 */
var Station = {

    init:function(){
        var self = this;
        self.initGetSheet();
        self.initExportBook();
    },

    initGetSheet:function(){
        var self = this;
        $("#form-query").validate({
            submitHandler: function() { self.getSheet(); }
        });
    },

    getSheet:function(){
        var self = this;
        var url = $("#load-record-url").val();
        var query = $("#form-query").serialize();

        $.ajax({
            url:url,
            data:query,
            dataType:'json',
            type:'POST',
            success:function (response) {
                var txt = '';
                if (typeof response.msg == 'object') {
                    for (megTxt in response.msg) {
                        txt += response.msg[megTxt] + '<br />';
                    }
                }
                else {
                    txt = response.msg
                }
                if (response.code === 0) {
                    $("#tab-content").html(response.html);
                    $(".form_tabs").tabs();
                }
                //* When there are Errors *//*
               /* else if (response.code === 1) {
                    alertify.error(txt);

                }*/
            },
            error:function (xhr) {
                // console.log(xhr.responseText);
                jLib.serverError();
            }
        });
    },


    initExportBook:function(){
        $("#export_sales_btn").click(function(){
            var record_dt = $("#record_dt").val();
            record_dt  = record_dt.trim();
            if(record_dt.length == 0){
                alertify.error('Please Specify The Record Date');
                $("#form-query").submit();
                return;
            }
            var station = $("#station").val();
            var sales_form_id = $("#sales_form_id").val();

            $("#export-daily-sales-form #data_station").val(station);
            $("#export-daily-sales-form #data_sales_form_id").val(sales_form_id);
            $("#export-daily-sales-form #data_record_dt").val(record_dt);

            window.open('', "ExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
            $("#export-daily-sales-form").submit();
        });
    }
}

/* when the page is loaded */
$(document).ready(function () {
    Station.init();
});