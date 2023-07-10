var Enter = {
    selected_row_id:null,
    objGrid:null,
    depot_id:null,

    init:function () {
        var self = this;

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
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Loading Date', name:'loading_date', width:100, sortable:false, align:'left', hide:false},
                {display:'Invoice Number', name:'invoice_number', width:100, sortable:true, align:'left', hide:false},
                //{display:'Loading Depot', name:'depot_id', width:100, sortable:true, align:'left', hide:false},
                {display:'Product Type', name:'product_type_id', width:160, sortable:true, align:'left', hide:false},
                {display:'Product Quantity', name:'quantity', width:140, sortable:true, align:'left', hide:false},
                //{display:'Region', name:'region_id', width:100, sortable:true, align:'left', hide:false},
                {display:'Delivery Location', name:'location_id', width:140, sortable:true, align:'left', hide:false},
                {display:'Transporter', name:'transporter', width:120, sortable:true, align:'left', hide:false},
                {display:'Vehicle No.', name:'vehicle_no', width:100, sortable:true, align:'left', hide:false}
            ],
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
                    //{display:'Invoice Number', name:'invoice_number', width:100, align:'center', editable:{form:'text', validate:'empty', defval:''}},
                    {display:'Customer Name', name:'customer', width:150, align:'center', editable:{form:'text', validate:'empty', defval:''}},
                    {display:'Quantity', name:'quantity', width:100, align:'center', editable:{form:'text', validate:'empty,numeric', defval:'0'}},
                    {display:'Region', name:'region_id', width:120, sortable:true, align:'center', hide:false, editable:{form:'select', validate:'', defval:'', bclass:'region-class', options:region}},
                    {display:'Location', name:'location', width:180, align:'center', hide:false, editable:{form:'text', validate:'empty', defval:''}},
                    {display:'Transporter', name:'transporter', width:150, align:'center', hide:false, editable:{form:'text', validate:'empty', defval:''}}
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
    },

    handleSubGridEvent:function (com, inner_table) {
        if (com == 'New') {
            Enter.objGrid.flexBeginSubAdd(inner_table);
        }
        else if (com == 'Edit') {
            var rows = jLib.getSelectedSubRows(inner_table);
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