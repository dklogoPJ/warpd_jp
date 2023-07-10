var DsrpCommon = {

    init:function(){
        var self = this;

    },

    initRowSelect:function(){
        var self = this;
        $("table.form-table tbody tr").live('click',function(){
            $("table.form-table tbody tr").removeClass('selected');
            $(this).addClass('selected');
        });

        $("table.form-table tbody tr td a.inline-row-edit").live('click',function(){
            $("table.form-table tbody tr").removeClass('selected');
            $(this).parent().parent().addClass('selected');
            $("#edit_row_btn").click();
        });

        $('.numeric').live('keyup',function(){
            this.value = this.value.replace(/[^0-9.-]/g, '');
        });
    },

    numberOnly:function(field){
        field.value = field.value.replace(/[^0-9.]/g, '');
    },

    validateRow:function(table_setup){
        var self = this;
        var pass = true;
        var error_msg = '';
        //Clear the validation Message
        $("table.form-table tbody tr.editing td span.error_span").remove();
        $("table.form-table tbody tr.editing td input").removeClass('error_field');
        $("table.form-table tbody tr.editing td select").removeClass('error_field');

        var tr = $("table.form-table tbody tr.editing");
        if(tr.length > 0){
            $("table.form-table tbody tr.editing td").each(function(){
                var td = $(this);
                var editable = td.attr('data-editable');
                if(editable == 'yes'){
                    var field_id = td.attr('data-field');
                    var field_lookup = table_setup[field_id];
                    var field_type = field_lookup['field_type'];
                    var field_name = field_lookup['header'];
                    var field_validate_rules = field_lookup['validate'];
                    var rules = field_validate_rules.split(',');
                    if(inArray('required',rules)){
                        var el = '';
                        if(field_type == 'text'){
                            el = td.find('input');
                        }
                        else if(field_type == 'select'){
                            el = td.find('select');
                        }
                        var val = el.val();
                        val = val.trim();
                        if(val.length == 0){
                            pass = false;
                            el.addClass('error_field');
                           /* var span = $("<span />");
                            span.addClass('error_span');
                            span.html(field_name+" is required. ");
                            td.append(span);*/
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

    renderRow:function(table_setup){
        var self = this;
        var tr = $("table.form-table tbody tr.selected");
        if(tr.length > 0){
            $("table.form-table tbody tr.selected").addClass('editing');
            $("table.form-table tbody tr.editing td").each(function(){
                var td = $(this);
                var editable = td.attr('data-editable');
                if(editable == 'yes'){
                    var default_val = td.attr('data-value');
                    var field_id = td.attr('data-field');
                   // var field_lookup = table_setup[field_id];
                    var field_type = self.getFormField(field_id,default_val,table_setup);
                    td.attr('data-field_type',field_type.type);
                    td.html('');
                    td.append(field_type.field);
                }
            });
        }
        else{
            alertify.error("You have to select a record before you can edit.");
        }
    },


    getFormField:function(field_id,default_val,table_setup){
        var self = this;
        var field_lookup = table_setup[field_id];
        var field_type = field_lookup['field_type'];
        var return_field = '';
        var field_validate_rules = field_lookup['validate'];
        var rules = field_validate_rules.split(',');

        if(field_type == "text"){
            var text = $("<input />");
            text.attr('type','text');
            var clas  = 'dsrp_text';
            if(inArray('numeric',rules)){
                clas = 'dsrp_text numeric';
            }
            text.attr('class',clas);
            //text.attr('onkeyup',"DsrpCommon.numberOnly(this);");
            text.attr('style','width:100%;');
            text.attr('id',field_id);
            text.attr('data-field_id',field_id);
            text.val(default_val);
            return_field = text;
        }
        else if(field_type == "select"){
            /* var select = $("<select />");
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
             return_field = select;*/
        }

        return {'field':return_field,'type':field_type};
    },

    clearEditing:function(){
        var self = this;
        $("table.form-table tbody tr.editing td").each(function(){
            var td = $(this);
            var editable = td.attr('data-editable');
            if(editable == 'yes'){
                var field_id = td.attr('data-field');
                var field_lookup = table_setup[field_id];
                var format = field_lookup['format'];
                var html_val = td.attr('data-value');
                console.log(field_id + ' ' +html_val);
                if(html_val && !isNaN(html_val)){
                    html_val = parseFloat(html_val);
                    var decimal_places = 0;
                    var format_type = format;
                    if(format == 'float'){
                        decimal_places = 2;
                        format_type = 'money';
                    }
                    if(format_type !=''){
                        console.log('format type :  ' +format_type);
                        console.log('decimal places :  ' +decimal_places);
                        html_val = jLib.formatNumber(html_val,format_type,decimal_places);
                        console.log('format :  ' +html_val);
                    }
                }
                td.html(html_val);
            }
        });
        $("table.form-table tbody tr.editing").removeClass('selected').removeClass('editing');
    },


    saveRow:function(callback){
        var self = this;

        var record_id = $("table.form-table tbody tr.editing").attr('data-id');
        var field_values = {'id':record_id};
        $("table.form-table tbody tr.editing td").each(function(){
            var td = $(this);
            var editable = td.attr('data-editable');
            if(editable == 'yes'){
                var field_id = td.attr('data-field');
                var field_lookup = table_setup[field_id];
                //var value_id = td.attr('data-value_id');
                var field_type = field_lookup['field_type'];
                var el = '';
                if(field_type == 'text'){
                    el = td.find('input');
                }
                else if(field_type == 'select'){
                    el = td.find('select');
                }
                var val = el.val();
                val = val.trim();
                field_values[field_id] = val;
            }
        });

        var len = countObjectProperties(field_values);
        if(len <= 1){
            callback(null,null);
            return
        }

        if(typeof record_id != "undefined"){
            var save = field_values;
            var url = $("#form-save-url").val();
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
                        self.updateRow(save);
                        if(typeof callback == "function"){
                            callback(save,response);
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
    },


    updateRow:function(data){
        var self = this;
        var tr = $("table.form-table tbody tr.editing");
        $("table.form-table tbody tr.editing td").each(function(){
            var td = $(this);
            var editable = td.attr('data-editable');
            if(editable == 'yes'){
                var field_id = td.attr('data-field');
                var field_lookup = table_setup[field_id];
                td.attr('data-value', data[field_id]);
            }
        });
    }
};

/* when the page is loaded */
$(document).ready(function () {
    DsrpCommon.init();
});