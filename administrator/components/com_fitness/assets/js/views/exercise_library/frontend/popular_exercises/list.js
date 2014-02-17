define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/exercise_library/frontend/popular_exercises/item',
	'text!templates/exercise_library/frontend/popular_exercises/list.html'
], function (
        $,
        _,
        Backbone,
        app,
        Item_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            this.collection.bind("reset", this.clearItems, this);
        },

        render : function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find("#items_container");
            var self = this;
            _.each(this.collection.models, function(model){
                console.log(model);
                self.container_el.append( new Item_view({model : model}).render().el);
            });
                
            return this;
        },

        clearItems : function() {
            this.container_el.empty();
        },

    });
            
    return view;
});