define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/nutrition_plan/nutrition_guide/example_day_recipe',
	'text!templates/nutrition_plan/nutrition_guide/example_day_recipe.html',
], function ( $, _, Backbone, app, Example_day_recipe_model, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize : function() {
            this.edit_mode();
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
            console.log(app.collections.example_day_recipes.toJSON());
            var self = this;
            app.collections.example_day_recipes.create(model, {
                success : function (model, response) {
                    console.log(app.collections.example_day_recipes.toJSON());
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