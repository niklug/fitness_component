'use strict';
/*global define */
define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/recipe_database/ingredient_categories',
    'views/exercise_library/select_element',
    'text!templates/recipe_database/frontend/nutrition_database/form.html',
    'jquery.recipe_database'
], function ($, _, Backbone,app, Categories_collection, Select_element_view, template) {
    var view = Backbone.View.extend({
        initialize : function () {
            $.recipe_database({'specific_gravity' : '' }).run();
            
            app.collections.categories = new Categories_collection();
            var self = this;
            app.collections.categories.fetch({
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },

        template : _.template(template),

        render : function () {
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            
            this.connectCategorySelect();
            
            return this;
        },
        
        connectCategorySelect : function() {
             new Select_element_view({
                model : this.model,
                el : this.$el.find("#category_wrapper"),
                collection : app.collections.categories,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'category',
                model_field : 'category'
            }).render();
        },
    });
    return view;
});