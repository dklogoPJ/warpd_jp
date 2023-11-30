<div class="span3">
    <?php
        if($user_type == 'system'){
            echo $this->element('admin_nav_left');
        }
        elseif($user_type == 'bdc'){
            echo $this->element('admin_bdc_nav_left');
        }
        elseif($user_type == 'omc'){
            echo $this->element('admin_omc_nav_left');
        }
    ?>
</div>
<div class="span9">

    <div class="form-actions" style="margin-top: 0px; font-weight: bold">
        Create Users
    </div>

    <table id="dg" title="" style="height: 400px;" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
        <thead>
        <tr>

            <th field="title" width="30"  sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:title,required:true}}" formatter="titleFormatter">Title</th>
            <th field="fname" width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}" >First Name</th>
            <th field="lname"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}" >Last Name</th>
            <th field="mname" width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}" >Middle Name</th>
            <th field="username" width="50"  sortable="true" editor="{type:'validatebox',options:{required:true}}">Userame</th>
            <?php
            if($user_type == 'system' || $user_type == 'bdc'){
            ?>
                <th field="user_level"width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:user_level,required:true,editable:false}}" formatter="user_levelFormatter">User Level</th>
            <?php
            }
           ?>
            <th field="email"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}">Email</th>
        </tr>
        </thead>
    </table>
    <div class="row-fluid" id="toolbar">
        <div class="span12">
            <div class="btn-group">
                <a href="javascript: void(0);" class="btn" iconCls="icon-add" plain="true" onclick="javascript:$('#dg').edatagrid('addRow')"><i class="icon-plus"></i> New</a>
                <!--<a href="javascript: void(0);" class="btn" iconCls="icon-remove" plain="true" onclick="javascript:$('#dg').edatagrid('destroyRow')"><i class="icon-trash"></i> Destroy</a>-->
                <a href="javascript: void(0);" class="btn" iconCls="icon-save" plain="true" onclick="javascript:$('#dg').edatagrid('saveRow')"><i class="icon-hdd"></i> Save</a>
                <a href="javascript: void(0);" class="btn" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg').edatagrid('cancelRow')"><i class="icon-ban-circle"></i> Cancel</a>
            </div>
        </div>
        <!--<div class="span6">
            <div class="input-append">
                <label>Search On:</label>
                <select class="span2">
                    <option>Date</option>
                    <option>Waybill No.</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                </select>
                <input class="span4" id="appendedInputButton" size="16" type="text">
                <button class="btn" type="button">Search</button>
            </div>
        </div>-->
    </div>

</div>

<?php
echo $this->Html->css('editable_grid/gray/easyui.css');
echo $this->Html->css('editable_grid/icon');
?>

<script type="text/javascript">
    var title = [
        {id:'Mr',name:'Mr'},
        {id:'Ms',name:'Ms'},
        {id:'Mrs',name:'Mrs'},
        {id:'Miss',name:'Miss'}
    ];
    function titleFormatter(value){
        for(var i=0; i<title.length; i++){
            if (title[i].id == value) return title[i].name;
        }
        return value;
    }

    var user_type = <?php echo json_encode($user_type) ;?>

    var user_level = [];
    if(user_type == 'system'){
        user_level = [
            {'id':'admin','name':'Admin'},
            {'id':'normal_user','name':'Normal User'}
        ];
    }
    else if(user_type == 'bdc'){
        user_level = [
            {'id':'admin','name':'Admin'},
            {'id':'normal_user','name':'Operations'},
            {'id':'normal_user','name':'Marketing'},
            {'id':'normal_user','name':'Finance'},
            {'id':'normal_user','name':'Consolidation'}
        ];
    }



    function user_levelFormatter(value){
        for(var i=0; i<user_level.length; i++){
            if (user_level[i].id == value) return user_level[i].name;
        }
        return value;
    }

</script>

<!-- Le Script -->
<?php
    echo $this->Html->script('editable_grid/jquery.easyui.min.js');
    echo $this->Html->script('editable_grid/jquery.edatagrid.js');
    echo $this->Html->script('scripts/create_user.js');
?>

<input type="hidden" id="grid_get_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user/get')); ?>" />
<input type="hidden" id="grid_save_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user/save')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'create_user/delete')); ?>" />