var DailySalesProduct = {

    edit_row: false,

    init: function () {
        var self = this;

        self.initRowSelect();
        self.initRowMenus();
        self.bindRules();

        $('#fixed_hdr').fxdHdrCol({
            fixedCols: 1,
            width:     "100%",
            height:    400,
            colModal: [
                { width: 75, align: 'center' },
                { width: 80, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 215, align: 'center' },
                { width: 215, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' },
                { width: 250, align: 'center' }
            ]
        });


        $('#fixed_hdr2').fxdHdrCol({
            fixedCols: 1,
            width:     "100%",
            height:    400,
            colModal: [
                { width: 75, align: 'center' },
                { width: 80, align: 'center' },
                { width: 155, align: 'center' },
                { width: 155, align: 'center' },
                { width: 215, align: 'center' },
                { width: 215, align: 'center' },
                { width: 200, align: 'center' },
                { width: 200, align: 'center' }
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
                DailySalesProduct.clearEditing();
                DailySalesProduct.updateTotalTable(response.data);
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


    updateTotalTable:function(data){
        var self = this;
        var row_id  = data.id;
        $("table#fixed_hdr3 tbody tr[data-id='" + row_id +"'] td").each(function(){
            var td = $(this);
            var field_id = td.attr('data-field');
            var field_lookup = table_total_setup[field_id];
           /* console.log(table_setup);
            console.log(field_lookup);*/

            var format = field_lookup['format'];
            td.attr('data-value', data[field_id]);
            var html_val = data[field_id];
            if(html_val && !isNaN(html_val)){
                html_val = parseFloat(html_val);
                var decimal_places = 0;
                var format_type = format;
                if(format == 'float'){
                    decimal_places = 2;
                    format_type = 'money';
                }
                if(format_type !=''){
                    html_val = jLib.formatNumber(html_val,format_type,decimal_places);
                }
            }
            td.html(html_val);
        });
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
        $("#unit_price").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var search = first_td.attr('data-value');
            var haystack = control_data['daily_sales_products'];
            var targets = ['unit_price'];
            var value = RuleActions.price_change(targets, search,haystack);
        });
        /** Cash Sales*/
        $("#cash_day_sales_value").live('focusin', function () {
            var operands = ['unit_price','cash_day_sales_qty'];
            var targets = ['cash_day_sales_value'];
            var value = RuleActions.multiply(targets, operands);
        });
        $("#cash_previous_day_sales_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'cash_day_sales_qty';
            var haystack = previous_data;
            var targets = ['cash_previous_day_sales_qty'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#cash_previous_day_sales_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'cash_day_sales_value';
            var haystack = previous_data;
            var targets = ['cash_previous_day_sales_value'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#cash_month_to_date_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'cash_month_to_date_qty';
            var haystack = previous_data;
            var current_days_field = 'cash_day_sales_qty';
            var targets = ['cash_month_to_date_qty'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });
        $("#cash_month_to_date_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'cash_month_to_date_value';
            var haystack = previous_data;
            var current_days_field = 'cash_day_sales_value';
            var targets = ['cash_month_to_date_value'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });

        /** Dealers Sales*/
        $("#dealer_credit_day_sales_value").live('focusin', function () {
            var operands = ['unit_price','dealer_credit_day_sales_qty'];
            var targets = ['dealer_credit_day_sales_value'];
            var value = RuleActions.multiply(targets, operands);
        });
        $("#dealer_credit_previous_day_sales_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'dealer_credit_day_sales_qty';
            var haystack = previous_data;
            var targets = ['dealer_credit_previous_day_sales_qty'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#dealer_credit_previous_day_sales_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'dealer_credit_day_sales_value';
            var haystack = previous_data;
            var targets = ['dealer_credit_previous_day_sales_value'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#dealer_credit_month_to_date_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'dealer_credit_month_to_date_qty';
            var haystack = previous_data;
            var current_days_field = 'dealer_credit_day_sales_qty';
            var targets = ['dealer_credit_month_to_date_qty'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });
        $("#dealer_credit_month_to_date_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'dealer_credit_month_to_date_value';
            var haystack = previous_data;
            var current_days_field = 'dealer_credit_day_sales_value';
            var targets = ['dealer_credit_month_to_date_value'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });

        /** Customers Sales*/

        $("#customers_day_sales_value").live('focusin', function () {
            var operands = ['unit_price','customers_day_sales_qty'];
            var targets = ['customers_day_sales_value'];
            var value = RuleActions.multiply(targets, operands);
        });
        $("#customers_previous_day_sales_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'customers_day_sales_qty';
            var haystack = previous_data;
            var targets = ['customers_previous_day_sales_qty'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#customers_previous_day_sales_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'customers_day_sales_value';
            var haystack = previous_data;
            var targets = ['customers_previous_day_sales_value'];
            var value = RuleActions.previous_val(targets, control_key, control_value, source_field, haystack);
        });
        $("#customers_month_to_date_qty").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'customers_month_to_date_qty';
            var haystack = previous_data;
            var current_days_field = 'customers_day_sales_qty';
            var targets = ['customers_month_to_date_qty'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });
        $("#customers_month_to_date_value").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first");
            var control_key = 'products';
            var control_value = first_td.attr('data-value');
            var source_field = 'customers_month_to_date_value';
            var haystack = previous_data;
            var current_days_field = 'customers_day_sales_value';
            var targets = ['customers_month_to_date_value'];
            var value = RuleActions.month_to_date(targets, current_days_field,control_key, control_value, source_field, haystack);
        });
    }

};

/* when the page is loaded */
$(document).ready(function () {
    DailySalesProduct.init();
});