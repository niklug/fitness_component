/*
 * class provide comments system
 */
(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory($);
    }
}(function($) {
    function AjaxIndicator(options) {
        this.options = options;
        
        if(!this.options.text) {
            this.options.text = 'Processing... Please wait';
        }
        
        if(!this.options.image_class) {
            this.options.image_class = 'ajax_indicator';
        }
        
        this.run();
    }

    AjaxIndicator.prototype.run = function() {
        this.setEventListeners();
    }

    AjaxIndicator.prototype.setEventListeners = function() {
        var self = this;
        $(document).ajaxStart(function() {
            self.start();
        }).ajaxStop(function() {
            self.stop();
        });
    }

    AjaxIndicator.prototype.start = function() {
        if ($('body').find('#resultLoading').attr('id') != 'resultLoading') {
            $('body').append('<div id="resultLoading" style="display:none"><div class="' + this.options.image_class + '"><div style="margin-left: 50px;margin-top: 10px;">' + this.options.text + '</div></div><div class="bg"></div></div>');
        }

        $('#resultLoading').css({
            'width': '100%',
            'height': '100%',
            'position': 'fixed',
            'z-index': '10000000',
            'top': '0',
            'left': '0',
            'right': '0',
            'bottom': '0',
            'margin': 'auto'
        });

        $('#resultLoading .bg').css({
            'background': '#000000',
            'opacity': '0.7',
            'width': '100%',
            'height': '100%',
            'position': 'absolute',
            'top': '0'
        });

        $('#resultLoading>div:first').css({
            'width': '250px',
            'height': '75px',
            'text-align': 'center',
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'right': '0',
            'bottom': '0',
            'margin': 'auto',
            'font-size': '16px',
            'z-index': '10',
            'color': '#ffffff'

        });

        $('#resultLoading .bg').height('100%');
        $('#resultLoading').fadeTo(10000,1);
        $('body').css('cursor', 'wait');
    }

    AjaxIndicator.prototype.stop = function() {
        $('#resultLoading .bg').height('100%');
        $('#resultLoading').stop(true,false).fadeOut(300);
        $('body').css('cursor', 'default');
    }




    // Add the  function to the top level of the $ object
    $.ajax_indicator = function(options) {
        var constr = new AjaxIndicator(options);
        return constr;
    };

}));
