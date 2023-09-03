var SalesReport = {
    datasource_category:[],
    datasource_sub_category:[],
    tr_edit:null,

    init:function () {
        var self = this;

        $("#sales-reports").validate({
            debug: false,
            submitHandler: function() { self.save_report(); }
        });
        $("#sales-report-fields").validate({
            debug: false,
            submitHandler: function() { self.save_report_fields(); }
        });

        $("#sales-report-primary-field-option").validate({
            debug: false,
            submitHandler: function() { self.save_report_primary_field_option(); }
        });

        self.bind_sales_reports();
        $("#sales-reports #report_reset").click();
        self.bind_report_fields();
        self.bind_report_primary_field_option();
        $("#sales-report-fields #field_reset").click();
        self.render_report_fields();
        $("#sales-report-primary-field-option #pf_option_reset").click();
        self.render_report_primary_field_option();
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

    bind_sales_reports:function(){
        var self = this;

        $("#report_preview_btn").click(function(){
            var count = 0;
            $("table#sales_report_list tbody tr").each(function(){
                if($(this).hasClass('selected')){
                    count = count + 1;
                }
            });
            if(count === 0){
                alertify.alert('You Have To Select At Least One Report For Preview');
            }
            else{
                var report_id = self.tr_edit.attr('data-report_id') ;
                self.preview_report(report_id);
            }
        });

        $("table#sales_report_list tbody tr").live('click',function(){
            $("table#sales_report_list tbody tr").removeClass('selected');

            self.tr_edit = $(this);
            $(this).addClass('selected');
            var report_id = $(this).attr('data-report_id');
            var report = $reports_fields[report_id];
            $("#sales-reports #report_id").val(report_id);
            $("#sales-reports #menu_id").val(report.menu_id);
            $("#sales-reports #report_name").val(report.name);
            $("#sales-reports #report_order").val(report.order);
            $("#sales-reports #report_description").val(report.description);
            $("#sales-reports #report_primary_field").val(report.primary_field_name);
            if(report.omc_customer_list) {
                self.reset_report_customer_list(report.omc_customer_list.split(','));
            } else {
                self.reset_report_customer_list(['all']);
            }
        });

        $("#sales-reports #report_reset").click(function(){
            $("#sales-reports #menu_id").val('');
            $("#sales-reports #report_id").val('0');
            $("#sales-reports #report_name").val('');
            $("#sales-reports #report_order").val('');
            $("#sales-reports #report_description").val('');
            $("#sales-reports #report_primary_field").val('');
            $("#sales-reports #report_action_type").val('report_save');
            self.reset_report_customer_list(['all']);

            $("table#sales_report_list tbody tr").removeClass('selected');
            self.tr_edit = null;
        });

        $("#report_delete_btn").click(function(){
            var count = 0;
            $("table#sales_report_list tbody tr").each(function(){
                if($(this).hasClass('selected')){
                    count = count + 1;
                }
            });

            if(count === 0){
                alertify.alert('You Have To Select At Least One Report');
            }
            else{
                var ques = "Are You Sure You Want To Delete This Report ?";
                alertify.confirm( ques, function (e) {
                    if (e) {
                        $("#sales-reports #report_action_type").val('report_delete');
                        $("#sales-reports").submit();
                    }
                });
            }
        });

        $("#sales-reports #omc_customer_list").select2();
    },

    save_report:function(){
        var self = this;
        var $salesReports = $("#sales-reports");
        var url = $salesReports.attr('action');
        var report_customers_arr =  $("#sales-reports #report_omc_customer_list").val();
        var report_customers_str = report_customers_arr ? report_customers_arr.toString() : '';
        var query = $salesReports.serialize()+"&report_omc_customer_list_str="+report_customers_str;
        var reportObjCollection = $salesReports.serializeArray();
        reportObjCollection.push({name: 'omc_customer_list', value: report_customers_str});

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
                        'name' : self.getValue('report_name', reportObjCollection),
                        'order' : self.getValue('report_order', reportObjCollection),
                        'description' : self.getValue('report_description', reportObjCollection),
                        'primary_field_name' :  self.getValue('report_primary_field', reportObjCollection),
                        'omc_customer_list' : self.getValue('omc_customer_list', reportObjCollection),
                        'action_type' : self.getValue('report_action_type', reportObjCollection),
                        'omc_id' : self.getValue('omc_id', reportObjCollection)
                    };
                    alertify.success(txt);
                    self.update_report_data(post_data);
                    self.render_report_list();
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

    update_report_data: function (post_data) {
        var self = this;
        var report_id = post_data['id'];
        var action_type = post_data['action_type'];

        if(action_type === 'report_save'){
            if(typeof $reports_fields[report_id] == "undefined"){
                console.log("Report is undefined adding to collection.");
                $reports_fields[report_id] = post_data;
                $reports_fields[report_id]['primary_field_options'] = [];
                $reports_fields[report_id]['fields'] = [];
            } else {
                $reports_fields[report_id]['menu_id'] = post_data['menu_id'];
                $reports_fields[report_id]['name'] = post_data['name'];
                $reports_fields[report_id]['description'] = post_data['description'];
                $reports_fields[report_id]['order'] = post_data['order'];
                $reports_fields[report_id]['primary_field_name'] = post_data['primary_field_name'];
                $reports_fields[report_id]['omc_customer_list'] = post_data['omc_customer_list'];
            }
        }
        if(action_type === 'report_delete'){
            delete $reports_fields[report_id];
        }
        self.update_report_options(post_data);
    },

    update_report_options: function(post_data){
        var self = this;
        var id = post_data['id'];
        var action_type = post_data['action_type'];
        if(action_type === 'report_save'){
            $sale_report_options[id] = post_data['name'];
        }
        else if(action_type === 'report_delete'){
            delete $sale_report_options[id];
        }
        //render new options
        var d_options = $sale_report_options;
        var select_ome_sales_report = document.getElementById('omc_sales_report_id');
        var select_pf_omc_sales_report = document.getElementById('pf_omc_sales_report_id');
        select_ome_sales_report.options.length = 0;
        select_pf_omc_sales_report.options.length = 0;
        for(var nx in d_options){
            var opt_report_field = document.createElement('option');
            var opt_pf_opt = document.createElement('option');
            opt_report_field.value = nx;
            opt_report_field.text = d_options[nx];
            opt_pf_opt.value = nx;
            opt_pf_opt.text = d_options[nx];
            try{ //Standard
                select_ome_sales_report.add(opt_report_field, null) ;
                select_pf_omc_sales_report.add(opt_pf_opt,null) ;
            }
            catch(error){ //IE Only
                select_ome_sales_report.add(opt_report_field) ;
                select_pf_omc_sales_report.add(opt_pf_opt) ;
            }
        }

        self.render_report_fields();
        self.render_report_primary_field_option();
    },

    render_report_list: function (){
        var $salesReportListTbody = $("#sales_report_list tbody");
        $salesReportListTbody.html('');
        var collection = $reports_fields;
        var sortable = [];
        for (var item in collection) {
            sortable.push(collection[item]);
        }
        sortable.sort((a, b)=> a.order - b.order);

        for(var x in sortable){
            var report = sortable[x];
            var tr = $("<tr />");
            tr.attr('data-report_id',report['id']);

            var td = $("<td />").html(report['name']);
            tr.append(td);
            var td = $("<td />").html(report['description']);
            tr.append(td);
            var td = $("<td />").html(report['primary_field_name']);
            tr.append(td);
            var td = $("<td />").html(report['order']);
            tr.append(td);

            $salesReportListTbody.append(tr);
        }

        $("#sales-reports #report_reset").click();
    },

    bind_report_fields:function(){
        var self = this;

        $("#report_field_preview_btn").click(function(){
            var report_id = $("#sales-report-fields #omc_sales_report_id").val();
            self.preview_report(report_id);
        });

        $("#report_fields_tab").click(function(){
            var report_id = $("#sales-report-fields #omc_sales_report_id").val();
            $("#sales-report-fields #report_dsrp_form").change();
            self.reset_report_field_action_targets(report_id);
        });

        $("table#report_field_list tbody tr").live('click',function(){
            $("table#report_field_list tbody tr").removeClass('selected');
            self.tr_edit = $(this);
            $(this).addClass('selected');
            var report_id = $(this).attr('data-report_id');
            var report_field_id = $(this).attr('data-report_field_id');
            var report_field = $reports_fields[report_id]['fields'][report_field_id];
            $("#sales-report-fields #report_field_id").val(report_field.id);
            $("#sales-report-fields #report_field_name").val(report_field.report_field_name);
            $("#sales-report-fields #report_field_order").val(report_field.report_field_order);
            $("#sales-report-fields #report_dsrp_form").val(report_field.report_dsrp_form).change();
        });

        $("#sales-report-fields #report_field_reset").click(function(){
            $("#sales-report-fields #report_field_id").val('0');
            $("#sales-report-fields #report_field_name").val('');
            $("#sales-report-fields #report_field_order").val('');
            $("#sales-report-fields #report_dsrp_form").val('').change();
            $("#sales-report-fields #report_field_action_type").val('report_field_save');
            var report_id = $("#sales-report-fields #omc_sales_report_id").val();
            self.reset_report_field_action_targets(report_id);
            $("table#report_field_list tbody tr").removeClass('selected');
            self.tr_edit = null;
        });

        $("#report_field_delete_btn").click(function(){
            var count = 0;
            $("table#report_field_list tbody tr").each(function(){
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
                        $("#sales-report-fields #field_action_type").val('field_delete');
                        $("#sales-report-fields").submit();
                    }
                });
            }
        });


        $("#sales-report-fields #omc_sales_report_id").change(function(){
            $("#sales-report-fields #report_field_reset").click();
            self.render_report_fields();
        });

        $("#sales-report-fields #report_dsrp_form").change(function(){
            var dsrp_form_id = $(this).val();
            var report_id = $("#sales-report-fields #omc_sales_report_id").val();
            if(self.tr_edit){
                report_id = self.tr_edit.attr('data-report_id');
                var report_field_id = self.tr_edit.attr('data-report_field_id');
                var report_field = $reports_fields[report_id]['fields'][report_field_id];
                if(report_field) {
                    if(report_field.report_dsrp_fields) {
                        self.reset_report_dsrp_fields(dsrp_form_id, report_field.report_dsrp_fields.split(','));
                    } else {
                        self.reset_report_dsrp_fields(dsrp_form_id);
                    }
                } else {
                    self.reset_report_dsrp_fields(dsrp_form_id);
                }
            } else {
                self.reset_report_dsrp_fields(dsrp_form_id);
            }
        });

        $("#sales-report-fields #report_dsrp_fields").select2();
        $("#sales-report-fields #report_field_action_targets").select2();
    },

    save_report_fields:function(){
        var self = this;
        var $salesReportFields = $("#sales-report-fields");
        var url = $salesReportFields.attr('action');
        var report_dsrp_fields_arr_obj = $("#sales-report-fields #report_dsrp_fields").select2("data");
        var report_dsrp_fields_arr = report_dsrp_fields_arr_obj.map((x) => {return x.id});
        var report_dsrp_fields_str = report_dsrp_fields_arr ? report_dsrp_fields_arr.toString() : '';
        var report_field_action_targets_arr = $("#sales-report-fields #report_field_action_targets").val();
        var report_field_action_targets_str = report_field_action_targets_arr ? report_field_action_targets_arr.toString() : '';
        var query = $salesReportFields.serialize()+"&report_dsrp_fields_str="+report_dsrp_fields_str+"&report_field_action_targets_str="+report_field_action_targets_str;
        var salesReportFieldsObjCollection = $salesReportFields.serializeArray();
        salesReportFieldsObjCollection.push({name: 'report_dsrp_fields', value: report_dsrp_fields_str});
        salesReportFieldsObjCollection.push({name: 'report_field_action_targets', value: report_field_action_targets_str});

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
                        'report_id' : self.getValue('omc_sales_report_id', salesReportFieldsObjCollection),
                        'report_field_name' : self.getValue('report_field_name', salesReportFieldsObjCollection),
                        'report_field_order' : self.getValue('report_field_order', salesReportFieldsObjCollection),
                        'report_dsrp_form' : self.getValue('report_dsrp_form', salesReportFieldsObjCollection),
                        'report_dsrp_fields' : self.getValue('report_dsrp_fields', salesReportFieldsObjCollection),
                        'report_field_action_targets' : self.getValue('report_field_action_targets', salesReportFieldsObjCollection),
                        'report_field_action_type' : self.getValue('report_field_action_type', salesReportFieldsObjCollection)
                    };
                    alertify.success(txt);
                    self.update_report_fields(post_data)
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

    update_report_fields:function(post_data){
        var self = this;
        var report_id = post_data['report_id'];
        var report_field_id = post_data['id'];
        var report_field_action_type = post_data['report_field_action_type'];

        if(report_field_action_type === 'report_field_save'){
            if(typeof $reports_fields[report_id]['fields'][report_field_id] == "undefined"){
                $reports_fields[report_id]['fields'][report_field_id] = post_data;
            } else {
                $reports_fields[report_id]['fields'][report_field_id] = {
                    'id': post_data['id'],
                    'report_id': post_data['report_id'],
                    'report_field_name': post_data['report_field_name'],
                    'report_field_order': post_data['report_field_order'],
                    'report_dsrp_form': post_data['report_dsrp_form'],
                    'report_dsrp_fields': post_data['report_dsrp_fields'],
                    'report_field_action_targets': post_data['report_field_action_targets']
                };
            }
        }
        if(report_field_action_type === 'report_field_delete'){
            delete $reports_fields[report_id]['fields'][report_field_id];
        }
        $("#sales-report-fields #report_field_reset").click();
        self.render_report_fields();
    },

    render_report_fields:function(){
        var self = this;
        var report_id = $("#sales-report-fields #omc_sales_report_id").val();
        if(typeof $reports_fields[report_id] == "undefined"){
            return false;
        }

        var $reportFieldListTbody = $("#report_field_list tbody");
        $reportFieldListTbody.html('');
        var collection = $reports_fields[report_id]['fields'];
        var sortable = [];
        for (var item in collection) {
            sortable.push(collection[item]);
        }
        sortable.sort((a, b)=> a.field_order - b.field_order);
        for(var x in sortable){
            var field = sortable[x];
            var tr = $("<tr />");
            tr.attr('data-report_field_id',field['id']);
            tr.attr('data-report_id',field['report_id']);

            var td = $("<td />").html(field['report_field_name']);
            tr.append(td);
            /*var td = $("<td />").html(field['field_type']);
            tr.append(td);*/
            var td = $("<td />").html(field['report_field_order']);
            tr.append(td);
           /* var td = $("<td />").html(field['field_required']);
            tr.append(td);*/

            $reportFieldListTbody.append(tr);
        }
    },

    reset_select_option: function (collection, value_prop, text_prop, select_dom_id, selected_id) {
        var select = document.getElementById(select_dom_id);
        select.options.length = 0;
        for(var nx in collection) {
            var opt = document.createElement('option');
            opt.value = collection[nx][value_prop];
            opt.text = collection[nx][text_prop];
            opt.selected = collection[nx][value_prop] === selected_id;
            try{ //Standard
                select.add(opt,null) ;
            }
            catch(error){ //IE Only
                select.add(opt) ;
            }
        }
    },

    reset_option_link_ids:function (report_pf_option_link_type, selected_id = null) {
        var jquerySelect2Obj = $("#sales-report-primary-field-option #report_pf_option_link_id");
        var init_arr = [{'id':'', 'name':'None'}];
        var collection = [];

        var link_type = $all_option_link_types.find(item => item.id === report_pf_option_link_type);
        if(link_type) {
            collection = init_arr.concat(link_type['data']);
        } else {
            collection = init_arr.concat([]);
        }
        this.reset_select_option(collection, 'id', 'name', 'report_pf_option_link_id', selected_id);
    },

    reset_select2_option: function (collection, value_prop, text_prop, jquerySelect2Obj, select_dom_id, selected_ids){
        jquerySelect2Obj.select2('destroy');
        var new_collection = [];
        //convert collection to array of objects
        for(var nx in collection) {
            new_collection.push(collection[nx]);
        }

        var sorted_collection = []
        var selected_items_collection = []
        //Filter out the items that where selected. To get only the items that where not selected.
        var non_selected_items_collection = new_collection.filter(function(el) {
            return selected_ids.indexOf(el[value_prop]) === -1;
        });

        if(selected_ids.length > 0) {
            //push the selected ids items in order as they appear in the selected_ids array
            selected_ids.forEach(function(key) {
                var found = false;
                var t = new_collection.find(function(item) {
                    return item[value_prop] === key;
                });

                if(t) {
                    selected_items_collection.push(t);
                }
            });
            sorted_collection = selected_items_collection.concat(non_selected_items_collection);
        } else {
            sorted_collection = new_collection;
        }

        var select = document.getElementById(select_dom_id);
        select.options.length = 0;
        for(var nx in sorted_collection) {
            var opt = document.createElement('option');
            opt.value = sorted_collection[nx][value_prop];
            opt.text = sorted_collection[nx][text_prop];
            try{ //Standard
                select.add(opt,null) ;
            }
            catch(error){ //IE Only
                select.add(opt) ;
            }
        }

        jquerySelect2Obj.select2();
        jquerySelect2Obj.select2('val', selected_ids);
    },

    reset_report_customer_list: function (selected_ids=[]){
        var jquerySelect2Obj = $("#sales-reports #report_omc_customer_list");
        this.reset_select2_option($customers, 'id', 'name', jquerySelect2Obj, 'report_omc_customer_list', selected_ids);
    },

    reset_report_dsrp_fields: function (dsrp_form_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-report-fields #report_dsrp_fields");
        var fields = $forms_fields[dsrp_form_id]['fields'];
        this.reset_select2_option(fields, 'id', 'field_name', jquerySelect2Obj, 'report_dsrp_fields', selected_ids);
    },

    reset_report_field_action_targets: function (report_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-report-fields #report_field_action_targets");
        var fields = $reports_fields[report_id]['fields'];
        this.reset_select2_option(fields, 'id', 'report_field_name', jquerySelect2Obj, 'report_field_action_targets', selected_ids);
    },

    reset_primary_field_total_options_list: function (report_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-report-primary-field-option #report_pf_total_option_list");
        var pf_options = $reports_fields[report_id]['primary_field_options'];
        this.reset_select2_option(pf_options, 'id', 'report_option_name', jquerySelect2Obj, 'report_pf_total_option_list', selected_ids);
    },

    reset_primary_field_total_fields_list: function (report_id, selected_ids=[]){
        var jquerySelect2Obj = $("#sales-report-primary-field-option #report_pf_total_field_list");
        var fields = $reports_fields[report_id]['fields'];
        this.reset_select2_option(fields, 'id', 'report_field_name', jquerySelect2Obj, 'report_pf_total_field_list', selected_ids);
    },

    bind_report_primary_field_option:function(){
        var self = this;

        $("#report_primary_field_tab").click(function(){
            var report_id = $("#sales-report-primary-field-option #pf_omc_sales_report_id").val();
            self.reset_primary_field_total_options_list(report_id);
            self.reset_primary_field_total_fields_list(report_id);
            $("#sales-report-primary-field-option #primary_field_html b").html($reports_fields[report_id].primary_field_name);
        });

        $("table#primary-field-option_list_table tbody tr").live('click',function(){
            $("table#primary-field-option_list_table tr").removeClass('selected');
            self.tr_edit = $(this);
            $(this).addClass('selected');
            var report_id = $(this).attr('data-report_id');
            var report_option_id = $(this).attr('data-pf_option_id');
            var report_pf_option = $reports_fields[report_id]['primary_field_options'][report_option_id];

            $("#sales-report-primary-field-option #report_pf_option_id").val(report_pf_option.id);
            $("#sales-report-primary-field-option #report_pf_option_name").val(report_pf_option.report_option_name);
            $("#sales-report-primary-field-option #report_pf_option_order").val(report_pf_option.report_option_order);
            $("#sales-report-primary-field-option #report_pf_option_link_type").val(report_pf_option.report_option_link_type).change();
            $("#sales-report-primary-field-option #report_dsrp_form").val(report_pf_option.report_dsrp_form)
           // $("#sales-report-primary-field-option #pf_option_link_id").val(pf_option.option_link_id);
            if(report_pf_option.report_is_total === 'no'){
                $("#sales-report-primary-field-option #pf_is_total_no").prop("checked", true).click();
            }
            else if(report_pf_option.report_is_total === 'yes'){
                $("#sales-report-primary-field-option #pf_is_total_yes").prop("checked", true).click();
            }
        });

        $("#sales-report-primary-field-option #report_pf_option_reset").click(function(){
            $("#sales-report-primary-field-option #report_pf_option_id").val('0');
            $("#sales-report-primary-field-option #report_pf_option_name").val('');
            $("#sales-report-primary-field-option #report_pf_option_link_type").val('').change();
            $("#sales-report-primary-field-option #report_dsrp_form").val('')
            $("#sales-report-primary-field-option #report_pf_option_order").val('');
            $("#sales-report-primary-field-option #report_pf_option_is_total").val('no');
            $("#sales-report-primary-field-option #pf_is_total_no").prop("checked", true).click();
            $("#sales-report-primary-field-option #report_pf_option_action_type").val('report_option_save');
            var report_id = $("#sales-report-primary-field-option #pf_omc_sales_report_id").val();
            self.reset_primary_field_total_options_list(report_id);
            self.reset_primary_field_total_fields_list(report_id);
            $("table#primary-field-option_list_table tbody tr").removeClass('selected');
            self.tr_edit = null;
        });

        $("#report_pf_option_delete_btn").click(function(){
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
                        $("#sales-report-primary-field-option #report_pf_option_action_type").val('report_option_delete');
                        $("#sales-report-primary-field-option").submit();
                    }
                });
            }
        });


        $("#sales-report-primary-field-option #pf_omc_sales_report_id").change(function(){
            $("#sales-report-primary-field-option #primary_field_html b").html($reports_fields[$(this).val()].report_primary_field_name);
            self.reset_primary_field_total_options_list($(this).val());
            self.reset_primary_field_total_fields_list($(this).val());
            $("#sales-report-primary-field-option #report_pf_option_reset").click();
            self.render_report_primary_field_option();
        });

        $("#sales-report-primary-field-option #pf_is_total_no").click(function(){
            var val = $(this).val();
            $("#sales-report-primary-field-option #report_pf_option_is_total").val(val);
            $(".pf_total_options_and_fields_wrapper").hide('slow');
            $("#sales-report-primary-field-option").validate().cancelSubmit = false;
            var report_id = $("#sales-report-primary-field-option #pf_omc_sales_report_id").val();
            self.reset_primary_field_total_options_list(report_id);
            self.reset_primary_field_total_fields_list(report_id);
        });

        $("#sales-report-primary-field-option #pf_is_total_yes").click(function(){
            var val = $(this).val();
            $("#sales-report-primary-field-option #report_pf_option_is_total").val(val);
            $(".pf_total_options_and_fields_wrapper").show('slow');
            //$("#sales-report-primary-field-option").validate().cancelSubmit = true;
            if(self.tr_edit){
                var report_id = self.tr_edit.attr('data-report_id');
                var report_option_id = self.tr_edit.attr('data-pf_option_id');
                var report_pf_option = $reports_fields[report_id]['primary_field_options'][report_option_id];
                if(report_pf_option) {
                    if(report_pf_option.report_total_option_list) {
                        self.reset_primary_field_total_options_list(report_id, report_pf_option.report_total_option_list.split(','));
                    } else {
                        self.reset_primary_field_total_options_list(report_id);
                    }

                    if(report_pf_option.report_total_field_list) {
                        self.reset_primary_field_total_fields_list(report_id, report_pf_option.report_total_field_list.split(','));
                    } else {
                        self.reset_primary_field_total_fields_list(report_id);
                    }
                } else {
                    self.reset_primary_field_total_fields_list(report_id);
                }
            }
        });

        $("#sales-report-primary-field-option #report_pf_option_link_type").change(function(){
            var val = $(this).val();
            var text = $(this).find("option:selected").text();
            $("#sales-report-primary-field-option #option_link_id_label").html(text);

            if(self.tr_edit){
                var report_id = self.tr_edit.attr('data-report_id');
                var report_option_id = self.tr_edit.attr('data-pf_option_id');
                var report_pf_option = $reports_fields[report_id]['primary_field_options'][report_option_id];
                if(report_pf_option) {
                    self.reset_option_link_ids(val, report_pf_option.report_option_link_id);
                }else {
                    self.reset_option_link_ids(val);
                }
            } else {
                self.reset_option_link_ids(val);
            }
        });

        $("#sales-report-primary-field-option #report_pf_total_option_list").select2();
        $("#sales-report-primary-field-option #report_pf_total_field_list").select2();
    },

    save_report_primary_field_option:function(){
        var self = this;
        var $salesReportPrimaryFieldOption = $("#sales-report-primary-field-option");
        var url = $salesReportPrimaryFieldOption.attr('action');
        var opts_arr = $("#sales-report-primary-field-option #report_pf_total_option_list").val();
        var opts_str = opts_arr ? opts_arr.toString() : '';
        var field_arr = $("#sales-report-primary-field-option #report_pf_total_field_list").val();
        var field_str = field_arr ? field_arr.toString() : '';
        var query = $salesReportPrimaryFieldOption.serialize()+"&report_pf_total_option_list="+opts_str+"&report_pf_total_field_list="+field_str;
        var salesReportPrimaryFieldOptionObjCollection = $salesReportPrimaryFieldOption.serializeArray();
        salesReportPrimaryFieldOptionObjCollection.push({name: 'report_pf_total_option_list', value: opts_str})
        salesReportPrimaryFieldOptionObjCollection.push({name: 'report_pf_total_field_list', value: field_str})

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
                        'report_option_id' :  response.id,
                        'report_id' : self.getValue('pf_omc_sales_report_id', salesReportPrimaryFieldOptionObjCollection),
                        'report_option_name' : self.getValue('report_pf_option_name', salesReportPrimaryFieldOptionObjCollection),
                        'report_option_link_type' : self.getValue('report_pf_option_link_type', salesReportPrimaryFieldOptionObjCollection),
                        'report_option_link_id' : self.getValue('report_pf_option_link_id', salesReportPrimaryFieldOptionObjCollection),
                        'report_option_order' : self.getValue('report_pf_option_order', salesReportPrimaryFieldOptionObjCollection),
                        'report_dsrp_form' : self.getValue('report_dsrp_form', salesReportPrimaryFieldOptionObjCollection),
                        'report_is_total' : self.getValue('report_pf_option_is_total', salesReportPrimaryFieldOptionObjCollection),
                        'report_total_option_list' : self.getValue('report_pf_total_option_list', salesReportPrimaryFieldOptionObjCollection),
                        'report_total_field_list' : self.getValue('report_pf_total_field_list', salesReportPrimaryFieldOptionObjCollection),
                        'report_option_action_type' : self.getValue('report_pf_option_action_type', salesReportPrimaryFieldOptionObjCollection)
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
        var report_id = post_data['report_id'];
        var report_option_id = post_data['report_option_id'];
        var report_action_type = post_data['report_option_action_type'];
        if(typeof $reports_fields[report_id] == "undefined"){
            return false;
        }
        if(report_action_type === 'report_option_save'){
            $reports_fields[report_id]['primary_field_options'][report_option_id] = {
                'id': report_option_id,
                'report_id': post_data['report_id'],
                'report_option_name': post_data['report_option_name'],
                'report_option_link_type': post_data['report_option_link_type'],
                'report_option_link_id': post_data['report_option_link_id'],
                'report_option_order': post_data['report_option_order'],
                'report_dsrp_form': post_data['report_dsrp_form'],
                'report_is_total': post_data['report_is_total'],
                'report_total_option_list': post_data['report_total_option_list'],
                'report_total_field_list': post_data['report_total_field_list']
            };
        }
        if(report_action_type === 'report_option_delete'){
            delete $reports_fields[report_id]['primary_field_options'][report_option_id];
        }
        $("#sales-report-primary-field-option #report_pf_option_reset").click();
        self.render_report_primary_field_option();
    },

    render_report_primary_field_option:function(){
        var self = this;
        var report_id = $("#sales-report-primary-field-option #pf_omc_sales_report_id").val();
        if(typeof $reports_fields[report_id] == "undefined"){
            return false;
        }
        var $primaryFieldOptionListTableTbody = $("#primary-field-option_list_table tbody");
        $primaryFieldOptionListTableTbody.html('');
        var collection = $reports_fields[report_id]['primary_field_options'];
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
                tr.attr('data-report_id',pf_option['report_id']);

                var td = $("<td />").html(pf_option['report_option_name']);
                tr.append(td);
                var td = $("<td />").html(pf_option['report_is_total']);
                tr.append(td);
                var td = $("<td />").html(pf_option['report_option_order']);
                tr.append(td);

                $primaryFieldOptionListTableTbody.append(tr);
            }
        }
    },


    preview_report:function(report_id){
        var self = this;
        var url = $("#sales-report-fields").attr('action');
        var query = "report_id="+report_id+"&report_action_type=report_preview";

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
                    var report_name = response.report_name;
                    $("#preview-report-window .preview-content").html(response.html);
                    $.colorbox({
                        inline:true,
                        scrolling:false,
                        overlayClose:false,
                        escKey:false,
                        top:'5%',
                        title:'Preview: '+report_name,
                        href:"#preview-report-window"
                    });
                    $('#preview-report-window').colorbox.resize();
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
    SalesReport.init();
});