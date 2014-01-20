define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/meal_recipe.html',
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render:function () {
            var data = this.model.toJSON();
            data.$ = $;
            data.menu_plan = app.models.menu_plan.toJSON();
            $(this.el).html(this.template( data ));
            return this;
        },

        events: {
            "click .save_recipe" : "onClickSaveRecipe",
            "click .delete_recipe" : "onClickDeleteRecipe",
            "click .view_recipe" : "onClickViewRecipe",
        },

        onClickSaveRecipe : function() {
            var recipe_comments = this.$el.find('.recipe_comments').val();
            
            this.model.set({'description' : recipe_comments});

            this.model.save(null, {
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
            var url = app.options.base_url + 'index.php?option=com_fitness&view=recipe_database&Itemid=1002#!/nutrition_database/nutrition_recipe/' + this.model.get('original_recipe_id');
            window.open(url);
        },

        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });
            
    return view;
});