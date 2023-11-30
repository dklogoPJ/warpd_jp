
function setReadOnlyAndDefaultValues() {
    // set read-only fields...
    $("input[name='temp_coeff_1']").prop('readonly', true);
    $("input[name='temp_coeff_2']").prop('readonly', true);
    $("input[name='temp_vol_station']").prop('readonly', true);
    $("input[name='variance_depot']").prop('readonly', true);
    $("input[name='variance_received_qty']").prop('readonly', true);
    $("input[name='vol_15']").prop('readonly', true);
    $("input[name='temp_20']").prop('readonly', true);
    $("input[name='product_coeff']").val('0.6278').prop('readonly', true);
    $("input[name='transporter']").prop('readonly', true);
    $("input[name='temp_station']").prop('readonly', true);
    $("input[name='temp_depot']").prop('readonly', true);
    $("input[name='received_quantity']").prop('readonly', true);
    $("input[name='order_id']").prop('readonly', true);
    $("input[name='dens_vac']").prop('readonly', true);
    $("input[name='product_type_id']").prop('readonly', true);
}

function calculateTempCoeff1(product_coeff, density_vac, temp_depot, temp_20_degrees) {
    var intermediate_value = product_coeff / (1000 * density_vac);
    return Number(Math.exp((-Number((intermediate_value).toFixed(8)) * (temp_depot - temp_20_degrees)) * ((0.8 * Number((intermediate_value).toFixed(8)) * (temp_depot - temp_20_degrees)) + 1)).toFixed(5));
}

function calculateVolumeAt15Degrees(volume_depot, temp_coeff_1, density_vac) {
    return ((volume_depot * temp_coeff_1) * density_vac / density_vac).toFixed(2);
}

function calculateTempCoeff2(product_coeff, density_vac, temp_station, temp_20_degrees) {
    var intermediate_value = product_coeff / (1000 * density_vac);  
    return Number(Math.exp((-Number((intermediate_value).toFixed(8)) * (temp_station - temp_20_degrees)) * ((0.8 * Number((intermediate_value).toFixed(8)) * (temp_station - temp_20_degrees)) + 1)).toFixed(5));
}

function calculateTempCompensatedVolumeAtStation(volumeAt15Degrees, tempCoeff2) {
    return (volumeAt15Degrees / tempCoeff2).toFixed(2);
}

function calculateVarianceReceivedVsDepotQty(tempCompensatedVolumeAtStation, volumeDepot) {
    return (tempCompensatedVolumeAtStation - volumeDepot).toFixed(2);
}

function calculateVarianceReceivedVsStationCompensatedQty(receivedQuantity, tempCompensatedVolumeAtStation) {
    console.log(`${receivedQuantity}, ${tempCompensatedVolumeAtStation}`)

    return (receivedQuantity - tempCompensatedVolumeAtStation).toFixed(2);
}


var Order = {

    selected_row_id:null,
    objGrid:null,

    init:function () {
        var self = this;

        var btn_actions = [];
        // if(inArray('A',permissions)){
        //     btn_actions.push({type:'buttom', name:'New', bclass:'add', onpress:self.handleGridEvent});
        //     btn_actions.push({separator:true});
        // }
        if(inArray('E',permissions)){
            btn_actions.push({type:'buttom', name:'Edit', bclass:'edit', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
        
        if(inArray('A',permissions) || inArray('E',permissions)|| inArray('D',permissions)){
            btn_actions.push({type:'buttom', name:'Save', bclass:'save', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Cancel', bclass:'cancel', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            // btn_actions.push({type:'buttom', name:'Delete', bclass:'delete', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
            btn_actions.push({type:'buttom', name:'Attachment', bclass:'attach', onpress:self.handleGridEvent});
            btn_actions.push({separator:true});
        }
       // btn_actions.push({type:'select',name: 'Order Status', id: 'filter_status',bclass: 'filter',onchange:self.handleGridEvent,options:order_filter});

        self.objGrid = $('#flex').flexigrid({
            url:$('#table-url').val(),
            reload_after_add:true,
            reload_after_edit:true,
            dataType:'json',
            colModel:[
                {display:'ID', name:'id', width:20, sortable:false, align:'left', hide:true},
                {display:'Invoice Date', name:'invoice_date', width:120, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'empty', placeholder:'dd-mm-yyyy',bclass:'datepicker', maxlength:'10', defval:jLib.getTodaysDate('mysql_flip')}}, //jLib.getTodaysDate('mysql_flip')
                {display:'Invoice No.', name:'invoice_no', width:100, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Order ID.', name:'order_id', width:100, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Product Name', name:'product_type_id', width:180, sortable:true, align:'left', hide:false, editable:{form:'select', validate:'', defval:'', options:products}},
                {display:'Transporter', name:'transporter', width:180, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:'', options:products}},
                {display:'Volume at Depot', name:'volume_depot', width:140, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Dens. In Vac', name:'dens_vac', width:100, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Temp. at 20 degrees', name:'temp_20', width:130, sortable:true, align:'left', hide:false,  editable:{form:'text', validate:'', defval:'20'}},
                {display:'Temp. at Depot', name:'temp_depot', width:170, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Product Coeff', name:'product_coeff', width:100, sortable:false, align:'left', hide:false, editable:{form:'text', validate:'', defval:'0.6278'}},
                {display:'Temp Coeff1', name:'temp_coeff_1', width:80, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Temp. at Station', name:'temp_station', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Volume @ 15 degrees', name:'vol_15', width:130, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Temp Coeff 2', name:'temp_coeff_2', width:150, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Temp Compensated Volume @ Station', name:'temp_vol_station', width:190, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Received Quantity', name:'received_quantity', width:170, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}},
                {display:'Variance - Received vs. Depot Qty', name: 'variance_depot', width: 200, sortable: true, align: 'left', hide: false, editable:{form:'text', validate:'', defval:''}},
                {display:'Variance - Received vs. Station Compensated Qty', name:'variance_received_qty', width:230, sortable:true, align:'left', hide:false, editable:{form:'text', validate:'', defval:''}}
            ],
            formFields:btn_actions,
            searchitems:[
                {display:'Order Id', name:'id', isdefault:true}
            ],
            checkboxSelection:true,
            editablegrid:{
                use:true,
                url:$('#table-editable-url').val(),
                add:inArray('A',permissions),
                edit:inArray('E',permissions),
                confirmSave:true,
                confirmSaveText:"Are you sure the information you entered is correct ?",

            },
            columnControl:true,
            sortname:"id",
            sortorder:"desc",
            usepager:true,
            useRp:true,
            rp:15,
            showTableToggleBtn:false,
            height:370,
            subgrid:{
                use:false
            },
            callback:function ($type,$title,$message) {
                jLib.message($title, $message, $type);
            }
        });

        $('input.datepicker').live('focus', function(){
            if (false == $(this).hasClass('hasDatepicker')) {
                $(this).datepicker({
                    inline: true,
                    changeMonth: true,
                    changeYear: true
                });
                $(this).datepicker( "option", "dateFormat", 'dd-mm-yy' );
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

        $('.quantity-class').live('focus', function(){
            if (false == $(this).hasClass('hasMore')) {
                $(this).select_more();
            }
        });
    },

    handleGridEvent:function (com, grid, json) {
        if (com == 'New') {
            Order.objGrid.flexBeginAdd();
            setTimeout(setReadOnlyAndDefaultValues, 1000);
        }
        else if (com == 'Edit') {
            var row = FlexObject.getSelectedRows(grid);
            Order.objGrid.flexBeginEdit(row[0]);
            setTimeout(setReadOnlyAndDefaultValues, 1000);
        }
        else if (com == 'Save') {

            var isValid = true;
            var errorMessage = "";
    
            $('#flex .trSelected').each(function() { 
                var volumeDepot = $(this).find("input[name='volume_depot']").val();
                var invoiceNo = $(this).find("input[name='invoice_no']").val();
                var invoiceDate = $(this).find("input[name='invoice_date']").val();
    
                // Validate Volume at Depot
                if (volumeDepot === undefined || volumeDepot.trim() === "") {
                    errorMessage = "Please enter Volume at Depot in all rows.";
                    isValid = false;
                    return false; 
                }
    
                // Validate Invoice No
                if (invoiceNo === undefined || invoiceNo.trim() === "") {
                    errorMessage = "Please enter an Invoice No in all rows.";
                    isValid = false;
                    return false; 
                }
    
                // Validate Invoice Date
                if (invoiceDate === undefined || invoiceDate.trim() === "") {
                    errorMessage = "Please enter an Invoice Date in all rows.";
                    isValid = false;
                    return false; 
                }
            });
    
            if (!isValid) {
                alert(errorMessage);
            } else {
                Order.objGrid.flexSaveChanges();
            }
        }
        else if (com == 'Cancel') {
            Order.objGrid.flexCancel();
        }
        else if (com == 'Delete') {
            if (FlexObject.rowSelectedCheck(Order.objGrid,grid,1)) {
                Order.delete_(grid);
            }
        }
        else if (com == 'Attachment') {
            if (FlexObject.rowSelectedCheck(Order.objGrid,grid,1)) {
                Order.attach_file(grid);
            }
        }
        else if (com == 'Filter BDC' || com == 'Order Status') {
            Order.filterGrid(json);
        }
    },

    filterGrid:function(json){
        //var bdc_filter = $("#filter_bdc").val();
        var filter_status = $("#filter_status").val();
        $(Order.objGrid).flexOptions({
            params: [
                //{name: 'filter', value: bdc_filter},
                {name: 'filter_status', value: filter_status}
            ]
        }).flexReload();
    },

    attach_file:function(grid){
        var row_ids = FlexObject.getSelectedRowIds(grid);
        var item_id = row_ids[0];
        document.getElementById('fileupload').reset();
        var attachment_type = 'Customer Order';
        var log_activity_type = 'Order';
        $("#fileupload #type_id").val(item_id);
        $("#fileupload #type").val(attachment_type);//
        $("#fileupload #log_activity_type").val(log_activity_type);
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#get_attachments_url').val()+'/'+item_id+'/'+attachment_type,
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                $('#ajax_upload_table tbody').html('');
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});

                $('#attachment_modal').modal({
                    backdrop: 'static',
                    show: true,
                    keyboard: true
                });
            });

    },
    delete_:function (grid) {
        var self = this;
        var url = $('#grid_delete_url').val();
        jLib.do_delete(url, grid);
    }
    
};



/* when the page is loaded */
$(document).ready(function () {

    Order.init();

    $('#flex').on('change input keypress', "input[name='dens_vac'], input[name='temp_depot'], input[name='volume_depot'], input[name='temp_station'], input[name='received_quantity']", function() {
        performCalculation();
    });



    function performCalculation() {
        $('#flex tr').each(function() {
            var row = $(this);

            var product_coeff = parseFloat(row.find("input[name='product_coeff']").val()) || 0;
            var density_vac = parseFloat(row.find("input[name='dens_vac']").val()) || 0;
            var temp_depot = parseFloat(row.find("input[name='temp_depot']").val()) || 0;
            var temp_20_degrees = parseFloat(row.find("input[name='temp_20']").val()) || 20; 
            var volume_depot = parseFloat(row.find("input[name='volume_depot']").val()) || 0;
            var temp_station = parseFloat(row.find("input[name='temp_station']").val()) || 0;
            var received_quantity = parseFloat(row.find("input[name='received_quantity']").val()) || 0
            
            // calculate temp_coeff_1
            var temp_coeff_1 = calculateTempCoeff1(product_coeff, density_vac, temp_depot, temp_20_degrees);
            // calculate vol_15
            var vol_15 = calculateVolumeAt15Degrees(volume_depot, temp_coeff_1, density_vac)
            // calculate temp_coeff_2
            var temp_coeff_2 = calculateTempCoeff2(product_coeff, density_vac, temp_station, temp_20_degrees)
            // calculate temp compensation at station
            var temp_vol_station = calculateTempCompensatedVolumeAtStation(vol_15, temp_coeff_2) 
            // calculate variance depot
            var variance_depot = calculateVarianceReceivedVsDepotQty(temp_vol_station, volume_depot);
           // calculate variance received qty
           var variance_received_qty = calculateVarianceReceivedVsStationCompensatedQty(received_quantity, temp_vol_station);

            // ensure robustness for temp_coeff_1
            density_vac != 0 && temp_depot != 0 && temp_coeff_1 != 0 && row.find("input[name='temp_coeff_1']").val(temp_coeff_1);
            // vol_15
            row.find('input[name="temp_coeff_1"]').val() != '' && row.find("input[name='vol_15']").val(vol_15);
            // temp_coeff_2
            temp_station != 0 &&  row.find("input[name='temp_coeff_2']").val(temp_coeff_2)            
            // temp vol @ station
            vol_15 != 0 && temp_coeff_2 != 0 && row.find("input[name='temp_vol_station']").val(temp_vol_station)
            // variance depot
            volume_depot != 0 && row.find("input[name='temp_vol_station']").val() != '' && row.find("input[name='variance_depot']").val(variance_depot)
            // variance received qty
            received_quantity != 0 && row.find("input[name='temp_vol_station']").val() != '' && row.find("input[name='variance_received_qty']").val(variance_received_qty)
          

            // clear fields when other fields are empty
            temp_coeff_1 == 0  && row.find('input[name="temp_coeff_1"]').val('') && row.find('input[name="vol_15"]').val('')
            temp_station == 0 && row.find('input[name="density_vac"]').val('') && row.find('input[name="temp_coeff_2"]').val('')

            temp_coeff_1 == 0 && row.find('input[name="vol_15"]').val('') && row.find('input[name="temp_vol_station"]').val('')

            row.find('input[name="volume_depot"]').val() == ''  &&  row.find('input[name="variance_depot"]').val('') 

            row.find('input[name="received_quantity"]').val() == ''  && temp_vol_station == 0 &&  row.find('input[name="variance_received_qty"]').val('') 
          
            
            console.log(temp_coeff_1)
            console.log(`tempt 1 ${temp_coeff_1}, vol 15 ${vol_15}, vol depot ${volume_depot}, dens vac ${density_vac}, temp station ${temp_station}, temp 20 degrees ${temp_20_degrees}`)
        });
    }

    // validate inputs
    $('#flex').on('keypress paste', "input[name='dens_vac'], input[name='temp_depot'], input[name='volume_depot'], input[name='temp_station']", function(e) {
        var charCode = (typeof e.which === "undefined") ? e.keyCode : e.which;
        var charStr = String.fromCharCode(charCode);

        // Regular expression to allow digits and a dot
        if (!charStr.match(/^[0-9.]*$/)) {
            e.preventDefault();
            return false;
        }

        // ensure only one dot is present
        if (charStr === '.' && this.value.indexOf('.') > -1) {
            e.preventDefault();
            return false;
        }
    });

});