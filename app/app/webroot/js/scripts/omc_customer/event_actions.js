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
        }else if(action === 'dsrp') {
            result = this.calcDSRPValue(properties.collection, properties.search_row, properties.search_row2, properties.search_column ,properties.compare_row_property, properties.compare_row_property2, properties.compare_column_property, properties.return_property, source_data, properties.operands);
        }else if(action === 'file_upload') {
            //might need a callback
            this.file_upload(properties.form_name, properties.field_id, properties.callback);
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

    getFieldValue: function (collection, search_row, search_column, compare_row_property, compare_column_property, value_key) {
        var return_value = '';
        for (var x in collection) {
            var row_fields = collection[x];
            for (var st in row_fields) {
                var field_item = row_fields[st];
                if(field_item[compare_row_property] === search_row && field_item[compare_column_property] === search_column) {
                    return_value = field_item[value_key]
                    break;
                }
            }
        }
        return return_value;
    },

    calcDSRPValue: function (collection, search_row, search_row2 ,search_column, compare_row_property, compare_row_property2, compare_column_property, value_key, source_data, operands) {
        var total_collection_columns = [];
        for (var x in collection) {
            var row_fields = collection[x];  //Row of column fields
            for (var st in row_fields) { // For each field check if the row value and column value matches
                var field_item = row_fields[st];
                if(field_item[compare_row_property] === search_row && field_item[compare_row_property2] === search_row2 && field_item[compare_column_property] === search_column) {
                    var val = parseFloat(field_item[value_key]);
                    if(!isNaN(val)) {
                        total_collection_columns.push(val);
                    }
                }
            }
        }

        if(total_collection_columns.length === 0) {
            console.log("The form options are not linked to any option_link_type, So external DSRP value will not work")
        }

        var return_value = total_collection_columns.length ? total_collection_columns.reduce(this.sum) : 0;
        //console.log("typeof return_value:", typeof return_value);
       // console.log("return_value:", return_value);
        var operands_list  = operands.split(',');
       // console.log("source_data:", source_data);
       // console.log("operands_list:", operands_list);
        //For each source_data perform arithmetic per operands (operands elements should be array popped, so we don't perform the same operand element multiple times)
        source_data.forEach(source_value => {
            var operand = operands_list.shift(); //remove the first operand to work with and remove it from the operands_list array
            if(operand && !isNaN(source_value)) {
                var arr = [return_value, source_value];
                if(operand === 'sum') {
                    return_value = arr.reduce(this.sum);
                }else if(operand === 'subtract') {
                    return_value = arr.reduce(this.subtract);
                }else if(operand === 'multiply') {
                    return_value = arr.reduce(this.multiply);
                }else if(operand === 'division') {
                    return_value = arr.reduce(this.divide);
                }
            }
        })

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
    },

    file_upload: function(form_name, field_id, callback){
        var item_id = field_id;
        document.getElementById('fileupload').reset();
        var attachment_type = form_name;
        var log_activity_type = 'DSRP Data Input';
        $("#fileupload #type_id").val(item_id);
        $("#fileupload #type").val(attachment_type);//
        $("#fileupload #log_activity_type").val(log_activity_type);
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#get_attachments_url').val()+'/'+item_id+'/'+attachment_type,
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $('#ajax_upload_table tbody').html('');
            $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
            $('#attachment_modal').modal({
                backdrop: 'static',
                show: true,
                keyboard: true
            });
        });

        $('#fileupload').unbind('fileuploaddone');
        $('#fileupload').bind('fileuploaddone', function (e, data) {
            var results = [];
            $('#ajax_upload_table tbody tr').each(function(){
                $(this).find('td').each(function (){
                    var a = $(this).find('p.name a');
                    if(a.text()) {
                        results.push(a.text());
                    }
                });
            });
            //Need to do the following, the newly added file is not rendered i=on the dom before the data scrapping above is executed.
            data.result.files.forEach(file => {
                results.push(file.name);
            })
            callback(results);
        });

        $('#fileupload').unbind('fileuploaddestroyed');
        $('#fileupload').bind('fileuploaddestroyed', function (e, data) {
            var results = [];
            $('#ajax_upload_table tbody tr').each(function(){
                $(this).find('td').each(function (){
                    var a = $(this).find('p.name a');
                    if(a.text()) {
                        results.push(a.text());
                    }
                });
            });
            callback(results);
        });
    }

};

/* when the page is loaded */
$(document).ready(function () {
    EventActions.init();
});