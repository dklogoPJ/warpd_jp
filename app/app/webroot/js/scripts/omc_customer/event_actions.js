var EventActions = {

    init: function () {},

    getValue: function (action, source_data = [], properties={} ) {
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
            result = this.previous_value(properties.collection, properties.search_row, properties.search_column, properties.compare_row_property, properties.compare_column_property, properties.return_property);
        }else if(action === 'month_to_date') {
            result = this.month_to_date(properties.collection, source_data, properties.search_row, properties.search_column, properties.compare_row_property, properties.compare_column_property, properties.return_property);
        }else if(action === 'price_change') {
            result = this.price_change(properties.collection, properties.search_row, properties.compare_row_property, properties.return_property);
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

    getFieldValue: function (collection, search_row, search_column, compare_row_property, compare_column_property, value_key, ) {
        var return_value = '';
        for (var x in collection) {
            var fields = collection[x];
            for (var st in fields) {
                var field_item = fields[st];
                if(field_item[compare_row_property] === search_row && field_item[compare_column_property] === search_column) {
                    return_value = field_item[value_key]
                    break;
                }
            }
        }
        return return_value;
    },

    previous_value: function (collection, search_row, search_column, compare_row_property, compare_column_property, return_property) {
        return this.getFieldValue(collection, search_row, search_column, compare_row_property, compare_column_property, return_property);
    },

    month_to_date: function (collection, source_data, search_row, search_column, compare_row_property, compare_column_property, return_property) {
        var month_to_date_value = this.getFieldValue(collection, search_row, search_column, compare_row_property, compare_column_property, return_property);
        if(month_to_date_value) {
            source_data.push(parseFloat(month_to_date_value));
        }
        return source_data.reduce(this.sum);
    },

    price_change: function (collection, search_row, compare_row_property, return_property) {
        var found_item = collection.find(x => x[compare_row_property] === search_row);
        if(found_item) {
            return found_item[return_property];
        }
        return ''
    }

};

/* when the page is loaded */
$(document).ready(function () {
    EventActions.init();
});