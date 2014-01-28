define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/nutrition_database/list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render : function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click .search_ingredients" : "onClickSearch",
            "click .clear" : "onClickClear",
        },

        onClickSearch : function() {
            window.app.nutrition_database_model.setLocalStorageItem('currentPage', 1);
            window.app.nutrition_database_model.set({'currentPage' : ""});
            window.app.nutrition_database_model.loadIngredients();
        },

        onClickClear : function(){
            $("#search_field").val('');
            window.app.nutrition_database_model.setLocalStorageItem('currentPage', 1);
            window.app.nutrition_database_model.set({'currentPage' : ""});
            window.app.nutrition_database_model.loadIngredients();
        }
    });
            
    return view;
});