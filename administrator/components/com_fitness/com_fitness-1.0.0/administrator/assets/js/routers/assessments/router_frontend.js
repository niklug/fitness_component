define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/items',
        'models/assessments/item',
        'models/assessments/request_params_items',
        'models/assessments/favourite',
        'views/assessments/frontend/menus/submenu_list',
        'views/assessments/frontend/menus/submenu_item',
        'views/assessments/frontend/menus/submenu_form',
        'views/assessments/frontend/list',
        'views/assessments/frontend/item',
        'views/assessments/frontend/form',
        'views/programs/backend/comments_block',
        'views/assessments/frontend/form_standard_assessment',
        'views/assessments/frontend/form_bio_assessment',
        'views/graph/graph',
        
        'views/client_progress/frontend/search_header',
        'views/client_progress/frontend/sub_search_container',
        
        'jquery.flot',
        'jquery.flot.time',
        'jquery.validate'
        
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Item_model,
        Request_params_items_model,
        Favourite_model,
        Submenu_list_view,
        Submenu_item_view,
        Submenu_form_view,
        List_view,
        Item_view,
        Form_view,
        Comments_block_view,
        Form_standard_assessment_view,
        Form_bio_assessment_view,
        Graph_view,
        
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
                        
            app.models.item = new Item_model({});
            
            app.collections.items = new Items_collection();
            
            //business logic
            var business_profile_id = null;
            if(!app.options.is_superuser) {
                business_profile_id = app.options.business_profile_id;
            }
            //

            
            app.models.request_params = new Request_params_items_model({business_profile_id : business_profile_id, current_page : 'my_progress'});
            app.models.request_params.bind("change", this.get_items, this);
            
        },

        routes: {
            "": "assessments", 
            "!/my_progress": "my_progress", 
            "!/self_assessments": "self_assessments", 
            "!/assessments": "assessments", 
            "!/trash_list": "trash_list", 
            
            "!/item_view/:id": "item_view",
            
            "!/form_view/:id": "form_view",
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
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
        
        my_progress : function() {
            $(".menu_link").removeClass("active_link");
            $("#submenu_container, #main_container").empty();        
            $("#my_progress_link").addClass("active_link");
            this.removeGraph();
            
            this.search_header();
        },
        
        search_header : function() {
            $("#submenu_container").after('<div id="sub_search_wrapper" class="fitness_wrapper" style="padding:0;margin-bottom:10px;"></div>');  
            
            $("#submenu_container").html(new Search_header_view({model : app.models.request_params, collection : app.collections.items}).render().el);
        },
        
        load_sub_search : function() {
            $("#sub_search_wrapper").after('<div id="progress_graph_container" class="fitness_wrapper" style="padding:0;"></div>'); 
            
            $("#sub_search_wrapper").html(new Sub_search_container_view({model : app.models.request_params, collection : app.collections.items}).render().el);
        },
        
        self_assessments : function() {
            app.models.request_params.set({page : 1, current_page : 'self_assessments', published : '1', frontend_published : '2',  uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#self_assessments_link").addClass("active_link");
        },
        
        trash_list : function() {
            app.models.request_params.set({page : 1, current_page : 'trash_list', published : '-2', frontend_published : '2',  uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_workouts_link").addClass("active_link");
        },
        
        assessments : function () {
            app.models.request_params.set({page : 1, current_page : 'assessments', published : '1', frontend_published : '2', uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#assessments_link").addClass("active_link");
        },
        
        
        list_actions : function () {
            $("#sub_search_wrapper, #progress_graph_container").remove();  
            
            $("#submenu_container").html(new Submenu_list_view({model : app.models.request_params}).render().el);
            
            new Graph_view({
                el : "#graph_container",
                model : this.model,
                show : {
                    primary_goals : true,
                    mini_goals : true,
                    personal_training : false,
                    semi_private : false,
                    resistance_workout : false,
                    cardio_workout : false,
                    assessment : true,
                    current_time : true,
                    
                    client_select : false,
                    choices : false
                },
                style : 'dark'
            });
            
            $(".menu_link").removeClass("active_link");
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
        },
        
        set_params_model : function() {
            app.collections.items.reset();
            app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
       
        update_list : function() {
            app.models.request_params.set({ uid : app.getUniqueId()});
        },
        
        
        edit_allowed : function(model) {
            var access = true;

            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            var appointment = model.get('title');
            var status = model.get('status');
            
            //if status ASSESSING
            if(status == '2') {
                return false;
            }
            
            return access;
        },
        
        is_item_owner : function(model) {
            var access = false;
            
            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            
            if(user_id == created_by) {
                access = true;
            }
            return access;
        },
        
        delete_allowed : function(model) {
            var access = false;

            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            
            if(user_id == created_by) {
                access = true;
            }

            return access;
        },
        
        status_change_allowed : function(model) {
            var access = false;
            
            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            var appointment = model.get('title');
            var status = model.get('status');
            
            // if ‘COMPLETE', 'INCOMPLETE' or 'NOT ATTEMPTED’,
            if(status == '6' || status == '7' || status == '8' || status == '2') {
                return false;
            }
            
            if(user_id == created_by) {
                return true;
            }

            //'Resistance Workout' and 'Cardio Workout'
            if(appointment == '3' || appointment == '4') {
                return true;
            }
           
            return access;
        },
        

        item_view : function(id) {
            this.removeGraph();
            var self = this;
            app.models.item.set({id : id});
            app.models.item.fetch({
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model), status_change_allowed : self.status_change_allowed(model), delete_allowed : self.delete_allowed(model)});
            
                    $("#submenu_container").html(new Submenu_item_view({model : model, request_params_model : app.models.request_params}).render().el);

                    $("#main_container").html(new Item_view({model : model, request_params_model : app.models.request_params}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        removeGraph : function() {
            $("#graph_container").empty();
        },
        
        connectStatus : function(model, view) {
            var id = model.get('client_item_id');
            if(!id) {
                return;
            }
            var status = model.get('status');

            var options = _.extend({}, app.options.status_options);
            if(id) {
                var status_change_allowed = model.get('status_change_allowed');
                
                if(!parseInt(model.get('frontend_published'))) {
                    options.status_button = 'status_button_not_active';
                }
                
                if(status_change_allowed == false) {
                    options.status_button = 'status_button_not_active';
                }
                
                options.model = model;
                
                var status_obj = $.status(options);

                view.find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

                status_obj.run();
            }
        },
        
        connectComments : function(model, view) {
            if(model.get('id')) {
                new Comments_block_view({el : view.find("#comments_block"), model : model, read_only : true});
            }
        },
        
        add_favourite : function(id) {
            var favourite_model = new Favourite_model({id : id})
            favourite_model.save(null, {
                success: function (model) {
                    model.trigger('save');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        remove_favourite : function(id) {
            var favourite_model = new Favourite_model({id : id})
            var self = this;
            favourite_model.destroy({
                success: function (model) {
                    model.trigger('detroy');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        form_view : function(id) {
            this.removeGraph();
            $("#filters_container").empty();
            if(!parseInt(id)) {
                this.load_form_view(new Item_model());
                return;
            }
            
            var self = this;
            app.models.item.set({id : id});
            app.models.item.fetch({
                wait : true,
                success: function (model, response) {
                    if(self.edit_allowed(model)) {
                        self.load_form_view(model);
                    } else {
                        self.navigate("!/my_progress", true);
                    }
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        load_form_view : function(model) {

            $("#submenu_container").html(new Submenu_form_view({model : model, request_params_model : app.models.request_params}).render().el);
            
            new Form_view({el : $("#main_container"), model : model});
        },
        
        loadAssessmentsForm : function(value, model, options) {
            $("#assessment_form_wrapper").empty();
            
            $("#workout_instuctions_wrapper").show();
            $("#exercises_list").parent().show();
            $("#save_template_button").show();
            
            var form = 'standard';
            
            if(this.is_bio_assessment(value)) {
                form = 'bio';
                $("#workout_instuctions_wrapper").hide();
                $("#exercises_list").parent().hide();
            }
            
            var html = new Form_standard_assessment_view({model : model, readonly : options.readonly}).render().el;
                        
            if(form == 'bio') {
                html = new Form_bio_assessment_view({model : model, readonly : options.readonly}).render().el;
                $("#save_template_button").hide();
            }

            $("#assessment_form_wrapper").html(html);
        },
        
        is_bio_assessment : function(name) {
            var result = false;
            if((name.toLowerCase().indexOf("bio") > -1)) {
                result = true;
            }
            return result;
        }

    });

    return Controller;
});