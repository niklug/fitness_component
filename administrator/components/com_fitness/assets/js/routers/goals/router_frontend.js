define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/primary_goals','collections/goals/mini_goals',
        
        'models/goals/request_params_primary',
        'models/goals/primary_goal',
        'models/goals/mini_goal',
        'views/graph/graph',
        'views/goals/backend/list',
        'views/goals/backend/form_primary',
        'views/goals/backend/form_mini',
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
        Mini_goals_collection,
        Request_params_primary_model,
        Primary_goal_model,
        Mini_goal_model,
        Graph_view,
        List_view,
        Form_primary_view,
        Form_mini_view
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
            this.get_minigoals();

        },

        routes: {
            "": "list_view", 
            "!/list_view": "list_view", 
            "!/form_primary/:id": "form_primary",
            "!/form_mini/:id/:primary_goal_id": "form_mini",
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
            app.collections.primary_goals.fetch({
                data : params,
                success : function (collection, response) {
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        get_minigoals : function() {
            app.collections.mini_goals.fetch({
                wait : true,
                data : {user_id : app.options.user_id},
                success : function (collection, response) {
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
        },

        form_primary : function(id) {
            if(!parseInt(id)) {
                this.load_form_primary(new Primary_goal_model());
                return;
            }

            var model = app.collections.primary_goals.get(id);
            if(model) {
                this.load_form_primary(model);
                return;
            }

            model = new Mini_goal_model({id : id});
            var self = this;
            model.fetch({
                wait : true,
                success: function (model, response) {
                    app.collections.primary_goals.add(model);
                    self.load_form_primary(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        load_form_primary : function(model) {
            $("#main_container").html(new Form_primary_view({collection : app.collections.primary_goals, model : model}).render().el);
        },
        
        form_mini : function(id, primary_goal_id) {
            if(!parseInt(id)) {
                this.load_form_mini(new Mini_goal_model(), primary_goal_id);
                return;
            }

            var model = app.collections.mini_goals.get(id);
            if(model) {
                this.load_form_mini(model, primary_goal_id);
                return;
            }

            model = new Mini_goal_model({id : id});
            var self = this;
            model.fetch({
                wait : true,
                success: function (model, response) {
                    app.collections.mini_goals.add(model);
                    self.load_form_mini(model, primary_goal_id);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        load_form_mini : function(model, primary_goal_id) {
            $("#main_container").html(new Form_mini_view({collection : app.collections.mini_goals, model : model, primary_goal_id : primary_goal_id}).render().el);
        }

    });

    return Controller;
});