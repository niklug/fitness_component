define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/comments/item.html'
        
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            
        },
        
        template : _.template(template),

        render : function () {
            
            var data = {};
            data.$ = $;
            $(this.el).html(this.template(data));
      
            return this;
        },
        
    });
            
    return view;

});