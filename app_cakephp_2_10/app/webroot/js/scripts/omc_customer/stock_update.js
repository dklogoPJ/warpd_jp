var StockUpdate = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,

    init:function () {
        var self = this;

        document.querySelector('form').onkeypress = self.preventEnterSubmit;

        $("#update-btn").click(function () {
            jConfirm('Are you sure the information you entered is correct ?','Confirm Save', function(confirmation) {
                if(confirmation){
                    self.save();
                }
            });
        });
    },

    save:function(){
        var self = this;

        var validationStatus = self.tankCapacityValidation();
        if (validationStatus) {
            var overrides = false;
            var bulk_msg = "";
            $('#form .tank_class').each(function(){
                var tank_id = $(this).val();
                var tank_name = $(this).attr('data-text');
                var sibling_qty = $(this).next("input[type='text']").val();
                sibling_qty = sibling_qty.trim();
                if(sibling_qty.length > 0){
                    var value = gbl_tanks_arr[tank_id];
                    var d = value['OmcCustomerStock'];
                    //console.log(d);
                    if(d.length > 0){
                        bulk_msg = bulk_msg + (tank_name+' was updated to '+d[0]['quantity']+' ltrs, at this time '+d[0]['created']+"\n");
                        overrides =true;
                    }
                }
            });
            if(overrides){
                bulk_msg = bulk_msg + "Do you want to change your earlier figure(s) ?";
                // Do you want to change your earlier figure for AGO for this day
                //jConfirm(tank_name+' was updated at this time '+jLib.convertDate(d[0]['created'],'ui_time')+', do want to add a second update for the same day ?', 'Confirm same day tank update', function(confirmation) {
                jConfirm(bulk_msg, 'Confirm same day stock updates', function(confirmation) {
                    if(confirmation){
                        $("#form").submit();
                    }
                });
            }
            else{
                $("#form").submit();
            }
        }
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


    tankCapacityValidation:function () {
        var self = this;
        var capacity_exceeded = false;
        var bulk_msg = "";
        $('#form .tank_class').each(function(){
            var tank_id = $(this).val();
            var tank_name = $(this).attr('data-text');
            var sibling_qty = $(this).next("input[type='text']").val();
            sibling_qty = sibling_qty.trim();
            if(sibling_qty.length > 0){
                var value = gbl_tanks_arr[tank_id];
                var d = value['OmcCustomerTank'];
                var tank_capacity = parseFloat(d['capacity']);
                sibling_qty = parseFloat(sibling_qty);
                //console.log(d);
                if(sibling_qty > tank_capacity){
                    bulk_msg = bulk_msg + (tank_name+' capacity is '+tank_capacity+' ltrs, your stock update value is  '+sibling_qty+" <br />");
                    capacity_exceeded =true;
                }
            }
        });
        if(capacity_exceeded){
            bulk_msg = bulk_msg + "<br /> You can't update more than the required tank capacity.";
            // Do you want to change your earlier figure for AGO for this day
            //jConfirm(tank_name+' was updated at this time '+jLib.convertDate(d[0]['created'],'ui_time')+', do want to add a second update for the same day ?', 'Confirm same day tank update', function(confirmation) {
            jLib.message('Tank Capacity Error',bulk_msg,'error');
            /*jConfirm(bulk_msg, 'Confirm same day stock updates', function(confirmation) {
                if(confirmation){
                    $("#form").submit();
                }
            });*/

            return false;
        }
        else{
            return true;
        }
    }

};

/* when the page is loaded */
$(document).ready(function () {
    StockUpdate.init();
});