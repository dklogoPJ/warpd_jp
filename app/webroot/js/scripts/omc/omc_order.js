var OmcOrder = {

    selected_row_id:null,
    objGrid:null,
    sel_bdc:null,
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
            btn_actions.push({type:'buttom', name:'Attachment', bclass:'attach', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        btn_actions.push({type:'select',name: 'Filter BDC', id: 'filter_bdc',bclass: 'filter',onchange:self.handleGridEvent,options:bdclists});
        //btn_actions.push({separator:true});
       // btn_actions.push({type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:order_filter});


        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'Order Id', name:'id', width:60, sortable:false, align:'left', hide:false},
                {display:'Order Date', name:'order_date', width:80, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}},
                //{display:'Priority', name:'omc_order_priority', width:85, sortable:false, align:'left', hide:false},
                {display:'Time Elapsed', name:'time_elapsed', width:85, sortable:false, align:'left', hide:false},
                {display:'Customer', name:'omc_customer_id', width:150, sortable:false, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:customers}},
                {display:'Loading Depot', name:'depot_id', width:130, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'',bclass:'depot-class', options:depot}},
				{display:'Product Type', name:'product_type_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'',bclass:'product-class', options:products}},
                {display:'Order Quantity', name:'order_quantity', width:100, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'empty,numeric', defval:'',bclass:'quantity-class',options:volumes}},
                {display:'BDC', name:'bdc_id', width:160, sortable:true, align:'left', hide:false}
                /*{display:'Delivery Priority', name:'delivery_priority', width:89, sortable:true, align:'left', hide:false},*/
                // {display:'BDC Feedback', name:'bdc_feedback', width:140, sortable:true, align:'left', hide:false},
                //{display:'BDC Finance Approval', name:'finance_approval', width:140, sortable:true, align:'left', hide:false},
               // {display:'Approved Quantity', name:'approved_quantity', width:130, sortable:true, align:'left', hide:false}
            ],
            formFields:btn_actions,
            searchitems:[
                {display:'Order Id', name:'id', isdefault:true}
            ],
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions),
                confirmSave:true,
                confirmSaveText:"If a BDC is allocated after saving, you cannot change it again. \n Are you sure the information you entered is correct ?"
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

       /* $(".bdc-class").live('change',function () {
            var sel = $(this);
            var value = self.sel_bdc = sel.val();
            var row_id = sel.parent().parent().parent().attr('data-id');
            var extra_data = sel.parent().parent().parent().attr('extra-data');
            var validate_pro = false;
            if(sel.parent().parent().parent().hasClass('tr_mauve')){
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
            if(typeof bdc_depots[value] == "undefined"){
                return;
            }
            var  my_depot =  bdc_depots[value]['my_depots'];
            my_depot = my_depot.split(',');
            var d_options = depot;
            var select = document.getElementById('depot_id_'+row_id);
            select.options.length = 0;
            for(var nx in d_options){
                var k = d_options[nx]['id'];
                if(inArray(k,my_depot)){
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
        });*/

      /*  $(".depot-class").live('change',function () {
            var sel = $(this);
            var value = sel.val();
            var row_id = sel.parent().parent().parent().attr('data-id');
            if(typeof bdc_depots[self.sel_bdc] == "undefined"){
                return;
            }
            var my_dtp_str =  bdc_depots[self.sel_bdc]['my_depots_to_products'];
            var my_dtp_arr = my_dtp_str.split('#');//4|1,2,3#6|4,5
            var pros = [];
             for(var x in my_dtp_arr){
                var b = my_dtp_arr[x];
                var my_dtp_pair = b.split('|');//4|1,2,3
                var depot_id = my_dtp_pair[0];//4
                if(value == depot_id){
                    var u = my_dtp_pair[1];
                    var product_ids =  u.split(',');//1,2,3
                    pros = product_ids;
                    break;
                }
            }
             var fin_pro = {};
            for(var nx in products){
                var k = products[nx]['id'];
                var t = products[nx]['name'];
                if(inArray(k,pros)){
                    fin_pro[k]=t;
                }
            }
            //console.log(fin_pro);
            var d_options = fin_pro;
            //console.log(customer_credit_data)
            var select = document.getElementById('product_type_id_'+row_id);
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
        });*/

        $(".product-class").live('change',function () {
            var sel = self.sel_obj =  $(this);
        });

        $('.quantity-class').live('focus', function(){
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
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

    filterGrid:function(json){
        var bdc_filter = $("#filter_bdc").val();
       // var filter_status = $("#filter_status").val();
        $(OmcOrder.objGrid).flexOptions({
            params: [
                {name: 'filter', value: bdc_filter}
                //{name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    },

    attach_file:function(grid){
        var row_ids = FlexObject.getSelectedRowIds(grid);
        var item_id = row_ids[0];
        document.getElementById('fileupload').reset();
        var attachment_type = 'Order';
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