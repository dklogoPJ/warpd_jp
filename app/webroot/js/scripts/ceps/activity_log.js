var ActivityLog = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;
        var columns = Array();
        columns.push({display:'Activity Date', name:'created', width:80, sortable:true, align:'center', hide:false});
        columns.push({display:'Performed By', name:'user_full_name', width:160, sortable:true, align:'center', hide:false});
        columns.push({display:'Activity', name:'activity', width:100, sortable:true, align:'center', hide:false});
        columns.push({display:'Description', name:'description', width:600, sortable:true, align:'center', hide:false});

        var btn_actions = [];
       /* if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }*/

        //btn_actions.push({type:'select',name: 'Filter OMC', id: 'filter_omc' ,bclass: 'filter',onchange:self.handleGridEvent,options:omc_lists});
        //btn_actions.push({separator:true});
        btn_actions.push({type:'select',name: 'Filter User', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:entity_users_filter});
        btn_actions.push({separator:true});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:columns,
            formFields:btn_actions,
            /*searchitems:[
             {display:'Order Id', name:'id', isdefault:true}
             ],*/
            checkboxSelection:true,
            editablegrid:{
                use:false
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
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'Filter User') {
            ActivityLog.filterGrid(json);
        }
    },

    filterGrid:function(json){
       // var omc_filter = $("#filter_omc").val();
        var filter_status = $("#filter_status").val();
        $(ActivityLog.objGrid).flexOptions({
            params: [
                {name: 'filter', value: filter_status}
            ]
        }).flexReload();
    }
};

/* when the page is loaded */
$(document).ready(function () {
    ActivityLog.init();
});