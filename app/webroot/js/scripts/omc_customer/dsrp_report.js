/* global permissions, report_key */

var DSRP_Report = {

    init:function(){
        $("#sales-sheet-dates").bind('change',function(){
            var url = window.location.origin+$("#dsrp-report-url").val()+"/index/"+report_key+"/"+$(this).val();
            window.location = url;
            console.log("new location", url);
        });
    }

};

/* when the page is loaded */
$(document).ready(function () {
    DSRP_Report.init();
});