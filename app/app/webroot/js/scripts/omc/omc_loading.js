var Enter = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,
    connects_with_bdc: true,

    init:function () {
        var self = this;

        self.connects_with_bdc = my_bdc_list_ids.length > 0;

        var btn_actions_sub = [];
        if(inArray('A',permissions)){
            btn_actions_sub.push({type:'buttom', name:'New', bclass:'add', onpress:self.handleSubGridEvent});
            btn_actions_sub.push({separator:true});
        }
        if(inArray('E',permissions)){
            btn_actions_sub.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleSubGridEvent});
            btn_actions_sub.push({separator:true});
        }
        if(inArray('A',permissions) || inArray('E',permissions)){
            btn_actions_sub.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleSubGridEvent});
            btn_actions_sub.push({separator:true});
            btn_actions_sub.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleSubGridEvent});
            btn_actions_sub.push({separator:true});
        }

        var col_models = [
            {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
            {display:'Date', name:'loading_date', width:80, sortable:false, align:'left', hide:false},
            {display:'Order No.', name:'order_id', width:80, sortable:true, align:'left', hide:false},
            {display:'Loading Depot', name:'depot_id', width:100, sortable:true, align:'left', hide:false},
            {display:'Product Type', name:'product_type_id', width:150, sortable:true, align:'left', hide:false},
            {display:'Product Quantity', name:'quantity', width:100, sortable:true, align:'left', hide:false},
            /*{display:'Region', name:'region_id', width:150, sortable:true, align:'left', hide:false},
            {display:'Districts', name:'district_id', width:100, sortable:true, align:'left', hide:false},*/
            {display:'Truck No.', name:'vehicle_no', width:80, sortable:true, align:'left', hide:false}
        ];

        if(self.connects_with_bdc) {
            col_models.splice(3, 0, {display:'Waybill Date.', name:'waybill_date', width:80, sortable:true, align:'left', hide:false});
            col_models.splice(4, 0, {display:'Waybill No.', name:'waybill_id', width:80, sortable:true, align:'left', hide:false});
            col_models.splice(5, 0, {display:'BDC', name:'bdc_id', width:200, sortable:true, align:'left', hide:false});
        }


        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:col_models,
            /*formFields:[
             {type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent},
             {separator:true},
             {type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent},
             {separator:true},
             {type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent},
             {separator:true},
             {type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent},
             {separator:true}
             ],*/
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:false,
            editablegrid:{
                use:false
            },
            columnControl:false,
            sortname:"id",
            sortorder:"desc",
            usepager:true,
            useRp:true,
            rp:15,
            showTableToggleBtn:false,
            height:300,
            subgrid:{
                use:true,
                url:$('#table-details-url').val(),
                colModel:[
                    {display:'Inv. No.', name:'invoice_number', width:100, align:'center', format_number:false, editable:{form:'text', validate:'empty', defval: ''}},
                    {display:'Customer Name', name:'omc_customer_id', width:150, align:'center', editable:{form:'select', validate:'', defval:'', bclass:'omc_customer-class', options:customers}},
                    {display:'Unit Price', name:'unit_price', width:100, align:'center', editable:{form:'text', validate:'empty', defval:'', on_key_up:'{"action":"multiply", "sources":["unit_price","quantity"], "targets":["total_amount"]}'}},
                    {display:'Quantity', name:'quantity', width:100, align:'center', editable:{form:'select', validate:'empty,numeric', defval:'',bclass:'quantity-class',options:volumes, on_change:'{"action":"multiply", "sources":["unit_price","quantity"], "targets":["total_amount"]}'}},
                    {display:'Total Amt', name:'total_amount', width:100, align:'center', editable:{form:'text', validate:'empty', defval:'', readonly:'readonly', on_focus:'{"action":"multiply", "sources":["unit_price","quantity"], "targets":["total_amount"]}'}},
                    {display:'Region', name:'region_id', width:120, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'region-class', options:region}},
                    {display:'Delivery Location', name:'delivery_location_id', width:180, align:'center', hide:false, editable:{form:'select', validate:'empty', defval:'',options:[]}},
                    {display:'Transporter', name:'transporter', width:150, align:'center', editable:{form:'select', validate:'', defval:'', options:truckList}},
                    {display:'Driver', name:'driver', width:150, align:'center', hide:false, editable:{form:'text', validate:'empty', defval:''}}
                ],
                editablegrid:{
                    use:true,
                    url:$('#table-editable-sub-url').val(),
                    add:inArray('A',permissions),
                    edit:inArray('E',permissions),
                    confirmSave:true,
                    confirmSaveText:"Are you sure the information you entered is correct ?"
                },
                formFields:btn_actions_sub
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
            },
            before_expand:function (tr) {
                var extra_data  = tr.attr('extra-data');
                var ex_dt_arr_str = extra_data.split(',');
                var ex_dt_arr = {};
                for(var k in ex_dt_arr_str){
                    var key_value = ex_dt_arr_str[k].split('=>');
                    ex_dt_arr[key_value[0]]= key_value[1];
                }
                Enter.depot_id = ex_dt_arr['depot_id'];
                if(typeof  delivery_locations[Enter.depot_id] != "undefined"){
                    var regions_data = delivery_locations[Enter.depot_id]['regions'];
                    var new_regions = [];
                    for(var x in regions_data){
                        new_regions.push({id:x,name:regions_data[x]})
                    }
                    Enter.objGrid.flexUpdateEditableSubCol('Region',new_regions);
                }
                else{
                    Enter.objGrid.flexUpdateEditableSubCol('Region',region);
                }
            }
        });


        $("#form-export").validationEngine();
        $("#export-btn").click(function () {
            var validationStatus = $('#form-export').validationEngine({returnIsValid:true});
            if (validationStatus) {
                $("#form-export").attr('action', $("#export_url").val());
                window.open('', "ExportWindow", "menubar=yes, width=300, height=200,location=no,status=no,scrollbars=yes,resizable=yes");
                $("#form-export").submit();
            }
        });


        $(".omc_customer-class").live('change',function () {
            var omc_customer_id = $(this).val();
            var tbody = $(this).closest('.master');
            var row_tr = $(this).parent().parent().parent();
            var row_id = $(row_tr).attr('data-id');
            var parent_id = $(row_tr).attr('parent_id');
            var master_tr =  $(tbody).find('tr#row'+parent_id);
            var product_type_id = null;
            var unit_price = 0;
            var extra_data  = master_tr.attr('extra-data');
            if(extra_data) {
                var ex_dt_arr_str = extra_data.split(',');
                var ex_dt_arr = {};
                for(var k in ex_dt_arr_str){
                    var key_value = ex_dt_arr_str[k].split('=>');
                    ex_dt_arr[key_value[0]]= key_value[1];
                }
                product_type_id = ex_dt_arr['product_type_id'];
            }
            if(all_customers_products_prices[omc_customer_id] !== undefined && all_customers_products_prices[omc_customer_id][product_type_id] !== undefined) {
                var quantity = parseInt($(row_tr).find('td div #quantity_'+row_id).val());
                unit_price = parseFloat(all_customers_products_prices[omc_customer_id][product_type_id]);
                $(row_tr).find('td div #unit_price_'+row_id).val(unit_price);
                $(row_tr).find('td div #total_amount_'+row_id).val(unit_price * quantity);
            } else {
                $(row_tr).find('td div #unit_price_'+row_id).val('');
                $(row_tr).find('td div #total_amount_'+row_id).val('');
            }
        });

        $(".region-class").live('change',function () {
            var value = $(this).val();
            var row_tr = $(this).parent().parent().parent();
            var row_id = $(row_tr).attr('data-id');
            if(typeof delivery_locations[Enter.depot_id] == "undefined"){
                return;
            }
            var d_options = delivery_locations[Enter.depot_id]['data'][value]['data'];
            var select = document.getElementById('delivery_location_id_'+row_id);
            select.options.length = 0;
            for(nx in d_options){
                var opt = document.createElement('option');
                opt.value = d_options[nx]['id'];
                if(d_options[nx]['alternate_route']){
                    opt.text = d_options[nx]['name']+' ('+d_options[nx]['alternate_route']+')';
                }
                else{
                    opt.text = d_options[nx]['name'];
                }
                try{ //Standard
                    select.add(opt,null) ;
                }
                catch(error){ //IE Only
                    select.add(opt) ;
                }
            }
        });

        $('.quantity-class').live('focus', function(){
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });
    },

    handleSubGridEvent:function (com, inner_table) {
        if (com == 'New') {
            Enter.objGrid.flexBeginSubAdd(inner_table);
        }
        else if (com == 'Edit') {
            var rows = FlexObject.getSelectedSubRows(inner_table);
            //we only need to edit the first one we can't do multiple editing
            if (rows.length > 0) {
                Enter.objGrid.flexBeginSubEdit(rows[0]);
            }
        }
        else if (com == 'Save') {
            Enter.objGrid.flexSubSaveChanges();
        }
        else if (com == 'Cancel') {
            Enter.objGrid.flexSubCancel();
        }
        else if (com == 'Export All') {
            var url = $("#export_url").val();
            window.open(url, "PrintExportWindow", "menubar=yes, width=600, height=500,location=no,status=no,scrollbars=yes,resizable=yes");
        }
    }
};

/* when the page is loaded */
$(document).ready(function () {
    Enter.init();
});