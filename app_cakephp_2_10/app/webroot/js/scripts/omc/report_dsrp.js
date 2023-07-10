var ReportDSRP = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

       /* $("#form-query #product_type").change(function(){
            var text  = $("#form-query #product_type option:selected").text();
            $("#form-query #product_type_name").val(text);
        });*/

        $("#export-btn").click(function () {
            self.print_export_Details('export');
        });
        $("#print-btn").click(function () {
            self.print_export_Details('print');
        });

        $("#view_station_dashboard").click(function () {
            self.station_dashboard();
        });


        $("#form-query #product_type").change();
    },

    print_export_Details: function(dtype){
        $("#print-export-form #data_dsrp_type").val($("#form-query #dsrp_opt").val());
        $("#print-export-form #data_customer").val($("#form-query #customer").val());
        $("#print-export-form #data_month").val($("#form-query #month").val());
        $("#print-export-form #data_year").val($("#form-query #year").val());
        $("#print-export-form #data_day").val($("#form-query #day").val());
        $("#print-export-form #data_doc_type").val(dtype);
        window.open('', "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#print-export-form").submit();
    },


    station_dashboard: function(){
        var customer_id = $("#form-query #customer").val();
        var year = $("#form-query #year").val();
        var month = $("#form-query #month").val();
        var day = $("#form-query #day").val();

        var url = $("#station_dashboard_url").val()+'/'+customer_id+'/'+year+'/'+month+'/'+day;

        window.open(url, "StationDashboardWindow", "menubar=yes, width=1250, height=600,location=no,status=no,scrollbars=yes,resizable=yes");
    }


};

/* when the page is loaded */
$(document).ready(function () {
    ReportDSRP.init();
});