var Uppf = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        $("#form-query #product_group").change(function(){
            var text  = $("#form-query #product_group option:selected").text();
            $("#form-query #product_group_name").val(text);
        });

        $("#export-btn").click(function () {
            self.print_export_Details('export');
        });
        $("#print-btn").click(function () {
            self.print_export_Details('print');
        });

        $("#form-query #product_group").change();
    },

    print_export_Details: function(data_type){
        $("#print-export-form #data_type").val(data_type);
        $("#print-export-form #data_start_dt").val($("#form-query #start_dt").val());
        $("#print-export-form #data_end_dt").val($("#form-query #end_dt").val());
        $("#print-export-form #data_product_group").val($("#form-query #product_group").val());
        $("#print-export-form #data_product_group_name").val($("#form-query #product_group option:selected").text());
        window.open('', "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#print-export-form").submit();
    }

};

/* when the page is loaded */
$(document).ready(function () {
    Uppf.init();
});