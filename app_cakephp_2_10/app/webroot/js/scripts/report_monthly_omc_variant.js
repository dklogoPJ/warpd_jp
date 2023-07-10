var MonthlyOmcVariant = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        $("#form-query #product_type").change(function(){
            var text  = $("#form-query #product_type option:selected").text();
            $("#form-query #product_type_name").val(text);
        });

        $("#export-btn").click(function () {
            self.print_export_Details('export');
        });
        $("#print-btn").click(function () {
            self.print_export_Details('print');
        });

        $("#form-query #product_type").change();
    },

    print_export_Details: function(data_type){
        $("#print-export-form #data_type").val(data_type);
        $("#print-export-form #data_year").val($("#form-query #year").val());
        $("#print-export-form #data_month").val($("#form-query #month").val());
        $("#print-export-form #data_product_type").val($("#form-query #product_type").val());
        $("#print-export-form #data_product_type_name").val($("#form-query #product_type option:selected").text());
        window.open('', "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#print-export-form").submit();
    }

};

/* when the page is loaded */
$(document).ready(function () {
    MonthlyOmcVariant.init();
});