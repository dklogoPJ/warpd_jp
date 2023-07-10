var DailySales = {
    current_form_id:'',
    current_form: '',
    current_tab:'',
    current_row:'',
    edit_row:false,

    init:function(){
        var self = this;

        self.initTabs();
        self.initRowSelect();
        self.initRowMenus();
    },

    initTabs:function(){
        var self = this;
        $("#form_tabs").tabs({
            select: function( event, ui ) {
                var a = $(ui.tab);
                var render_type = a.attr('data-render_type');
                if(self.edit_row){
                    var res =  confirm('Switching to a new form will cause all unsaved changes to be lost, do you want to continue switching ?');
                    if(res){
                        if(render_type == 'Pre Populate'){
                            $("#add_row_btn").hide();
                        }
                        else{
                            $("#add_row_btn").show();
                        }
                        self.resetTab(ui);
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    if(render_type == 'Pre Populate'){
                        $("#add_row_btn").hide();
                    }
                    else{
                        $("#add_row_btn").show();
                    }
                    self.resetTab(ui);
                    return true;
                }
            }
        });
        self.current_form_id = $("ul#sales-form-tabs li.ui-state-active a").attr('data-form_id');
        self.current_form = $("ul#sales-form-tabs li.ui-state-active a").attr('data-form_table_id');//On page load, Current Form
        self.current_tab = $("ul#sales-form-tabs li.ui-state-active a").attr('href');//On page load, Current Tab
        //Active tab
       // var active = $( "#form_tabs" ).tabs( "option", "active" );
    },


   resetTab:function(ui){
       var self = this;
       var new_tab = $(ui.tab).attr('href');
       var new_form = $(ui.tab).attr('data-form_table_id');
       var new_form_id = $(ui.tab).attr('data-form_id');
       self.current_form_id = new_form_id;
       self.current_tab = new_tab;
       self.current_form = new_form;
       $("table.form-tables tbody tr").removeClass('selected');
       self.clearEditing();
   },


    initRowSelect:function(){
        var self = this;
        $("table.form-tables tbody tr").live('click',function(){
            $("table.form-tables tbody tr").removeClass('selected');
            $(this).addClass('selected');
        });
    },


    initRowMenus:function(){
        var self = this;
        $("#add_row_btn").click(function(){
            self.addRow();
        });
        $("#edit_row_btn").click(function(){
            self.editRow();
        });
        $("#cancel_row_btn").click(function(){
            self.cancelRow();
        });
        $("#save_row_btn").click(function(){
            self.saveRow();
        });
    },

    addRow:function(){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        if(self.edit_row){
            self.saveRow(function(){
                self.renderRow('new',form_id,table_form);
            });
        }
        else{
            self.renderRow('new',form_id,table_form);
        }
    },

    editRow:function(){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        if(self.edit_row){
            self.saveRow(function(){
                self.renderRow('edit_row',form_id,table_form);
            });
        }
        else{
            self.renderRow('edit_row',form_id,table_form);
        }
    },

    cancelRow:function(){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        self.clearEditing();
    },


    saveRow:function(callback){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        var res = self.validateRow();
        if(!self.edit_row){
           return;
        }

        if(res.status){//validation pass get the values
            var record_id = $(table_form+" tbody tr.editing").attr('data-record_id');
            var field_values = [];
            $(table_form+" tbody tr.editing td").each(function(){
                var td = $(this);
                var field_id = td.attr('data-field_id');
                var control_field = td.attr('data-control_field');
                if(control_field == 'no'){
                    var value_id = td.attr('data-value_id');
                    var field_lookup = forms_n_fields[form_id]['fields'][field_id];
                    var field_type = field_lookup['field_type'];
                    var el = '';
                    if(field_type == 'Text'){
                        el = td.find('input');
                    }
                    else if(field_type == 'Drop Down'){
                        el = td.find('select');
                    }
                    var val = el.val();
                    val = val.trim();
                    field_values.push({'id':value_id,'omc_sales_record_id':record_id,'omc_sales_form_field_id':field_id,'value':val});
                }
            });

            if(typeof record_id != "undefined"){
                var save = {
                    'form_id':form_id,
                    'form_action_type':'form_save',
                    'record_id':record_id,
                    'field_values':field_values
                }

                var url = $("#form-save-url").val();
                //ajax
                $.ajax({
                    url:url,
                    data:save,
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
                            self.updateCurrentData(response.data)
                            self.updateRow(response.data);
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
                if(typeof callback == "function"){
                    callback();
                }
            }


        }
        else{
            alertify.error(res.message);
            return false;
        }
    },

    updateCurrentData:function(data){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        var record_id = data['OmcSalesRecord']['id'];
        var updates = data['OmcSalesValue'];
        var update_values = {};
        for(var x = 0; x < updates.length; x++){
            var v = updates[x];
            var field_id = v.omc_sales_form_field_id;
            update_values[field_id] = v;
        }

        var new_record = {'record_id':record_id,'values':update_values};

        current_day_records[form_id]['values'][record_id] = new_record;
    },

    updateRow:function(data){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        var tr = $(table_form+" tbody tr.editing");
        if(tr.length > 0){
            tr.attr('data-record_id',data['OmcSalesRecord']['id']);
            var updates = data['OmcSalesValue'];
            $(table_form+" tbody tr.editing td").each(function(){
                var td = $(this);
                var field_id = td.attr('data-field_id');
                var control_field = td.attr('data-control_field');
                if(control_field == 'no'){
                    for(var x = 0; x < updates.length; x++){
                        var v = updates[x];
                        if(v.omc_sales_form_field_id == field_id){
                            td.attr('data-value', v.value);
                            td.attr('data-value_id', v.id);
                            break;
                        }
                    }
                }
            });
        }
    },


    validateRow:function(){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        var pass = true;
        var error_msg = '';
        //Clear the validation Message
        $(table_form+" tbody tr.editing td span.error_span").remove();
        $(table_form+" tbody tr.editing td input").removeClass('error_field');
        $(table_form+" tbody tr.editing td select").removeClass('error_field');

        var tr = $(table_form+" tbody tr.editing");
        if(tr.length > 0){
            $(table_form+" tbody tr.editing td").each(function(){
                var td = $(this);
                var field_id = td.attr('data-field_id');
                var control_field = td.attr('data-control_field');
                if(control_field == 'no'){
                    var field_lookup = forms_n_fields[form_id]['fields'][field_id];
                    var field_type = field_lookup['field_type'];
                    var field_name = field_lookup['field_name'];
                    var field_required = field_lookup['field_required'];
                    if(field_required == 'Yes'){
                        var el = '';
                        if(field_type == 'Text'){
                            el = td.find('input');
                        }
                        else if(field_type == 'Drop Down'){
                            el = td.find('select');
                        }
                        var val = el.val();
                        val = val.trim();
                        if(val.length == 0){
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

    renderRow:function(type,form_id,table_form){
        var self = this;

        if(type == 'new'){//New Row
            var tr = $("<tr />");
            tr.attr('data-record_id','0');
            tr.attr('class','editing');
            var fields_arr = form_field_rendered[form_id];
            for(var x in fields_arr){
                var field_id = fields_arr[x]['id'];
                var rule_type = fields_arr[x]['rule_type'];
                var control_field = (fields_arr[x]['control_field']) ? 'yes':'no';
                var td = $("<td />");
                td.attr('data-control_field',control_field);
                td.attr('data-rule_type',rule_type);
                td.attr('data-field_id',field_id);
                td.attr('data-value','');
                td.attr('data-value_id','0');
                if(control_field == 'no'){
                    var field_type = self.getFormField(form_id,field_id,'');
                    td.attr('data-field_type',field_type.type);
                    td.append(field_type.field);
                }
                tr.append(td);
            }
            $("table.form-tables tbody tr").removeClass('selected');
            tr.addClass('selected');
            $(table_form+" tbody").append(tr);
        }
        else{//Existing Row
            var tr = $(table_form+" tbody tr.selected");
            if(tr.length > 0){
                $(table_form+" tbody tr.selected").addClass('editing');
                $(table_form+" tbody tr.editing td").each(function(){
                    var td = $(this);
                    var default_val = td.attr('data-value');
                    var field_id = td.attr('data-field_id');
                    var control_field = td.attr('data-control_field');
                    if(control_field == 'no'){
                        var field_type = self.getFormField(form_id,field_id,default_val);
                        td.attr('data-field_type',field_type.type);
                        td.html('');
                        td.append(field_type.field);
                    }
                });
            }
            else{
                alertify.error("You have to select a record before you can edit.");
            }
        }

        self.edit_row = true;
    },



    getFormField:function(form_id,field_id,default_val){
        var self = this;
        var field_lookup = forms_n_fields[form_id]['fields'][field_id];
        var field_type = field_lookup['field_type'];
        var field_required = field_lookup['field_required'];
        var field_type_values = field_lookup['field_type_values'];
        var return_field = '';

        if(field_type == "Text"){
            var text = $("<input />");
            text.attr('type','text');
            text.attr('class','dsrp_text');
            text.attr('id','field_id_'+field_id);
            text.attr('data-field_id',field_id);
            text.attr('data-field-form_id',form_id);
            if(field_required == 'Yes'){
                text.attr('required','required');
            }
            text.val(default_val);
            return_field = text;
        }
        else if(field_type == "Drop Down"){
            var select = $("<select />");
            select.attr('class','dsrp_select');
            select.attr('id','field_id_'+field_id);
            select.attr('data-field_id',field_id);
            select.attr('data-field-form_id',form_id);
            var options_arr = field_type_values.split(',');
            var option = $("<option />");
            option.attr('value','');
            option.html('--- Select ---');
            select.append(option);
            select.append(option);
            for(var y in options_arr){
                var opt_val = options_arr[y];
                var option = $("<option />");
                option.attr('value',opt_val);
                option.html(opt_val);
                select.append(option);
            }
            select.val(default_val);
            return_field = select;
        }

        return {'field':return_field,'type':field_type};
    },


    clearEditing:function(){
        var self = this;
        var form_id = self.current_form_id;
        var table_form = self.current_form;
        var record_id = $(table_form+" tbody tr.editing").attr('data-record_id');
        record_id = parseInt(record_id);
        if(record_id > 0){
            $(table_form+" tbody tr.editing td").each(function(){
                var td = $(this);
                var html_val = td.attr('data-value');
                if(!isNaN(html_val)){
                    html_val = parseFloat(html_val);
                    html_val = jLib.formatNumber(html_val,'money',2);
                }
                td.html(html_val);
            });
            $(table_form+" tbody tr.editing").removeClass('editing');
        }
        else{
            $(table_form+" tbody tr.editing").removeClass('editing').remove();
        }
        $("table.form-tables tbody tr").removeClass('selected');
        self.edit_row = false;
    }

};

/* when the page is loaded */
$(document).ready(function () {
    DailySales.init();
});