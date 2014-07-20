define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_ingredients',
        'models/nutrition_plan/nutrition_guide/add_original_recipe',
        'views/nutrition_plan/nutrition_guide/add_recipe_details',
	'text!templates/nutrition_plan/nutrition_guide/add_recipe_item.html',
], function (
        $,
        _,
        Backbone,
        app, 
        Recipe_ingredients_collection,
        Add_original_recipe_model,
        Add_recipe_details_view, 
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            _.bindAll(this, 'render',  'onClickViewRecipe', 'onClickEnterServes');
            app.models.original_recipe = new Add_original_recipe_model();
        },
        render:function () {
            var data = this.model.toJSON();
            data.$ = $;
            $(this.el).html(this.template( data ));
            return this;
        },

        events: {
            "click .view_add_recipe" : "onClickViewRecipe",
            "click .enter_number_serves" : "onClickEnterServes"
        },

        onClickViewRecipe : function() {
            var recipe_id = this.model.get('id');

            app.collections.recipe_ingredients = new Recipe_ingredients_collection();

            this.container_el = this.$el.find(".recipe_details");
            var self = this;
            app.collections.recipe_ingredients.fetch({
                data: {
                    id : recipe_id
                },
                success : function (collection, response) {
                    self.container_el.html( new Add_recipe_details_view({model : response}).render().el );
                    self.$el.find(".view_add_recipe").hide();
                    self.$el.find(".number_serves_wrapper, .recipe_details").show();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickEnterServes : function() {
            var number_serves = parseInt(this.$el.find(".number_serves").val());
            this.$el.find(".number_serves").removeClass("red_style_border");
            if(!number_serves) {
                this.$el.find(".number_serves").addClass("red_style_border");
                return false;
            }
            
            app.models.original_recipe.set(this.model.toJSON());
            
            app.models.original_recipe.set({
                example_day_id : this.options.example_day_id,
                nutrition_plan_id : this.options.nutrition_plan_id
            });
            
            var menu_id = app.models.menu_plan.get('id');

            app.models.original_recipe.set({menu_id : menu_id});

            var original_recipe_id = this.model.get('id');

            app.models.original_recipe.set({'number_serves_new' : number_serves, 'original_recipe_id' : original_recipe_id});

            app.models.original_recipe.unset('id');

            this.$el.find(".number_serves_wrapper, .recipe_details").hide();
            this.$el.find(".view_add_recipe").show();


            app.models.original_recipe.save(null, {
                success: function (model, response) {
                    //console.log(response);
                    app.collections.example_day_recipes.add(model);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }

    });
            
    return view;
});