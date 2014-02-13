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
        'views/exercise_library/backend/menus/main_menu',
        'views/exercise_library/backend/exercise_details',
        'views/exercise_library/backend/exercise_video',
        'views/exercise_library/backend/business_permissions',
        'views/exercise_library/backend/list',
        'views/exercise_library/backend/list_header_container',
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
        Main_menu_view,
        Exercise_details_view,
        Exercise_video_view,
        Business_permissions_view,
        List_view,
        List_header_container_view
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
            
            //business logic
            var business_profiles = null;
            if(!app.options.is_superuser) {
                business_profiles = app.options.business_profile_id;
            }
            //
            
            app.models.request_params = new Request_params_items_model({business_profiles : business_profiles});
            app.models.request_params.bind("change", this.get_items, this);
        },

        routes: {
            "": "list_view", 
            "!/form_view/:id": "form_view", 
            "!/list_view": "list_view", 
            "!/trash_list": "trash_list", 
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});t
            }
        },

        form_view : function(id) {
            $("#main_container").html(new Form_container_view().render().el);
            if(!parseInt(id)) {
                this.load_form_view(new Exercise_library_item_model());
                return;
            }
            
            var self = this;
            app.models.exercise_library_item.set({id : id});
            app.models.exercise_library_item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    self.load_form_view(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        load_form_view : function(model) {
            $("#header_wrapper").html(new Main_menu_view({model : model}).render().el);

            $("#exercise_details_wrapper").html(new Exercise_details_view({model : model}).render().el);

            $("#select_filter_wrapper").html(new Select_filter_block_view({model : model, block_width : '140px'}).render().el);

            if(model.get('id')) {
                $("#exercise_video_wrapper").html(new Exercise_video_view({model : model}).render().el);
                this.loadVideoPlayer();
            }

            new Business_permissions_view({el : $("#permissions_wrapper"), model : model});
        },
        
        loadVideoPlayer : function() {
            var no_video_image_big = app.options.no_video_image_big;

            var video_path = app.models.exercise_library_item.get('video');

            var base_url = app.options.base_url;

            var imageType = /no_video_image.*/;  

            if (video_path && !video_path.match(imageType) && video_path) {  

                jwplayer('exercise_video').setup({
                    file: base_url + video_path,
                    image: "",
                    height: 250,
                    width: 400,
                    autostart: true,
                    mute: true,
                    controls: false,
                    events: {
                        onReady: function () { 
                            var self = this;
                            setTimeout(function(){
                                self.pause();
                                self.setMute(false);
                                self.setControls(true);
                            },3000);
                        }
                    }
                });
            } else {
                $("#exercise_video").css('background-image', 'url(' +  no_video_image_big + ')');
            }
        },
        
        get_items : function() {
            var params = app.models.request_params.toJSON();
            if($.isEmptyObject(params)) {
                return;
            }
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        list_view : function() {
            app.models.request_params.set({page : 1, current_page : 'list',  state : 1, uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        list_actions : function () {
            $("#header_wrapper").html(new List_header_container_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
        },
        
        set_params_model : function() {
            app.collections.items.reset();
            app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        trash_list : function() {
            app.models.request_params.set({page : 1, current_page : 'trash_list',  state : '-2', uid : app.getUniqueId()});
            
            this.list_actions();
        },
    });

    return Controller;
});