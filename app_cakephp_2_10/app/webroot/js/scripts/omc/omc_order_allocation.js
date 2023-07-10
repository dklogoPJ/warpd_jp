var OmcOrder = {

    selected_row_id: null,
    objGrid: null,
    sel_bdc: null,
    preferred_product: null,
    preferred_depot: null,
    bdc_depot_products: {},
    sel_obj: null,
    val_pro: false,

    init: function () {
        var self = this;
        /** Will be used to determine which BDCs apply **/
        for (var k in bdc_depots_gbl) {
            var bdc = bdc_depots_gbl[k]['my_depots_to_products'];
            var products_depots_arr = bdc.split('#');
            for (var k1 in products_depots_arr) {
                var dp = products_depots_arr[k1];
                var pro_dept_arr = dp.split('|');
                var depot_id = pro_dept_arr[0];
                var p = pro_dept_arr[1];
                var product_ids = p.split(',');
                if (self.bdc_depot_products[k]) {
                    self.bdc_depot_products[k][depot_id] = product_ids;
                }
                else {
                    self.bdc_depot_products[k] = {};
                    self.bdc_depot_products[k][depot_id] = product_ids;
                }
            }
        }

        var btn_actions = [];
        if (inArray('A', permissions)) {
            btn_actions.push({type: 'buttom', name: 'New', bclass: 'add', onpress: self.handleGridEvent});
            btn_actions.push({separator: true});
        }
        if (inArray('E', permissions)) {
            btn_actions.push({type: 'buttom', name: 'Edit', bclass: 'edit', onpress: self.handleGridEvent});
            btn_actions.push({separator: true});
        }
        if (inArray('A', permissions) || inArray('E', permissions)) {
            btn_actions.push({type: 'buttom', name: 'Save', bclass: 'save', onpress: self.handleGridEvent});
            btn_actions.push({separator: true});
            btn_actions.push({type: 'buttom', name: 'Cancel', bclass: 'cancel', onpress: self.handleGridEvent});
            btn_actions.push({separator: true});
            btn_actions.push({type:'buttom', name:'Attachment', bclass:'attach', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        btn_actions.push({type: 'select', name: 'Filter BDC', id: 'filter_bdc', bclass: 'filter', onchange: self.handleGridEvent, options: bdclists});
        btn_actions.push({separator: true});
        btn_actions.push({type: 'select', name: 'Order Status', id: 'filter_status', bclass: 'filter', onchange: self.handleGridEvent, options: order_filter});


        self.objGrid = $('#flex').flexigrid({
            url: $('#table-url').val(),
            reload_after_add: true,
            reload_after_edit: true,
            dataType: 'json',
            colModel: [
                {display: 'Order Id', name: 'id', width: 50, sortable: false, align: 'left', hide: false},
                {display: 'Order Date', name: 'order_date', width: 90, sortable: false, align: 'left', hide: false},
                //{display:'Priority', name:'omc_order_priority', width:85, sortable:false, align:'left', hide:false},
                {display: 'Time Elapsed', name: 'time_elapsed', width: 85, sortable: false, align: 'left', hide: false},
                {display: 'Customer', name: 'omc_customer_id', width: 100, sortable: false, align: 'left', hide: false},
                {display: 'Loading Depot', name: 'depot_id', width: 120, sortable: true, align: 'left', hide: false},
                {display: 'Product Type', name: 'product_type_id', width: 100, sortable: true, align: 'left', hide: false},
                {display: 'Order Quantity', name: 'order_quantity', width: 100, sortable: true, align: 'left', hide: false},
                {display: 'Transporter', name: 'transporter', width: 95, sortable: true, align: 'left', hide: false, editable: {form: 'text', validate: 'empty', defval: ''}},
                {display: 'Truck No.', name: 'truck_no', width: 89, sortable: true, align: 'left', hide: false, editable: {form: 'text', validate: 'empty', defval: ''}},
                {display: 'BDC', name: 'bdc_id', width: 120, sortable: true, align: 'left', hide: false, editable: {form: 'select', validate: 'empty', defval: '', bclass: 'bdc-class', options: {}}},
                {display: 'Approved Quantity', name: 'approved_quantity', width: 130, sortable: true, align: 'left', hide: false, editable: {form: 'select', validate: 'empty,numeric', defval: '', bclass: 'approved_quantity-class', options: volumes}},
                {display: 'Loaded Quantity', name: 'loaded_quantity', width: 130, sortable: true, align: 'left', hide: false, editable: {form: 'select', validate: 'empty,numeric', defval: '', bclass: 'loaded_quantity-class', options: volumes}},
                {display:'Loading Date', name:'loaded_date', width:80, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}},
                {display: 'BDC Feedback', name: 'bdc_feedback', width: 160, sortable: true, align: 'left', hide: false}
                //{display:'BDC Finance Approval', name:'finance_approval', width:140, sortable:true, align:'left', hide:false},

            ],
            formFields: btn_actions,
            searchitems: [
                {display: 'Order Id', name: 'id', isdefault: true}
            ],
            checkboxSelection: true,
            editablegrid: {
                use: true,
                url: $('#table-editable-url').val(),
                add: inArray('A', permissions),
                edit: inArray('E', permissions),
                confirmSave: true,
                confirmSaveText: "Once a BDC is allocated you cannot change it again. \n Are you sure the information you entered is correct ?",
                afterRender: function (tr) {
                    var td = tr.find("[field='bdc_id']");
                    var selected_name = td.attr('data-id');

                    var row_id = tr.attr('data-id');
                    var extra_data = tr.attr('extra-data');
                    if (extra_data) {
                        var exd_arr = extra_data.split(',');
                        var ex_ar = {};
                        for (var d in exd_arr) {
                            var kv_str = exd_arr[d];
                            var kv_arr = kv_str.split('=>');
                            ex_ar[kv_arr[0]] = kv_arr[1]
                        }
                        self.preferred_product = {'id': ex_ar['product_type_id'], 'text': ex_ar['product_type_name']};
                        self.preferred_depot = {'id': ex_ar['depot_id'], 'text': ex_ar['depot_name']};
                    }
                    /*** Which BDCs has a this product in this depot ? **/
                    var qualified_bdcs = [];
                    for (var bdc_id in  self.bdc_depot_products) {
                        var bdc_depots = self.bdc_depot_products[bdc_id];
                        var the_depot_products = (bdc_depots[self.preferred_depot['id']]) ? bdc_depots[self.preferred_depot['id']] : false;
                        if (the_depot_products) {
                            if (inArray(self.preferred_product['id'], the_depot_products)) {
                                qualified_bdcs.push(bdc_id);
                            }
                        }
                    }
                    var select = document.getElementById('bdc_id_' + row_id);
                    select.options.length = 0;
                    var selected = '';
                    for (var nx in qualified_bdcs) {
                        var bdc_name = bdc_depots_gbl[qualified_bdcs[nx]]['name'];
                        var bdc_id = bdc_depots_gbl[qualified_bdcs[nx]]['id'];
                        if (selected_name == bdc_name) {
                            selected = bdc_id;
                        }
                        var opt = document.createElement('option');
                        opt.value = bdc_id;
                        opt.text = bdc_name;
                        try { //Standard
                            select.add(opt, null);
                        }
                        catch (error) { //IE Only
                            select.add(opt);
                        }
                    }
                    $("#bdc_id_" + row_id).val(selected).change();
                }
            },
            columnControl: true,
            sortname: "id",
            sortorder: "desc",
            usepager: true,
            useRp: true,
            rp: 15,
            showTableToggleBtn: false,
            height: 300,
            subgrid: {
                use: false
            }
        });

        $('input.datepicker').live('focus', function () {
            if (false == $(this).hasClass('hasDatepicker')) {
                $(this).datepicker({
                    inline: true,
                    changeMonth: true,
                    changeYear: true
                });
                $(this).datepicker("option", "dateFormat", 'dd-mm-yy');
            }
        });


        $("#form-export").validationEngine();
        $("#export-btn").click(function () {
            var validationStatus = $('#form-export').validationEngine({returnIsValid: true});
            if (validationStatus) {
                var bdc_filter = $("#filter_bdc").val();
                var filter_status = $("#filter_status").val();
                $('#form-export #export_filter_bdc').val(bdc_filter);
                $('#form-export #export_filter_status').val(filter_status);

                $("#form-export").attr('action', $("#export_url").val());
                window.open('', "ExportWindow", "menubar=yes, width=300, height=200,location=no,status=no,scrollbars=yes,resizable=yes");
                $("#form-export").submit();
            }
        });

        $('.approved_quantity-class').live('focus', function () {
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });

        $('.loaded_quantity-class').live('focus', function () {
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });

        $(".bdc-class").live('change', function () {
            var sel = $(this);
            var value = sel.val();
            var row_id = sel.parent().parent().parent().attr('data-id');
            var approved_quantity  = $('#approved_quantity_' + row_id);
            var loaded_quantity  = $('#loaded_quantity_' + row_id);
            var loaded_date  = $('#loaded_date_' + row_id);

            if (inArray(value, my_bdc_list_ids)) {
                approved_quantity.hide();
                loaded_quantity.hide();
                loaded_date.hide();
            }
            else{
                approved_quantity.show();
                loaded_quantity.show();
                loaded_date.show();
            }
        });
    },

    handleGridEvent: function (com, grid, json) {
        if (com == 'New') {
            OmcOrder.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            OmcOrder.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {

            OmcOrder.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            OmcOrder.objGrid.flexCancel();
        }
        else if (com == 'Attachment') {
            if (FlexObject.rowSelectedCheck(OmcOrder.objGrid,grid,1)) {
                OmcOrder.attach_file(grid);
            }
        }
        else if (com == 'Filter BDC' || com == 'Order Status') {
            OmcOrder.filterGrid(json);
        }
    },

    filterGrid: function (json) {
        var bdc_filter = $("#filter_bdc").val();
        var filter_status = $("#filter_status").val();
        $(OmcOrder.objGrid).flexOptions({
            params: [
                {name: 'filter', value: bdc_filter},
                {name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    },


    attach_file:function(grid){
        var row_ids = FlexObject.getSelectedRowIds(grid);
        var row_trs = FlexObject.getSelectedRows(grid);
        var tr = $(row_trs[0]);
        var extra_data = tr.attr('extra-data');
        var record_origin = 'manual';
        if (extra_data) {
            var exd_arr = extra_data.split(',');
            var ex_ar = {};
            for (var d in exd_arr) {
                var kv_str = exd_arr[d];
                var kv_arr = kv_str.split('=>');
                ex_ar[kv_arr[0]] = kv_arr[1]
            }
            record_origin = ex_ar['record_origin'];
        }

        var item_id = row_ids[0];
        document.getElementById('fileupload').reset();
        var attachment_type = 'Order';
        /*if(record_origin == 'customer_order'){
            attachment_type = 'Customer Order';
        }*/
        var log_activity_type = 'Order';
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
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});

                $('#attachment_modal').modal({
                    backdrop: 'static',
                    show: true,
                    keyboard: true
                });
            });

    }
};

/* when the page is loaded */
$(document).ready(function () {
    OmcOrder.init();
});