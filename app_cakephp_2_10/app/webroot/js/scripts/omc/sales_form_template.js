var SalesForm = {
    datasource_category:[],
    datasource_sub_category:[],
    tr_edit:null,

    init:function () {
        var self = this;

        $("#sales-forms").validate({
            submitHandler: function() { self.save_form(); }
        });
        $("#sales-form-fields").validate({
            submitHandler: function() { self.save_form_fields(); }
        });

        self.bind_sales_forms();
        self.bind_form_fields();
        self.render_form_fields();
    },

    bind_sales_forms:function(){
        var self = this;

        $("#form_preview_btn").click(function(){
            var count = 0;
            $("table#sales_form_list tbody tr").each(function(){
                if($(this).hasClass('selected')){
                    count = count + 1;
                }
            });
            if(count == 0){
                alertify.alert('You Have To Select At Least One Form For Preview');
            }
            else{
                var form_id = self.tr_edit.attr('data-form_id') ;
                self.preview_form(form_id);
            }
        });

        $("table#sales_form_list tbody tr").live('click',function(){
            $("table#sales_form_list tbody tr").removeClass('selected');
            self.tr_edit = $(this);
            $(this).addClass('selected');
            $("#sales-forms #form_id").val($(this).attr('data-form_id'));
            $("#sales-forms #form_name").val($(this).attr('data-form_name'));
            $("#sales-forms #form_description").val($(this).attr('data-description'));
        });

        $("#sales-forms #form_reset").click(function(){
            $("#sales-forms #form_id").val('0');
            $("#sales-forms #form_name").val('');
            $("#sales-forms #form_description").val('');
            $("#sales-forms #form_action_type").val('form_save');
            $("table#sales_form_list tbody tr").removeClass('selected');
            self.tr_edit = null;
        });

        $("#form_delete_btn").click(function(){
            var count = 0;
            $("table#sales_form_list tbody tr").each(function(){
                if($(this).hasClass('selected')){
                    count = count + 1;
                }
            });

            if(count == 0){
                alertify.alert('You Have To Select At Least One Form');
            }
            else{
                var ques = "Are You Sure You Want To Delete This Form ?";
                alertify.confirm( ques, function (e) {
                    if (e) {
                        $("#sales-forms #form_action_type").val('form_delete');
                        $("#sales-forms").submit();
                    } else {
                        //after clicking Cancel
                    }
                });
            }
        });
    },

    save_form:function(){
        var self = this;
        var url = $("#sales-forms").attr('action');
        var query = $("#sales-forms").serialize();

        var action_type = $("#sales-forms #form_action_type").val();
        var form_id = parseInt($("#sales-forms #form_id").val());
        var record_type = 'new';
        if(form_id > 0){
            record_type = 'edit';
        }

        $.ajax({
            url:url,
            data:query,
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

                    var post_data ={
                        'id' :  response.id,
                        'omc_id' :  $("#sales-forms #omc_id").val(),
                        'form_name' : $("#sales-forms #form_name").val(),
                        'description' :  $("#sales-forms #form_description").val(),
                        'action_type' : action_type
                    };
                    alertify.success(txt);
                    self.post_form_save(record_type,post_data)
                }
                //* When there are Errors *//*
                else if (response.code === 1) {
                    alertify.error(txt);
                   // $("#user_message_counter").html(0);
                }
            },
            error:function (xhr) {
               // console.log(xhr.responseText);
                jLib.serverError();
            }
        });
    },


    post_form_save: function (record_type,post_data){
        var self = this;
        //Update Table
        //var temp_id;
        var tr = null;
        var form_id = post_data['id'];
        console.log(form_id);
        var action_type = post_data['action_type'];
        if(record_type == 'new'){
            tr = $("<tr />");
        }
        else{
            tr = self.tr_edit;
            tr.html('');
        }

        if(action_type == 'form_save'){
            tr.attr('data-form_id',post_data['id']);
            tr.attr('data-form_name',post_data['form_name']);
            tr.attr('data-description',post_data['description']);

            var td = $("<td />").html(post_data['form_name']);
            tr.append(td);
            var td = $("<td />").html(post_data['description']);

            tr.append(td);

            $("#sales_form_list tbody").prepend(tr);
        }
        else if(action_type == 'form_delete'){
            tr.hide('slow').remove();
        }

        $("#sales-forms #form_reset").click();

        self.update_form_options(post_data);
    },


    update_form_options:function(post_data){
        var self = this;
        var id = post_data['id'];
        var action_type = post_data['action_type'];
        if(action_type == 'form_save'){
            $sale_form_options[id] = post_data['form_name'];
        }
        else if(action_type == 'form_delete'){
            delete $sale_form_options[id];
        }
        //render new options
        var d_options = $sale_form_options;
        var select = document.getElementById('omc_sales_form_id');
        select.options.length = 0;
        for(var nx in d_options){
            var opt = document.createElement('option');
            opt.value = nx;
            opt.text = d_options[nx];
            try{ //Standard
                select.add(opt,null) ;
            }
            catch(error){ //IE Only
                select.add(opt) ;
            }
        }
        self.render_form_fields();
    },


    bind_form_fields:function(){
        var self = this;

        $("#form_field_preview_btn").click(function(){
            var form_id = $("#sales-form-fields #omc_sales_form_id").val();
            self.preview_form(form_id);
        });

        $("table#form_field_list tbody tr").live('click',function(){
            $("table#form_field_list tbody tr").removeClass('selected');
            self.tr_edit = $(this);
            $(this).addClass('selected');

            $("#sales-form-fields #field_id").val($(this).attr('data-field_id'));
            $("#sales-form-fields #field_name").val($(this).attr('data-field_name'));
            if($(this).attr('data-field_type') == 'Text'){
                $("#sales-form-fields #field_type_text").prop("checked", true).click();
            }
            else if($(this).attr('data-field_type') == 'Drop Down'){
                $("#sales-form-fields #field_type_dropdown").prop("checked", true).click();
            }
            $("#sales-form-fields #field_required").val($(this).attr('data-field_required'));
        });

        $("#sales-form-fields #field_reset").click(function(){
            $("#sales-form-fields #field_id").val('0');
            $("#sales-form-fields #field_name").val('');
            $("#sales-form-fields #field_type").val('Text');
            $("#sales-form-fields #field_type_text").prop("checked", true).click();
            $("#sales-form-fields #field_required").val('No');
            $("#sales-form-fields #field_action_type").val('field_save');
            $('#field_type_values').importTags('');
            $("table#form_field_list tbody tr").removeClass('selected');
            self.tr_edit = null;
        });

        $("#field_delete_btn").click(function(){
            var count = 0;
            $("table#form_field_list tbody tr").each(function(){
                if($(this).hasClass('selected')){
                    count = count + 1;
                }
            });
            if(count == 0){
                alertify.alert('You Have To Select At Least One Field');
            }
            else{
                var ques = "Are You Sure You Want To Delete This Field ?";
                alertify.confirm( ques, function (e) {
                    if (e) {
                        $("#sales-form-fields #field_action_type").val('field_delete');
                        $("#sales-form-fields").submit();
                    } else {
                        //after clicking Cancel
                    }
                });
            }
        });


        $("#sales-form-fields #omc_sales_form_id").change(function(){
            $("#sales-form-fields #field_reset").click();
            self.render_form_fields();
        });

        $("#field_type_values").tagsInput({'width':'100%','height':'auto','defaultText':'add option'});

        $("#sales-form-fields #field_type_text").click(function(){
            var val = $(this).val();
            $("#sales-form-fields #field_type").val(val);
            $("#drop_down_options").hide('slow');
            $("#sales-form-fields").validate().cancelSubmit = false;
            $('#field_type_values').importTags('');
        });

        $("#sales-form-fields #field_type_dropdown").click(function(){
            var val = $(this).val();
            $("#sales-form-fields #field_type").val(val);
            $("#drop_down_options").show('slow');
            $("#sales-form-fields").validate().cancelSubmit = true;

            if(self.tr_edit){
                var field_type_values = self.tr_edit.attr('data-field_type_values');
                $('#field_type_values').importTags(field_type_values); //'foo,bar,baz'
            }
        });

    },


    save_form_fields:function(){
        var self = this;
        var url = $("#sales-form-fields").attr('action');
        var field_type_values = $("#sales-form-fields #field_type_values").val();
        var query = $("#sales-form-fields").serialize()+"&field_type_values="+field_type_values;

        $.ajax({
            url:url,
            data:query,
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
                    var post_data ={
                        'field_id' :  response.id,
                        'form_id' : $("#sales-form-fields #omc_sales_form_id").val(),
                        'groups' :  '',
                        'field_name' : $("#sales-form-fields #field_name").val(),
                        'field_type' : $("#sales-form-fields #field_type").val(),
                        'field_type_values' : $("#sales-form-fields #field_type_values").val(),
                        'field_required' : $("#sales-form-fields #field_required").val(),
                        'field_action_type' : $("#sales-form-fields #field_action_type").val()
                    };
                    alertify.success(txt);
                    self.update_form_fields(post_data)
                }
                //* When there are Errors *//*
                else if (response.code === 1) {
                    alertify.error(txt);
                    // $("#user_message_counter").html(0);
                }
            },
            error:function (xhr) {
                // console.log(xhr.responseText);
                jLib.serverError();
            }
        });
    },


    update_form_fields:function(post_data){
        var self = this;
        var form_id = post_data['form_id'];
        var field_id = post_data['field_id'];
        var field_action_type = post_data['field_action_type'];
        if(typeof $forms_fields[form_id] == "undefined"){
            return false;
        }
        if(field_action_type == 'field_save'){
            var data = {
                'id':post_data['field_id'],
                'form_id':post_data['form_id'],
                'groups':post_data['groups'],
                'field_name':post_data['field_name'],
                'field_type':post_data['field_type'],
                'field_type_values':post_data['field_type_values'],
                'field_required':post_data['field_required']
            };
            $forms_fields[form_id]['fields'][field_id] = data ;
        }
        if(field_action_type == 'field_delete'){
            delete $forms_fields[form_id]['fields'][field_id];
        }
        $("#sales-form-fields #field_reset").click();
        self.render_form_fields();
    },


    render_form_fields:function(){
        var self = this;
        var form_id = $("#sales-form-fields #omc_sales_form_id").val();
        if(typeof $forms_fields[form_id] == "undefined"){
            return false;
        }
        $("#form_field_list tbody").html('');
        var form_fields = $forms_fields[form_id]['fields'];
        for(var x in form_fields){
            var field = form_fields[x];
            var tr = $("<tr />");
            tr.attr('data-field_id',field['id']);
            tr.attr('data-form_id',field['form_id']);
            tr.attr('data-groups',field['groups']);
            tr.attr('data-field_name',field['field_name']);
            tr.attr('data-field_type',field['field_type']);
            tr.attr('data-field_type_values',field['field_type_values']);
            tr.attr('data-field_required',field['field_required']);

            var td = $("<td />").html(field['field_name']);
            tr.append(td);
            var td = $("<td />").html(field['field_type']);
            tr.append(td);
            var td = $("<td />").html(field['field_required']);
            tr.append(td);

            $("#form_field_list tbody").prepend(tr);
        }
    },


    preview_form:function(form_id){
        var self = this;
        var url = $("#sales-form-fields").attr('action');
        var query = "form_id="+form_id+"&form_action_type=form_preview";

        $.ajax({
            url:url,
            data:query,
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
                    var form_name = response.form_name;
                    $("#preview-form-window .preview-content").html(response.html);
                    $.colorbox({
                        inline:true,
                        scrolling:false,
                        overlayClose:false,
                        escKey:false,
                        top:'5%',
                        title:'Preview: '+form_name,
                        href:"#preview-form-window"
                    });
                    $('#preview-form-window').colorbox.resize();
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

};


/* when the page is loaded */
$(document).ready(function () {
    SalesForm.init();
});