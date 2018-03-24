define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            sort_by : 'recipe_name',
            order_dirrection : 'ASC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
            state : 1,
            filter_options : '',
            recipe_variations_filter_options : '',
            current_page : 'meal_recipes'
        }
    });
    return model;
});