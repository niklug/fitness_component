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
            published : '',
            frontend_published : '',
            date_from : '',
            date_to : '',
            client_name : '',
            trainer_name : '',
            created_by_name : '',
            title : '',
            location : '',
            session_type : '',
            session_focus :'',
            appointment_types : '1,2,3,4,6,7,8,9'// all, except Assessments
        }
    });
    return model;
});