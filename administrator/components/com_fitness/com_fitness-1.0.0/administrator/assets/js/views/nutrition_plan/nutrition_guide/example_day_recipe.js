define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/menu_descriptions',
        'models/nutrition_plan/nutrition_guide/example_day_recipe',
        'views/programs/select_element',
	'text!templates/nutrition_plan/nutrition_guide/example_day_recipe.html',
], function (
        $,
        _,
        Backbone, 
        app, 
        Menu_descriptions_collection,
        Example_day_recipe_model,
        Select_element_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize : function() {
            this.edit_mode();

        },

        render : function () {
            var data = { item : this.model.toJSON()};
            data.menu_plan_model = this.options.menu_plan_model.toJSON();
            data.$ = $;
            data.app = app;
            data.menu_plan = app.models.menu_plan.toJSON();
            $(this.el).html(this.template( data ));
            $(this.$el.find('.recipe_time')).timepicker({ 'timeFormat': 'H:i', 'step': 15 });
            
            this.loadDescriptions();
            
            return this;
        },

        events: {
            "click .save_close_recipe" : "saveItem",
            "click .delete_recipe" : "deleteItem",
            "click .view_recipe" : "onClickViewRecipe",
            "click .edit_recipe" : "onClickEdit",
            "click .copy_recipe" : "onClickCopy",
            
            "click .add_to_diary" : "onClickAddToDiary",
        },
        

        saveItem : function() {
            var description_field = this.$el.find('.recipe_description');
            var time_field = this.$el.find('.recipe_time');
            var comments_field = this.$el.find('.recipe_comments');
            
            description_field.removeClass("red_style_border");
            time_field.removeClass("red_style_border");
            
            var description = description_field.find(":selected").val();
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
                    app.collections.example_day_recipes.sort();
                    app.views.example_day.loadItems();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }, 

        deleteItem : function() {
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
            if(this.model.get('edit_mode')) {
                return true;
            }
            
            var edit_mode = false;
            
            var description = this.model.get('description');
            var time = this.model.get('time');
            
            if(!description || !time) {
                edit_mode = true;
            }
            
            if(this.options.menu_plan_model.get('is_submitted')) {
                edit_mode = false;
            }
            this.model.set({edit_mode : edit_mode});
        },
        
        onClickEdit : function() {
            this.model.set({edit_mode : true});
            this.render();
        },
        
        onClickCopy : function() {
            var model = new Example_day_recipe_model(this.model.toJSON());
            model.set({id : null, menu_id : this.options.menu_id, number_serves_new : '1'});
            var self = this;
            app.collections.example_day_recipes.create(model, {
                success : function (model, response) {
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadDescriptions : function() {
            var collection = Menu_descriptions_collection;
            var description_id = this.model.get('description');
            
            if(description_id) {
                var model = collection.get(description_id);
                
                if(model) {
                    var name = model.get('name');
                    var image = model.get('image');

                    if(image) {
                        this.$el.find(".description_image").css('background-image', 'url(' + image + ')');
                    }
                    this.$el.find(".description_select").html(name);
                }
            }
            
            if(this.model.get('edit_mode')) {
                new Select_element_view({
                    model : this.model,
                    el : this.$el.find(".description_select"),
                    collection : collection,
                    first_option_title : '-Select-',
                    class_name : 'recipe_description ',
                    id_name : 'description',
                    model_field : 'description',
                    element_disabled :  ""
                }).render();
            }
        },
        
        onClickAddToDiary : function() {
            var number_serves = this.model.get('number_serves');
            var data = {};
            data.recipe_id = this.model.get('original_recipe_id');
            data.number_serves = number_serves;

            data.number_serves_recipe = this.model.get('number_serves');
            data.type = 'nutrition_plan';
            $.fitness_helper.add_diary(data, app);
        },
        
        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });
            
    return view;
});