define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/exercise_library/exercise_library_item',
        'views/exercise_library/select_filter_block',
        'views/exercise_library/backend/menus/main_menu',
        'views/exercise_library/backend/exercise_details',
        'views/exercise_library/backend/exercise_video',
        'views/exercise_library/backend/business_permissions',
        
        'jwplayer', 
        'jwplayer_key',
], function (
        $,
        _,
        Backbone,
        app,
        Exercise_library_item_model,
        Select_filter_block_view,
        Main_menu_view,
        Exercise_details_view,
        Exercise_video_view,
        Business_permissions_view
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
        },

        routes: {
            "": "form_view", 
            "!/form_view": "form_view", 
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },

        form_view : function() {
            var self = this;
            app.models.exercise_library_item.fetch({
                data : {},
                success: function (model, response) {
                    $("#main_menu").html(new Main_menu_view({model : app.models.exercise_library_item}).render().el);
                    
                    $("#exercise_details_wrapper").html(new Exercise_details_view({model : app.models.exercise_library_item}).render().el);
                    
                    $("#select_filter_wrapper").html(new Select_filter_block_view({model : app.models.exercise_library_item, block_width : '140px'}).render().el);
                    
                    $("#exercise_video_wrapper").html(new Exercise_video_view({model : app.models.exercise_library_item}).render().el);
                    
                    self.loadVideoPlayer();
                    
                    new Business_permissions_view({el : $("#permissions_wrapper"), model : app.models.exercise_library_item});
                    
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        loadVideoPlayer : function() {
            var no_video_image_big = app.options.no_video_image_big;

            var video_path = app.models.exercise_library_item.get('video');

            var base_url = app.options.base_url;

            var imageType = /no_video_image.*/;  

            if (!video_path.match(imageType) && video_path) {  

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
    });

    return Controller;
});