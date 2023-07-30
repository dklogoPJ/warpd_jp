var EventActions = {

    init: function () {},

    getValue: function (action, source_data = [], search_row= '', search_column='', current_value='') {
        var result = '';

        if(action === 'sum') {
            result = source_data.reduce(this.sum);
        }else if(action === 'subtract') {
            result = source_data.reduce(this.subtract);
        }else if(action === 'multiply') {
            result = source_data.reduce(this.multiply);
        }else if(action === 'division') {
            result = source_data.reduce(this.divide);
        }else if(action === 'previous_value') {
            result = this.previous_value(search_row, search_column, source_data);
        }else if(action === 'month_to_date') {
            result = this.month_to_date(current_value, search_row, search_column, source_data);
        }else if(action === 'price_change') {
            result = this.price_change(search_row, source_data);
        }

        return result;
    },

    sum: function (accumulator, number) {
        return accumulator + number;
    },

    subtract: function (accumulator, number) {
        return accumulator - number;
    },

    multiply: function (accumulator, number) {
        return accumulator * number;
    },

    divide: function (accumulator, number) {
        return accumulator / number;
    },

    getFieldValue: function (search_row, search_column, value_key, collection) {
        var return_value = '';
        for (var x in collection) {
            var fields = collection[x];
            for (var st in fields) {
                var field_item = fields[st];
                if(field_item.primary_field_option_row_id === search_row && field_item.element_column_id === search_column) {
                    return_value = field_item[value_key]
                    break;
                }
            }
        }
        return return_value;
    },

    previous_value: function (search_row, search_column, collection) {
        return this.getFieldValue(search_row, search_column, 'value', collection);
    },

    month_to_date: function (current_value, search_row, search_column, collection) {
        var source_data = [];
        source_data.push(this.getFieldValue(search_row, search_column, 'value', collection));
        source_data.push(current_value);
        return source_data.reduce(this.sum);
    },

    price_change: function (key, haystack) {
        if (typeof haystack[key] == "undefined") {
            return '';
        }
        return haystack[key].value;
    }

};

/* when the page is loaded */
$(document).ready(function () {
    EventActions.init();
});