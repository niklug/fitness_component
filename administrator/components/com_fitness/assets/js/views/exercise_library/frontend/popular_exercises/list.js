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
         
            this.collection.bind("add", this.addItem, this);
            
            this.model.set({sort_by : 'viewed', current_page : 'exercise_database', order_dirrection : 'DESC', limit : 15});

            if(this.collection.length == 0) {
                this.collection.fetch({
                    data : this.model.toJSON(),
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }); 
            }
        },

        render : function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find("#items_container");
            
            var self = this;
            _.each(this.collection.models, function(model){
                self.addItem(model);
            });
                
            return this;
        },

        addItem : function(model) {
            this.container_el.append( new Item_view({model : model}).render().el);
        },
    });
            
    return view;
});