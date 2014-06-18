define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/primary_goals',
        'collections/goals/mini_goals',
        'models/goals/request_params_primary',
        'models/goals/primary_goal',
        'models/goals/mini_goal',
        'views/graph/graph',
        'views/goals/frontend/list',
        'views/goals/frontend/form_primary',
        'views/goals/frontend/form_mini',
        'views/goals/frontend/comments_block',
        'jquery.flot',
        'jquery.flot.time',
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
        Form_mini_view,
        Comments_block_view
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

            app.models.request_params_primary = new Request_params_primary_model({user_id : app.options.client_id});
            app.models.request_params_primary.bind("change", this.get_items, this);
  
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
        
        get_minigoals : function() {
            app.collections.mini_goals.reset();
            app.collections.mini_goals.fetch({
                wait : true,
                data : {user_id : app.options.user_id},
                success : function (collection, response) {
                    //console.log(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 
        },

        connectGraph : function() {
            this.graph = new Graph_view({
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
                style : 'dark',
                reloads : true,
                list_type : $("#list_type").val()
            });
        },
        
        list_view : function() {
            this.connectGraph();
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
            model = new Primary_goal_model({id : id});
            var self = this;
            model.fetch({
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
            var readonly_allowed = this.readonly_allowed(model);
            model.set({readonly_allowed : readonly_allowed});
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
            var readonly_allowed = this.readonly_allowed(model);
            model.set({readonly_allowed : readonly_allowed});
            $("#main_container").html(new Form_mini_view({collection : app.collections.mini_goals, model : model, primary_goal_id : primary_goal_id}).render().el);
        },
        
        readonly_allowed : function(model) {
            var access = false;

            var status = model.get('status');

            if(
                    status != app.options.statuses.PENDING_GOAL_STATUS.id 
                    && status != app.options.statuses.EVELUATING_GOAL_STATUS.id
                    && status != app.options.statuses.ASSESSING_GOAL_STATUS.id
            ) {
                access = true;
            }
            
            return access;
        },
        
        connectStatus : function(id, status, el) {
            var status_obj = $.status(app.options.status_options);
              
            var html =  status_obj.statusButtonHtml(id, status);

            el.find("#status_button_place_" + id).html(html);
        },
        
        connectComments : function(model, view, type) {
            if(model.get('id')) {
                new Comments_block_view({el : view.find("#comments_block"), model : model, read_only : true, type : type});
            }
        },
        
        sendGoalEmail : function(id, method) {
            var data = {};
            var url = app.options.fitness_frontend_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'Goal';
            data.method = method;

            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                console.log(output);
            });
        },


    });

    return Controller;
});