/* global permissions, form_field_rendered, price_change_data, previous_day_records, current_day_records, form_key */

var DailySales = {
    active_row: null,
    row_editing_in_progress: false,

    init:function(){
        var self = this;

        self.initNewSalesSheet();
        self.initRowSelect();
        self.initRowMenus();
    },

    initNewSalesSheet:function(){
        var self = this;
        $("#new_sales_sheet_btn").bind('click',function(){
            var ques = "Are you want to create a new sales sheet for today ?";
            alertify.confirm( ques, function (e) {
                if (e) {
                    //ajax create and reload on success
                    var save = {
                        'form_action_type': 'create_sales_sheet',
                        'form_key': form_key
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
            if(self.active_row.length > 0 && self.row_editing_in_progress){
                self.active_row.find("td").each(function() {
                    var td = $(this);
                    var field_id = td.attr('data-id');
                    var row_id = td.attr('data-row-id');
                    var f = current_day_records['fields'][row_id][field_id];
                    if (f.is_primary_field === false && f.is_editable === true) {
                        var field_type = f.options['field_type'];
                        var el = '';
                        if(field_type === 'Text'){
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
                        self.updateCurrentData(tr_id, field_values)
                        //self.updateRow(field_values);
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

        if(self.active_row.length > 0 && self.row_editing_in_progress){
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
                        if(field_type === 'Text'){
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
        if(self.active_row.length > 0) {
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

        if(self.active_row.length > 0){
            self.active_row.addClass('editing');
            self.active_row.find("td").each(function(){
                var td = $(this);
                var row_id = td.attr('data-row-id');
                var field_id = td.attr('data-id');
                var field_type = '';
                var el = '';
                var f = current_day_records['fields'][row_id][field_id];
                var default_val = f.value;
                if(f.is_primary_field === false && f.is_editable === true) {
                    var formField = self.getFormField(field_id, f.options, default_val);
                    field_type = formField.type;
                    el = formField.field;
                    default_val = '';
                }
                td.attr('data-field_type', field_type);
                td.html(default_val);
                td.append(el);
            });

            self.row_editing_in_progress = true;
        }
        else{
            alertify.error("You have to select a record before you can edit.");
        }
    },



    getFormField:function(field_id, options, default_val){
        var self = this;
        var field_type = options['field_type'];
        var field_required = options['field_required'];
        var field_type_values = options['field_type_values'];
        var field_event = options['field_event'];
        var field_action = options['field_action'];
        var field_action_sources = options['field_action_sources'];
        var field_action_targets = options['field_action_targets'];
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
        else if(field_type === "Drop Down"){
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

        //Add event binding where applicable
        if(field_event && field_action && field_action_sources && field_action_targets) {
            element.on( field_event, function() {
                self.onElementEventCallback(field_action, field_action_sources.split(','), field_action_targets.split(','));
            });
        }

        return {'field':element,'type':field_type};
    },


    onElementEventCallback: function (action, action_sources, action_targets) {
        var self = this;
        var sources_values = [];
        action_sources.forEach(function (source_id) {
            self.active_row.find("td[data-column-id='" + source_id + "']").each(function(){
                var td = $(this);
                var field_type = td.attr('data-field_type');
                var el = '';
                if(field_type === 'Text'){
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

        var result = '';
        const sum = (accumulator, number) => accumulator + number;
        const subtract = (accumulator, number) =>  accumulator - number;
        const multiply = (accumulator, number) =>  accumulator * number;
        const division = (accumulator, number) =>  accumulator / number;

        if(action === 'sum') {
            result = sources_values.reduce(sum);
        }else if(action === 'subtract') {
            result = sources_values.reduce(subtract);
        }else if(action === 'multiply') {
            result = sources_values.reduce(multiply);
        }else if(action === 'division') {
            result = sources_values.reduce(division);
        }

        action_targets.forEach(function (target_id) {
            self.active_row.find("td[data-column-id='" + target_id + "']").each(function(){
                var td = $(this);
                var field_type = td.attr('data-field_type');
                var el = '';
                if(field_type === 'Text'){
                    el = td.find('input');
                }
                else if(field_type === 'Drop Down'){
                    el = td.find('select');
                }
                el.val(result);
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