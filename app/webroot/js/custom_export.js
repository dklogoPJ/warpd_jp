var CustomExport = {

    extra_params: null,
    external_elements: {},
    url: '',

    init: function () {
        var self = this;

        if(typeof validationEngine == "function"){
            if( $('#form-export').length > 0){
                $("#form-export").validationEngine();
            }
        }

        $(".export-me-btn").click(function () {
            var btn = $(this);
            self.url = btn.attr('data-url');
            var filter_elements = btn.attr('data-filter-elements');
            var properties = [];
            if(typeof filter_elements != "undefined"){
                properties = filter_elements.split(',');
            }
            properties.forEach(function(property) {
                var label_fieldname_arr = property.split(':');
                var label = label_fieldname_arr[0];
                var field_name = label_fieldname_arr[1];
                self.external_elements[label]=field_name;
            });

            self.displayFilterElements();

            $('#export_modal').modal({
                backdrop: 'static',
                show: true,
                keyboard: true
            });
        });

        $("#export-window-btn").click(function () {
            //$('#reset_modal #id').val(id);
            var validationStatus = $('#form-export').validationEngine({returnIsValid: true});
            if (validationStatus) {
                self.processExportWindowForm();
            }
        });

        $('input.datepicker').live('focus', function () {
            if (false == $(this).hasClass('hasDatepicker')) {
                $(this).datepicker({
                    inline: true,
                    changeMonth: true,
                    changeYear: true
                });
                $(this).datepicker("option", "dateFormat", 'dd-mm-yy');
            }
        });
    },

    displayFilterElements: function () {
        var self = this;
        $("#export_modal .modal-filter-elements").html('');
        var elements = self.external_elements;
        for (var label in  elements) {
            var element = elements[label];
            var elementType = $("#"+element).prop('tagName');
            var get_element_val = $("#"+element).val();
            if(elementType == 'SELECT'){
                get_element_val = $("#"+element+" option:selected").text();
            }
            var control = $("<div />").addClass('control-group');
            var control_label = $("<label />").addClass('control-label').attr('style',"float: left; width: 140px;").html(label+' :');
            var control_value = $("<div />").addClass('controls').html(get_element_val);
            control.append(control_label).append(control_value);
            $("#export_modal .modal-filter-elements").append(control);
        }
    },

    processExportWindowForm: function () {
        var self = this;
        $("#export-window-form").html('');
        var hidden_el = $("<input />").attr('type','hidden').attr('name','exp_startdt').val($("#export_startdt").val());
        $("#export-window-form").append(hidden_el);
        var hidden_el = $("<input />").attr('type','hidden').attr('name','exp_enddt').val($("#export_enddt").val());
        $("#export-window-form").append(hidden_el);
        var elements = self.external_elements;
        for (var label in  elements) {
            var element = elements[label];
            var name = 'exp_'+element;
            var get_element_val = $("#"+element).val();
            var hidden_el = $("<input />").attr('type','hidden').attr('name',name).val(get_element_val);
            $("#export-window-form").append(hidden_el);
        }

        $("#export-window-form").attr('action', self.url);
         window.open('', "ExportWindow", "menubar=yes, width=300, height=200,location=no,status=no,scrollbars=yes,resizable=yes");
         $("#export-window-form").submit();
    }
};

/* when the page is loaded */
$(document).ready(function () {
    CustomExport.init();
});