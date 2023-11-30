var BulkStockPosition = {

    edit_row:false,

    init:function(){
        var self = this;

        self.initRowSelect();
        self.initRowMenus();
        self.bindRules();

        $('#fixed_hdr').fxdHdrCol({
            fixedCols: 1,
            width:     "100%",
            height:    400,
            colModal: [
                { width: 100, align: 'center' },
                { width: 170, align: 'center' },
                { width: 170, align: 'center' },
                { width: 170, align: 'center' },
                { width: 170, align: 'center' },
                { width: 170, align: 'center' }
            ]
        });
    },

    initRowSelect:function(){
        var self = this;
        DsrpCommon.initRowSelect();
    },

    initRowMenus:function(){
        var self = this;
        $("#edit_row_btn").click(function(){
            self.editRow();
        });
        $("#cancel_row_btn").click(function(){
            self.cancelRow();
        });
        $("#save_row_btn").click(function(){
            self.saveRow();
        });
    },

    editRow:function(){
        var self = this;
        if(self.edit_row){
            self.saveRow(function(){
                self.renderRow();
            });
        }
        else{
            self.renderRow();
        }
    },

    cancelRow:function(){
        var self = this;
        self.clearEditing();
    },


    saveRow:function(callback){
        var self = this;
        var table_setup_data  = table_setup;
        var res = DsrpCommon.validateRow(table_setup_data);
        if(!self.edit_row){
            return;
        }
        if(res.status){//validation pass get the values
            DsrpCommon.saveRow(function(data,response){
                BulkStockPosition.clearEditing();
                if(typeof callback == "function"){
                    callback();
                }
            });
        }
        else{
            alertify.error(res.message);
            return false;
        }
    },


    renderRow:function(){
        var self = this;
        var table_setup_data  = table_setup;
        DsrpCommon.renderRow(table_setup_data);
        self.edit_row = true;
    },

    clearEditing:function(){
        var self = this;
        DsrpCommon.clearEditing();
        self.edit_row = false;
    },


    /**** Field Rules ***/
    bindRules:function(){
      $("#day_throughout").live('focusin',function(){
           var operands = ['closing_meter','-opening_meter'];
           var targets = ['day_throughout'];
          RuleActions.sum(targets,operands,true);
      });
      $("#day_sales").live('focusin',function(){
            var operands = ['day_throughout','-return_to_stock'];
            var targets = ['day_sales'];
            RuleActions.sum(targets,operands,true);
      });
    }

};

/* when the page is loaded */
$(document).ready(function () {
    BulkStockPosition.init();
});