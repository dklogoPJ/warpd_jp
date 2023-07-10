var CashCreditSummary = {

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
                { width: 300, align: 'center' },
                { width: 250, align: 'center' },
                { width: 150, align: 'center' }
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
            if(self.compare_table_rows()){
                self.saveRow(function () {
                    self.renderRow();
                });
            }
            else{
                self.saveRow(function () {});
            }
        }
        else {
            self.renderRow();
        }
    },

    cancelRow: function () {
        var self = this;
        self.clearEditing();
    },


    compare_table_rows:function(){
        var val_1 = $("table.form-table tbody tr.editing td:nth-child(2)").attr('data-field');
        var val_2 = $("table.form-table tbody tr.selected td:nth-child(2)").attr('data-field');
        if(val_1 != val_2){
            return true;
        }
        else{
            return false;
        }
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
                CashCreditSummary.clearEditing();
                if(response != null){
                    CashCreditSummary.refreshValues(response.data);
                }
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


    refreshValues: function(data){
        var self = this;
        var d = data['OmcCashCreditSummary'];
        $("table.form-table tbody tr td").each(function(){
            var td = $(this);
            var field_id = td.attr('data-field');
            //var editable = td.attr('data-editable');
            if(field_id && typeof field_id != "undefined"){
                var field_lookup = table_setup[field_id];
                var format = field_lookup['format'];
                var html_val = d[field_id];
                td.attr('data-value', html_val);
                if(html_val && !isNaN(html_val)){
                    html_val = parseFloat(html_val);
                    var decimal_places = 0;
                    if(format == 'float'){
                        decimal_places = 2;
                    }
                    html_val = jLib.formatNumber(html_val,'money',decimal_places);
                }
                td.html(html_val);
            }
        });
    },



    /**** Field Rules ***/
    bindRules: function () {
       /* $("#stock_in_hand").live('focusin', function () {
            var operands = ['open_stock', 'quantity_received'];
            var targets = ['stock_in_hand'];
            RuleActions.sum(targets, operands,true);
        });
        $("#closing_stock").live('focusin', function () {
            var operands = ['stock_in_hand', '-day_sales'];
            var targets = ['closing_stock'];
            RuleActions.sum(targets, operands,true);
        });
        $("#variance").live('focusin', function () {
            var operands = ['closing_stock', '-dipping'];
            var targets = ['variance'];
            var value = RuleActions.sum(targets, operands);
            //Update the comment too
            value = parseFloat(value);
            var comment = 'Okay';
            if(value > 0){
                comment = 'Reconciliation Required (Possible Stock Loss)';
            }
            else if(value == 0){
                comment = 'Okay';
            }
            $("#comments").val(comment);
        });
        $("#day_sales").live('focusin', function () {
            var tr_parent = $(this).parents().parents();
            var first_td = tr_parent.find("td:first")
            var search = first_td.attr('data-value');
            var search_key = 'product_stock';
            var value_key = 'day_sales';
            var haystack = stock_position_data;
            var targets = ['day_sales'];
            var value = RuleActions.getValueBySearch(targets, search,search_key,value_key,haystack);
        });*/
    }

};

/* when the page is loaded */
$(document).ready(function () {
    CashCreditSummary.init();
});