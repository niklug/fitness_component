define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/nutrition_guide/shopping_list_item',
	'text!templates/nutrition_plan/nutrition_guide/shopping_list.html'
], function ( $, _, Backbone, app, Shopping_list_item_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render:function () {
            $(this.el).html(this.template({}));
            
            var container = this.$el.find("#shopping_list_container");
            
            var categories_collection = this.options.categories_collection;
            var ingredients_collection = this.options.ingredients_collection;
            
            var count = 0;
            categories_collection.each(function(model) {
                var ingredients = ingredients_collection.where({category : model.get('id')});
                if(ingredients.length > 0) {
                    //console.log(ingredients);
                    model.set({'ingredients' : ingredients, count : count});
                    var item_view = new Shopping_list_item_view({el : container, model : model});
                    item_view.render();
                    count++;
                }
            });
            
            //console.log(categories_collection);
            //console.log(ingredients_collection);
            
            return this;
        },

        events:{
           
        },


    });
            
    return view;
});