define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/recipe_database/frontend/latest_recipes/item',
	'text!templates/recipe_database/frontend/latest_recipes/list.html'
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
            
            this.model.set({
                sort_by : 'created', 
                order_dirrection : 'DESC',
                limit : 15,
                business_profile_id : app.options.business_profile_id,
                current_page : 'recipe_database'
            });

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
            this.container_el = this.$el.find("#latest_recipes_container");
  
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