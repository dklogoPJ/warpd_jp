/*** Plugin for extending select to be  **/
(function($){
    var SelectMore = function(element){
        if(element && element.length > 0){
            this.el = element;
            this.Configure();
        }
    }
    SelectMore.prototype.Configure = function(){
        var self = this;
        this.el.addClass('hasMore');
        var opt = $("<option />").attr("value",'click').html('Custom');
        this.el.append(opt);
        this.el.live('change',function(){
            var val = $(this).val();
            if(val == 'click'){
                document.getElementById('select_more_form').reset();
                $('#select_more_add_modal').modal({
                    backdrop: 'static',
                    show: true,
                    keyboard: true
                });
            }
        });

        $('#select_more_form #add_more_btn').click(function(){
            var new_value = $('#select_more_form #vol').val();
            new_value = new_value.trim();
            //validate for numeric
            //isFinite()
            self.addNewValue(new_value)
        });
    }

    SelectMore.prototype.addNewValue = function(new_value){
        var self = this;
        var url  = $("#volumes_url").val();
        if(new_value.length == 0){
            return false;
        }
        //Add the new value to the select element and send the value to be stored in db
        var opt = $("<option />").attr("value",new_value).html(new_value);
        //$("select option:last").before("<option>hi</option>");
        this.el.find("option:last").before(opt);
        //this.el.append(opt);
        this.el.val(new_value);

        //update global volumes
        volumes.push({
            'id':new_value,
            'name':new_value
        })
        asort(volumes);

        var query = "vol=" + new_value;

        $("#close_more_btn").click();

        $.ajax({
            type: 'post',
            url: url,
            data: query,
            dataType: 'json',
            success: function (response) {
                var txt = '';
                if (typeof response.msg == 'object') {
                    var messages = response.msg;
                    var len = messages.length;
                    for (var x = 0; x < len; x++) {
                        txt += messages[x] + '<br />';
                    }
                }
                else {
                    txt = response.msg
                }
                if (response.code == 0) {

                }
                else {

                }
            },
            error: function () {
                jLib.serverError();
            }
        });
    }

    $.fn.select_more  = function(){
        return $(this).each(function () {
            var obj = new SelectMore($(this));
            this.obj = obj;
        });
    };
})(jQuery);