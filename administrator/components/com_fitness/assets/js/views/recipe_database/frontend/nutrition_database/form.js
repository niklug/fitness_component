'use strict';
/*global define */
define([
    'jquery',
    'underscore',
    'backbone',
    'text!templates/recipe_database/frontend/nutrition_database/form.html',
    'jquery.recipe_database'
], function ($, _, Backbone, template) {
    var view = Backbone.View.extend({
        initialize : function () {
            $.recipe_database({'specific_gravity' : '' }).run();
        },

        template : _.template(template),

        render : function () {
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },
    });
    return view;
});