define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            current_page: '',
            sort_by : 'a.starttime',
            order_dirrection : 'DESC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
            published : null,
            frontend_published : null,
            date_from : null,
            date_to : null,
            client_name : null,
            trainer_name : null,
            created_by_name : null,
            title : null,
            location : null,
            session_type : null,
            session_focus :null
        }
    });
    return model;
});