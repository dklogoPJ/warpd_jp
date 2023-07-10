<script>
    var user_levels = [
        {id:'admin',name:'Admin'},
        {id:'normal_user',name:'Normal User'}
    ];
    function userLevelsFormatter(value){
        for(var i=0; i<user_levels.length; i++){
            if (user_levels[i].id == value) return user_levels[i].name;
        }
        return value;
    }

    var user_types = [
        {id:'Admin',name:'Admin'},
        {id:'Operations',name:'Operations'},
        {id:'Marketing',name:'Marketing'},
        {id:'Finance',name:'Finance'}
    ];


    var account_active = [
        {id:'y',name:'Active'},
        {id:'n',name:'Disabled'}
    ];
    function account_activeFormatter(value){
        for(var i=0; i<account_active.length; i++){
            if (account_active[i].id == value) return account_active[i].name;
        }
        return value;
    }

</script>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $company_profile['name']; ?> <small> Users</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <table id="dg" title="" style="height: 400px;" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                    <tr>
                        <th field="fname"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}">First Name</th>
                        <th field="lname"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}">Last Name</th>
                        <th field="username"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}">Username</th>
                        <th field="user_level" width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:user_levels,required:true}}" formatter="userLevelsFormatter">User Level</th>
                        <th field="bdc_user_type" width="50" sortable="false" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:user_types,required:true}}">User Type</th>
                        <th field="active" width="50" sortable="false" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:account_active,required:true}}" formatter="account_activeFormatter">Active</th>
                    </tr>
                </thead>
            </table>
            <div class="row-fluid" id="toolbar">
                <div class="span12">
                    <div class="btn-group">
                        <a href="javascript: void(0);" class="btn" iconCls="icon-add" plain="true" onclick="javascript:$('#dg').edatagrid('addRow')"><i class="icon-plus"></i> New</a>
                        <a href="javascript: void(0);" class="btn" iconCls="icon-remove" plain="true" onclick="javascript:$('#dg').edatagrid('destroyRow')"><i class="icon-trash"></i> Delete</a>
                        <a href="javascript: void(0);" class="btn" iconCls="icon-save" plain="true" onclick="javascript:$('#dg').edatagrid('saveRow')"><i class="icon-hdd"></i> Save</a>
                        <a href="javascript: void(0);" class="btn" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg').edatagrid('cancelRow')"><i class="icon-ban-circle"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="grid_get_url" value="<?php echo $this->Html->url(array('controller' => 'Bdc', 'action' => 'admin_users/get')); ?>" />
<input type="hidden" id="grid_save_url" value="<?php echo $this->Html->url(array('controller' => 'Bdc', 'action' => 'admin_users/save')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'Bdc', 'action' => 'admin_users/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'Bdc', 'action' => 'admin_users/delete')); ?>" />


<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/omc_users.js');
?>
