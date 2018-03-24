(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    function BatchCopy(options) {
        this.options = options;
        this.run();
    }
    
    
    BatchCopy.prototype.run = function() {
        this.setEventListeners();
    }
    
    
    BatchCopy.prototype.setEventListeners = function() {
        var self = this;
        

        $("#batch_copy").die().live('click', function() {
            var ids = $('input[type="checkbox"][name="cid\\[\\]"]:checked').map(function() { return this.value; }).get().join(',');
            self.copyItems(ids);
        });
        
        
        $("#batch_clear").die().live('click', function() {
            var ids = $('input[type="checkbox"]:checked').attr('checked', false);
        });
        
    }

    
    BatchCopy.prototype.copyItems = function(ids) {
        var batch_business_profile =  $("#batch_business_profile").val();

        var data = {};
            var url = this.options.ajax_call_url;
            var view = 'settings';
            var task = 'batch_copy';
            var table = this.options.table;
            data.ids = ids;
            data.business_profile_id = batch_business_profile;
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                window.location.reload();
            });

    }

    
    // Add the  function to the top level of the jQuery object
    $.batch_copy= function(options) {

        var constr = new BatchCopy(options);

        return constr;
    };
        
}));



