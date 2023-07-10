var StockHistories = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,

    init:function () {
        var self = this;
        $("#query-btn").click(function () {
            var validationStatus = $('#form-query').validationEngine({returnIsValid:true});
            if (validationStatus) {
                $("#form-query").submit();
            }
        });

        $("#export-btn").click(function () {
            self.print_export_Details('export');
        });
        $("#print-btn").click(function () {
            self.print_export_Details('print');
        });
    },

    print_export_Details: function(data_type){
        $("#print-export-form #data_type").val(data_type);
        $("#print-export-form #data_month").val($("#form-query #month").val());
        $("#print-export-form #data_year").val($("#form-query #year").val());
        $("#print-export-form #data_tank_type").val($("#form-query #type").val());
        $("#print-export-form #data_product_group_name").val($("#form-query #product_group option:selected").text());
        window.open('', "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#print-export-form").submit();
    }

};

/* when the page is loaded */
$(document).ready(function () {
    StockHistories.init();
});