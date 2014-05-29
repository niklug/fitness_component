define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/primary_goals',
        'models/goals/request_params_primary',
        'views/graph/graph',
        'views/goals/backend/list',
        
        'jquery.flot',
        'jquery.flot.time',
        'jquery.validate',
        'jquery.status'
        
], function (
        $,
        _,
        Backbone,
        app,
        Primary_goals_collection,
        Request_params_primary_model,
        Graph_view,
        List_view
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
            
            app.collections.primary_goals = new Primary_goals_collection();

            app.models.request_params_primary = new Request_params_primary_model({client_id : app.options.client_id});
            app.models.request_params_primary.bind("change", this.get_items, this);
            
            this.connectGraph();
            
            this.get_items();

        },

        routes: {
            "": "list_view", 
  
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        get_items : function() {
            var params = app.models.request_params_primary.toJSON();
            app.collections.primary_goals.reset();
            app.collections.primary_goals.fetch({
                data : params,
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },

        connectGraph : function() {
            new Graph_view({
                el : "#graph_container",
                model : '',
                show : {
                    primary_goals : true,
                    mini_goals : true,
                    personal_training : false,
                    semi_private : false,
                    resistance_workout : false,
                    cardio_workout : false,
                    assessment : false,
                    current_time : true,
                    
                    client_select : false,
                    choices : false
                },
                style : 'dark'
            });
        },
        
        list_view : function() {
            $("#main_container").html(new List_view({model : app.models.request_params_primary, collection : app.collections.primary_goals}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
        },
        
        set_params_model : function() {
            app.collections.primary_goals.reset();
            app.models.request_params_primary.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },

    });

    return Controller;
});