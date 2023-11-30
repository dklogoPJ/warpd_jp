var OmcReportOrders = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        /*$("#form-query #product_type").change(function(){
            var text  = $("#form-query #product_type option:selected").text();
            $("#form-query #product_type_name").val(text);
        });*/
        $("#form-query #bdc").change(function(){
            var text  = $("#form-query #bdc option:selected").text();
            $("#form-query #bdc_name").val(text);
        });

        $("#export-btn").click(function () {
            self.print_export_Details('export');
        });
        $("#print-btn").click(function () {
            self.print_export_Details('print');
        });

       // $("#form-query #product_type").change();
        $("#form-query #bdc").change();
    },

    print_export_Details: function(data_type){
        $("#print-export-form #data_type").val(data_type);
        $("#print-export-form #data_start_dt").val($("#form-query #start_dt").val());
        $("#print-export-form #data_end_dt").val($("#form-query #end_dt").val());
        $("#print-export-form #data_group_by").val($("#form-query #group_by").val());
        $("#print-export-form #data_bdc").val($("#form-query #bdc").val());
        $("#print-export-form #data_bdc_name").val($("#form-query #bdc option:selected").text());
        window.open('', "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#print-export-form").submit();
    }

};

/* when the page is loaded */
$(document).ready(function () {
    OmcReportOrders.init();
});