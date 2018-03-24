define([
    'underscore',
    'backbone'
], function ( _, Backbone) {
    var model = Backbone.Model.extend({
        defaults : {
            current_page: '',
            sort_by : 'a.created',
            order_dirrection : 'DESC',
            page : localStorage.getItem('currentPage') || 1,
            limit : localStorage.getItem('items_number') || 10,
            state : '',
            date_from : '',
            date_to : '',
            client_name : '',
            created_by_name : '',
            appointment_id : '',
            session_type : '',
            session_focus :''
        }
    });
    return model;
});