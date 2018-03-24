define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/frontend/form_video.html',
        'jquery.backbone_video_upload',
        'jwplayer_key',
        'jwplayer'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize : function() {
            this.render();
        },
        
        render: function(){
            var data = {data : this.model.toJSON()};
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.readonly = this.options.readonly || false;
            
            $(this.el).find("#video_trainer_comments").attr('disabled', true);
            
            if(this.readonly) {
                $(this.el).find("input, textarea").attr('disabled', true);
                
                this.setClientCommentsEdit(this.model);
            }
            
            this.connectVideoUpload();
            
            
            return this;
        },
        
        events: {
            "click #save_video" : "onClickSave",
        },
        
        setClientCommentsEdit : function(model) {
            if(app.options.const_trainer_assessment != this.model.get('session_type')) {
                return;
            }
            if(!model.get('video_client_comments')) {
                $(this.el).find("#video_client_comments").attr('readonly', false);
                $(this.el).find("#save_video").show();
            } else {
                $(this.el).find("#video_client_comments").attr('readonly', true);
                $(this.el).find("#save_video").hide();
            }
        },
        
        onClickSave : function() {
            var self = this;
            this.model.set({video_client_comments : $(this.el).find("#video_client_comments").val()});
            this.model.save(null, {
                success : function(model, response) {
                    self.setClientCommentsEdit(model);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        connectVideoUpload : function() {
            var videopath = '';
            if(this.model.get('id')) {
                videopath = this.model.get('video');
            }
            var filename = '';
            if(typeof videopath !== 'undefined') {
                var fileNameIndex = videopath.lastIndexOf("/") + 1;
                filename = videopath.substr(fileNameIndex);
            }

            var video_upload_options = {
                'url' : app.options.fitness_frontend_url + '&view=recipe_database&task=uploadVideo&format=text',
                'video' : filename,
                'default_video_image' : app.options.default_video_image,
                'upload_folder' : app.options.video_upload_folder,
                'preview_height' : '180px',
                'preview_width' : '250px',
                'el' : this.$el.find("#video_upload_content"),
                'video_path' : app.options.video_path,
                'base_url' : app.options.base_url,
                'video_name' : this.model.get('id'),
                'readonly' : this.readonly

            };
            var video_upload = $.backbone_video_upload(video_upload_options); 
         
        }

    });
            
    return view;
});