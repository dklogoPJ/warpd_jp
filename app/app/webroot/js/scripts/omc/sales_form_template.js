var SalesForm = {
    datasource_category:[],
    datasource_sub_category:[],
    tr_edit:null,

    init:function () {
        var self = this;

        $("#sales-forms").validate({
            debug: false,
            submitHandler: function() { self.save_form(); }
        });
        $("#sales-form-fields").validate({
            debug: false,
            submitHandler: function() { self.save_form_fields(); }
        });

        $("#sales-form-primary-field-option").validate({
            debug: false,
            submitHandler: function() { self.save_form_primary_field_option(); }
        });

        self.bind_sales_forms();
        $("#sales-forms #form_reset").click();
        self.bind_form_fields();
        self.bind_form_primary_field_option();
        $("#sales-form-fields #field_reset").click();
        self.render_form_fields();
        $("#sales-form-primary-field-option #pf_option_reset").click();
        self.render_form_primary_field_option();
    },

    findAttributeInCollection: function (attribute, collection = []) {
        return collection.find(item =>  item.name === attribute );
    },

    getValue: function (attribute, collection = []) {
        var obj = this.findAttributeInCollection(attribute, collection);
        if(obj) {
            return obj.value;
        }
        return '';
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
            if(count === 0){
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
            var form_id = $(this).attr('data-form_id');
            var form = $forms_fields[form_id];
            $("#sales-forms #form_id").val(form_id);
            $("#sales-forms #menu_id").val(form.menu_id);
            $("#sales-forms #form_name").val(form.name);
            $("#sales-forms #form_order").val(form.order);
            $("#sales-forms #form_description").val(form.description);
            $("#sales-forms #form_primary_field").val(form.primary_field_name);
            if(form.omc_customer_list) {
                self.reset_form_customer_list(form.omc_customer_list.split(','));
            } else {
                self.reset_form_customer_list(['all']);
            }
        });

        $("#sales-forms #form_reset").click(function(){
            $("#sales-forms #menu_id").val('');
            $("#sales-forms #form_id").val('0');
            $("#sales-forms #form_name").val('');
            $("#sales-forms #form_order").val('');
            $("#sales-forms #form_description").val('');
            $("#sales-forms #form_primary_field").val('');
            $("#sales-forms #form_action_type").val('form_save');
            self.reset_form_customer_list(['all']);

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

            if(count === 0){
                alertify.alert('You Have To Select At Least One Form');
            }
            else{
                var ques = "Are You Sure You Want To Delete This Form ?";
                alertify.confirm( ques, function (e) {
                    if (e) {
                        $("#sales-forms #form_action_type").val('form_delete');
                        $("#sales-forms").submit();
                    }
                });
            }
        });

        $("#sales-forms #omc_customer_list").select2();
    },

    save_form:function(){
        var self = this;
        var $salesForms = $("#sales-forms");
        var url = $salesForms.attr('action');
        var form_customers_arr =  $("#sales-forms #form_omc_customer_list").val();
        var form_customers_str = form_customers_arr ? form_customers_arr.toString() : '';
        var query = $salesForms.serialize()+"&form_omc_customer_list_str="+form_customers_str;
        var formObjCollection = $salesForms.serializeArray();
        formObjCollection.push({name: 'omc_customer_list', value: form_customers_str});


       /* var field_action_sources_arr = $("#sales-form-fields #field_action_sources").val();
        var field_action_sources_str = field_action_sources_arr ? field_action_sources_arr.toString() : '';
        var field_action_targets_arr = $("#sales-form-fields #field_action_targets").val();
        var field_action_targets_str = field_action_targets_arr ? field_action_targets_arr.toString() : '';
        var query = $salesFormFields.serialize()+"&field_type_values="+field_type_values+"&field_action_sources_str="+field_action_sources_str+"&field_action_targets_str="+field_action_targets_str;
        var salesFormFieldsObjCollection = $salesFormFields.serializeArray();
        salesFormFieldsObjCollection.push({name: 'field_type_values', value: field_type_values});
        salesFormFieldsObjCollection.push({name: 'field_action_sources', value: field_action_sources_str});
        salesFormFieldsObjCollection.push({name: 'field_action_targets', value: field_action_targets_str});
*/


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

                    var post_data = {
                        'id' :  response.id,
                        'menu_id' : response.menu_id,
                        'name' : self.getValue('form_name', formObjCollection),
                        'order' : self.getValue('form_order', formObjCollection),
                        'description' : self.getValue('form_description', formObjCollection),
                        'primary_field_name' :  self.getValue('form_primary_field', formObjCollection),
                        'omc_customer_list' : self.getValue('omc_customer_list', formObjCollection),
                        'action_type' : self.getValue('form_action_type', formObjCollection),
                        'omc_id' : self.getValue('omc_id', formObjCollection)
                    };
                    alertify.success(txt);
                    self.update_form_data(post_data);
                    self.render_form_list();
                }
                //* When there are Errors *//*
                else if (response.code === 1) {
                    alertify.error(txt);
                }
            },
            error:function (xhr) {
                jLib.serverError();
            }
        });
    },

    update_form_data: function (post_data) {
        var self = this;
        var form_id = post_data['id'];
        var action_type = post_data['action_type'];

        if(action_type === 'form_save'){
            if(typeof $forms_fields[form_id] == "undefined"){
                console.log("Form is undefined adding to collection.");
                $forms_fields[form_id] = post_data;
                $forms_fields[form_id]['primary_field_options'] = [];
                $forms_fields[form_id]['fields'] = [];
            } else {
                $forms_fields[form_id]['menu_id'] = post_data['menu_id'];
                $forms_fields[form_id]['name'] = post_data['name'];
                $forms_fields[form_id]['description'] = post_data['description'];
                $forms_fields[form_id]['order'] = post_data['order'];
                $forms_fields[form_id]['primary_field_name'] = post_data['primary_field_name'];
                $forms_fields[form_id]['omc_customer_list'] = post_data['omc_customer_list'];
            }
        }
        if(action_type === 'form_delete'){
            delete $forms_fields[form_id];
        }
        self.update_form_options(post_data);
    },

    update_form_options: function(post_data){
        var self = this;
        var id = post_data['id'];
        var action_type = post_data['action_type'];
        if(action_type === 'form_save'){
            $sale_form_options[id] = post_data['name'];
        }
        else if(action_type === 'form_delete'){
            delete $sale_form_options[id];
        }
        //render new options
        var d_options = $sale_form_options;
        var select_ome_sales_form = document.getElementById('omc_sales_form_id');
        var select_pf_omc_sales_form = document.getElementById('pf_omc_sales_form_id');
        select_ome_sales_form.options.length = 0;
        select_pf_omc_sales_form.options.length = 0;
        for(var nx in d_options){
            var opt_form_field = document.createElement('option');
            var opt_pf_opt = document.createElement('option');
            opt_form_field.value = nx;
            opt_form_field.text = d_options[nx];
            opt_pf_opt.value = nx;
            opt_pf_opt.text = d_options[nx];
            try{ //Standard
                select_ome_sales_form.add(opt_form_field, null) ;
                select_pf_omc_sales_form.add(opt_pf_opt,null) ;
            }
            catch(error){ //IE Only
                select_ome_sales_form.add(opt_form_field) ;
                select_pf_omc_sales_form.add(opt_pf_opt) ;
            }
        }

        self.render_form_fields();
        self.render_form_primary_field_option();
    },

    render_form_list: function (){
        var self = this;

        var $salesFormListTbody = $("#sales_form_list tbody");
        $salesFormListTbody.html('');
        var collection = $forms_fields;
        var sortable = [];
        for (var item in collection) {
            sortable.push(collection[item]);
        }
        sortable.sort((a, b)=> a.order - b.order);

        for(var x in sortable){
            var form = sortable[x];
            var tr = $("<tr />");
            tr.attr('data-form_id',form['id']);

            var td = $("<td />").html(form['name']);
            tr.append(td);
            var td = $("<td />").html(form['description']);
            tr.append(td);
            var td = $("<td />").html(form['primary_field_name']);
            tr.append(td);
            var td = $("<td />").html(form['order']);
            tr.append(td);

            $salesFormListTbody.append(tr);
        }

        $("#sales-forms #form_reset").click();
    },

    bind_form_fields:function(){
        var self = this;

        $("#form_field_preview_btn").click(function(){
            var form_id = $("#sales-form-fields #omc_sales_form_id").val();
            self.preview_form(form_id);
        });

        $("#form_fields_tab").click(function(){
            var form_id = $("#sales-form-fields #omc_sales_form_id").val();
            self.reset_form_field_action_sources(form_id);
            self.reset_form_field_action_targets(form_id);
        });

        $("table#form_field_list tbody tr").live('click',function(){
            $("table#form_field_list tbody tr").removeClass('selected');
            self.tr_edit = $(this);
            $(this).addClass('selected');
            var form_id = $(this).attr('data-form_id');
            var field_id = $(this).attr('data-field_id');
            var field = $forms_fields[form_id]['fields'][field_id];
            $("#sales-form-fields #field_id").val(field.id);
            $("#sales-form-fields #field_name").val(field.field_name);
            $("#sales-form-fields #field_order").val(field.field_order);
            $("#sales-form-fields #field_event").val( field.field_event).change();
            $("#sales-form-fields #field_action").val( field.field_action);
            if(field.field_type === 'Text'){
                $("#sales-form-fields #field_type_text").prop("checked", true).click();
            }
            else if(field.field_type === 'Drop Down'){
                $("#sales-form-fields #field_type_dropdown").prop("checked", true).click();
            }
            $("#sales-form-fields #field_required").val(field.field_required);
            $("#sales-form-fields #field_disabled").val(field.field_disabled).change();
        });

        $("#sales-form-fields #field_reset").click(function(){
            $("#sales-form-fields #field_id").val('0');
            $("#sales-form-fields #field_name").val('');
            $("#sales-form-fields #field_order").val('');
            $("#sales-form-fields #field_event").val('none').change();
            $("#sales-form-fields #field_action").val('none');
            $("#sales-form-fields #field_type").val('Text');
            $("#sales-form-fields #field_type_text").prop("checked", true).click();
            $("#sales-form-fields #field_required").val('No');
            $("#sales-form-fields #field_disabled").val('n');
            $("#sales-form-fields #field_action_type").val('field_save');
            $('#field_type_values').importTags('');
            var form_id = $("#sales-form-fields #omc_sales_form_id").val();
            self.reset_form_field_action_sources(form_id);
            self.reset_form_field_action_targets(form_id);
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
            if(count === 0){
                alertify.alert('You Have To Select At Least One Field');
            }
            else{
                var ques = "Are You Sure You Want To Delete This Field ?";
                alertify.confirm( ques, function (e) {
                    if (e) {
                        $("#sales-form-fields #field_action_type").val('field_delete');
                        $("#sales-form-fields").submit();
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
            var $fieldDisabled = $("#sales-form-fields #field_disabled");
            $fieldDisabled.find('option').removeAttr("disabled");
            $fieldDisabled.val('n').change();
            var $fieldEvent = $("#sales-form-fields #field_event");
            $fieldEvent.find('option').removeAttr("disabled");
            $("#drop_down_options").hide('slow');
            $("#sales-form-fields").validate().cancelSubmit = false;
            $('#field_type_values').importTags('');
        });

        $("#sales-form-fields #field_type_dropdown").click(function(){
            var val = $(this).val();
            $("#sales-form-fields #field_type").val(val);
            //Disable field disabled property for dropdown element
            var $fieldDisabled = $("#sales-form-fields #field_disabled");
            $fieldDisabled.find('option[value="y"]').attr("disabled","disabled");
            $fieldDisabled.val('n').change();

            //Disable all event options except 'None' and 'Select'
            var $fieldEvent = $("#sales-form-fields #field_event");
            ['focus','focusout','keyup'].forEach( e => {
                $fieldEvent.find('option[value="' + e + '"]').attr("disabled","disabled");
            });

            $("#drop_down_options").show('slow');
           // $("#sales-form-fields").validate().cancelSubmit = true;
            if(self.tr_edit){
                var form_id = self.tr_edit.attr('data-form_id');
                var field_id = self.tr_edit.attr('data-field_id');
                var field = $forms_fields[form_id]['fields'][field_id];
                $('#field_type_values').importTags(field.field_type_values); //'foo,bar,baz'
            }
        });

        $("#sales-form-fields #field_event").change(function(){
            var val = $(this).val();
            var form_id = $("#sales-form-fields #omc_sales_form_id").val();
            if(val === 'none' ) {
                $(".field_event_wrapper").hide('slow');
                $("#sales-form-fields #field_action").val('none');
                self.reset_form_field_action_sources(form_id);
                self.reset_form_field_action_targets(form_id);
            } else {
                $(".field_event_wrapper").show('slow');
                if(self.tr_edit){
                    var field_id = self.tr_edit.attr('data-field_id');
                    var field = $forms_fields[form_id]['fields'][field_id];
                    var action_sources_ids = field.field_action_sources;
                    if(action_sources_ids) {
                        self.reset_form_field_action_sources(form_id, action_sources_ids.split(','));
                    } else {
                        self.reset_form_field_action_sources(form_id);
                    }
                    var action_targets_ids = field.field_action_targets;
                    if(action_targets_ids) {
                        self.reset_form_field_action_targets(form_id, action_targets_ids.split(','));
                    } else {
                        self.reset_form_field_action_targets(form_id);
                    }
                }
            }
        });

        $("#sales-form-fields #field_disabled").change(function(){
            var val = $(this).val();
            var $fieldEvent = $("#sales-form-fields #field_event");
            //$fieldEvent.removeAttr("disabled");
            $fieldEvent.find('option').removeAttr("disabled");
            if(val === 'y' ) {
                //Disable all other event options except Focus In
                ['none','focusout','keyup','change'].forEach( e => {
                    $fieldEvent.find('option[value="' + e + '"]').attr("disabled","disabled");
                });
                $fieldEvent.val('focus').change();
            }
        });

        //TODO Re implement this, it's causing conflict with action sources hide and show from other event changes
     /*   $("#sales-form-fields #field_action").change(function(){
            var val = $(this).val();
            var $fieldActionSourcesContainer = $("#field_action_sources_wrapper");
            $fieldActionSourcesContainer.show('slow');
            if(val === 'price_change' ) {
                $fieldActionSourcesContainer.hide('slow');
            }
        });*/

        $("#sales-form-fields #field_action_sources").select2();
        $("#sales-form-fields #field_action_targets").select2();
    },

    save_form_fields:function(){
        var self = this;
        var $salesFormFields = $("#sales-form-fields");
        var url = $salesFormFields.attr('action');
        var field_type_values = $("#sales-form-fields #field_type_values").val();
        var field_action_sources_arr = $("#sales-form-fields #field_action_sources").val();
        var field_action_sources_str = field_action_sources_arr ? field_action_sources_arr.toString() : '';
        var field_action_targets_arr = $("#sales-form-fields #field_action_targets").val();
        var field_action_targets_str = field_action_targets_arr ? field_action_targets_arr.toString() : '';
        var query = $salesFormFields.serialize()+"&field_type_values="+field_type_values+"&field_action_sources_str="+field_action_sources_str+"&field_action_targets_str="+field_action_targets_str;
        var salesFormFieldsObjCollection = $salesFormFields.serializeArray();
        salesFormFieldsObjCollection.push({name: 'field_type_values', value: field_type_values});
        salesFormFieldsObjCollection.push({name: 'field_action_sources', value: field_action_sources_str});
        salesFormFieldsObjCollection.push({name: 'field_action_targets', value: field_action_targets_str});

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
                        'form_id' : self.getValue('omc_sales_form_id', salesFormFieldsObjCollection),
                        'field_name' : self.getValue('field_name', salesFormFieldsObjCollection),
                        'field_order' : self.getValue('field_order', salesFormFieldsObjCollection),
                        'field_type' : self.getValue('field_type', salesFormFieldsObjCollection),
                        'field_type_values' : self.getValue('field_type_values', salesFormFieldsObjCollection),
                        'field_required' : self.getValue('field_required', salesFormFieldsObjCollection),
                        'field_disabled' : self.getValue('field_disabled', salesFormFieldsObjCollection),
                        'field_event' : self.getValue('field_event', salesFormFieldsObjCollection),
                        'field_action' : self.getValue('field_action', salesFormFieldsObjCollection),
                        'field_action_sources' : self.getValue('field_action_sources', salesFormFieldsObjCollection),
                        'field_action_targets' : self.getValue('field_action_targets', salesFormFieldsObjCollection),
                        'field_action_type' : self.getValue('field_action_type', salesFormFieldsObjCollection)
                    };
                    alertify.success(txt);
                    self.update_form_fields(post_data)
                }
                //* When there are Errors *//*
                else if (response.code === 1) {
                    alertify.error(txt);
                }
            },
            error:function (xhr) {
                jLib.serverError();
            }
        });
    },

    update_form_fields:function(post_data){
        var self = this;
        var form_id = post_data['form_id'];
        var field_id = post_data['id'];
        var field_action_type = post_data['field_action_type'];

        if(field_action_type === 'field_save'){
            if(typeof $forms_fields[form_id]['fields'][field_id] == "undefined"){
                $forms_fields[form_id]['fields'][field_id] = post_data;
            } else {
                $forms_fields[form_id]['fields'][field_id] = {
                    'id': post_data['id'],
                    'form_id': post_data['form_id'],
                    'field_name': post_data['field_name'],
                    'field_order': post_data['field_order'],
                    'field_type': post_data['field_type'],
                    'field_type_values': post_data['field_type_values'],
                    'field_required': post_data['field_required'],
                    'field_disabled': post_data['field_disabled'],
                    'field_event': post_data['field_event'],
                    'field_action': post_data['field_action'],
                    'field_action_sources': post_data['field_action_sources'],
                    'field_action_targets': post_data['field_action_targets']
                };
            }
        }
        if(field_action_type === 'field_delete'){
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

        var $formFieldListTbody = $("#form_field_list tbody");
        $formFieldListTbody.html('');
        var collection = $forms_fields[form_id]['fields'];
        var sortable = [];
        for (var item in collection) {
            sortable.push(collection[item]);
        }
        sortable.sort((a, b)=> a.field_order - b.field_order);
        for(var x in sortable){
            var field = sortable[x];
            var tr = $("<tr />");
            tr.attr('data-field_id',field['id']);
            tr.attr('data-form_id',field['form_id']);

            var td = $("<td />").html(field['field_name']);
            tr.append(td);
            var td = $("<td />").html(field['field_type']);
            tr.append(td);
            var td = $("<td />").html(field['field_order']);
            tr.append(td);
            var td = $("<td />").html(field['field_required']);
            tr.append(td);

            $formFieldListTbody.append(tr);
        }
    },

    reset_select_option: function (collection, value_prop, text_prop, jquerySelect2Obj, select_dom_id, selected_ids){
        jquerySelect2Obj.select2('destroy');
            var select = document.getElementById(select_dom_id);
            select.options.length = 0;
            for(var nx in collection){
                var opt = document.createElement('option');
                opt.value = collection[nx][value_prop];
                opt.text = collection[nx][text_prop];
                opt.selected = selected_ids.indexOf(collection[nx].id) >= 0
                try{ //Standard
                    select.add(opt,null) ;
                }
                catch(error){ //IE Only
                    select.add(opt) ;
                }
            }
        jquerySelect2Obj.select2();
    },

    reset_form_customer_list: function (selected_ids=[]){
        var jquerySelect2Obj = $("#sales-forms #form_omc_customer_list");
        this.reset_select_option($customers, 'id', 'name', jquerySelect2Obj, 'form_omc_customer_list', selected_ids);
    },

    reset_form_field_action_sources: function (form_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-form-fields #field_action_sources");
        var fields = $forms_fields[form_id]['fields'];
        this.reset_select_option(fields, 'id', 'field_name', jquerySelect2Obj, 'field_action_sources', selected_ids);
    },

    reset_form_field_action_targets: function (form_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-form-fields #field_action_targets");
        var fields = $forms_fields[form_id]['fields'];
        this.reset_select_option(fields, 'id', 'field_name', jquerySelect2Obj, 'field_action_targets', selected_ids);
    },

    reset_primary_field_total_options_list: function (form_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-form-primary-field-option #pf_total_option_list");
        var pf_options = $forms_fields[form_id]['primary_field_options'];
        this.reset_select_option(pf_options, 'id', 'option_name', jquerySelect2Obj, 'pf_total_option_list', selected_ids);
    },

    reset_primary_field_total_fields_list: function (form_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-form-primary-field-option #pf_total_field_list");
        var fields = $forms_fields[form_id]['fields'];
        this.reset_select_option(fields, 'id', 'field_name', jquerySelect2Obj, 'pf_total_field_list', selected_ids);
    },

    bind_form_primary_field_option:function(){
        var self = this;

        $("#form_primary_field_tab").click(function(){
            var form_id = $("#sales-form-primary-field-option #pf_omc_sales_form_id").val();
            self.reset_primary_field_total_options_list(form_id);
            self.reset_primary_field_total_fields_list(form_id);
            $("#sales-form-primary-field-option #primary_field_html b").html($forms_fields[form_id].primary_field_name);
        });

        $("table#primary-field-option_list_table tbody tr").live('click',function(){
            $("table#primary-field-option_list_table tr").removeClass('selected');
            self.tr_edit = $(this);
            $(this).addClass('selected');
            var form_id = $(this).attr('data-form_id');
            var option_id = $(this).attr('data-pf_option_id');
            var pf_option = $forms_fields[form_id]['primary_field_options'][option_id];

            $("#sales-form-primary-field-option #pf_option_id").val(pf_option.id);
            $("#sales-form-primary-field-option #pf_option_name").val(pf_option.option_name);
            $("#sales-form-primary-field-option #pf_order").val(pf_option.order);
            $("#sales-form-primary-field-option #pf_product_type_id").val(pf_option.product_type_id);
            if(pf_option.is_total === 'no'){
                $("#sales-form-primary-field-option #pf_is_total_no").prop("checked", true).click();
            }
            else if(pf_option.is_total === 'yes'){
                $("#sales-form-primary-field-option #pf_is_total_yes").prop("checked", true).click();
            }
        });

        $("#sales-form-primary-field-option #pf_option_reset").click(function(){
            $("#sales-form-primary-field-option #pf_option_id").val('0');
            $("#sales-form-primary-field-option #pf_option_name").val('');
            $("#sales-form-primary-field-option #pf_product_type_id").val('');
            $("#sales-form-primary-field-option #pf_order").val('');
            $("#sales-form-primary-field-option #pf_option_is_total").val('no');
            $("#sales-form-primary-field-option #pf_is_total_no").prop("checked", true).click();
            $("#sales-form-primary-field-option #pf_option_action_type").val('option_save');
            var form_id = $("#sales-form-primary-field-option #pf_omc_sales_form_id").val();
            self.reset_primary_field_total_options_list(form_id);
            self.reset_primary_field_total_fields_list(form_id);
            $("table#primary-field-option_list_table tbody tr").removeClass('selected');
            self.tr_edit = null;
        });

        $("#pf_option_delete_btn").click(function(){
            var count = 0;
            $("table#primary-field-option_list_table tbody tr").each(function(){
                if($(this).hasClass('selected')){
                    count = count + 1;
                }
            });
            if(count === 0){
                alertify.alert('You Have To Select At Least One Option');
            }
            else{
                var ques = "Are You Sure You Want To Delete This Option ?";
                alertify.confirm( ques, function (e) {
                    if (e) {
                        $("#sales-form-primary-field-option #pf_option_action_type").val('option_delete');
                        $("#sales-form-primary-field-option").submit();
                    }
                });
            }
        });


        $("#sales-form-primary-field-option #pf_omc_sales_form_id").change(function(){
            $("#sales-form-primary-field-option #primary_field_html b").html($forms_fields[$(this).val()].primary_field_name);
            self.reset_primary_field_total_options_list($(this).val());
            self.reset_primary_field_total_fields_list($(this).val());
            $("#sales-form-primary-field-option #pf_option_reset").click();
            self.render_form_primary_field_option();
        });

        $("#sales-form-primary-field-option #pf_is_total_no").click(function(){
            var val = $(this).val();
            $("#sales-form-primary-field-option #pf_option_is_total").val(val);
            $(".pf_total_options_and_fields_wrapper").hide('slow');
            $("#sales-form-primary-field-option").validate().cancelSubmit = false;
            var form_id = $("#sales-form-primary-field-option #pf_omc_sales_form_id").val();
            self.reset_primary_field_total_options_list(form_id);
            self.reset_primary_field_total_fields_list(form_id);
        });

        $("#sales-form-primary-field-option #pf_is_total_yes").click(function(){
            var val = $(this).val();
            $("#sales-form-primary-field-option #pf_option_is_total").val(val);
            $(".pf_total_options_and_fields_wrapper").show('slow');
            //$("#sales-form-primary-field-option").validate().cancelSubmit = true;
            if(self.tr_edit){
                var form_id = self.tr_edit.attr('data-form_id');
                var option_id = self.tr_edit.attr('data-pf_option_id');
                var pf_option = $forms_fields[form_id]['primary_field_options'][option_id];
                if(pf_option) {
                    if(pf_option.total_option_list) {
                        self.reset_primary_field_total_options_list(form_id, pf_option.total_option_list.split(','));
                    } else {
                        self.reset_primary_field_total_options_list(form_id);
                    }

                    if(pf_option.total_field_list) {
                        self.reset_primary_field_total_fields_list(form_id, pf_option.total_field_list.split(','));
                    } else {
                        self.reset_primary_field_total_fields_list(form_id);
                    }
                } else {
                    self.reset_primary_field_total_fields_list(form_id);
                }
            }
        });

        $("#sales-form-primary-field-option #pf_total_option_list").select2();
        $("#sales-form-primary-field-option #pf_total_field_list").select2();
    },


    save_form_primary_field_option:function(){
        var self = this;
        var $salesFormPrimaryFieldOption = $("#sales-form-primary-field-option");
        var url = $salesFormPrimaryFieldOption.attr('action');
        var opts_arr = $("#sales-form-primary-field-option #pf_total_option_list").val();
        var opts_str = opts_arr ? opts_arr.toString() : '';
        var field_arr = $("#sales-form-primary-field-option #pf_total_field_list").val();
        var field_str = field_arr ? field_arr.toString() : '';
        var query = $salesFormPrimaryFieldOption.serialize()+"&pf_total_option_list="+opts_str+"&pf_total_field_list="+field_str;
        var salesFormPrimaryFieldOptionObjCollection = $salesFormPrimaryFieldOption.serializeArray();
        salesFormPrimaryFieldOptionObjCollection.push({name: 'pf_total_option_list', value: opts_str})
        salesFormPrimaryFieldOptionObjCollection.push({name: 'pf_total_field_list', value: field_str})

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
                        'option_id' :  response.id,
                        'form_id' : self.getValue('pf_omc_sales_form_id', salesFormPrimaryFieldOptionObjCollection),
                        'option_name' : self.getValue('pf_option_name', salesFormPrimaryFieldOptionObjCollection),
                        'product_type_id' : self.getValue('pf_product_type_id', salesFormPrimaryFieldOptionObjCollection),
                        'order' : self.getValue('pf_order', salesFormPrimaryFieldOptionObjCollection),
                        'is_total' : self.getValue('pf_option_is_total', salesFormPrimaryFieldOptionObjCollection),
                        'total_option_list' : self.getValue('pf_total_option_list', salesFormPrimaryFieldOptionObjCollection),
                        'total_field_list' : self.getValue('pf_total_field_list', salesFormPrimaryFieldOptionObjCollection),
                        'option_action_type' : self.getValue('pf_option_action_type', salesFormPrimaryFieldOptionObjCollection)
                    };
                    alertify.success(txt);
                    self.update_primary_field_options(post_data)
                }
                //* When there are Errors *//*
                else if (response.code === 1) {
                    alertify.error(txt);
                }
            },
            error:function (xhr) {
                jLib.serverError();
            }
        });
    },


    update_primary_field_options:function(post_data){
        var self = this;
        var form_id = post_data['form_id'];
        var option_id = post_data['option_id'];
        var action_type = post_data['option_action_type'];
        if(typeof $forms_fields[form_id] == "undefined"){
            return false;
        }
        if(action_type === 'option_save'){
            $forms_fields[form_id]['primary_field_options'][option_id] = {
                'id': option_id,
                'form_id': post_data['form_id'],
                'option_name': post_data['option_name'],
                'product_type_id': post_data['product_type_id'],
                'order': post_data['order'],
                'is_total': post_data['is_total'],
                'total_option_list': post_data['total_option_list'],
                'total_field_list': post_data['total_field_list']
            };
        }
        if(action_type === 'option_delete'){
            delete $forms_fields[form_id]['primary_field_options'][option_id];
        }
        $("#sales-form-primary-field-option #pf_option_reset").click();
        self.render_form_primary_field_option();
    },

    render_form_primary_field_option:function(){
        var self = this;
        var form_id = $("#sales-form-primary-field-option #pf_omc_sales_form_id").val();
        if(typeof $forms_fields[form_id] == "undefined"){
            return false;
        }
        var $primaryFieldOptionListTableTbody = $("#primary-field-option_list_table tbody");
        $primaryFieldOptionListTableTbody.html('');
        var collection = $forms_fields[form_id]['primary_field_options'];
        var sortable = [];
        for (var item in collection) {
            sortable.push(collection[item]);
        }
        sortable.sort((a, b)=> a.order - b.order);
        for(var x in sortable){
            var pf_option = sortable[x];
            if(pf_option) {
                var tr = $("<tr />");
                tr.attr('data-pf_option_id',pf_option['id']);
                tr.attr('data-form_id',pf_option['form_id']);

                var td = $("<td />").html(pf_option['option_name']);
                tr.append(td);
                var td = $("<td />").html(pf_option['is_total']);
                tr.append(td);
                var td = $("<td />").html(pf_option['order']);
                tr.append(td);

                $primaryFieldOptionListTableTbody.append(tr);
            }
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