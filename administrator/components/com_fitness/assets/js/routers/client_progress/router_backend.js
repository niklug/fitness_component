define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/items',
        'models/assessments/request_params_items',
        'views/client_progress/backend/search_header',
        'views/client_progress/backend/sub_search_container',
        'jquery.flot',
        
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Request_params_items_model,
        Search_header_view,
        Sub_search_container_view
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

            //business logic
            var business_profile_id = null;
            if(!app.options.is_superuser) {
                business_profile_id = app.options.business_profile_id;
            }
            //
            
            app.collections.items = new Items_collection();
            
            app.models.request_params = new Request_params_items_model({published : '1', frontend_published : '1', limit : '100'});
            app.models.request_params.bind("change", this.get_items, this);
            
            app.collections.items.on("sync", this.load_sub_search, this);

        },

        routes: {
            "": "search_header", 

        },
        
        get_items : function() {
            var params = app.models.request_params.toJSON();
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        search_header : function() {
             $("#header_wrapper").html(new Search_header_view({model : app.models.request_params, collection : app.collections.items}).render().el);
        },
        
        load_sub_search : function() {
            $("#sub_search_wrapper").html(new Sub_search_container_view({model : app.models.request_params, collection : app.collections.items}).render().el);
        }

    });

    return Controller;
});