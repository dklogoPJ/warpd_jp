var StockUpdate = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,

    init:function () {
        var self = this;

        document.querySelector('form').onkeypress = self.preventEnterSubmit;

        $("#form").validate({
            rules: {
                quantity_ltrs: {
                    required: true,
                    number: true
                },
                quantity_metric_ton: {
                    number: true
                }
            }
        });
       /* $("#update-btn").click(function () {
            $("#form").submit();
        });*/
        $("#delivery_date").datepicker({
            inline: true,
            changeMonth: true,
            changeYear: true,
            showOn: "button",
            buttonImage: $("#datepicker_btn_img").val()+"calendar.png"
            //buttonImageOnly: true
        });
        $("#delivery_date").datepicker( "option", "dateFormat", 'yy-mm-dd' );


        $("#depot_id").change(function() {
            var value = $(this).val();
            var select = document.getElementById('product_type_id');
            select.options.length = 0;
            if( typeof gbl_depots_to_products[value] == "undefined" ) {
                return;
            }
            var d_options = gbl_depots_to_products[value];
            for(var nx in d_options) {
                var opt = document.createElement('option');
                opt.value = d_options[nx];
                opt.text = products[d_options[nx]];
                try { //Standard
                    select.add(opt, null);
                }
                catch( error ) { //IE Only
                    select.add(opt);
                }
            }
        }).change();
    },

    preventEnterSubmit:function (e) {
        if (e.which == 13) {
            var $targ = $(e.target);

            if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
                var focusNext = false;
                $(this).find(":input:visible:not([disabled],[readonly]), a").each(function(){
                    if (this === e.target) {
                        focusNext = true;
                    }
                    else if (focusNext){
                        $(this).focus();
                        return false;
                    }
                });
                return false;
            }
        }
    },

};

/* when the page is loaded */
$(document).ready(function () {
    StockUpdate.init();
});