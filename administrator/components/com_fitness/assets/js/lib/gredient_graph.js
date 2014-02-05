(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    
    function GredientGraph(options) {

        var Gredient_graph_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },

            render : function(){
                var template = _.template($("#gredient_graph_template").html(), this.options.data);
                this.$el.html(template);
            },
        });

        return new Gredient_graph_view({ el: $(options.el), 'data' : options});
    }


    $.gredient_graph = function(options) {

        var constr =  GredientGraph(options);

        return constr;
    };


}));