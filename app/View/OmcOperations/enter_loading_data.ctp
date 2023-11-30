<!-- Le Css -->
<?php
    echo $this->Html->css('editable_grid/gray/easyui');
    echo $this->Html->css('editable_grid/icon');
    echo $this->Html->css('overwrite-editable-css-conflict');
?>
<script type="text/javascript">
    var omc = <?php echo json_encode($omclists);?>;
    function omcFormatter(value){
        for(var i=0; i<omc.length; i++){
            if (omc[i].id == value) return omc[i].name;
        }
        return value;
    }

    var depot = <?php echo json_encode($bdc_depot_lists);?>;
    function depotFormatter(value){
        for(var i=0; i<depot.length; i++){
            if (depot[i].id == value) return depot[i].name;
        }
        return value;
    }

    var product_type =  <?php echo json_encode($products_lists);?>;
    function productFormatter(value){
        for(var i=0; i<product_type.length; i++){
            if (product_type[i].id == value) return product_type[i].name;
        }
        return value;
    }

    var region = <?php echo json_encode($regions_lists);?>;
    function regionFormatter(value){
        for(var i=0; i<region.length; i++){
            if (region[i].id == value) return region[i].name;
        }
        return value;
    }

    var district = <?php echo json_encode($district_lists);?>;
    function districtFormatter(value){
        for(var i=0; i<district.length; i++){
            if (district[i].id == value) return district[i].name;
        }
        return value;
    }

    var waybill_no = [
        {id:'1',name:'Koi'},
        {id:'2',name:'Dalmation'},
        {id:'3',name:'Rattlesnake'},
        {id:'4',name:'Iguana'},
        {id:'5',name:'Manx'},
        {id:'6',name:'Persian'},
        {id:'7',name:'Amazon Parrot'}
    ];
    function waybillFormatter(value){
        for(var i=0; i<waybill_no.length; i++){
            if (waybill_no[i].id == value) return waybill_no[i].name;
        }
        return value;
    }
</script>

<div class="workplace">

    <div class="page-header">
        <h1>Daily Truck <small> Uploaded</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <table id="dg" title="" style="height: 400px;" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                <tr>
                    <th field="loading_date" width="50%"  sortable="true" editor="{type:'validatebox',options:{required:true}}">Date</th>
                    <th field="waybill_id" width="50"  sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:waybill_no,required:true}}" formatter="waybillFormatter">Waybill No.</th>
                    <th field="omc_id" width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:omc,required:true}}" formatter="omcFormatter">OMC</th>
                    <th field="depot_id"width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:depot,required:true}}" formatter="depotFormatter">Depot</th>
                    <th field="product_type_id" width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:product_type,required:true}}" formatter="productFormatter">Product Type</th>
                    <th field="quantity" width="50"  sortable="true" editor="{type:'validatebox',options:{required:true}}">Quantity</th>
                    <th field="region_id" width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:region,required:true}}" formatter="regionFormatter">Region</th>
                    <th field="district_id"width="50" sortable="true" editor="{type:'combobox',options:{valueField:'id',textField:'name',data:district,required:true}}" formatter="districtFormatter">Districts</th>
                    <th field="vehicle_no"width="50" sortable="true" editor="{type:'validatebox',options:{required:true}}">Vehicle No.</th>
                </tr>
                </thead>
            </table>
            <div class="row-fluid" id="toolbar">
                <div class="span12">
                    <div class="btn-group">
                        <a href="javascript: void(0);" class="btn" id="add_row_btn" iconCls="icon-add" plain="true"><i class="icon-plus"></i> New</a>
                        <!--<a href="javascript: void(0);" class="btn" iconCls="icon-remove" plain="true" onclick="javascript:$('#dg').edatagrid('destroyRow')"><i class="icon-trash"></i> Destroy</a>-->
                        <a href="javascript: void(0);" class="btn" id="save_row_btn" iconCls="icon-save" plain="true"><i class="icon-hdd"></i> Save</a>
                        <a href="javascript: void(0);" class="btn" id="cancel_row_btn" iconCls="icon-undo" plain="true"><i class="icon-ban-circle"></i> Cancel</a>
                    </div>
                </div>
            </div>


        </div>



    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="grid_get_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'daily_truck_upload/get')); ?>" />
<input type="hidden" id="grid_save_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'daily_truck_upload/save')); ?>" />
<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'daily_truck_upload/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOperations', 'action' => 'daily_truck_upload/delete')); ?>" />


<!-- Le Script -->
<?php
    echo $this->Html->script('editable_grid/jquery.easyui.min.js');
    echo $this->Html->script('editable_grid/jquery.edatagrid.js');
    echo $this->Html->script('scripts/enter_loading.js');
?>
