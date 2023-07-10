var Enter = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,

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


        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Date', name:'loading_date', width:80, sortable:false, align:'left', hide:false,  editable:{form:'text', validate:'empty', readonly:'readonly', defval:jLib.getTodaysDate('mysql_flip')}},
				{display:'Waybill Date.', name:'waybill_date', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty',placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}},
               // {display:'Collection Order No.', name:'collection_order_no', width:120, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty,onlyNumber', defval:''}},
                {display:'Waybill No.', name:'waybill_id', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty,numeric', defval:''}},
                {display:'BDC', name:'bdc_id', width:200, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:bdc_lists}},
                {display:'Loading Depot', name:'depot_id', width:100, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:depot}},
                {display:'Product Type', name:'product_type_id', width:150, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:product_type}},
                {display:'Product Quantity', name:'quantity', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty,numeric', defval:''}},
                /*{display:'Region', name:'region_id', width:150, sortable:true, align:'left', hide:false},
                {display:'Districts', name:'district_id', width:100, sortable:true, align:'left', hide:false},*/
                {display:'Truck No.', name:'vehicle_no', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'empty', defval:''}}
            ],
            formFields:btn_actions,
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:false,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions)
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
                    {display:'Invoice Number', name:'invoice_number', width:100, align:'center', editable:{form:'text', validate:'empty', defval:''}},
                    {display:'Customer Name', name:'omc_customer_id', width:200, align:'center', editable:{form:'select', validate:'', defval:'', options:customers}},
                    {display:'Quantity', name:'quantity', width:100, align:'center', editable:{form:'text', validate:'empty,numeric', defval:'0'}},
                    {display:'Region', name:'region_id', width:120, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'region-class', options:region}},
                    {display:'Delivery Location', name:'delivery_location_id', width:180, align:'center', hide:false, editable:{form:'select', validate:'empty', defval:'',options:[]}},
                    {display:'Transporter', name:'transporter', width:150, align:'center', editable:{form:'text', validate:'empty', defval:''}}
                ],
                editablegrid:{
                    use:true,
                    url:$('#table-editable-sub-url').val(),
                    add:inArray('A',permissions),
                    edit:inArray('E',permissions)
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


        $("#form-export").validationEngine();
        $("#export-btn").click(function () {
            var validationStatus = $('#form-export').validationEngine({returnIsValid:true});
            if (validationStatus) {
                $("#form-export").attr('action', $("#export_url").val());
                window.open('', "ExportWindow", "menubar=yes, width=300, height=200,location=no,status=no,scrollbars=yes,resizable=yes");
                $("#form-export").submit();
            }
        });

        $(".region-class").live('change',function () {
            var value = $(this).val();
            var parent_tr = $(this).parent().parent().parent();
            var row_id = $(parent_tr).attr('data-id');
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
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            Enter.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            Enter.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            Enter.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            Enter.objGrid.flexCancel();
        }
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
    }
};

/* when the page is loaded */
$(document).ready(function () {
    Enter.init();
});