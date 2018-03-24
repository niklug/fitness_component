define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            current_page: 'list',
            sort_by : 'entry_date',
            order_dirrection : 'DESC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
            state : 1,
        }
    });
    return model;
});