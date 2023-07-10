var RuleActions = {

    product_map:{
        1:['Automative Gasoil','AGO','A.G.O'],
        2:['Premium Motor Spirit','PMS','P.M.S'],
        3:['Kerosene','Kero'],
        4:['Marine Gas Oil','MGO','M.G.O'],
        5:['Premix','Super'],
        6:['Liquified Petroleum Gas','LPG','L.P.G'],
        7:['Residual Fuel Oil','RFO','R.F.O'],
        8:['Naphtha']
    },


    getProductIdFromMap:function(pname){
        var self = this;
        var product_id = false;
        var found = false;
        for(var pid in self.product_map){
            if(found){
                break;
            }
            var names =  self.product_map[pid];
            for(var x in names){
                var n = names[x];
                n = n.toLowerCase();
                pname = pname.toLowerCase();
                if(n.indexOf(pname) > -1){
                    product_id = pid;
                    found = true;
                    break;
                }
            }
        }
        return product_id;
    },


    price_change:function(source_field_ids,target_field_ids){
        var self = this;
        var source_field = source_field_ids[0];
        var value =  $("#field_id_"+source_field).val();
        var product_id = self.getProductIdFromMap(value);
        if(product_id){
            if(typeof price_change_data[product_id] == "undefined"){
                for(var field_index in target_field_ids){
                    var field =  target_field_ids[field_index];
                    if(field){
                        $("#field_id_"+field).val('');
                    }
                }
                return false;
            }
            else{
                var value = price_change_data[product_id]['value'];
                //get the fields to insert the value.
                for(var field_index in target_field_ids){
                    var field =  target_field_ids[field_index];
                    if(field){
                        $("#field_id_"+field).val(value);
                    }
                }
                return value;
            }
        }
        else{
            for(var field_index in target_field_ids){
                var field =  target_field_ids[field_index];
                if(field){
                    $("#field_id_"+field).val('');
                }
            }
            return false;
        }
    },


    multiply:function(source_field_ids,target_field_ids){
        var values_arr = [];
        for(var field_index in source_field_ids){
            var field =  source_field_ids[field_index];
            if(field){
                var val = $("#field_id_"+field).val();
                if(!val){
                    val = 0;
                }
                values_arr.push(parseFloat(val));
            }
        }
        var total = 1;
        for(var field_index in values_arr){
            var v =  values_arr[field_index];
            total = total * v;
        }
        total = total.toFixed(2);
        for(var field_index in target_field_ids){
            var field =  target_field_ids[field_index];
            if(field){
                if(total == 1){
                    $("#field_id_"+field).val('');
                }
                else{
                    $("#field_id_"+field).val(total);
                }
            }
        }
    },


    previous_day:function(source_field_ids,target_field_ids){
        var self = this;
        var control_field = source_field_ids[0];
        var source_field = source_field_ids[1];
        var form_id =  $("#field_id_"+control_field).attr('data-field-form_id');
        var control_field_current_value =  $("#field_id_"+control_field).val();
        var fdata = previous_day_records[form_id];
        var record_values = fdata['values'];
        for(var field_index in target_field_ids){
            var target_field =  target_field_ids[field_index];
            if(target_field){
                for(var row_id in record_values){
                    if(typeof  record_values[row_id]['values'][control_field] == "undefined"){
                        var previous_val = '';
                        $("#field_id_"+target_field).val(previous_val);
                    }
                    else{
                        var p_value =  record_values[row_id]['values'][control_field]['value'];
                        var previous_val = '';
                        if(control_field_current_value == p_value){
                            if(typeof  record_values[row_id]['values'][source_field] == "undefined"){

                            }
                            else{
                                var previous_val  =  record_values[row_id]['values'][source_field]['value'];
                            }
                            $("#field_id_"+target_field).val(previous_val);
                            break;
                        }
                        else{
                            $("#field_id_"+target_field).val(previous_val);
                        }
                    }
                }
            }
        }
    },

    sum_current:function(source_field_ids,target_field_ids){
        var self = this;
        return self.sum_fields(source_field_ids,target_field_ids,current_day_records);
    },

    sum_previous:function(source_field_ids,target_field_ids){
        var self = this;
        return self.sum_fields(source_field_ids,target_field_ids,previous_day_records);
    },


    sum_fields:function(source_field_ids,target_field_ids,data_source){
        var self = this;
        var source_field = source_field_ids[0];
        var form_id = self.getFormIdByFieldId(source_field);
        var records = data_source[form_id]['values'];
        var total = 0;
        for(var x in records){
            var record = records[x]['values'];
            var val = parseFloat(record[source_field]['value']);
            total = total + val;
        }

        for(var field_index in target_field_ids){
            var target_field =  target_field_ids[field_index];
            if(target_field){
                $("#field_id_"+target_field).val(total);
            }
        }
        return total;
    },


    sum_total_by_value:function(source_field_values,target_field_ids,field_id){
        var self = this;
        var form_id = self.getFormIdByFieldId(field_id);
        var records = current_day_records[form_id]['values'];
        var total = 0;

        for(var x in source_field_values){
            var val =  source_field_values[x];
            var break_record_loop = false;
            if(val){
                var sign = val.charAt(0);
                if(sign == '-'){
                    val = val.substr(1);
                }
                for(var r_id in records){
                    if(break_record_loop){
                        break;
                    }
                    var record_fields = records[r_id]['values'];
                    for(var f_id in record_fields){
                        var field = record_fields[f_id];
                        if(field.value == val){
                            var temp = parseFloat(current_day_records[form_id]['values'][r_id]['values'][field_id]['value']);
                            if(sign == '-'){
                                total = total - temp;
                            }
                            else{
                                total = total + temp;
                            }

                            break_record_loop = true;
                            break;
                        }
                    }
                }
            }
        }

        for(var field_index in target_field_ids){
            var target_field =  target_field_ids[field_index];
            if(target_field){
                $("#field_id_"+target_field).val(total);
            }
        }
    },



    getFormIdByFieldId:function(field_id){
        var form_id = null;
        for(var x in forms_n_fields){
            var form_obj = forms_n_fields[x];
            var fields = form_obj.fields;
            if(typeof fields[field_id] == "undefined"){

            }
            else{
                form_id = form_obj['id'];
                break;
            }
        }
        return form_id;
    }
}


var FormRules = {

    actions:{
        'on_focus':{},
        'on_blur':{},
        'on_change':{},
        'before_render':{},
        'after_render':{}
    },

    init:function(){
        var self = this;
        self.initFieldActions();
    },


    initFieldActions:function(){
        var self = this;
        for(var form_id in forms_n_fields){
            var fields =  forms_n_fields[form_id]['fields'];
            for(var x in fields){
                var field = fields[x];
                self.actions['on_focus'][field['id']]=field['on_focus'];
                self.actions['on_blur'][field['id']]=field['on_blur'];
                self.actions['on_change'][field['id']]=field['on_change'];
                self.actions['before_render'][field['id']]=field['before_render'];
                self.actions['after_render'][field['id']]=field['after_render'];
            }
        }

        //Now bind all form fields for these events
        $('.dsrp_select').live('change',function(){
            var me = $(this);
            var value = me.val();
            var tr = me.parents().parents();
            var field_id = me.attr('data-field_id');
            var rule_type = me.parents().attr('data-rule_type');
            self.getAction('change',field_id,value,tr,rule_type);
        });

        $('.dsrp_text').live('focus',function(){
            var me = $(this);
            var value = me.val();
            var tr = me.parents().parents();
            var field_id = me.attr('data-field_id');
            var rule_type = me.parents().attr('data-rule_type');
            self.getAction('on_focus',field_id,value,tr,rule_type);
        });

        $('.dsrp_text').live('blur',function(){
            var me = $(this);
            var value = me.val();
            var tr = me.parents().parents();
            var field_id = me.attr('data-field_id');
            var rule_type = me.parents().attr('data-rule_type');
            self.getAction('on_blur',field_id,value,tr,rule_type);
        });
    },


    getAction:function(type,field_id,value,parent_tr,rule_type){
        var self = this;
        if(type == 'change'){
            var action = self.actions['on_change'][field_id];
            if(action){
                self.processAction(field_id,action,value,parent_tr,rule_type);
            }
        }
        else if(type == 'on_focus'){
            var action = self.actions['on_focus'][field_id];
            if(action){
                self.processAction(field_id,action,value,parent_tr,rule_type);
            }
        }
        else if(type == 'on_blur'){
            var action = self.actions['on_blur'][field_id];
            if(action){
                self.processAction(field_id,action,value,parent_tr,rule_type);
            }
        }
    },


    processAction:function(field_id,action_p,value,parent_tr,rule_type){
        var self = this;
        var action = '';
        var source_field_ids = [];
        var target_field_ids = [];
        var trigger_field_ids = false;
        var action_block = action_p;
        if(rule_type == 'rule_by_value'){
            action_block = self.getActionByValue(parent_tr,action_p);
        }
        var action_fields = action_block.split('=');
        for(var x in action_fields){
            var part = action_fields[x];
            //Action
            if(part.indexOf('action') > -1){
                var action_arr = part.split(':');
                action = action_arr[1];
            }
            //Source
            else if(part.indexOf('source') > -1){
                var fields_arr = part.split(':');
                var fields = fields_arr[1];
                source_field_ids = fields.split(',');
            }
            //Target
            else if(part.indexOf('target') > -1){
                var target_arr = part.split(':');
                var targets = target_arr[1];
                target_field_ids = targets.split(',');
            }
            //Trigger
            else if(part.indexOf('trigger') > -1){
                var trigger_arr = part.split(':');
                var trigger_ids = trigger_arr[1];
                trigger_field_ids = trigger_ids.split(',');
            }
        }

        if(action == 'price_change'){
            RuleActions.price_change(source_field_ids,target_field_ids);
        }
        if(action == 'multiply'){
            RuleActions.multiply(source_field_ids,target_field_ids);
        }
        if(action == 'previous_day'){
            RuleActions.previous_day(source_field_ids,target_field_ids);
        }
        if(action == 'sum_previous'){
            RuleActions.sum_previous(source_field_ids,target_field_ids);
        }
        if(action == 'sum_current'){
            RuleActions.sum_current(source_field_ids,target_field_ids);
        }
        if(action == 'sum_total_by_value'){
            RuleActions.sum_total_by_value(source_field_ids,target_field_ids,field_id);
        }

        //Trigger the trigger if any
        if(trigger_field_ids){
            for(var field_index in trigger_field_ids){
                var field =  trigger_field_ids[field_index];
                if(field){
                    $("#field_id_"+field).change();
                    $("#field_id_"+field).focus();
                    $("#field_id_"+field).blur();
                }
            }
            //Set the focus back to the working field
            $("#field_id_"+field_id).focus();
        }
    },


    getActionByValue:function(parent_tr,action_p){
        //Get the control field value
        var td = parent_tr.find("[data-control_field='yes']");
        var return_action = '';
        if(td){
            var value = td.attr('data-value');
            if(value){
                var action_blocks = action_p.split('||');
                for(var x in action_blocks){
                    var part = action_blocks[x];
                    //Action
                    if(part.indexOf(value) > -1){
                        var action_arr = part.split('@');
                        return_action = action_arr[1];
                        break;
                    }
                }
                return return_action;
            }
            else{
                return return_action;
            }
        }
        else{
            return return_action;
        }
    }

};

/* when the page is loaded */
$(document).ready(function () {
    FormRules.init();
});