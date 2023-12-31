/* global permissions, form_field_rendered, price_change_data, previous_day_records, current_day_records, form_key */

var DailySales = {
    active_row: null,
    row_editing_in_progress: false,

    init:function(){
        var self = this;

        $("#sales-sheet-dates").bind('change',function(){
            var url = window.location.origin+$("#form-save-url").val()+"/index"+"/"+form_key+"/"+$(this).val();
            window.location = url;
            console.log("new location", url);
        });

        self.initNewSalesSheet();
        self.initRowSelect();
        self.initRowMenus();
        self.initReport();
    },

    initReport: function () {
        var self = this;
        $("#linked_report_date").bind('change',function(){
            self.getReportData($(this).val());
        });

        $("#linked_report_refresh").bind('click',function(){
            self.getReportData($("#linked_report_date").val());
        });
        $("#linked_report_refresh").click();
    },

    getReportData: function (report_date) {
        $("#linked_report_loader").html("Loading Report...");
        $("#linked_report_html").html('');

        var report_id = current_day_records['form']['omc_sales_report_id']

        var form_report = {
            'report_id': report_id,
            'report_date': report_date,
        }

        $.ajax({
            url: $("#linked_report_url").val(),
            data: form_report,
            dataType:'json',
            type:'POST',
            success:function (response) {
                var txt = '';
                if (typeof response.msg == 'object') {
                    for (megTxt in response.msg) {
                        txt += response.msg[megTxt] + '<br />';
                    }
                }
                else {
                    txt = response.msg
                }
                if (response.code === 0) {
                    alertify.success(txt);
                    $("#linked_report_loader").html("");
                    $("#linked_report_html").html(response.html)
                }
                //* When there are Errors *//*
                else if (response.code === 1) {
                   // alertify.error(txt);
                    $("#linked_report_html").html(txt);
                }
            },
            error:function (xhr) {
                // console.log(xhr.responseText);
                jLib.serverError();
            }
        });
    },

    initNewSalesSheet:function(){
        var self = this;
        $("#new_sales_sheet_btn").bind('click',function(){
            var sales_sheet_dt = $("#sales-sheet-dates").val();
            var ques = "Are you want to create a new sales sheet for "+sales_sheet_dt+"?";
            alertify.confirm( ques, function (e) {
                if (e) {
                    //ajax create and reload on success
                    //var todayDate = new Date().toISOString().slice(0, 10);
                    var save = {
                        'form_action_type': 'create_sales_sheet',
                        'form_key': form_key,
                        'sales_sheet_date': sales_sheet_dt
                    }

                    $.ajax({
                        url: $("#form-save-url").val(),
                        data:save,
                        dataType:'json',
                        type:'POST',
                        success:function (response) {
                            var txt = response.msg;
                            if (response.code === 0) {
                                alertify.success(txt);
                                window.location.reload();
                            }
                            else if (response.code === 1) {
                                alertify.error(txt);
                            }
                        },
                        error:function (xhr) {
                            // console.log(xhr.responseText);
                            jLib.serverError();
                        }
                    });
                }
            });
        });
    },


    initRowSelect:function(){
        var self = this;
        $("table.form-tables tbody tr").live('click',function(){
            if(self.row_editing_in_progress) {
                if(!(self.active_row.attr('data-id') === $(this).attr('data-id'))) {
                    alertify.error("Please finish saving the current editing row.");
                }
            } else {
                $("table.form-tables tbody tr").removeClass('selected');
                $(this).addClass('selected');
                self.active_row = $(this);
                self.calculateSelectedRowTotal(self.active_row);
            }
        });
    },


    initRowMenus:function(){
        var self = this;
        $("#delete_sales_sheet_btn").click(function(){
            self.deleteSalesSheet();
        });
        $("#edit_row_btn").click(function(){
            self.editRow();
        });
        $("#cancel_row_btn").click(function(){
            self.clearEditing();
        });
        $("#save_row_btn").click(function(){
            self.saveRow();
        });
    },

    editRow:function(){
        var self = this;
        if(self.row_editing_in_progress){
            self.saveRow(function(){
                self.renderRowFormElements();
            });
        }
        else{
            self.renderRowFormElements();
        }
    },


    saveRow:function(callback){
        var self = this;
        var res = self.validateRow();
        if(!self.row_editing_in_progress){
           return;
        }

        if(res.status) {//validation pass get the values
            var tr_id = self.active_row.attr('data-id');
            var field_values = [];
            if(self.active_row && self.active_row.length > 0 && self.row_editing_in_progress){
                self.active_row.find("td").each(function() {
                    var td = $(this);
                    var field_id = td.attr('data-id');
                    var row_id = td.attr('data-row-id');
                    var f = current_day_records['fields'][row_id][field_id];
                    if (f.is_primary_field === false && f.is_editable === true) {
                        var field_type = f.options['field_type'];
                        var el = '';
                        if(field_type === 'Text' || field_type === 'File Upload'){
                            el = td.find('input');
                        }
                        else if(field_type === 'Drop Down'){
                            el = td.find('select');
                        }
                        var val = el.val();
                        val = val.trim();
                        field_values.push({'id':field_id, 'value':val});
                    }
                });
            }

            self.updateCurrentData(tr_id, field_values);
            self.calculateAllRowsTotals(); //Re calculate to get the latest update

            //Get all totals and save them too

            Array.prototype.push.apply(field_values, self.getAllRowsTotals());

            var save = {
                'form_action_type':'form_save_sales_record',
                'field_values': field_values
            }

            var url = $("#form-save-url").val();
            //ajax
            $.ajax({
                url:url,
                data: save,
                dataType:'json',
                type:'POST',
                success:function (response) {
                    var txt = '';
                    if (typeof response.msg == 'object') {
                        for (megTxt in response.msg) {
                            txt += response.msg[megTxt] + '<br />';
                        }
                    }
                    else {
                        txt = response.msg
                    }
                    if (response.code === 0) {
                        alertify.success(txt);
                        self.clearEditing();
                        if(typeof callback == "function"){
                            callback();
                        }
                    }
                    //* When there are Errors *//*
                    else if (response.code === 1) {
                        alertify.error(txt);
                    }
                },
                error:function (xhr) {
                    // console.log(xhr.responseText);
                    jLib.serverError();
                }
            });
        }
        else{
            alertify.error(res.message);
            return false;
        }
    },


    validateRow:function(){
        var self = this;
        var pass = true;
        var error_msg = '';
        //Clear the validation Message
        self.active_row.find("td span.error_span").remove();
        self.active_row.find("td input").removeClass('error_field');
        self.active_row.find("td select").removeClass('error_field');

        if(self.active_row && self.active_row.length > 0 && self.row_editing_in_progress){
            self.active_row.find("td").each(function(){
                var td = $(this);
                var field_id = td.attr('data-id');
                var row_id = td.attr('data-row-id');
                var f = current_day_records['fields'][row_id][field_id];
                if(f.is_primary_field === false && f.is_editable === true) {
                    var field_type = f.options['field_type'];
                    var field_name = f.options['field_name'];
                    var field_required = f.options['field_required'];
                    if(field_required === 'Yes'){
                        var el = '';
                        if(field_type === 'Text' || field_type === 'File Upload'){
                            el = td.find('input');
                        }
                        else if(field_type === 'Drop Down'){
                            el = td.find('select');
                        }
                        var val = el.val();
                        val = val.trim();
                        if(val.length === 0){
                            pass = false;
                            el.addClass('error_field');
                            var span = $("<span />");
                            span.addClass('error_span');
                            span.html(field_name+" is required. ");
                            td.append(span);
                            error_msg += field_name+" is required. <br />";
                        }
                    }
                }
            });
        }
        else{
            pass = true;
            error_msg ="Validation Success!";
        }

        return {
            'status':pass,
            'message':error_msg
        };
    },

    updateCurrentData:function(row_id, data){
        var self = this;
        var collection  = current_day_records['fields'][row_id];
        for(var nx in collection){
            var record_found = data.find( x => x.id === collection[nx].id);
            if(record_found) {
                collection[nx].value = record_found.value;
            }
        }
    },

    clearEditing:function(){
        var self = this;
        if(self.active_row && self.active_row.length > 0) {
            self.active_row.find("td").each(function(){
                var td = $(this);
                var row_id = td.attr('data-row-id');
                var field_id = td.attr('data-id');
                var f = current_day_records['fields'][row_id][field_id];
                var default_val = f.value;
                /*if(!isNaN(default_val)){
                    default_val = jLib.formatNumber(parseFloat(default_val),'money',2);
                }*/
                td.html(default_val);
            });
            self.active_row.removeClass('editing').removeClass('selected');
            $("table.form-tables tbody tr");
            self.row_editing_in_progress = false;
        }
    },

    renderRowFormElements: function(){
        var self = this;
        var is_total_row = false;
        if(self.active_row && self.active_row.length > 0){
            self.active_row.addClass('editing');
            self.active_row.find("td").each(function(){
                var td = $(this);
                var row_id = td.attr('data-row-id');
                var field_id = td.attr('data-id');
                var field_type = '';
                var el = '';
                var f = current_day_records['fields'][row_id][field_id];
                var form_name = current_day_records['form']['name'];
                if(f.is_total_row === true) {
                    //Means this td belongs to a row that is flagged for totaling
                    is_total_row = true;
                }
                var default_val = f.value;
                if(f.is_primary_field === false && f.is_total_row === false && f.is_editable === true) {
                    var formField = self.getFormField(field_id, f, default_val, form_name);
                    field_type = formField.type;
                    el = formField.field;
                    default_val = '';
                }
                td.attr('data-field_type', field_type);
                td.html(default_val);
                td.append(el);
            });

            self.row_editing_in_progress = true;
            if(is_total_row) {
                self.row_editing_in_progress = false;
            }
        }
        else{
            alertify.error("You have to select a record before you can edit.");
        }
    },


    calculateAllRowsTotals:function(){
        var self = this;
        $("table.form-tables tbody tr").each(function(){
            self.calculateSelectedRowTotal($(this));
        });
    },

    calculateSelectedRowTotal: function(tr){
        var self = this;
        if(tr.length > 0) {
            tr.find("td").each(function(){
                var td = $(this);
                var row_id = td.attr('data-row-id');
                var field_id = td.attr('data-id');
                var f = current_day_records['fields'][row_id][field_id];
                if(f.is_total_row === true) {
                    var r = self.calculateColumnTotal(row_id, field_id);
                    if(r) {
                        current_day_records['fields'][row_id][field_id].value = r;
                        td.html(r);
                    }
                }
            });
        }
    },

    getAllRowsTotals:function(){
        var self = this;
        var saves = [];
        $("table.form-tables tbody tr").each(function(){
            Array.prototype.push.apply(saves, self.getRowTotals($(this)));
        });
        return saves;
    },

    getRowTotals: function(tr){
        var self = this;
        var field_values = [];
        if(tr.length > 0) {
            tr.find("td").each(function(){
                var td = $(this);
                var row_id = td.attr('data-row-id');
                var field_id = td.attr('data-id');
                var f = current_day_records['fields'][row_id][field_id];
                if(f.is_total_row === true) {
                    var r = self.calculateColumnTotal(row_id, field_id);
                    if(r) {
                        field_values.push({'id':field_id, 'value':r});
                    }
                }
            });
        }
        return field_values;
    },

    calculateColumnTotal: function(row_id, field_id){
        var result = [];
        var f = current_day_records['fields'][row_id][field_id];
        var is_total_options = f.is_total_options;
        const total_field_list = is_total_options.total_field_list.split(',');
        const total_option_list = is_total_options.total_option_list.split(',');
        //Check if this column needs a total
        if(total_field_list.indexOf(f.element_column_id) >= 0) {
            total_option_list.forEach(option_row_id => {
                var g = EventActions.getFieldValue(current_day_records['fields'], option_row_id, f.element_column_id, 'primary_field_option_row_id','element_column_id', 'value' );
                if(g) {
                    result.push(parseFloat(g));
                }
            });
        }
        return result.length > 0 ? result.reduce(EventActions.sum) : false;
    },

    getFormField:function(field_id, fieldObj, default_val, form_name){
        var self = this;
        var field_options = fieldObj.options;
        var field_type = field_options['field_type'];
        var field_required = field_options['field_required'];
        var field_disabled = field_options['field_disabled'];
        var field_type_values = field_options['field_type_values'];
        var field_event = field_options['field_event'];
        var field_action = field_options['field_action'];
        var field_action_sources = field_options['field_action_sources'];
        var field_action_source_column = field_options['field_action_source_column'];
        var field_action_targets = field_options['field_action_targets'];
        var element = '';

        if(field_type === "Text"){
            element = $("<input />");
            element.attr('type','text');
            element.attr('class','dsrp_text');
            element.attr('id','field_id_'+field_id);
            element.attr('data-field_id', field_id);
            if(field_required === 'Yes'){
                element.attr('required','required');
            }
            element.val(default_val);
        }
        else if(field_type === "File Upload"){
            element = $("<input />");
            element.attr('type','text');
            element.attr('class','dsrp_file_upload');
            element.attr('id','field_id_'+field_id);
            element.attr('data-field_id', field_id);
            if(field_required === 'Yes'){
                element.attr('required','required');
            }
            element.val(default_val);
            element.attr('readonly','readonly');
        } else if(field_type === "Drop Down"){
            element = $("<select />");
            element.attr('class','dsrp_select');
            element.attr('id','field_id_'+field_id);
            element.attr('data-field_id',field_id);
            var options_arr = field_type_values.split(',');
            for(var y in options_arr){
                var opt_val = options_arr[y];
                var option_el = $("<option />");
                option_el.attr('value',opt_val);
                option_el.html(opt_val);
                element.append(option_el);
            }
            element.val(default_val);
        }

        //Add other element properties
        if( field_disabled === 'y') {
            element.attr('readonly','readonly');
        }

        //Add event binding where applicable
        if(field_event && field_action && field_action_targets) {
            var eventCallbackFunc = function() {
                //Field sources will depend on action type

                var options = {
                    collection: [],
                    search_row: '',
                    search_column: '',
                    compare_row_property: '',
                    compare_column_property: '',
                    return_property: '',
                    current_value: ''
                };
                var action_sources = field_action_sources.split(',');

                if(field_action === 'previous_value') {
                    options['collection'] = previous_day_records['fields'];
                    options['search_row'] = fieldObj.primary_field_option_row_id
                    options['search_column'] = action_sources && action_sources[0] ? action_sources[0] : '';
                    options['compare_row_property'] = 'primary_field_option_row_id';
                    options['compare_column_property'] = 'element_column_id';
                    options['return_property'] = 'value';
                } else if (field_action === 'month_to_date') {
                    //Get the previous's day month-to-date value and add it to the action_sources column. Suggest use focus in disable on data as element event
                    var search_column =  action_sources && action_sources[1] ? action_sources[1] : '';
                    action_sources = [action_sources && action_sources[0] ? action_sources[0] : '']
                    options['collection'] = previous_day_records['fields'];
                    options['search_row'] = fieldObj.primary_field_option_row_id
                    options['search_column'] = search_column;
                    options['compare_row_property'] = 'primary_field_option_row_id';
                    options['compare_column_property'] = 'element_column_id';
                    options['return_property'] = 'value';
                } else if(field_action === 'price_change') {
                    options['collection'] = all_external_data_sources[fieldObj.option_link_type];
                    options['search_row'] = fieldObj.option_link_id;
                    options['compare_row_property'] = 'id';
                    var a = field_action_source_column.split(":");
                    options['return_property'] = a[1];
                } else if(field_action === 'other_modules') {
                    options['collection'] = [];
                    options['return_property'] = '';
                    if(field_action_source_column) {
                        var a = field_action_source_column.split(":");
                        options['collection'] = all_external_data_sources[a[0]];
                        options['return_property'] = a[1];
                    }
                    options['search_row'] = fieldObj.option_link_id;
                    options['compare_row_property'] = 'id';
                }else if(field_action === 'dsrp') {
                    options['collection'] = all_external_data_sources[field_action][fieldObj.options.dsrp_form] ? all_external_data_sources[field_action][fieldObj.options.dsrp_form] : [];
                    options['search_row'] = fieldObj.option_link_id;
                    options['search_row2'] = fieldObj.option_link_type
                    options['search_column'] = fieldObj.options.dsrp_form_fields;
                    options['compare_row_property'] = 'option_link_id';
                    options['compare_row_property2'] = 'option_link_type';
                    options['compare_column_property'] = 'element_column_id';
                    options['return_property'] = 'value';
                    options['operands'] = fieldObj.options.operands;
                } else if(field_action === 'file_upload') {
                    options['form_name'] = form_name
                    options['field_id'] = field_id;
                    options['callback'] = (result)=>{
                       // console.log("The Callback:", result)
                        element.val(result.join('<br />'));
                    };
                }

                self.onElementEventCallback(field_event, field_action, action_sources, field_action_targets.split(','), options);
            }

            //Handling custom events and standard events
            if(field_event === 'disable_on_data') {
                element.on( 'focus', eventCallbackFunc);
            } else {
                element.on( field_event, eventCallbackFunc);
            }
        }

        return {'field':element,'type':field_type};
    },


    onElementEventCallback: function (event, action, action_sources, action_targets, options={}) {
        var self = this;
        var sources_values = [];

        action_sources.forEach(function (source_id) {
            self.active_row.find("td[data-column-id='" + source_id + "']").each(function(){
                var td = $(this);
                var field_type = td.attr('data-field_type');
                var el = '';
                if(field_type === 'Text' || field_type === 'File Upload'){
                    el = td.find('input');
                }
                else if(field_type === 'Drop Down'){
                    el = td.find('select');
                }
                var val = el.val();
                val = val.trim();
                sources_values.push(parseFloat(val));
            });
        });

        var result = EventActions.getValue(action, sources_values, options);

        action_targets.forEach(function (target_id) {
            self.active_row.find("td[data-column-id='" + target_id + "']").each(function(){
                var td = $(this);
                var field_type = td.attr('data-field_type');
                var el = '';

                if(field_type === 'Text' || field_type === 'File Upload'){
                    el = td.find('input');
                }
                else if(field_type === 'Drop Down'){
                    el = td.find('select');
                }
                var el_data = el.val();//get the current data entered by user
                el.val(result);

                if(result && event === 'disable_on_data') {
                    el.attr( 'readonly', 'readonly');
                }

                if(el_data && event === 'disable_on_data') {
                    el.val(el_data);
                }

            });
        });
    },


    deleteSalesSheet:function(){
        var ques = "Are you want to delete this sales sheet records ?";
        alertify.confirm( ques, function (e) {
            if (e) {
                //ajax create and reload on success
                var save = {
                    'form_action_type': 'delete_sales_sheet',
                    'form_sales_sheet_id': $("#form-sales-sheet-id").val()
                }
                //ajax
                $.ajax({
                    url: $("#form-save-url").val(),
                    data:save,
                    dataType:'json',
                    type:'POST',
                    success:function (response) {
                        var txt = response.msg;
                        if (response.code === 0) {
                            alertify.success(txt);
                            window.location.reload();
                        }
                        else if (response.code === 1) {
                            alertify.error(txt);
                        }
                    },
                    error:function (xhr) {
                        // console.log(xhr.responseText);
                        jLib.serverError();
                    }
                });
            }
        });

    }


};

/* when the page is loaded */
$(document).ready(function () {
    DailySales.init();
});