/*
 * Basic Ajax Call with callback response
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
        function AjaxCall(data, url, view, task, table, handleData) {
            var data_encoded = JSON.stringify(data);
            $.ajax({
                type : "POST",
                url : url,
                data : {
                    view : view,
                    task : task,
                    format : 'text',
                    data_encoded : data_encoded,
                    table : table
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.message);
                        return;
                    }
                    handleData(response.data);
                },
                error: function(response)
                {
                   alert(response.responseText);
                }
            }); 
        }

        // Add the  function to the top level of the jQuery object
        $.AjaxCall = function(data, url, view, task, table, handleData) {

            var constr = new AjaxCall(data, url, view, task, table, handleData);

            return constr;
        };
        

}));
