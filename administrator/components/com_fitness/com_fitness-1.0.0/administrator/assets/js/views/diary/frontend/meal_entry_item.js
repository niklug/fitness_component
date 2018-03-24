define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/meal_ingredients',
        'models/diary/diary_meal',
        'views/diary/frontend/diary_meal_item',
	'text!templates/diary/frontend/meal_entry_item.html',
        'jquery.timepicker',
        'jquery.scrollTo'
], function (
        $,
        _,
        Backbone,
        app,
        Meal_ingredients_collection,
        Diary_meal_model,
        Diary_meal_item_view,
        template 
    ) {

    var view = Backbone.View.extend({
            initialize: function(){
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                data.app = app;
                //console.log(this.model.toJSON());
                //console.log(this.options.diary_model.toJSON());
                data.diary_model = this.options.diary_model.toJSON();
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                $(this.el).find('.meal_time').timepicker({ 'timeFormat': 'H:i', 'step': 15 });
                
                this.populateDiaryMeals();
                
                this.onRender();

                return this;
            },
            
            onRender : function() {
                var self = this;
                $(this.el).show('0', function() {
                    self.scrollTo();
               });
          
            },

            scrollTo : function() {
                var scrollToY = localStorage.getItem('scrollToY');
                
                if(parseInt(scrollToY)) {
                    $('html, body').animate({
                        scrollTop: scrollToY - 180
                    }, 1000);

                    localStorage.setItem('scrollToY', '0');
                }
            },
            
            scrollToTarget : function(target) {
                $('body').scrollTo(target);
            },
        
            
            events : {
                "click .save_meal_entry" : "onClickSave",
                "click .copy_meal_entry" : "onClickCopy",
                "click .delete_meal_entry" : "onClickClose",
                "click .create_meal" : "onClickCreateMeal",
                "click .add_meal_from_database" : "onClickCreateMealFromDatabase",
                "click .add_meal_from_plans" : "onClickCreateMealFromPlans",
            },

            onClickSave :function(event) {
                var container = $(event.target);
                
                var meal_time_field = $(this.el).find('.meal_time');
                var water_field = $(this.el).find('.meal_water');
                var previous_water_field = $(this.el).find('.meal_previous_water');
                var self  = this;
                var data = {};
                data.meal_time = meal_time_field.val();
                data.water = water_field.val();
                data.previous_water = previous_water_field.val();
                data.nutrition_plan_id = this.options.plan_model.get('id');
                this.model.set(data);
                
                //console.log(this.model.toJSON());

                meal_time_field.removeClass("red_style_border");
                water_field.removeClass("red_style_border");
                previous_water_field.removeClass("red_style_border");
  
                if (!this.model.isValid()) {
                    var validate_error = this.model.validationError;

                    if(validate_error == 'meal_time') {
                        meal_time_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'water') {
                        water_field.addClass("red_style_border");
                        return false;
                    } else if(validate_error == 'previous_water') {
                        previous_water_field.addClass("red_style_border");
                        return false;
                    }  else {
                        alert(this.model.validationError);
                        return false;
                    }
                }

                this.model.save(null, {
                    success: function (model, response) {
                        app.collections.meal_entries.add(model);
                        self.addHtml(container);
                        
                        //app.collections.meal_entries.sort();
                        //app.views.meal_entries_block.loadItems();
 
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },

            onClickClose : function(event) {
                if(this.model.isNew()) {
                    var container = $(event.target);
                    this.addHtml(container);
                    return;
                }

                var self = this;
                this.model.destroy({
                    success: function (model) {
                        self.onDelete(model);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onDelete : function(model) {
                var models = app.collections.meal_ingredients.where({meal_entry_id : model.get('id')});
                
                _.each(models, function(model) {
                    app.collections.meal_ingredients.remove(model);
                });
                
                app.collections.meal_entries.remove(model);
                
                this.close();
            },
            
            addHtml : function(container) {
                container.closest( ".add_meal_entry_container" ).html('<div class="add_meal_entry">click to create a new entry</div>');
            },
            
            onClickCreateMeal : function() {
                var model = new Diary_meal_model({
                    nutrition_plan_id : this.model.get('nutrition_plan_id'),
                    diary_id : this.model.get('diary_id'),
                    meal_entry_id :  this.model.get('id'),
                    description : '0',
                    edit_mode : true
                });

                //console.log(model.toJSON());
                
                var self = this;
                
                model.save(null, {
                    success: function (model, response) {
                        //console.log(model.toJSON());
                        self.addDiaryMealItem(model);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            addDiaryMealItem : function(model) {
                var models = app.collections.meal_ingredients.where({meal_id : model.get('id')});
                var collection = new Meal_ingredients_collection(models);
                //console.log(collection);
                $(this.el).find(".diary_meals_wrapper").append(new Diary_meal_item_view({
                    model : model,
                    diary_model : this.options.diary_model,
                    collection : collection
                }).render().el);
            },
            
            populateDiaryMeals : function() {
                var self = this;
                _.each(app.collections.diary_meals.models, function(model) {
                    self.addDiaryMeal(model);
                });
                
            },
            
            addDiaryMeal : function(model) {
                if(model.get('meal_entry_id') == this.model.get('id')) {
                    this.addDiaryMealItem(model);
                }
            },
            
            onClickCreateMealFromDatabase : function(event) {
                var back_url = encodeURIComponent(app.options.base_url_relative + 'index.php?option=com_fitness&view=nutrition_diaries#!/item_view/' + this.model.get('diary_id'));
                
                var position = $(event.target).offset();
                
                var top = position.top;
    
                localStorage.setItem('scrollToY', top);
                
                var model = new Diary_meal_model({
                    nutrition_plan_id : this.model.get('nutrition_plan_id'),
                    diary_id : this.model.get('diary_id'),
                    meal_entry_id :  this.model.get('id'),
                    description : '0'
                });

                model.save(null, {
                    success: function (model, response) {
                        var url = app.options.base_url_relative + 'index.php?option=com_fitness&view=recipe_database';
                        url += '&nutrition_plan_id=' + model.get('nutrition_plan_id');
                        url += '&diary_id=' + model.get('diary_id');
                        url += '&meal_entry_id=' + model.get('meal_entry_id');
                        url += '&meal_id=' + model.get('id');
                        url += '&back_url=' + back_url;
                        url += '#!/my_recipes';
                        window.location = url;
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onClickCreateMealFromPlans : function(event) {
                var back_url = encodeURIComponent(app.options.base_url_relative + 'index.php?option=com_fitness&view=nutrition_diaries#!/item_view/' + this.model.get('diary_id'));
                
                var position = $(event.target).offset();
                
                var top = position.top;
    
                localStorage.setItem('scrollToY', top);
                
                var model = new Diary_meal_model({
                    nutrition_plan_id : this.model.get('nutrition_plan_id'),
                    diary_id : this.model.get('diary_id'),
                    meal_entry_id :  this.model.get('id'),
                    description : '0'
                });
                
                var self = this;

                model.save(null, {
                    success: function (model, response) {
                        var url = app.options.base_url_relative + 'index.php?option=com_fitness&view=nutrition_planning';
                        url += '&nutrition_plan_id=' + model.get('nutrition_plan_id');
                        url += '&diary_id=' + model.get('diary_id');
                        url += '&meal_entry_id=' + model.get('meal_entry_id');
                        url += '&meal_id=' + model.get('id');
                        url += '&back_url=' + back_url;
                        url += '#!/nutrition_guide/' + self.model.get('nutrition_plan_id');
                        window.location = url;
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onClickCopy : function(event) {
                var position = $(event.target).offset();
                
                var top = position.top;
    
                localStorage.setItem('scrollToY', top);
                
                var id = this.model.get('id');
                var diary_id = this.model.get('diary_id');
                app.controller.copy_meal_entry(id, diary_id);
            },
            
            connectComments :function() {
             var comment_options = {
                    'item_id' :  this.model.get('diary_id'),
                    'fitness_administration_url' : app.options.ajax_call_url,
                    'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : '#__fitness_nutrition_diary_comments',
                    'read_only' : true,
                }
                
                if(app.options.is_backend) {
                    comment_options.read_only = false;
                }

                var comments = $.comments(comment_options, comment_options.item_id, this.model.get('id'));

                var comments_html = comments.run();
                $(this.el).find(".meal_entry_comments_wrapper").html(comments_html);
            },

            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
  
        });
            
    return view;
});