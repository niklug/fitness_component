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
        'views/goals/backend/list',
        'views/goals/backend/form_primary',
        'views/goals/backend/form_mini',
        'views/goals/backend/comments_block',
        'views/goals/backend/search_block',
        'views/goals/backend/periodization/list',
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
        Comments_block_view,
        Search_block_view,
        Periodization_view
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
            
            //business logic
            var business_profile_id = null;
            if(!app.options.is_superuser) {
                business_profile_id = app.options.business_profile_id;
            }
            //
            
            this.onClientChange();

            app.options.client_id = localStorage.getItem('client_id');
                        
            app.models.request_params_primary = new Request_params_primary_model({user_id : app.options.client_id, business_profile_id : business_profile_id});
            app.models.request_params_primary.bind("change", this.get_items, this);
            if(!app.options.client_id) {
                return;
            }
       
            this.get_items();
            this.get_minigoals();

        },

        routes: {
            "": "list_view", 
            "!/list_view": "list_view", 
            "!/form_primary/:id": "form_primary",
            "!/form_mini/:id/:primary_goal_id": "form_mini",
            "!/schedule/:id/:primary_goal_id": "schedule",
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        onClientChange : function() {
            var self = this;
            $("#graph_client_id").die().live('change', function() {
                var client_id = $(this).val();
                app.options.client_id = client_id;
                localStorage.setItem('client_id', client_id);
                app.models.request_params_primary.set({user_id : client_id});
                self.navigate("!/list_view", true);
            });
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
                data : {user_id :  app.options.client_id},
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 
        },

        connectGraph : function() {
            this.graph = new Graph_view({
                el : "#graph_container",
                model : app.models.request_params_primary,
                show : {
                    primary_goals : true,
                    mini_goals : true,
                    personal_training : false,
                    semi_private : false,
                    resistance_workout : false,
                    cardio_workout : false,
                    assessment : false,
                    current_time : true,
                    client_select : true,
                    choices : true
                },
                style : '',
                reloads : true,
                list_type : $("#list_type").val()
            });
        },
        
        list_view : function() {
            app.models.request_params_primary.set({page : 1, uid : app.getUniqueId()});
            this.list_actions();
        },

        
        list_actions : function () {
            this.connectGraph();
            if(!app.options.client_id) {
                return;
            }

            $("#header_wrapper").html(new Search_block_view({model : app.models.request_params_primary, collection : app.collections.primary_goals}).render().el);
            
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
            this.hideHeader();
            var readonly_allowed = this.readonly_allowed(model);
            model.set({readonly_allowed : readonly_allowed});
            $("#main_container").html(new Form_primary_view({collection : app.collections.primary_goals, model : model}).render().el);
        },
        
        form_mini : function(id, primary_goal_id) {
            this.hideHeader();
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

            if(status != app.options.statuses.PENDING_GOAL_STATUS.id && status != app.options.statuses.EVELUATING_GOAL_STATUS.id) {
                access = true;
            }
            
            return access;
        },
        
        connectStatus : function(model, el, type) {

            var id = model.get('id');
            var status = model.get('status');
            var options = _.extend({}, app.options.status_options);
            
            var target = "#status_button_place_" + id;
            
            if(type == 'mini') {
                options = _.extend({}, app.options.status_options_mini);
                
                target = "#status_button_place_mini_" + id;
            }

            if(id) {
                if(app.options.statuses.PENDING_GOAL_STATUS.id == status) {
                    options.status_button = 'status_button_not_active';
                }
                //console.log(options);
                var status_obj = $.status(options);

                el.find(target).html(status_obj.statusButtonHtml(id, status));

                status_obj.run();
            }
        },
        
        connectComments : function(model, view, type) {
            if(model.get('id')) {
                new Comments_block_view({el : view.find("#comments_block"), model : model, read_only : false, type : type});
            }
        },
        
        sendGoalEmail : function(id, method) {
            var data = {};
            var url = app.options.ajax_call_url;
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
        
        hideHeader : function() {
            $("#header_wrapper").empty();
        },
        
        emptyAll : function() {
            $("#graph_container, #main_container, #list_type, #header_wrapper").empty();
        },
        
        update_list : function() {
            app.models.request_params_primary.set({ uid : app.getUniqueId()});
        },
        
        schedule :function(primary_goal_id, mini_goal_id) {
            this.emptyAll();
            $("#main_container").html(new Periodization_view({mini_goal_id : mini_goal_id, primary_goal_id : primary_goal_id}).render().el);
        },
        
        schedule_session : function(model) {
            model.set({client_id : app.options.client_id, owner : app.options.user_id});
            var data = model.toJSON();
            var url = app.options.ajax_call_url;
            var view = 'goals';
            var task = 'scheduleSession';
            var table = '';
            //console.log(data);
            $.AjaxCall(data, url, view, task, table, function(output){
                console.log(output);
            });
        }


    });

    return Controller;
});