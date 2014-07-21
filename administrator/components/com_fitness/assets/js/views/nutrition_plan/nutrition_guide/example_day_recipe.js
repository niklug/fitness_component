define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/recipe_database/recipes',
        'models/nutrition_plan/nutrition_guide/example_day_recipe',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/nutrition_plan/nutrition_guide/add_recipe',
	'text!templates/nutrition_plan/nutrition_guide/example_day_recipe.html',
], function (
        $,
        _,
        Backbone, 
        app, 
        Recipes_collection,
        Example_day_recipe_model,
        Get_recipe_params_model,
        Example_day_add_recipe_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize : function() {
            this.edit_mode();
            
            this.recipe_params_model =  new Get_recipe_params_model();
                
            app.collections.recipes = app.collections.recipes || new Recipes_collection(); 

            this.recipe_params_model.bind("change", this.get_database_recipes, this);
        },

        render : function () {
            
            var data = { item : this.model.toJSON()};
            data.$ = $;
            data.menu_plan = app.models.menu_plan.toJSON();
            $(this.el).html(this.template( data ));
            $(this.$el.find('.recipe_time')).timepicker({ 'timeFormat': 'H:i', 'step': 15 });
            return this;
        },

        events: {
            "click .save_close_recipe" : "onClickSaveClose",
            "click .delete_recipe" : "onClickDeleteRecipe",
            "click .view_recipe" : "onClickViewRecipe",
            "click .edit_recipe" : "onClickEdit",
            "click .copy_recipe" : "onClickCopy",
            
            "click .add_recipe" : "onClickAddRecipe",
            "click .cancel_add_recipe": "onCancelViewRecipe",
        },
        

        onClickSaveClose : function() {
            var description_field = this.$el.find('.recipe_description');
            var time_field = this.$el.find('.recipe_time');
            var comments_field = this.$el.find('.recipe_comments');
            
            description_field.removeClass("red_style_border");
            time_field.removeClass("red_style_border");
            
            var description = description_field.val();
            var time = time_field.val();
            var comments = comments_field.val();
            
            if(!description) {
                description_field.addClass("red_style_border");
                return false;
            }
            
            if(!time) {
                time_field.addClass("red_style_border");
                return false;
            }
                
            this.model.set({
                description : description,
                time : time,
                comments : comments
            });

            var self = this;
            this.model.save(null, {
                success : function (model, response) {
                    self.model.set({edit_mode : false});
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }, 

        onClickDeleteRecipe : function() {
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
        
        onClickViewRecipe : function(event) {
            var url = app.options.relative_url + 'index.php?option=com_fitness&view=recipe_database&#!/nutrition_database/nutrition_recipe/' + this.model.get('original_recipe_id');
            
            if(app.options.is_backend) {
                var url = app.options.relative_url + 'index.php?option=com_fitness&view=nutrition_recipes#!/form_view/' + this.model.get('original_recipe_id');
            }

            window.open(url);
        },
        
        edit_mode : function() {
            var edit_mode = false;
            
            var description = this.model.get('description');
            var time = this.model.get('time');
            
            if(!description || !time) {
                edit_mode = true;
            }

            this.model.set({edit_mode : edit_mode});
        },
        
        onClickEdit : function() {
            this.model.set({edit_mode : true});
            this.render();
        },
        
        onClickCopy : function() {
            var model = new Example_day_recipe_model(this.model.toJSON());
            model.set({id : null, menu_id : this.options.menu_id});
            var self = this;
            app.collections.example_day_recipes.create(model, {
                success : function (model, response) {
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickAddRecipe : function() {
            $(this.el).find(".add_recipe").hide();
            
            if(!parseInt(app.collections.recipes.length)) {
                this.get_database_recipes();
            }
            
            $(this.el).find(".add_recipe_container").show().html(new Example_day_add_recipe_view({
                example_day_id : this.options.example_day_id,
                menu_id : this.options.menu_id,
                nutrition_plan_id : this.options.nutrition_plan_id,
                collection : app.collections.recipes,
                recipe_params_model : this.recipe_params_model
            }).render().el);
            
            this.connectPagination(app.collections.recipes);

        },
        
        connectPagination : function(collection) {
            this.pagination_model = $.backbone_pagination({el : $(this.el).find(".pagination_container")});
            var self = this;
            collection.once("add", function(model) {
                self.pagination_model.set({'items_total' : model.get('items_total')});
            });
            
            if(collection.models.length){
                this.pagination_model.set({'items_total' : collection.models[0].get('items_total')});
            }
            
            this.pagination_model.bind("change:currentPage", this.set_recipes_model, this);
            this.pagination_model.bind("change:items_number", this.set_recipes_model, this);
        },
        
        set_recipes_model : function() {
            app.collections.recipes.reset();
            this.recipe_params_model.set({"page" : this.pagination_model.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
        },
        
        onCancelViewRecipe :function () {
            $(this.el).find(".add_recipe_container").hide().empty();
            $(this.el).find(".add_recipe").show();
        },
        
        get_database_recipes : function() {
            app.collections.recipes.reset();
            var self = this;
            app.collections.recipes.fetch({
                data : self.recipe_params_model.toJSON(),
                success : function(collection, response) {
                    //console.log(collection.toJSON());
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        


        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });
            
    return view;
});