var Lubricant = {

    edit_row: false,

    init: function () {
        var self = this;

        self.initRowSelect();
        self.initRowMenus();
        self.bindRules();

        $('#fixed_hdr').fxdHdrCol({
            fixedCols: 1,
            width:     "100%",
            height:    650,
            colModal: [
                { width: 200, align: 'center' },
                { width: 100, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' }
            ]
        });
    },

    initRowSelect: function () {
        var self = this;
        DsrpCommon.initRowSelect();
    },

    initRowMenus: function () {
        var self = this;
        $("#edit_row_btn").click(function () {
            self.editRow();
        });
        $("#cancel_row_btn").click(function () {
            self.cancelRow();
        });
        $("#save_row_btn").click(function () {
            self.saveRow();
        });
    },

    editRow: function () {
        var self = this;
        if (self.edit_row) {
            self.saveRow(function () {
                self.renderRow();
            });
        }
        else {
            self.renderRow();
        }
    },

    cancelRow: function () {
        var self = this;
        self.clearEditing();
    },


    saveRow: function (callback) {
        var self = this;
        var table_setup_data = table_setup;
        var res = DsrpCommon.validateRow(table_setup_data);
        if (!self.edit_row) {
            return;
        }
        if (res.status) {//validation pass get the values
            DsrpCommon.saveRow(function (data,response) {
                Lubricant.clearEditing();
                if (typeof callback == "function") {
                    callback();
                }
            });
        }
        else {
            alertify.error(res.message);
            return false;
        }
    },


    renderRow: function () {
        var self = this;
        var table_setup_data = table_setup;
        DsrpCommon.renderRow(table_setup_data);
        self.edit_row = true;
    },

    clearEditing: function () {
        var self = this;
        DsrpCommon.clearEditing();
        self.edit_row = false;
    },


    /**** Field Rules ***/
    bindRules: function () {
        /** Total At Hand */
        $("#total_at_hand").live('focusin',function(){
            var operands = ['open_stock','quantity_rcd'];
            var targets = ['total_at_hand'];
            RuleActions.sum(targets,operands,true);
        });
        /** Total Day Sales*/
        $("#total_day_sales_value").live('focusin', function () {
            var operands = ['unit_price','total_day_sales_qty'];
            var targets = ['total_day_sales_value'];
            var value = RuleActions.multiply(targets, operands);
        });

        $("#bf_prev_day_qty").live('focusin', function () {
            var tr_parent = $(this).parent().parent();
            var first_td = tr_parent.find("td:first");
            var control_key = 'lubricant_type';
            var control_value = first_td.attr('data-value');
            var source_field = 'total_day_sales_qty';
            var haystack = previous_data;
            var targets = ['bf_prev_day_qty'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#bf_prev_day_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'lubricant_type';
            var control_value = first_td.attr('data-value');
            var source_field = 'total_day_sales_value';
            var haystack = previous_data;
            var targets = ['bf_prev_day_value'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });

        $("#month_to_date_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'lubricant_type';
            var control_value = first_td.attr('data-value');
            var source_field = 'month_to_date_qty';
            var haystack = previous_data;
            var current_days_field = 'total_day_sales_qty';
            var targets = ['month_to_date_qty'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });
        $("#month_to_date_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'lubricant_type';
            var control_value = first_td.attr('data-value');
            var source_field = 'month_to_date_value';
            var haystack = previous_data;
            var current_days_field = 'total_day_sales_value';
            var targets = ['month_to_date_value'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });

        $("#closing_stock_qty").live('focusin',function(){
            var operands = ['total_at_hand','-total_day_sales_qty'];
            var targets = ['closing_stock_qty'];
            RuleActions.sum(targets,operands,false);
        });

        $("#closing_stock_value").live('focusin', function () {
            var operands = ['unit_price','closing_stock_qty'];
            var targets = ['closing_stock_value'];
            var value = RuleActions.multiply(targets, operands);
        });

        //Custom rule
        $("#liter_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_value = first_td.attr('data-value');
            var  haystack = control_data['lubricants_products'];
            var control_key = RuleActions.getProductIdFromMap(control_value,haystack);
            var control_array = control_key.split('_');
            var key = control_array[0];
            var value = parseFloat(liter_setup_data[key].value);
            var unit_qty = parseFloat($("#unit_qty").val());
            var prod = value * unit_qty;
            $("#liter_qty").val(prod);
        });
    }

};

/* when the page is loaded */
$(document).ready(function () {
    Lubricant.init();
});