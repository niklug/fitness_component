define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/exercise_library',
        'models/exercise_library/exercise_library_item',
        'models/exercise_library/request_params_items',
        'views/exercise_library/backend/form_container',
        'views/exercise_library/select_filter_block',
        'views/exercise_library/backend/exercise_details',
        'views/exercise_library/backend/exercise_video',
        'views/exercise_library/frontend/list',
        'views/exercise_library/select_filter_block',
        'views/exercise_library/frontend/menus/submenu_exercise_database',
        'views/exercise_library/frontend/popular_exercises/list',
        'jwplayer', 
        'jwplayer_key',
], function (
        $,
        _,
        Backbone,
        app,
        Exercise_library_collection,
        Exercise_library_item_model,
        Request_params_items_model,
        Form_container_view,
        Select_filter_block_view,
        Exercise_details_view,
        Exercise_video_view,
        List_view,
        Select_filter_block_view,
        Submenu_exercise_database_view,
        Popular_exercises_view
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            //unique id
            app.getUniqueId = function() {
                return new Date().getUTCMilliseconds();
            }
                        
            app.models.exercise_library_item = new Exercise_library_item_model();
            
            app.collections.items = new Exercise_library_collection();
            
            app.models.request_params = new Request_params_items_model();
            app.models.request_params.bind("change", this.get_items, this);
            
            //popolar exercises
            app.collections.popular_items = new Exercise_library_collection();
            app.models.request_params_popular = new Request_params_items_model();
            
            
        },

        routes: {
            "": "my_exercises", 
            "!/exercise_database": "exercise_database", 
            "!/my_exercises": "my_exercises",
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});t
            }
        },

        get_items : function() {
            var params = app.models.request_params.toJSON();
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                success: function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        get_items_popular : function() {
            app.models.request_params_popular.set({sort_by : 'created', order_dirrection : 'DESC', limit : 15});
            app.collections.popular_items.reset();
            app.collections.popular_items.fetch({
                data : app.models.request_params_popular.toJSON(),
                success: function (collection, response) {
                    console.log(collection.toJSON());
                    $("#right_side").html(new Popular_exercises_view({collection : collection}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        exercise_database : function() {
            $("#submenu_container").html(new Submenu_exercise_database_view({model : app.models.request_params}).render().el);
            
            this.list_actions();
            
            $("#exercise_database_link").addClass("active_link");
            
            app.models.request_params.set({page : 1, current_page : 'exercise_database',  state : 1, uid : app.getUniqueId()});
        },
        
        my_exercises : function() {
            $("#submenu_container").html(new Submenu_exercise_database_view({model : app.models.request_params}).render().el);
            
            this.list_actions();
            
            $("#my_exercises_link").addClass("active_link");
            
            app.models.request_params.set({page : 1, current_page : 'my_exercises',  state : 1, uid : app.getUniqueId()});
        },
        
        
        list_actions : function () {
            $(".plan_menu_link").removeClass("active_link");
            
            $("#filters_container").html(new Select_filter_block_view({model : app.models.request_params, block_width : '152.5px'}).render().el);
            //hide 1 filter
            $("#mechanics_type_filter_wrapper").remove();
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);

            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
            
            this.get_items_popular();
        },
        
        set_params_model : function() {
            app.collections.items.reset();
            app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
       

    });

    return Controller;
});