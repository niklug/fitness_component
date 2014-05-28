define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/primary_goals',
        'collections/goals/mini_goals',
        'models/goals/request_params_primary',
        'views/graph/graph',
        
        'jquery.flot',
        'jquery.flot.time',
        'jquery.validate'
        
], function (
        $,
        _,
        Backbone,
        app,
        Primary_goals_collection,
        Mini_goals_collection,
        Request_params_primary_model,
        Graph_view
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
            app.collections.mini_goals = new Mini_goals_collection();
            
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
            
            app.collections.mini_goals.fetch({
                data : {},
                success : function (collection, response) {
                    console.log(collection.toJSON());
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
        }

    });

    return Controller;
});