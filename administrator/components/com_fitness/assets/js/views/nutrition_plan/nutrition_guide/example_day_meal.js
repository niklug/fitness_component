define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/nutrition_guide_recipes',
        'views/nutrition_plan/nutrition_guide/meal_recipe',
	'text!templates/nutrition_plan/nutrition_guide/example_day_meal.html',
        'jquery.timepicker'
], function ( $, _, Backbone, app, Example_day_collection, Meal_recipe_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            _.bindAll(this, 'onClickSaveMeal', 'onClickDeleteMeal','close', 'render', 'addRecipe');
            
            this.model.on("destroy", this.close, this);

            app.collections.meal_recipes = new Meal_recipes_collection();

            app.collections.meal_recipes.bind("add", this.addRecipe, this);

            var self = this;
            app.collections.meal_recipes.fetch({
                data: {
                    meal_id : self.model.get('id')
                },
                wait : true,
                success : function(collection, response) {
                    //console.log(collection);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });

        },

        render: function(){
            var data = this.model.toJSON();
            data.menu_plan = app.models.menu_plan.toJSON();
            $(this.el).html(this.template(data));

            $(this.$el.find('.meal_time')).timepicker({ 'timeFormat': 'H:i', 'step': 15 });
            this.connectComments();

            return this;
        },

        connectComments : function() {
            var meal_id = this.model.get('id');
            var comment_options = {
                'item_id' : this.options.nutrition_plan_id,
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : app.options.example_day_meal_comments_db_table,
                'read_only' : false,
                'anable_comment_email' : true,
                'comment_method' : 'MenuPlanComment'
            }
            
            if(app.options.is_client) {
                comment_options.read_only = true;
            }
            
            var comments = $.comments(comment_options, comment_options.item_id, meal_id).run();
      
            this.$el.find(".comments_wrapper").html(comments);
        },

        addRecipe : function(model) {
            app.views.meal_recipe = new Meal_recipe_view({collection : app.collections.meal_recipes, model : model}); 

            this.$el.find(".meal_recipes").append( app.views.meal_recipe.render().el );
        },

        events: {
            "click .save_example_day_meal" : "onClickSaveMeal",
            "click .delete_example_day_meal" : "onClickDeleteMeal",
            "click .add_meal_recipe" : "onClickAddMealRecipe",
        },

        onClickSaveMeal : function(event) {
            event.preventDefault();
            var data = Backbone.Syphon.serialize(this);

            this.model.set(data);

            //validation
            var meal_description_field = this.$el.find('.meal_description');
            meal_description_field.removeClass("red_style_border");
            var meal_time_field = this.$el.find('.meal_time');
            meal_time_field.removeClass("red_style_border");
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'description') {
                    meal_description_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'meal_time') {
                    meal_time_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }

            var self = this;
            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        self.close();
                        //console.log(self.collection);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        //console.log(self.collection);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
         },

         onClickDeleteMeal : function(event) {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickAddMealRecipe : function() {
            app.controller.navigate("!/add_meal_recipe/" + this.model.get('id') + '/' + this.options.nutrition_plan_id, true);
        },

        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });
            
    return view;
});