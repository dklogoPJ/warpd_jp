var EnterLoadingData = {

    selected_row_id:null,
    objGrid:null,
    preferred_product:null,
    sel_obj:null,
    val_pro:false,

    init:function () {
        var self = this;

        var btn_actions = [];
        if(inArray('A',permissions)){
            btn_actions.push({type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('A',permissions) || inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Date', name:'loading_date', width:80, sortable:false, align:'left', hide:false, editable:{form:'hidden', validate:'', defval:jLib.getTodaysDate('mysql_flip')}},
                {display:'Waybill Date', name:'waybill_date', width:100, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10',defval:jLib.getTodaysDate('mysql_flip')}},
				{display:'Waybill No.', name:'waybill_id', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty,onlyNumber', defval:''}},
                {display:'Collection Order No.', name:'collection_order_no', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty,numeric', defval:''}},
				{display:'OMC', name:'omc_id', width:200, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:omc}},
                {display:'Loading Depot', name:'depot_id', width:100, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'',bclass:'depot-class', options:depot}},
                {display:'Product Type', name:'product_type_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'',bclass:'product-class', options:product_type}},
                {display:'Approved Quantity', name:'approved_quantity', width:120, sortable:true, align:'left', hide:false},
                {display:'Loaded Quantity', name:'quantity', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty,moneyNumber', defval:''}},
                /*{display:'Region', name:'region_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:region}},
                {display:'Districts', name:'district_id', width:100, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:district}},*/
                {display:'Truck No.', name:'vehicle_no', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}}
            ],
            formFields:btn_actions,
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions),
                beforeSave:function () {
                    if(EnterLoadingData.val_pro && EnterLoadingData.preferred_product != null){
                        var sel_pro = EnterLoadingData.sel_obj.val();
                        if(EnterLoadingData.preferred_product.id != sel_pro){
                            var content = " The order product: "+EnterLoadingData.preferred_product.text+" must be selected. If you can't get the order product from the current depot, change the depots until you find a depot that this product is loaded from.";
                            EnterLoadingData.objGrid.flexNotify('Product Validation', content, 'error');
                            //jLib.message($title, $message, $type);
                            return false;
                        }
                        else{
                            return true;
                        }
                    }
                    else{
                        return true;
                    }
                }
            },
            columnControl:true,
            sortname:"id",
            sortorder:"desc",
            usepager:true,
            useRp:true,
            rp:15,
            showTableToggleBtn:false,
            height:300,
            subgrid:{
                use:false
            }
        });

        $('input.datepicker').live('focus', function(){
            if (false == $(this).hasClass('hasDatepicker')) {
                $(this).datepicker({
                    inline: true,
                    changeMonth: true,
                    changeYear: true
                });
                $(this).datepicker( "option", "dateFormat", 'dd-mm-yy' );
            }
        });

        $(".depot-class").live('change',function () {
            var sel = $(this);
            var value = sel.val();
            var row_id = sel.parent().parent().parent().attr('data-id');
            var extra_data = sel.parent().parent().parent().attr('extra-data');
            var validate_pro = false;
            if(sel.parent().parent().parent().hasClass('tr_green')){
                validate_pro = true;
            }
            self.val_pro = validate_pro;
            if(extra_data){
                var exd_arr = extra_data.split(',');
                var ex_ar = {};
                for(var d in exd_arr){
                    var kv_str = exd_arr[d];
                    var kv_arr = kv_str.split('=>');
                    ex_ar[kv_arr[0]]=kv_arr[1]
                }
                self.preferred_product = {'id':ex_ar['product_type_id']};
                self.preferred_product['text'] =ex_ar['product_type_name'];
            }
            if(typeof depots_to_products[value] == "undefined"){
                return;
            }
            var  my_products =  depots_to_products[value];
            //my_products = my_products.split(',');
            var d_options = product_type;
            //console.log(customer_credit_data)
            var select = document.getElementById('product_type_id_'+row_id);
            select.options.length = 0;
            for(var nx in d_options){
                var k = d_options[nx]['id'];
                if(inArray(k,my_products)){
                    var opt = document.createElement('option');
                    opt.value = k;
                    opt.text = d_options[nx]['name'];
                    try{ //Standard
                        select.add(opt,null) ;
                    }
                    catch(error){ //IE Only
                        select.add(opt) ;
                    }
                }
            }
        });
        $(".product-class").live('change',function () {
            var sel = self.sel_obj =  $(this);
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
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            EnterLoadingData.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            EnterLoadingData.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            EnterLoadingData.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            EnterLoadingData.objGrid.flexCancel();
        }
    }
};

/* when the page is loaded */
$(document).ready(function () {
    EnterLoadingData.init();
});