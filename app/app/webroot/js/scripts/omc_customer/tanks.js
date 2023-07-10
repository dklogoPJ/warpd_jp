var Tanks = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'Tank Name', name:'name', width:200, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'Tank 1', defval:''}},
                {display:'Product Type', name:'type', width:200, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:products_type}},
                {display:'Capacity (Liters)', name:'capacity', width:200, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty,onlyNumber', placeholder:'', defval:''}},
                {display:'Status', name:'status', width:200, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:tank_status}}
            ],
            formFields:[
                {type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent},
                {separator:true},
                {type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent}
                /*{separator:true},
                {type:'select',name: 'Filter BDC', id: 'filter_bdc',bclass: 'filter',onchange:self.handleGridEvent,options:bdclists},*/
                //{separator:true},
                //{type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:order_filter}
            ],
            /*searchitems:[
             {display:'Proforma Number', name:'invoice_number', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                callback:function (server_response) {
                    if (server_response.code == 0) {
                        jLib.message('Data Status', server_response.msg, 'success');
                        self.objGrid.flexReload();
                    }
                    else {
                        jLib.message('Data Status', server_response.msg, 'error');
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

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            Tanks.objGrid.flexBeginAdd();
        }
        else if (com == 'Edit') {
            var row = jLib.getSelectedRows(grid);
            Tanks.objGrid.flexBeginEdit(row[0]);
        }
        else if (com == 'Save') {
            Tanks.objGrid.flexSaveChanges();
        }
        else if (com == 'Cancel') {
            Tanks.objGrid.flexCancel();
        }
       /* else if (com == 'Filter BDC' || com == 'Order Status') {
            Tanks.filterGrid(json);
        }*/
    },

    filterGrid:function(json){
        //var bdc_filter = $("#filter_bdc").val();
        var filter_status = $("#filter_status").val();
        $(Tanks.objGrid).flexOptions({
            params: [
                //{name: 'filter', value: bdc_filter},
                {name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    }
};

/* when the page is loaded */
$(document).ready(function () {
    Tanks.init();
});