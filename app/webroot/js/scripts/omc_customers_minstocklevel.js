var JSObject = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Customer Name', name:'name', width:220, sortable:false, align:'left', hide:false},
                {display:'Product Type And Minimum Stock Level', name:'stock_level', width:720, sortable:false, align:'left', hide:false}
            ],
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
                    {display:'Product Type', name:'type', width:100, align:'center', editable:{form:'select', validate:'', defval:'', options:tanks_types_opt}},
                    {display:'Minimum Stock Level', name:'min_stock_level', width:150, align:'center', editable:{form:'text', validate:'empty,moneyNumber', defval:'0'}}
                ],
                editablegrid:{
                    use:true,
                    url:$('#table-editable-sub-url').val()
                },
                formFields:[
                    {type:'buttom', name:'New', bclass:'new', onpress:self.handleSubGridEvent},
                    {separator:true},
                    {type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleSubGridEvent},
                    {separator:true},
                    {type:'buttom', name:'Save', bclass:'save', onpress:self.handleSubGridEvent},
                    {separator:true},
                    {type:'buttom', name:'Cancel', bclass:'print', onpress:self.handleSubGridEvent}
                ]
            },
            before_expand:function (tr) {
                var omc_customer_id  = tr.attr('data-id');
                if(typeof  customer_tanks[omc_customer_id] != "undefined"){
                    var source_data = customer_tanks[omc_customer_id];
                    var new_options = [];
                    for(var x in source_data){
                        new_options.push({id:x,name:source_data[x]})
                    }
                    JSObject.objGrid.flexUpdateEditableSubCol('Product Type',new_options);
                }
                else{
                    JSObject.objGrid.flexUpdateEditableSubCol('Product Type',tanks_types_opt);
                }
            }
        });
    },

    handleSubGridEvent:function (com, inner_table) {
        if (com == 'New') {
            JSObject.objGrid.flexBeginSubAdd(inner_table);
        }
        else if (com == 'Edit') {
            var rows = jLib.getSelectedSubRows(inner_table);
            //we only need to edit the first one we can't do multiple editing
            if (rows.length > 0) {
                JSObject.objGrid.flexBeginSubEdit(rows[0]);
            }
        }
        else if (com == 'Save') {
            JSObject.objGrid.flexSubSaveChanges();
        }
        else if (com == 'Cancel') {
            JSObject.objGrid.flexSubCancel();
        }
    },

    delete_:function (grid) {
        var self = this;
        var url = $('#delete_url').val();
        jLib.do_delete(url, grid);
    }
};

/* when the page is loaded */
$(document).ready(function () {
    JSObject.init();
});