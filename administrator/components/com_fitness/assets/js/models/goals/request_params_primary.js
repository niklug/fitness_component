define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            current_page: '',
            sort_by : 'pg.start_date',
            order_dirrection : 'DESC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
            state : '*',
            list_type : 'current'
            
        }
    });
    return model;
});