var RuleActions = {

    init: function () {
        var self = this;

    },

    sum: function (targets, operands, always_positive) {
        var self = this;
        if (typeof always_positive == "undefined") {
            always_positive = false;
        }
        var total = 0.00;
        for (var op in operands) {
            var field = operands[op];
            var sign = field.charAt(0);
            if (sign == '-') {
                field = field.substr(1);
            }
            if (field) {
                var val = parseFloat($("#" + field).val());
                if (val && !isNaN(val)) {
                    if (sign == '-') {
                        total = total - val;
                    }
                    else {
                        total = total + val;
                    }
                }
            }
        }
        //Must be positive always
        if (always_positive) {
            if (total < 0) {
                total = -1 * total;
            }
        }
        for (var field_index in targets) {
            var target_field = targets[field_index];
            if (target_field) {
                $("#" + target_field).val(total);
            }
        }

        return total;
    },


    multiply: function (targets, operands, always_positive) {
        var self = this;
        if (typeof always_positive == "undefined") {
            always_positive = false;
        }
        var total = 0.00;
        for (var op in operands) {
            var field = operands[op];
            var sign = field.charAt(0);
            if (sign == '-') {
                field = field.substr(1);
            }
            if (field) {
                var val = parseFloat($("#" + field).val());
                if (val && !isNaN(val)) {
                    if(total == 0){ /* For the first time*/
                        total = 1;
                    }
                    total = total * val;
                }
            }
        }
        //Must be positive always
        if (always_positive) {
            if (total < 0) {
                total = -1 * total;
            }
        }
        for (var field_index in targets) {
            var target_field = targets[field_index];
            if (target_field) {
                $("#" + target_field).val(total);
            }
        }

        return total;
    },


    previous_val: function (targets, control_key, control_value, source_field, haystack) {
        var self = this;
        var total = 0;
        for (var op in haystack) {
            var stack = haystack[op];
            var stack_value = stack[control_key];
            if (control_value == stack_value) {
                if (stack[source_field] && !isNaN(stack[source_field])) {
                    total = parseFloat(stack[source_field]);
                }
                break;
            }
        }
        for (var field_index in targets) {
            var target_field = targets[field_index];
            if (target_field) {
                $("#" + target_field).val(total);
            }
        }

        return total;
    },


    month_to_date: function (targets, current_days_field,control_key, control_value, source_field, haystack) {
        var self = this;
        var total =0;
        for (var op in haystack) {
            var stack = haystack[op];
            var stack_value = stack[control_key];
            if (control_value == stack_value) {
                if (stack[source_field] && !isNaN(stack[source_field])) {
                    total = parseFloat(stack[source_field]);
                }
                break;
            }
        }
        total = total + parseFloat($("#" + current_days_field).val());
        for (var field_index in targets) {
            var target_field = targets[field_index];
            if (target_field) {
                $("#" + target_field).val(total);
            }
        }

        return total;
    },



    price_change: function (targets, source_val, haystack) {
        var self = this;
        var product_id = self.getProductIdFromMap(source_val, haystack);
        if (product_id) {
            if (typeof price_change_data[product_id] == "undefined") {
                for (var field_index in targets) {
                    var target_field = targets[field_index];
                    if (target_field) {
                        $("#" + target_field).val('');
                    }
                }
                return false;
            }
            else {
                var value = price_change_data[product_id]['value'];
                for (var field_index in targets) {
                    var target_field = targets[field_index];
                    if (target_field) {
                        $("#" + target_field).val(value);
                    }
                }
                return value;
            }
        }
        else {
            for (var field_index in targets) {
                var target_field = targets[field_index];
                if (target_field) {
                    $("#" + target_field).val('');
                }
            }
            return false;
        }
    },


    getValueBySearch: function (targets, search, search_key, value_key, haystack) {
        var self = this;
        var return_value = '';
        for (var op in haystack) {
            var stack = haystack[op];
            var stack_value = stack[search_key];
            if (search == stack_value) {
                return_value = stack[value_key];
                break;
            }
        }
        for (var field_index in targets) {
            var target_field = targets[field_index];
            if (target_field) {
                $("#" + target_field).val(return_value);
            }
        }
        return return_value;
    },

    getProductIdFromMap: function (pname, haystack) {
        var self = this;
        var product_id = false;
        for (var pid in haystack) {
            var name = haystack[pid]['value'];
            if (name == pname) {
                product_id = haystack[pid]['key'];
                break;
            }
        }
        return product_id;
    }


};

/* when the page is loaded */
$(document).ready(function () {
    RuleActions.init();
});