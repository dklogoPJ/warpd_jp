<div class="span3">
    <?php
        echo $this->element('admin_nav_left');
    ?>
</div>
<div class="span9">

    <div class="form-actions" style="margin-top: 0px; font-weight: bold">
        District
    </div>

    <table id="dg" title="" style="height: 400px;" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
        <thead>
        <tr>
            <th field="name"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}">District Name</th>
            <th field="region_id" width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:region,required:true}}" formatter="regionFormatter">Region</th>
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

<script>
    var regionx = [
        {id:'1',name:'Koi'},
        {id:'2',name:'Dalmation'},
        {id:'3',name:'Rattlesnake'},
        {id:'4',name:'Iguana'},
        {id:'5',name:'Manx'},
        {id:'6',name:'Persian'},
        {id:'7',name:'Amazon Parrot'}
    ];
    //console.log(regionx);
    var region = <?php echo json_encode($regions); ?>;
    //console.log(region);

    function regionFormatter(value){
        for(var i=0; i<region.length; i++){
            if (region[i].id == value) return region[i].name;
        }
        return value;
    }
</script>

<?php
    echo $this->Html->css('editable_grid/gray/easyui.css');
    echo $this->Html->css('editable_grid/icon');

    echo $this->Html->script('editable_grid/jquery.easyui.min.js');
    echo $this->Html->script('editable_grid/jquery.edatagrid.js');
    echo $this->Html->script('scripts/region.js');
?>

<input type="hidden" id="grid_get_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'district/get')); ?>" />
<input type="hidden" id="grid_save_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'district/save')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'district/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'Administration', 'action' => 'district/delete')); ?>" />