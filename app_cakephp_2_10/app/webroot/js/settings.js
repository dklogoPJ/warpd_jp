/**
 *
 * '||''|.                            '||
 *  ||   ||    ....  .... ...   ....   ||    ...   ... ...  ... ..
 *  ||    || .|...||  '|.  |  .|...||  ||  .|  '|.  ||'  ||  ||' ''
 *  ||    || ||        '|.|   ||       ||  ||   ||  ||    |  ||
 * .||...|'   '|...'    '|     '|...' .||.  '|..|'  ||...'  .||.
 *                                                  ||
 * --------------- By Display:inline ------------- '''' -----------
 *
 * Global template functions
 *
 * Content:
 * 1. Variables declaration
 * 2. Template interface
 * 3. Features detection
 * 4. Touch optimization
 * 5. Position: fixed polyfill
 * 6. Generic functions
 * 7. Custom events
 * 8. DOM watching functions
 * 9. Template setup functions
 * 10. Template setup
 * 11. Viewport resizing handling
 * 12. Template init
 * 13. Event delegation for template elements
 * 14. Tracked elements
 * 15. Custom animations
 * 16. Mobile browser chrome hidding
 * 17. Dependencies
 *
 * Structural good practices from the article from Addy Osmani 'Essential jQuery plugin patterns'
 * @url http://coding.smashingmagazine.com/2011/10/11/essential-jquery-plugin-patterns/
 */

/*
 * The semi-colon before the function invocation is a safety
 * net against concatenated scripts and/or other plugins
 * that are not closed properly.
 */
;(function($, window, document, undefined)
{
    /*
     * undefined is used here as the undefined global variable in ECMAScript 3 is mutable (i.e. it can
     * be changed by someone else). undefined isn't really being passed in so we can ensure that its value is
     * truly undefined. In ES5, undefined can no longer be modified.
     */

    /*
     * window and document are passed through as local variables rather than as globals, because this (slightly)
     * quickens the resolution process and can be more efficiently minified.
     */

    /********************************************************/
    /*               1. Variables declaration               */
    /********************************************************/

    // Objects cache
    var win = $(window),
        doc = $(document),
        bod = $(document.body),

    // Whether auto-watching DOM changes or not
        autoWatch = true,

    // Recursion prevention in setup/clear watcher functions (prevent unnecessary processing)
        watching = true,

    // List of setup functions
        setupFunctions = [],

    // List of clear functions
        clearFunctions = [],

    // Store the timeout id for window.resize
        resizeInt = false,

    // List of media queries sizes with corresponding width of the test element
        mediaQueries = [
            [10, 'mobile-portrait'],
            [20, 'mobile-landscape'],
            [30, 'tablet-portrait'],
            [40, 'tablet-landscape'],
            [50, 'desktop']
        ],

    // Height of the test element if a high-res screen is on
        hiresTestHeight = 20,

    // Position:fixed support
        fixedTest, supportFixed = true, fixed = $(),

    // Touchend instead of click support
        touchMoved = false, touchId = 0,

    // Template has been inited
        init = false;

    /**
     * Parse a css numeric value
     *
     * @param jQuery element the element whose property to parse
     * @param string prop the name of the property
     * @param int def the default value if parsing fails (default: 0)
     * @return the parsed css value, or def
     */
    $.fn.parseCSSValue = function(prop, def)
    {
        var parsed = parseInt(this.css(prop), 10);
        return isNaN(parsed) ? (def || 0) : parsed;
    };

    /********************************************************/
    /*      13. Event delegation for template elements      */
    /********************************************************/

    /*
     * Event delegation is used to handle most of the template setup, as it does also apply to dynamically added elements
     * @see http://api.jquery.com/on/
     */

    // Close buttons
    doc.on('click', '.n_close', function(event)
    {
        var close = $(this),
            parent = close.parent();

        event.preventDefault();

        close.remove();
        parent.addClass('closing').foldAndRemove().trigger('close');
    });

    /********************************************************/
    /*                 15. Custom animations                */
    /********************************************************/

    /**
     * Remove an element with folding effect
     *
     * @param string|int duration a string (fast, normal or slow) or a number of millisecond. Default: 'normal'. - optional
     * @param function callback any function to call at the end of the effect. Default: none. - optional
     */
    $.fn.foldAndRemove = function(duration, callback)
    {
        $(this).slideUp(duration, function()
        {
            // Callback function
            if (callback)
            {
                callback.apply(this);
            }

            $(this).remove();
        });

        return this;
    };

    /**
     * Remove an element with fading then folding effect
     *
     * @param string|int duration a string (fast, normal or slow) or a number of millisecond. Default: 'normal'. - optional
     * @param function callback any function to call at the end of the effect. Default: none. - optional
     */
    $.fn.fadeAndRemove = function(duration, callback)
    {
        this.animate({'opacity': 0}, {
            'duration': duration,
            'complete': function()
            {
                var element = $(this).trigger('endfade');

                // No folding required if the element has position: absolute (not in the elements flow)
                if (element.css('position') == 'absolute')
                {
                    // Callback function
                    if (callback)
                    {
                        callback.apply(this);
                    }

                    element.remove();
                }
                else
                {
                    element.slideUp(duration, function()
                    {
                        // Callback function
                        if (callback)
                        {
                            callback.apply(this);
                        }

                        element.remove();
                    });
                }
            }
        });

        return this;
    };

    /**
     * Shake an element
     * The jQuery UI's bounce effect messes with margins so let's build ours
     *
     * @param int force size (in pixels) of the movement (default: 15)
     * @param function callback any function to call at the end of the effect. Default: none. - optional
     */
    $.fn.shake = function(force, callback)
    {
        // Param check
        force = force || 15;

        this.each(function()
        {
            var element = $(this),

            // Initial margins
                leftMargin = element.parseCSSValue('margin-left'),
                rightMargin = element.parseCSSValue('margin-right'),

            // Force tweening
                steps = [
                    force,
                    Math.round(force*0.8),
                    Math.round(force*0.6),
                    Math.round(force*0.4),
                    Math.round(force*0.2)
                ],

            // Final range calculation
                effectMargins = [
                    [leftMargin-steps[0], rightMargin+steps[0]],
                    [leftMargin+steps[1], rightMargin-steps[1]],
                    [leftMargin-steps[2], rightMargin+steps[2]],
                    [leftMargin+steps[3], rightMargin-steps[3]],
                    [leftMargin-steps[4], rightMargin+steps[4]],
                    [leftMargin, leftMargin]
                ];

            // Queue animations
            $.each(effectMargins, function(i)
            {
                var options = {
                    duration: (i === 0) ? 40 : 80
                };

                // For last step
                if (i === 5)
                {
                    options.complete = function()
                    {
                        // Reset margins
                        $(this).css({
                            marginLeft: '',
                            marginRight: ''
                        });

                        // Callback
                        if (callback)
                        {
                            callback.apply(this);
                        }
                    }
                }

                // Queue animation
                element.animate({ marginLeft: this[0]+'px', marginRight: this[1]+'px' }, options);
            });
        });

        return this;
    };

    /********************************************************/
    /*          16. Mobile browser chrome hidding           */
    /********************************************************/

    /*
     * Normalized hide address bar for iOS & Android
     * Inspired from Scott Jehl's post: http://24ways.org/2011/raising-the-bar-on-mobile
     */

    // If there's a hash, stop here
    if (!location.hash)
    {
        // Scroll to 1
        window.scrollTo(0, 1);
        var scrollTop = 1,
            getScrollTop = function()
            {
                return window.pageYOffset || document.compatMode === 'CSS1Compat' && document.documentElement.scrollTop || document.body.scrollTop || 0;
            },

        // Reset to 0 on bodyready, if needed
            bodycheck = setInterval(function()
            {
                if (document.body)
                {
                    clearInterval(bodycheck);
                    scrollTop = getScrollTop();
                    window.scrollTo(0, scrollTop === 1 ? 0 : 1);
                }
            }, 15);

        win.on('load', function()
        {
            setTimeout(function()
            {
                // At load, if user hasn't scrolled more than 20 or so...
                if (getScrollTop() < 20)
                {
                    // Reset to hide addr bar at onload
                    window.scrollTo(0, scrollTop === 1 ? 0 : 1);
                }
            }, 0);
        });
    }

    /********************************************************/
    /*                   17. Dependencies                   */
    /********************************************************/

    /*
     * Add some easing functions if jQuery UI is not included
     */
    if ($.easing.easeOutQuad == undefined)
    {
        $.easing.jswing = $.easing.swing;
        $.extend($.easing,
            {
                def: 'easeOutQuad',
                swing: function (x, t, b, c, d) {
                    return $.easing[$.easing.def](x, t, b, c, d);
                },
                easeInQuad: function (x, t, b, c, d) {
                    return c*(t/=d)*t + b;
                },
                easeOutQuad: function (x, t, b, c, d) {
                    return -c *(t/=d)*(t-2) + b;
                },
                easeInOutQuad: function (x, t, b, c, d) {
                    if ((t/=d/2) < 1) return c/2*t*t + b;
                    return -c/2 * ((--t)*(t-2) - 1) + b;
                }
            });
    }

    /*
     * Support for mousewheel event
     * Copyright (c) 2010 Brandon Aaron (http://brandonaaron.net)
     * Licensed under the MIT License (LICENSE.txt).
     *
     * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
     * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
     * Thanks to: Seamus Leahy for adding deltaX and deltaY
     *
     * Version: 3.0.4
     *
     * Requires: 1.2.2+
     */

    // List of event names accross browsers
    var types = ['DOMMouseScroll', 'mousewheel'];

    // Event handler function
    function mouseWheelHandler(event)
    {
        var sentEvent = event || window.event,
            orgEvent = sentEvent.originalEvent || sentEvent,
            args = [].slice.call( arguments, 1 ),
            delta = 0,
            deltaX = 0,
            deltaY = 0;
        event = $.event.fix(orgEvent);
        event.type = "mousewheel";

        // Old school scrollwheel delta
        if ( orgEvent.wheelDelta ) { delta = orgEvent.wheelDelta/120; }
        if ( orgEvent.detail     ) { delta = -orgEvent.detail/3; }

        // New school multidimensional scroll (touchpads) deltas
        deltaY = delta;

        // Gecko
        if ( orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
            deltaY = 0;
            deltaX = -1*delta;
        }

        // Webkit
        if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY/120; }
        if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = -1*orgEvent.wheelDeltaX/120; }

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        return $.event.handle.apply(this, args);
    }

    // Register event
    $.event.special.mousewheel = {
        setup: function()
        {
            if (this.addEventListener)
            {
                for (var i=types.length; i;)
                {
                    this.addEventListener(types[--i], mouseWheelHandler, false);
                }
            }
            else
            {
                this.onmousewheel = mouseWheelHandler;
            }
        },

        teardown: function()
        {
            if (this.removeEventListener)
            {
                for (var i=types.length; i;)
                {
                    this.removeEventListener(types[--i], mouseWheelHandler, false);
                }
            }
            else
            {
                this.onmousewheel = null;
            }
        }
    };

    // Add methods
    $.fn.extend({
        mousewheel: function(fn)
        {
            return fn ? this.on("mousewheel", fn) : this.trigger("mousewheel");
        },

        unmousewheel: function(fn)
        {
            return this.off("mousewheel", fn);
        }
    });

})(this.jQuery, window, document);

$(document).ready(function(){
    /* Check cookies */
        /*fixed*/
        var tFixed = $.cookies.get('themeSettings_fixed');
        if(null != tFixed){
            $(".wrapper").addClass('fixed');
            $(".settings input[name=settings_fixed]").attr('checked',true).parent('span').addClass('checked');
        }
        
        /*menu*/
        var tMenu = $.cookies.get('themeSettings_menu');
        if(null != tMenu){
            if(null != tMenu){
                $(".menu").addClass('hidden').hide();
                $(".header_menu li.list_icon").show();
                $(".content").addClass('wide');      
                $(".settings input[name=settings_menu]").attr('checked',true).parent('span').addClass('checked');
            }
        }
        /*bg*/
        var tBg = $.cookies.get('themeSettings_bg');
        if(null != tBg){
            $('body').removeAttr('class').addClass(tBg);
            $('.settings .bgExample').removeClass('active');
            $('.settings .bgExample[data-style="'+tBg+'"]').addClass('active');
        }
        /*theme style*/
        var tStyle = $.cookies.get('themeSettings_style');
        if(null != tStyle){
            if($('.wrapper').hasClass('fixed'))
                $(".wrapper").attr('class','').addClass('wrapper fixed');
            else
                $(".wrapper").attr('class','').addClass('wrapper');            
            
            $('.settings .styleExample').removeClass('active');
            $(".wrapper").addClass(tStyle);        
            $('.settings .styleExample[data-style="'+tStyle+'"]').addClass('active');
        }        
    
    /* Check cookies */
    
    $(".link_themeSettings").click(function(){
        
        if($("#themeSettings").is(':visible')){
            $("#themeSettings").fadeOut(200);
            $("#themeSettings").find(".checker").hide();
        }else{
            $("#themeSettings").fadeIn(300);        
            $("#themeSettings").find(".checker").show();
        }
        
       return false;
       
    });
    
    $(".settings input[name=settings_fixed]").change(function(){
        if($(this).is(':checked')){
            $(".wrapper").addClass('fixed');
             $.cookies.set('themeSettings_fixed','1');
        }else{
            $(".wrapper").removeClass('fixed');
            $.cookies.set('themeSettings_fixed',null);
        }
    });
    
    $(".settings input[name=settings_menu]").change(function(){
        
        if($(this).is(':checked')){
            $(".menu").addClass('hidden').hide();
            $(".header_menu li.list_icon").show();
            $(".content").addClass('wide');
            $.cookies.set('themeSettings_menu','1');
        }else{
            $(".menu").removeClass('hidden').removeAttr('style');
            $(".header_menu li.list_icon").hide();
            $(".content").removeClass('wide');
            $("body > .modal-backdrop").remove();
            $.cookies.set('themeSettings_menu',null);
        }
        
    });    
    
    $(".settings .bgExample").click(function(){
        var cls = $(this).attr('data-style');        
        
        $('body').removeAttr('class');
        $('.settings .bgExample').removeClass('active');
        
        if(cls != ''){
            $('body').addClass(cls);
            $(this).addClass('active');
            $.cookies.set('themeSettings_bg',cls);
        }else{
            $(this).addClass('active');
            $.cookies.set('themeSettings_bg',null);
        }
        return false;
    });

    $(".settings .styleExample").click(function(){
        var cls = $(this).attr('data-style');        
        
        if($('.wrapper').hasClass('fixed'))
            $(".wrapper").attr('class','').addClass('wrapper fixed');
        else
            $(".wrapper").attr('class','').addClass('wrapper');
            
                        
        $('.settings .styleExample').removeClass('active');
        
        if(cls != ''){
            $(".wrapper").addClass(cls);
            $(this).addClass('active');
            $.cookies.set('themeSettings_style',cls);
        }else
            $.cookies.set('themeSettings_style',null);
    
        return false;
    });
    
});