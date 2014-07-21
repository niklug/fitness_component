define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        
        'collections/nutrition_plan/nutrition_guide/nutrition_guide_recipes',
        'views/nutrition_plan/nutrition_guide/example_day_recipe',
	'text!templates/nutrition_plan/nutrition_guide/example_day.html',
        'jquery.timepicker'
], function ( 
        $,
        _,
        Backbone,
        app,
        
        Example_day_recipes_collection,
        Example_day_recipe_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.example_day_recipes = new Example_day_recipes_collection();
            
            app.collections.example_day_recipes.bind("add", this.addItem, this);
            
            this.getExampleDayRecipes();
        },
        
        template : _.template(template),

        render: function(){
            $(this.el).html(this.template({ }));
            return this;
        },

        getExampleDayRecipes : function() {
            var self = this;
            app.collections.example_day_recipes.fetch({
                data: {
                    nutrition_plan_id : this.options.nutrition_plan_id,
                    example_day_id : this.options.example_day_id
                },
                success : function(collection, response) {
                    //console.log(collection.toJSON());
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadItems : function(collection) {
            var self = this;
            _.each(collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem : function(model) {
            $(this.el).find("#recipes_list_container").append(new Example_day_recipe_view({
                nutrition_plan_id : this.options.nutrition_plan_id,
                example_day_id : this.options.example_day_id,
                menu_id : this.options.menu_id,
                model : model,
                collection : app.collections.example_day_recipes
            }).render().el);
        },
        
        

            
    });
            
    return view;
});