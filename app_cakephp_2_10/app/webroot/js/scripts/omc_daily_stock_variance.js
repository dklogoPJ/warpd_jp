var DailyStockVariance = {

    init:function () {
        var self = this;
        $("#export-btn").click(function () {
            self.print_export_Details('export');
        });
        $("#print-btn").click(function () {
            self.print_export_Details('print');
        });
    },

    print_export_Details: function(data_type){
        $("#print-export-form #data_type").val(data_type);
        $("#print-export-form #data_indicator").val($("#form-query #indicator").val());
        window.open('', "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#print-export-form").submit();
    }

};

/* when the page is loaded */
$(document).ready(function () {
    DailyStockVariance.init();
});