define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/recipe_database_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(data){
            var data = data;
            //console.log(data);
            data.model = this.model;
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #pdf_button_recipe" : "onClickPdf",
            "click #email_button_recipe" : "onClickEmail",
        },

        onClickPdf : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            var user_id = this.model.get('user_id');
            var htmlPage = window.fitness_helper.get('base_url') + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_recipe&id=' + recipe_id + '&client_id=' + user_id;
            window.fitness_helper.printPage(htmlPage);
        },

        onClickEmail : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            var data = {};
            data.url = this.model.get('fitness_frontend_url');
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id = recipe_id;
            data.view = 'NutritionPlan';
            data.method = 'email_pdf_recipe';
            window.fitness_helper.sendEmail(data);
        },

        loadComments : function(){
            var comments_html = this.options.comments.run();
            $("#comments_wrapper").html(comments_html);
        },

        loadVideoPlayer : function() {
            var recipe = this.model.get('recipe');

            var no_video_image_big = this.model.get('no_video_image_big');

            var video_path = recipe.video;

            var base_url = this.model.get('base_url');

            var imageType = /no_video_image.*/;  

            if (!video_path.match(imageType) && video_path) {  

                jwplayer("recipe_video").setup({
                    file: base_url + video_path,
                    image: "",
                    height: 340,
                    width: 640,
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
                $("#recipe_video").css('background-image', 'url(' +  no_video_image_big + ')');
            }
        }
    });
            
    return view;
});