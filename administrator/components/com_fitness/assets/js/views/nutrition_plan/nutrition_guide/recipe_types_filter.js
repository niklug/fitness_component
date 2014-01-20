define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/recipe_types_filter.html',
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render : function(){
            var data = {'items' : this.model}
            $(this.el).html(this.template(data));
            return this;
        },

        events: {
            "change #categories_filter" : "onFilterSelect",
        },

        onFilterSelect : function(event){
            var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
            //console.log(ids);
            app.models.pagination.reset();
            app.collections.add_meal_recipes.reset();
            app.models.get_recipe_params.set({'filter_options' : ids});
        }
    });
            
    return view;
});