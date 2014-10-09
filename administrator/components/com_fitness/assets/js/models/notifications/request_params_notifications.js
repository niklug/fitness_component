define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            sort_by : 'a.created',
            order_dirrection : 'DESC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
        }
    });
    return model;
});