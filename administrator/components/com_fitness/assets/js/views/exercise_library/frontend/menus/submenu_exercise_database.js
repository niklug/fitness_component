define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/menus/submenu_exercise_database.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            
            this.setSelectFilter();
            
            return this;
        },
        
        events : {
            "click #search_by_name" : "search",
            'keypress input[type=text]': 'filterOnEnter',
            "click #clear_all" : "clearAll",
            "click #view_trash" : "onClickViewTrash",
            "click #close_trash_list" : "onClickCloseTrashList",
            "click #add_item" : "onClickAddItem",
            "change #sort_filter" : "onChangeSortFilter",
        },
        
        search : function() {
            var exercise_name = this.$el.find("#exercise_name").val();
            this.model.set({exercise_name : exercise_name});
        },
        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
        
        clearAll : function(){
            var form = $("#filters_container");
            form.find(".filter_select").val(0);
            this.$el.find("#exercise_name").val('');
            
            this.model.set(
                {
                    exercise_name : '',
                    client_name : '',
                    exercise_type : '',
                    force_type : '',
                    mechanics_type : '',
                    body_part : '',
                    target_muscles : '', 
                    equipment_type : '',
                    difficulty : '',
                }
            );
        },
        
        onClickViewTrash : function() {
            app.controller.navigate("!/trash_list", true);
        },
        
        onClickCloseTrashList : function(){
            app.controller.navigate("!/my_exercises", true);
        },
        
        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
        },
        
        onChangeSortFilter : function(event) {
            var id = $(event.target).val();
            
            if(id == '1') {
                app.models.request_params.set({sort_by : 'a.exercise_name', order_dirrection : 'ASC'});
            } else if(id == '2') {
                app.models.request_params.set({sort_by : 'a.exercise_name', order_dirrection : 'DESC'});
            } else if(id == '3') {
                app.models.request_params.set({sort_by : 'created_by_name', order_dirrection : 'ASC'});
            } else if(id == '4') {
                app.models.request_params.set({sort_by : 'created_by_name', order_dirrection : 'DESC'});
            } else if(id == '5') {
                app.models.request_params.set({sort_by : 'a.created', order_dirrection : 'ASC'});
            } else if(id == '6') {
                app.models.request_params.set({sort_by : 'a.created', order_dirrection : 'DESC'});
            } else if(id == '7') {
                app.models.request_params.set({sort_by : 'a.status', order_dirrection : 'ASC'});
            } else {
                app.models.request_params.set({sort_by : 'a.exercise_name', order_dirrection : 'ASC'});
            }
        },
        
        setSelectFilter : function() {
            var id = 0;
            
            var sort_by = app.models.request_params.get('sort_by');
            
            var order_dirrection = app.models.request_params.get('order_dirrection');
            
            if(sort_by == 'a.exercise_name' && order_dirrection == 'ASC') {
                id = 1;
            } else if(sort_by == 'a.exercise_name' && order_dirrection == 'DESC') {
                id = 2;
            } else if(sort_by == 'a.created_by_name' && order_dirrection == 'ASC') {
                id = 3;
            } else if(sort_by == 'a.created_by_name' && order_dirrection == 'DESC') {
                id = 4;
            } else if(sort_by == 'a.created' && order_dirrection == 'ASC') {
                id = 5;
            } else if(sort_by == 'a.created' && order_dirrection == 'DESC') {
                id = 6;
            } else if(sort_by == 'a.status' && order_dirrection == 'ASC') {
                id = 7;
            }
            
            this.$el.find("#sort_filter").val(id);
        }
    });
            
    return view;
});