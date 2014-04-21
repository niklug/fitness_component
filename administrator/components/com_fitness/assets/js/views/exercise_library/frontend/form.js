define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.connectStatus();
            
            var id = this.model.get('id');
            if(id) {
                this.connectComments();
                
                this.connectVideoUpload();
            }
            return this;
        },
        
        connectStatus : function() {
            var status = $.status(app.options.status_options);
        },
        
        connectComments : function() {
            var comment_options = {
                'item_id' : this.model.get('id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_exercise_library_comments',
                'read_only' : true,
                'anable_comment_email' : true,
                'comment_method' : 'ExerciseLibraryComment'
            }
            var comments = $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            $(this.el).find("#comments_wrapper").html(comments_html);
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
                'video_name' : this.model.get('id')

            };
            var video_upload = $.backbone_video_upload(video_upload_options); 
        }

    });
            
    return view;
});