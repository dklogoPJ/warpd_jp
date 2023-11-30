/**
 * Created by kuulmek on 6/8/14.
 */
var Station = {

    init:function(){
        var self = this;
        self.initMedia();
    },

    initMedia:function(){
        var self = this;
        $("#export-btn").click(function(){
           self.process_media('export');
        });
        $("#print-btn").click(function(){
            self.process_media('print');
        });
    },

    process_media:function(media_type){
        var self = this;
        var report_type = $("#report_type").val();
        var month = $("#month").val();
        var year = $("#year").val();
        year  = year.trim();
        if(year.length == 0){
            alertify.error('Please Specify The Year');
            return;
        }

        $("#export_margin_analysis_form #data_report_type").val(report_type);
        $("#export_margin_analysis_form #data_month").val(month);
        $("#export_margin_analysis_form #data_year").val(year);
        $("#export_margin_analysis_form #data_type").val(media_type);

        window.open('', "ExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        $("#export_margin_analysis_form").submit();
    }
}

/* when the page is loaded */
$(document).ready(function () {
    Station.init();
});