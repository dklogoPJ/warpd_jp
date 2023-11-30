
var Enter = {

    grid:null,

    init:function () {
        var self = this;

        self.grid = $('#dg').edatagrid({
            url: $("#grid_get_url").val(),
            saveUrl: $("#grid_save_url").val(),
            updateUrl: $("#grid_save_url").val(),
            destroyUrl: $("#grid_delete_url").val()
        });
    }
};

/* when the page is loaded */
$(document).ready(function () {
    Enter.init();
});