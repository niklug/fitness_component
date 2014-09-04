define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'models/recipe_database/recipe',
        'views/exercise_library/select_filter',
	'text!templates/diary/frontend/save_as_recipe.html',
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Recipe_database_model,
        Select_filter_fiew,
        template 
    ) {

    var view = Backbone.View.extend({
            initialize: function(){
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                this.onRender();
                
                return this;
            },
            
            onRender : function() {
                var self = this;
                $(this.el).show('0', function() {
                    self.connectRecipeTypesFilter();
                    self.connectRecipeVariationsFilter();
                });
            },
            
            events : {
                "click .save_as_recipe_save" : "saveRecipe",
                "click .save_as_recipe_cancel" : "onClickCancel",
            },
            
            connectRecipeTypesFilter : function() {
                if(app.collections.recipe_types) {
                    this.loadRecipeTypesSelect(app.collections.recipe_types );
                    return;
                }
                var self = this;
                app.collections.recipe_types = new Recipe_types_collection();
                app.collections.recipe_types.fetch({
                    success : function (collection, response) {
                        self.loadRecipeTypesSelect(collection);
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },

            loadRecipeTypesSelect : function(collection) {

                new Select_filter_fiew({
                    model : this.model,
                    el : this.$el.find(".recipe_type_wrapper"),
                    collection : collection,
                    title : 'RECIPE TYPE',
                    first_option_title : '-select-',
                    class_name : 'recipe_type dark_input_style ',
                    id_name : '',
                    select_size : 17,
                    model_field : 'recipe_type',
                    element_disabled : ''
                }).render();  
            },

            connectRecipeVariationsFilter : function() {
                if(app.collections.recipe_variations) {
                    this.loadRecipeVariationsSelect(app.collections.recipe_variations );
                    return;
                }
                var self = this;
                app.collections.recipe_variations = new Recipe_variations_collection();
                app.collections.recipe_variations.fetch({
                    success : function (collection, response) {
                        self.loadRecipeVariationsSelect(collection);
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },

            loadRecipeVariationsSelect : function(collection) {
                new Select_filter_fiew({
                    model : this.model,
                    el : this.$el.find(".recipe_variations_wrapper"),
                    collection : collection,
                    title : 'RECIPE VARIATION',
                    first_option_title : '-select-',
                    class_name : 'recipe_variation  dark_input_style ',
                    id_name : '',
                    select_size : 17,
                    model_field : 'recipe_variation',
                    element_disabled : ''
                }).render(); 
            },

            saveRecipe : function() {
                var recipe_name_field = $(this.el).find(".recipe_name");
                var recipe_type_field = $(this.el).find(".recipe_type");
                var recipe_variation_field = $(this.el).find(".recipe_variation");
                var number_serves_field = $(this.el).find(".number_serves");

                var data = {};

                data.recipe_name = recipe_name_field.val();

                data.recipe_type = recipe_type_field.find(':selected').map(function(){ return this.value }).get().join(",");

                data.recipe_variation = recipe_variation_field.find(':selected').map(function(){ return this.value }).get().join(",");

                data.number_serves = number_serves_field.val();

                data.state = '1';

                data.created_by = app.options.user_id;

                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");  

                data.business_profile_id = app.options.business_profile_id;

                data.status = '1';
                
                var model = new Recipe_database_model();

                model.set(data);
                
                //validation
                recipe_name_field.removeClass("red_style_border");
                recipe_type_field.removeClass("red_style_border");
                recipe_variation_field.removeClass("red_style_border");
                number_serves_field.removeClass("red_style_border");

                if (!model.isValid()) {
                    var validate_error = model.validationError;

                    if(validate_error == 'recipe_name') {
                        recipe_name_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'recipe_type') {
                        recipe_type_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'recipe_variation') {
                        recipe_variation_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'number_serves') {
                        number_serves_field.addClass("red_style_border");
                        return false;
                    } else {
                        alert(model.validationError);
                        return false;
                    }
                }

                this.saveAsRecipe(model.toJSON());
            },
            
            saveAsRecipe : function(data) {
                var url = app.options.ajax_call_url;
                var view = 'nutrition_diaries'
                var task = 'saveAsRecipe';
                var table = '#__fitness_nutrition_diary_ingredients';
                var self = this;
                var meal_id = this.model.get('id');
                data.meal_id = meal_id;
                $.AjaxCall(data, url, view, task, table, function(output) {
                    //console.log(output);
                    self.close();
                });
            },

            onClickCancel : function(event) {
                this.close();
            },
 
            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
  
        });
            
    return view;
});