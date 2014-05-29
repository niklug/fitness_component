define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/goals/backend/list_item_mini',
	'text!templates/goals/backend/list_mini.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        List_item_mini_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {

            this.status_obj = $.status(app.options.status_options);
        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.onRender();
          
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
            });
        },
        
        loadItems : function() {
           var self = this;
            _.each(this.collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        events: {

        },
        
        addItem : function(model) {
            $(this.el).find(".minigoals_container").append(new List_item_mini_view({model : model}).render().el); 
        },
        
        clearItems : function() {
            $(this.el).find(".minigoals_container").empty();
        },
        
     
    });
            
    return view;
});