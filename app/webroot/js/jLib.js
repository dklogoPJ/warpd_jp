/*
 * @name ajaxLib.js
 * @author : Amissah Gideon<kuulmek@yahoo.com>
 * @version 1.0
 */
var dateFormat = function () {
    var token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
        timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
        timezoneClip = /[^-+\dA-Z]/g,
        pad = function (val, len) {
            val = String(val);
            len = len || 2;
            while (val.length < len) val = "0" + val;
            return val;
        };

    // Regexes and supporting functions are cached through closure
    return function (date, mask, utc) {
        var dF = dateFormat;

        // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
        if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
            mask = date;
            date = undefined;
        }

        // Passing date through Date applies Date.parse, if necessary
        date = date ? new Date(date) : new Date;
        if (isNaN(date)) throw SyntaxError("invalid date");

        mask = String(dF.masks[mask] || mask || dF.masks["default"]);

        // Allow setting the utc argument via the mask
        if (mask.slice(0, 4) == "UTC:") {
            mask = mask.slice(4);
            utc = true;
        }

        var _ = utc ? "getUTC" : "get",
            d = date[_ + "Date"](),
            D = date[_ + "Day"](),
            m = date[_ + "Month"](),
            y = date[_ + "FullYear"](),
            H = date[_ + "Hours"](),
            M = date[_ + "Minutes"](),
            s = date[_ + "Seconds"](),
            L = date[_ + "Milliseconds"](),
            o = utc ? 0 : date.getTimezoneOffset(),
            flags = {
                d:    d,
                dd:   pad(d),
                ddd:  dF.i18n.dayNames[D],
                dddd: dF.i18n.dayNames[D + 7],
                m:    m + 1,
                mm:   pad(m + 1),
                mmm:  dF.i18n.monthNames[m],
                mmmm: dF.i18n.monthNames[m + 12],
                yy:   String(y).slice(2),
                yyyy: y,
                h:    H % 12 || 12,
                hh:   pad(H % 12 || 12),
                H:    H,
                HH:   pad(H),
                M:    M,
                MM:   pad(M),
                s:    s,
                ss:   pad(s),
                l:    pad(L, 3),
                L:    pad(L > 99 ? Math.round(L / 10) : L),
                t:    H < 12 ? "a"  : "p",
                tt:   H < 12 ? "am" : "pm",
                T:    H < 12 ? "A"  : "P",
                TT:   H < 12 ? "AM" : "PM",
                Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
            };

        return mask.replace(token, function ($0) {
            return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
        });
    };
}();

// Some common format strings
dateFormat.masks = {
    "default":      "ddd mmm dd yyyy HH:MM:ss",
    shortDate:      "m/d/yy",
    mediumDate:     "mmm d, yyyy",
    longDate:       "mmmm d, yyyy",
    fullDate:       "dddd, mmmm d, yyyy",
    shortTime:      "h:MM TT",
    mediumTime:     "h:MM:ss TT",
    longTime:       "h:MM:ss TT Z",
    isoDate:        "yyyy-mm-dd",
    isoTime:        "HH:MM:ss",
    isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
    isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
    dayNames: [
        "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
        "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
    ],
    monthNames: [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
        "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
    ]
};


Number.prototype.formatMoney = function (c, d, t) {
    var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

if(!String.trim){
    String.prototype.trim = function(){ return this.replace(/^\s+|\s+$/g,'');}
}

if(!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(needle) {
        for(var i = 0; i < this.length; i++) {
            if(this[i] === needle) {
                return i;
            }
        }
        return -1;
    };
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function isNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
        && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function countObjectProperties(obj) {
    var count = 0;

    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            ++count;
    }

    return count;
}

/*******************************************************************************
 *            COLLECTIONS CLASS 2.0
 ******************************************************************************/
function Collection(data) {
    this.list = {};
    this.size = 0;
    this.callbacks = {
        add:[],
        remove:[]
    };

    if (data) {
        var self = this;
        $.each(data, function (k, v) {
            self.add(k, v);
        });
    }
}

Collection.prototype.registerCallback = function (type, callback) {
    var self = this;
    if (typeof self.callbacks[type] != 'undefined') {
        self.callbacks[type].push(callback);
    }
}

Collection.prototype.fireCallbacks = function (type, key, value) {
    var self = this;
    for (var i = 0; i < self.callbacks[type].length; i++) {
        self.callbacks[type][i](key, value);
    }
}

/**
 * Adds a new object to the collection if the key for the object does not already exist
 * @param key the collection key
 * @param value the value to add
 **/
Collection.prototype.add = function (key, value) {
    var self = this;
    if (typeof self.list[key] == 'undefined') {
        self.list[key] = value;
        self.size++;

        self.fireCallbacks('add', key, value);
    }
}
/**
 * Removes the object identified by the key from the collection
 * @param key the search key
 **/
Collection.prototype.remove = function (key) {
    var self = this;
    if (typeof self.list[key] != 'undefined') {
        var value = self.list[key];
        delete self.list[key];
        self.size--;

        self.fireCallbacks('remove', key, value);
    }
}

/**
 * Returns the values stored for this key
 * @param key the search key
 **/
Collection.prototype.get = function (key) {
    var self = this;
    // if the a valid key is specified
    if (self.list.hasOwnProperty(key)) {
        return self.list[key];
    }
    // if the key specified is a number, then we may be looking for an element at a specified index
    if (!isNaN(parseInt(key))) {
        var index = 0;
        for (var k in self.list) {
            if (index == key) {
                return self.list[k];
            }
            index++;
        }
    }
    return null;
}

/**
 * Replaces or adds a new value to the map
 * @param key the key for the value
 * @param value the value to add
 */
Collection.prototype.set = function (key, value) {
    var self = this;
    self.list[key] = value;
}

/**
 * Returns the list of items stored in this collection
 * @return the list of items as an object
 */
Collection.prototype.getList = function () {
    var self = this;
    return self.list;
};

/**
 * Returns the count of the number items in this collection
 * @return number of items in this collection
 */
Collection.prototype.getSize = function () {
    var self = this;
    return self.size;
};

Collection.prototype.iterate = function (callback) {
    var self = this;
    for (var index in self.list) {
        if (typeof callback == "function") {
            callback.apply(self, [index, self.list[index]]);
        }
    }
};

Collection.prototype.getKeys = function () {
    var self = this;
    var keys = [];
    keys = Object.keys(self.list);
    return keys;
};

/**
 * Resets the list to an empty object
 */
Collection.prototype.clear = function () {
    var self = this;
    self.list = {};
}

/*******************************************************************************
 *            END OF COLLECTIONS CLASS
 ******************************************************************************/



var jLib = {
    /* Function init
     * This function is the entry point of the Object.
     * @param void
     * @return void
     * @access public
     * */
    inactivity_timer:null,
    user_group:'',
    user_right:0,
    halt_execution:false,

     init:function () {
        var self = this;
        self.user_group = $("#user_group_hidden").val();
         self.user_right = $("#user_rights").val();
        /*Show a loading message whenever an Ajax request starts (and none is already active).*/
        $("#loader_bar").ajaxStart(function () {
            $(this).show();
        });

        $("#loader_bar").ajaxStop(function () {
            $(this).hide();
        });

        //Fire inactivity so we can logout when inactive
        $(document).bind("click", self.setupInactivityTimer).bind("mouseenter", self.setupInactivityTimer);

        self.checkMail();
         if(typeof datepicker == "function"){
             if( $('.datepicker').length > 0){
                 $('.datepicker').datepicker({
                     inline: true,
                     changeMonth: true,
                     changeYear: true
                 });
                 $('.datepicker').datepicker( "option", "dateFormat", 'dd-mm-yy' );
             }
         }

         self.numbersOnly();
    },

    numbersOnly: function(){
        $(".numbersOnly").keydown(function(event) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ( $.inArray(event.keyCode,[46,8,9,27,13,190]) !== -1 ||
                // Allow: Ctrl+A
                (event.keyCode == 65 && event.ctrlKey === true) ||
                // Allow: home, end, left, right
                (event.keyCode >= 35 && event.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            else {
                // Ensure that it is a number and stop the keypress
                if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                    event.preventDefault();
                }
            }
        });
    },

    setupInactivityTimer: function() {
        var self = this;
        var controller = $("#current_page_controller").val();
        var action = $("#current_page_action").val();
        if(controller == 'Simulator'){
            return;
        }
        if(controller == 'Users' && action == 'login'){
            return;
        }
        var clear = function() {
            clearTimeout( self.inactivity_timer );
            self.inactivity_timer = setTimeout(function() {
                window.location.href = $("#logout-url").val()+'/auto';
            }, 1000 * 60 * 300);
           // console.log("inactivity timer reset");
        };
        clear();
    },

    serverError:function (text) {
        var self = this;
        //first hide modal loader if any
        //self.hideFormLoading();
        if (typeof text == 'undefined') {
            text = "Error while contacting server, please reload the page.";
        }

        self.message('Server Error',text, 'error');
    },

    //////////////////////Order General Functions
    array_diff:function (a1, a2) {
        var a = [], diff = [];
        for (var i = 0; i < a1.length; i++)
            a[a1[i]] = true;
        for (var i = 0; i < a2.length; i++)
            if (a[a2[i]]) delete a[a2[i]];
            else a[a2[i]] = true;
        for (var k in a)
            diff.push(k);
        return diff;
    },


    formatNumber:function($value,$type,$decimal_place){

        if(!$type){
            $type='number';
        }
        /*if(!$decimal_place){
            $decimal_place=2;
        }
        if($decimal_place == 0){

        }*/
        //$value = parseFloat(Math.round($value * 100) / 100).toFixed($decimal_place);
        var rt = 0;
        if($type == 'money'){// Money Format
            //rt =  parseFloat($value).formatMoney($decimal_place, '.', ',');
            rt = accounting.formatMoney($value,'',$decimal_place);
            //rt =  $value.formatMoney($decimal_place, '.', ',');
        }
        else{// Decinmal Number
            //rt =  parseFloat($value).toFixed($decimal_place);
           /* if($decimal_place == 0){

            }*/
            rt = accounting.toFixed($value,$decimal_place)
            //rt =  $value.toFixed($decimal_place);
        }

        return rt;
    },


    convertDate: function ($date, $type){
        if($date.length == 0){
            return '';
        }
        var d = new Date($date);
        var $res = '';
        if($type == 'mysql'){
            $res = d.toFormattedString('yyyy-MM-dd');
        }
        else if($type == 'ui'){
            $res =  d.toFormattedString('MMMM dd, yyyy');
        }
        else if($type == 'ui_time'){
           // d.to
            $res =  d.toFormattedString('MMMM dd, yyyy');
        }
        return $res;
    },

    getTodaysDate: function ($type){
        var $res = '';
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() +1 //January is 0
        var yyyy = today.getFullYear();

        if(dd < 10){dd='0'+dd;}
        if(mm < 10){mm='0'+mm;}
        var year = (yyyy < 1000) ? yyyy + 1900 : yyyy
        if($type == 'mysql'){
            $res = year+'-'+mm+'-'+dd;
        }
        else if($type == 'mysql_flip'){
            $res = dd+'-'+mm+'-'+year;
        }
        else{
            $res = dd+'/'+mm+'/'+year;
        }
        return $res;
    },


    message: function(title, txt, type ,callback){
        var self = this;
        var cl = '';
        //if modal exist close it and call new one
        var visible = $('#modal_notify').is(':visible');
        if(visible){
            $("#modal_notify a.close").click();
        }

        if(!type){
            type = 'info';
        }

        if(type == 'success'){
            cl = 'alert-success';
        }
        else if(type == 'warning'){
            cl = 'alert-gradient';
        }
        else if(type == 'error'){
            cl = 'alert-error';
        }
        else if(type == 'info'){
            cl = 'alert-info';
        }

        var msg = "<div id='modal_notify' class='alert "+cl+"' style='position: fixed; z-index: 9999; width:500px; left: 50%; margin-left: -250px; top: 180px;'>";
        msg += "<a class='close' data-dismiss='alert' style='opacity: 1; line-height: 16px;' href='#'>✕</a>";
        msg += "<h4 class='alert-heading'>"+title+"</h4>";
        msg += "<span><div id='flashMessage' class='message'>"+txt+"</div></span>";
        msg += "</div>";

        self.modal_blanket_show();
        $('body').append(msg);

        $("#modal_notify a.close").click(function(){
            self.modal_blanket_hide();
            if(typeof callback == 'function'){
                callback();
            }
        });
    },

    notification:function (title, mesg, auto_close, show_icon) {
        var positionHorizontal = 'right', positionVertical = 'top',
            closeButton = true, showCloseOnHover = false;
        if (typeof show_icon == 'undefined') {
            show_icon = true;
        }

        if (typeof auto_close == 'undefined') {
            auto_close = true;
        }

        // Gather options
        notify(title, mesg, {
            system:false, //
            vPos:positionVertical,
            hPos:positionHorizontal,
            autoClose:auto_close,
            icon:(show_icon) ? $("#notification-user-src").val() : '',
            iconOutside:true,
            closeButton:closeButton,
            showCloseOnHover:showCloseOnHover,
            groupSimilar:true
        });
    },

    modal_blanket_show:function(){
        $("#modal_blanket").show();
    },

    modal_blanket_hide:function(){
        $("#modal_blanket").hide();
    },

    confirm:function(txt){
        var self = this;
        self.halt_execution = true;
        var choice = null;
        jConfirm(txt, 'Confirm', function(confirmation) {
            choice = confirmation;
            self.halt_execution = false;
        });
        //loop until halt_execution is false
       // do{}while( self.halt_execution);
        for(var x= (self.halt_execution)?0:1; x==1;){

        }
        console.log('released');
        return choice
    },

    floatOnly:function($this, event){
        // Backspace, tab, enter, end, home, left, right,decimal(.)in number part, decimal(.) in alphabet
        // We don't support the del key in Opera because del == . == 46.
        var controlKeys = [8, 9, 13, 35, 36, 37, 39,110,190];
        // IE doesn't support indexOf
        var isControlKey = controlKeys.join(",").match(new RegExp(event.which));
        // Some browsers just don't raise events for control keys. Easy.
        // e.g. Safari backspace.
        if (!event.which || // Control keys in most browsers. e.g. Firefox tab is 0
            (49 <= event.which && event.which <= 57) || // Always 1 through 9
            (96 <= event.which && event.which <= 106) || // Always 1 through 9 from number section
            (48 == event.which && $this.attr("value")) || // No 0 first digit
            (96 == event.which && $this.attr("value")) || // No 0 first digit from number section
            isControlKey) { // Opera assigns values for control keys.
            return;
        } else {
            event.preventDefault();
        }
    },

  /*  highlightRow:function (grid) {
        var self = this;
        $('.trSelected', grid).each(function () {
            $(this).removeClass('trSelected');
            var chk = $(this).find(':checkbox').is(':checked');
            if (chk) {
                $(this).find(':checkbox').attr('checked', false);
            }
            var has_class = false;
            if ($(this).hasClass('erow')) {
                $(this).removeClass('erow');
                has_class = true;
            }
            $(this).animate({backgroundColor:"#bbe4ff"}, "slow", "linear")
                .animate({backgroundColor:"#bbe4ff"}, 4000)
                .animate({backgroundColor:"#ffffff"}, "slow", "linear", function () {
                    // Animation complete.
                    if (has_class) {
                        $(this).addClass('erow');
                    }
                });
        });
        return;
    },

    updateGridData:function (grid, data) {
        var self = this;
        $('.trSelected', grid).each(function () {
            var tr = $(this);
            $(tr).find('td').each(function () {
                var val = data[$(this).attr('field')];
                if (typeof val != 'undefined') {
                    $(this).find('div').html(val);
                }
            })
        });
    },

    removeGridRows:function (grid) {
        var self = this;
        $('.trSelected', grid).each(function () {
            //$(this).removeClass('trSelected');
            var tr = $(this);
            var data_id =  tr.attr('data-id');
            var next = tr.next("tr.parent_tr"+data_id);
            tr.animate({backgroundColor:"#bbe4ff"}, 800, "linear")
                .animate({opacity:0}, 800, "linear", function () {
                    tr.remove();
            });
            next.animate({backgroundColor:"#bbe4ff"}, 800, "linear")
                .animate({opacity:0}, 800, "linear", function () {
                    next.remove();
            });
        });
    },

    countSelectedRows:function (grid) {
        var self = this;
        var selected_values = 0;
        $('.trSelected', grid).each(function () {
            selected_values = selected_values + 1;
        });
        return selected_values;
    },

    rowSelectedCheck:function (grid, limit) {
        var self = this;
        var count = jLib.countSelectedRows(grid);
        if (limit) {
            if (count < limit) {
                //jAlert("At least "+limit+" Record(s) needs to be selected prior to this action",'Alert');
                var content = "At least " + limit + " Record(s) needs to be selected prior to this action";
                self.message('Validation', content, 'error');
                return false;
            }
            else if (count > limit) {
                //jAlert("Only "+limit+" Record(s) at a time",'Alert');
                var content = "Only " + limit + " Record(s) at a time";
                self.message('Validation', content, 'error');
                return false;
            }
        }
        else {
            if (count == 0) {
                //jAlert("At least 1 Record needs to be selected prior to this action",'Alert');
                var content = "At least 1 Record needs to be selected prior to this action";
                self.message('Validation', content, 'error');
                return false;
            }
        }

        return true
    },

    getSelectedRowIds:function (grid) {
        var self = this;
        var selected_values = new Array();
        $('.trSelected', grid).each(function () {
            var id = $(this).attr('id');
            id = id.substring(id.lastIndexOf('row') + 3);
            selected_values.push(id);
        });
        return selected_values;
    },

    getSelectedRowColData:function (grid, col) {
        var self = this;
        var selected_values = new Array();
        $('.trSelected', grid).each(function () {
            $(this).find('td').each(function () {
                var field_value = $(this).attr('field');
                if (typeof field_value !== 'undefined' && field_value !== false && field_value == col) {
                    var content = $(this).find('div').html();
                    selected_values.push(content);
                }
            });
        });
        return selected_values;
    },

    getSelectedRows:function (grid) {
        var self = this;
        var selected_rows = new Array();
        $('.trSelected', grid).each(function () {
            selected_rows.push($(this));
        });
        return selected_rows;
    },*/

    /*Universal Delete for all Main record DataTable*/
    do_delete:function (url, grid, callback) {
        var self = this;
        var row_ids = FlexObject.getSelectedRowIds(grid);

        jConfirm('Are you sure you want to continue ?', 'Confirm', function(confirmation) {
            if(confirmation){
                $.post(url, {'ids[]': row_ids},function(response){
                    //delete was successful, remove checkbox rows
                    if(response.code == 0){
                        FlexObject.removeGridRows(grid);
                        self.message('Delete',response.msg,'success');

                        if(typeof callback == 'function'){
                            callback();
                        }
                    }
                    else if(response.code == 1){
                        self.setFeedback(response.msg,'error_message',false);
                    }
                },"json");
            }
        });
    },



    // For Sub Grid ***********************************///////

  /*  highlightSubRow:function (grid) {
        var self = this;
        $('tr.trSubSelected', grid).each(function () {
            $(this).removeClass('trSubSelected');
            $(this).animate({backgroundColor:"#bbe4ff"}, "slow", "linear")
                .animate({backgroundColor:"#bbe4ff"}, 4000)
                .animate({backgroundColor:"#ffffff"}, "slow", "linear", function () {

                });
        });
        return;
    },

    updateSubGridData:function (grid, data) {
        var self = this;
        $('tr.trSubSelected', grid).each(function () {
            var tr = $(this);
            $(tr).find('td').each(function () {
                var val = data[$(this).attr('field')];
                if (typeof val != 'undefined') {
                    $(this).find('div').html(val);
                }
            })
        });
    },

    removeSubGridRows:function (grid) {
        var self = this;
        $('tr.trSubSelected', grid).each(function () {
            //$(this).removeClass('trSelected');
            $(this).animate({backgroundColor:"#bbe4ff"}, 800, "linear")
                .animate({opacity:0}, 800, "linear", function () {
                    $(this).remove();
                });
        });
    },

    countSelectedSubRows:function (grid) {
        var self = this;
        var selected_values = 0;
        $('tr.trSubSelected', grid).each(function () {
            selected_values = selected_values + 1;
        });
        return selected_values;
    },

    rowSubSelectedCheck:function (grid, limit) {
        var self = this;
        var count = jLib.countSelectedSubRows(grid);
        if (limit) {
            if (count < limit) {
                //jAlert("At least "+limit+" Record(s) needs to be selected prior to this action",'Alert');
                var content = "At least " + limit + " Record(s) needs to be selected prior to this action";
                self.message('Validation', content, 'error');
                return false;
            }
            else if (count > limit) {
                //jAlert("Only "+limit+" Record(s) at a time",'Alert');
                var content = "Only " + limit + " Record(s) at a time";
                self.message('Validation', content, 'error');
                return false;
            }
        }
        else {
            if (count == 0) {
                //jAlert("At least 1 Record needs to be selected prior to this action",'Alert');
                var content = "At least 1 Record needs to be selected prior to this action";
                self.message('Validation', content, 'error');
                return false;
            }
        }

        return true
    },

    getSelectedSubRowIds:function (grid) {
        var self = this;
        var selected_values = new Array();
        $('tr.trSubSelected', grid).each(function () {
            var id = $(this).attr('data-id');
            selected_values.push(id);
        });
        return selected_values;
    },

    getSelectedSubRowColData:function (grid, col) {
        var self = this;
        var selected_values = new Array();
        $('tr.trSubSelected', grid).each(function () {
            $(this).find('td').each(function () {
                var field_value = $(this).attr('field');
                if (typeof field_value !== 'undefined' && field_value !== false && field_value == col) {
                    var content = $(this).find('div').html();
                    selected_values.push(content);
                }
            });
        });
        return selected_values;
    },

    getSelectedSubRows:function (grid) {
        var self = this;
        var selected_rows = new Array();
        $('tr.trSubSelected', grid).each(function () {
            selected_rows.push($(this));
        });
        return selected_rows;
    },*/

    /*Universal Delete for all Main record DataTable*/
    do_sub_delete:function (url, grid, callback) {
        var self = this;
        var row_ids = FlexObject.getSelectedSubRowIds(grid);

        jConfirm('Are you sure you want to continue ?', 'Confirm', function(confirmation) {
            if(confirmation){
                $.post(url, {'ids[]': row_ids},function(response){
                    //delete was successful, remove checkbox rows
                    if(response.code == 0){
                        FlexObject.removeSubGridRows(grid);
                        self.message('Delete',response.msg,'success');

                        if(typeof callback == 'function'){
                            callback();
                        }
                    }
                    else if(response.code == 1){
                        self.setFeedback(response.msg,'error_message',false);
                    }
                },"json");
            }
        });
    },


    checkMail:function(){
        var self = this;
        setInterval(function(){
            var current_page_action = $('#current_page_action').val();
            var url = $("#mail-check-url").val();
            var query = '';
            if(current_page_action == 'login'){
                return;
            }
            /* Send the data to the server and handle the server response */

            $.ajax({
                url:url,
                data:query,
                dataType:'json',
                type:'POST',
                success:function (response) {
                    var txt = '';
                    if (typeof response.mesg == 'object') {
                        for (megTxt in response.mesg) {
                            txt += response.mesg[megTxt] + '<br />';
                        }
                    }
                    else {
                        txt = response.mesg
                    }
                    if (response.code === 0) {
                        //check for new mails and display notifications
                        var count = response.total_count;
                        var titles = response.data;
                        var message_link = $("#mail-link").val();
                        // What about a notification?
                        jLib.notification('Notification', "You have "+count+" new notification(s); <br />"+titles+" <a href='"+message_link+"'>click here to view.</a>");
                        $("#user_message_counter").html(count);
                    }
                    //* When there are Errors *//*
                    else if (response.code === 1) {
                        $("#user_message_counter").html(0);
                    }
                },
                error:function (xhr) {
                    console.log(xhr.responseText);
                   // jLib.serverError();
                }
            });
        },1000 * 150)// 15 sec system  mail check
    }

};

/* when the page is loaded */
$(document).ready(function () {
    //Hiding the loader bar
    jLib.init();
});