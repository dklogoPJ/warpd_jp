var OmcAccount = {

    grid:null,

    init:function () {
        var self = this;
        self.grid = $('#dg').datagrid();
        $("#new_btn").click(function () {
            self.addOmc();
        });
        /* self.grid = $('#dg').edatagrid({
         url: $("#grid_get_url").val(),
         saveUrl: $("#grid_save_url").val(),
         updateUrl: $("#grid_save_url").val(),
         destroyUrl: $("#grid_delete_url").val()
         });*/
    },

    addOmc:function () {
        $.colorbox({
            inline:true,
            scrolling:false,
            overlayClose:false,
            escKey:false,
            title:'Create Omc Account',
            href:"#customers-form-window"
        });
    }
};

/* when the page is loaded */
$(document).ready(function () {
    OmcAccount.init();
});