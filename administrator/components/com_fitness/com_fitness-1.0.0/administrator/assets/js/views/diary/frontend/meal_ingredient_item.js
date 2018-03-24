define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/nutrition_database_ingredients',
        'collections/diary/meal_ingredients',
        'views/diary/frontend/ingredients_search_results',
	'text!templates/diary/frontend/meal_ingredient_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Nutrition_database_ingredients_collection,
        Meal_ingredients_collection,
        Ingredients_search_results_view,
        template 
    ) {

    var view = Backbone.View.extend({
            tagName : "tr",

        
            initialize: function(){
                this.search_results_view = new Ingredients_search_results_view();

                this.edit_mode();
                
                if(!app.collections.nutrition_database_ingredients) {
                    app.collections.nutrition_database_ingredients = new Nutrition_database_ingredients_collection();
                        app.collections.nutrition_database_ingredients.fetch({
                        //data : {search_text : search_text},
                        success: function (collection, response) {
                        },
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    });
                }
            },
            
            template:_.template(template),
            
            render: function(){
                this.round_model_value(this.model, 'protein');
                this.round_model_value(this.model, 'fats');
                this.round_model_value(this.model, 'carbs');
                this.round_model_value(this.model, 'calories');
                this.round_model_value(this.model, 'energy');
                this.round_model_value(this.model, 'saturated_fat');
                this.round_model_value(this.model, 'total_sugars');
                this.round_model_value(this.model, 'sodium');
                
                var data = {item : this.model.toJSON()};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                if(!this.edit_mode()) {
                    this.$el.addClass('table_border_top_dark');
                }

                return this;
            },
            
            round_model_value : function(model, attribute) {
                var data = {};
                data[attribute] = this.round_2_sign(model.get(attribute));
                model.set(data);
            },
            
            events : {
                "click .delete_meal_ingredient" : "onClickDelete",
                "input .ingredient_name_input" : "onInputName",
                "change .ingredients_results " : "onChooseIngredient",
                "focusout .ingredient_quantity_input " : "onChangeQuantity"
                
            },
            
            edit_mode : function() {
                var edit_mode = false;
                
                if(this.model.get('edit_mode')) {
                    return true;
                }

                this.model.set({edit_mode : edit_mode});
            },
            
            onClickDelete : function() {
                var self = this;
                this.model.destroy({
                    success: function (model, response) {
                        self.collection.remove(model);
                        app.collections.meal_ingredients.remove(model);
                        self.close();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onInputName : function(event) {
                var search_text = $(event.target).val();
                
                this.search_results_view.close();
                
                $(event.target).parent().append(
                    this.search_results_view.render().el
                );

                var self = this;
                if (search_text) {
                    this.populateSearchContainer(app.collections.nutrition_database_ingredients, search_text);
                } else {
                    this.search_results_view.close();
                }
            },
            
            populateSearchContainer : function(collection, search_text) {
                
                var select_field =  $(this.el).find(".ingredients_results");
                select_field.html('');
                var search_parts_array = search_text.split(/[\s,]+/);
                /*
                var all_models = [];
                _.each(search_parts_array, function(search_text) {
                    var models = collection.filter(function(model) {
                        var ingredient_name = model.get('ingredient_name');
                        
                        ingredient_name = ingredient_name.toLowerCase();
                        search_text = search_text.toLowerCase();

                        if(!search_text) {
                            return false;
                        }

                        return (ingredient_name.indexOf(search_text) > -1);
                    });
                    
                    all_models = all_models.concat(models);
                });
                */

                //
                var all_models = [];
                _.each(collection.models, function(model) {
                    var flag = true;

                    
                    _.each(search_parts_array, function(search_text) {
                        var ingredient_name = model.get('ingredient_name');
                        ingredient_name = ingredient_name.toLowerCase();
                        search_text = search_text.toLowerCase();
                        var position = ingredient_name.indexOf(search_text);
                        model.set({position : position});
                        if(ingredient_name.indexOf(search_text) == -1) {
                            flag = false;
                        }
                    });
                    
                    if(flag) {
                        all_models = all_models.concat(model);
                    }

                });

                var collection = new Meal_ingredients_collection(all_models);

                var sorted_collection = _.sortBy(collection.models, function(model){ return  parseInt(model.get('position')); });
                
                collection = new Meal_ingredients_collection(sorted_collection);
                
                $(this.el).find(".results_count").html('Search returned ' + collection.length + ' ingredients.');

                var self = this;
                _.each(collection.models, function(model) {
                    select_field.append(
                        '<option value="' + model.get('id') + '" >' + model.get('ingredient_name') + '</option>'
                    );
                });
                
                select_field.find(":odd").css("background-color", "#F0F0EE");
             },
            
            onChooseIngredient : function(event) {
                var id = $(event.target).val();
                this.ingredient_model = app.collections.nutrition_database_ingredients.get(id);
                $(this.el).find(".ingredient_name_input").val(this.ingredient_model.get('ingredient_name'));
                this.search_results_view.close();
                
                var measurement = this.getMeasurement(this.ingredient_model.get('specific_gravity'));
                $(this.el).find(".grams_mil").html(measurement);
                
                $(this.el).find(".ingredient_quantity_input").focus();
            },
            
            getMeasurement : function(specific_gravity) {
                if(parseFloat(specific_gravity) > 0) {
                    return 'millilitres';
                } 
                return 'grams';
            },
            
            onChangeQuantity : function(event) {
                var ingredient_name = $(this.el).find(".ingredient_name_input").val();
                
                if(!ingredient_name){
                    return;
                }
                
                var quantity = $(event.target).val();
                var inredient_id = $(event.target).attr('data-ingredient_id');
                
                if(typeof this.ingredient_model !== "undefined") {
                    this.onSetQuantity(this.ingredient_model, quantity);
                    return;
                }
                
                if(app.collections.nutrition_database_ingredients.get(inredient_id)) {
                    this.onSetQuantity(app.collections.nutrition_database_ingredients.get(inredient_id), quantity);
                    return;
                }

                var self = this;
                app.collections.nutrition_database_ingredients.fetch({
                    success: function (collection, response) {
                        var model = collection.get(inredient_id);
                        self.onSetQuantity(model, quantity);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }); 
            },
            
            onSetQuantity : function(model, quantity) {
                var ingredient = model.toJSON();
                var calculatedIngredient = this.calculatedIngredientItems(ingredient, quantity);
                this.model.set(calculatedIngredient);
                
                if (!this.model.isValid()) {
                    var validate_error = this.model.validationError;
                    alert(this.model.validationError);
                    return false;
                }
                
                var self = this;
                this.model.save(null, {
                    success: function (model, response) {
                        
                        self.render();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            calculatedIngredientItems : function(ingredient, quantity) {
                var calculated_ingredient = {};
                var specific_gravity = ingredient.specific_gravity;
                //quantity = 100;
                //specific_gravity = 1.03;
                //ingredient.protein = 3.2;
                calculated_ingredient.ingredient_id = ingredient.id;

                calculated_ingredient.meal_name = ingredient.ingredient_name;

                calculated_ingredient.quantity = quantity;

                calculated_ingredient.measurement = this.getMeasurement(ingredient.specific_gravity);

                calculated_ingredient.protein = this.calculateDependsOnGravity(ingredient.protein, quantity, specific_gravity);

                calculated_ingredient.fats = this.calculateDependsOnGravity(ingredient.fats, quantity, specific_gravity);

                calculated_ingredient.carbs = this.calculateDependsOnGravity(ingredient.carbs, quantity, specific_gravity);

                calculated_ingredient.calories = this.calculateDependsOnGravity(ingredient.calories, quantity, specific_gravity);

                calculated_ingredient.energy = this.calculateDependsOnGravity(ingredient.energy, quantity, specific_gravity);

                calculated_ingredient.saturated_fat = this.calculateDependsOnGravity(ingredient.saturated_fat, quantity, specific_gravity);

                calculated_ingredient.total_sugars = this.calculateDependsOnGravity(ingredient.total_sugars, quantity, specific_gravity);

                calculated_ingredient.sodium = this.calculateDependsOnGravity(ingredient.sodium, quantity, specific_gravity);

                //console.log(ingredient.specific_gravity);
                //console.log(ingredient);
                //console.log(calculated_ingredient);

                return calculated_ingredient;
            },
            
            calculateDependsOnGravity : function(value, quantity, specific_gravity) {
                var calculated_value;
                if(parseFloat(specific_gravity) > 0) {
                    calculated_value = this.millilitresFormula(value, quantity, specific_gravity);
                } else {
                    calculated_value = this.gramsFormula(value, quantity);
                }
                return calculated_value;
            },

            gramsFormula : function(value, quantity) {
                return this.round_2_sign (value / 100 * quantity );
            },

            millilitresFormula : function(value, quantity, specific_gravity) {
                return this.round_2_sign (value / 100 * quantity * specific_gravity );
            },

            round_2_sign : function(value) {
                return Math.round(value * 100)/100;
            },
            
            

            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
        });
            
    return view;
});