define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            sort_by : '',
            order_dirrection : 'ASC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
            state : 1
        }
    });
    return model;
});