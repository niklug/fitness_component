define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/recipe_database_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        el: $("#recipe_main_container"), 
        
        initialize : function() {
            _.bindAll(this, 'render', 'loadTemplate', 'connect_file_upload', 'connect_video_upload');

            this.recipe_id = parseInt(this.options.recipe_id);

            if(!this.recipe_id) {
                window.app.recipe_items_model.set({'recipe' : null});
                this.get_recipe_types();
            }

            window.app.recipe_items_model.set({'recipe' : null});
            this.listenToOnce(window.app.recipe_items_model, "change:recipe", this.get_recipe_types);
            window.app.recipe_items_model.getRecipe(this.recipe_id);
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render : function(){

            if (window.app.recipe_items_model.get('current_page') != 'edit_recipe') { 
                return false;
            }

            this.recipe = window.app.recipe_items_model.get('recipe');

            this.recipe_types = this.options.filter_categories_model.get('recipe_types');

            var self = this;
            window.app.recipe_variations_collection.fetch({
                success : function (collection, response) {
                    self.loadTemplate({
                       'recipe_types' : self.recipe_types,
                       'recipe_variations' : response,
                       'recipe' : self.recipe,
                       'recipe_items_model' : window.app.recipe_items_model,
                    });
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });

        },
        
        get_recipe_types : function() {

            this.recipe_types = this.options.filter_categories_model.get('recipe_types');

            if(this.recipe_types) this.render();

            this.listenToOnce(this.options.filter_categories_model, "change:recipe_types", this.render);
            this.options.filter_categories_model.getRecipeTypes();
        },



        loadTemplate : function(data) {
            var template = _.template($("#recipe_database_edit_recipe_container_template").html(), data);
            this.$el.html(template);

            this.connect_file_upload();
            this.connect_video_upload();
            if(data.recipe.id) {
                this.connect_item_description();
                this.connect_comments();
            }
        },

        connect_file_upload : function() {
            var imagepath = '';
            if(this.recipe) {
                imagepath = this.recipe.image;
            }
            var filename = '';
            if(typeof imagepath !== 'undefined') {
                var fileNameIndex = imagepath.lastIndexOf("/") + 1;
                filename = imagepath.substr(fileNameIndex);
            }



            var image_upload_options = {
                'url' : window.app.recipe_items_model.get('fitness_frontend_url') + '&view=recipe_database&task=uploadImage&format=text',
                'picture' : filename,
                'default_image' : window.app.recipe_items_model.get('default_image'),
                'upload_folder' : window.app.recipe_items_model.get('upload_folder'),
                'preview_height' : '180px',
                'preview_width' : '200px',
                'el' : $('#image_upload_content'),
                'img_path' : window.app.recipe_items_model.get('img_path'),
                'base_url' : window.app.recipe_items_model.get('base_url'),
                'image_name' : this.recipe_id

            };

            var image_upload = $.backbone_image_upload(image_upload_options); 
            image_upload.render();
        },

        connect_video_upload : function() {

            var videopath = '';
            if(this.recipe) {
                videopath = this.recipe.video;
            }
            var filename = '';
            if(typeof videopath !== 'undefined') {
                var fileNameIndex = videopath.lastIndexOf("/") + 1;
                filename = videopath.substr(fileNameIndex);
            }

            var video_upload_options = {
                'url' : window.app.recipe_items_model.get('fitness_frontend_url') + '&view=recipe_database&task=uploadVideo&format=text',
                'video' : filename,
                'default_video_image' : window.app.recipe_items_model.get('default_video_image'),
                'upload_folder' : window.app.recipe_items_model.get('video_upload_folder'),
                'preview_height' : '180px',
                'preview_width' : '250px',
                'el' : $('#video_upload_content'),
                'video_path' : window.app.recipe_items_model.get('video_path'),
                'base_url' : window.app.recipe_items_model.get('base_url'),
                'video_name' : this.recipe_id

            };

            var video_upload = $.backbone_video_upload(video_upload_options); 
            video_upload.render();
        },

        connect_item_description : function() {
            var item_description_options = {
                'nutrition_plan_id' : this.recipe.id,
                'fitness_administration_url' : window.app.recipe_items_model.get('fitness_frontend_url'),
                'main_wrapper' : $("#item_descriptions"),
                'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
                'db_table' : '#__fitness_nutrition_recipes_meals',
                'parent_view' : '',
                'read_only' : false,
                'ingredient_model' : 'recipe_database'
            }
            var item_description_html = $.itemDescription(item_description_options, 'meal', 'MEAL ITEM DESCRIPTION', 0).run();
            $("#item_descriptions").html(item_description_html);
        },

        connect_comments : function() {
            var comment_options = {
                'item_id' : this.recipe.id,
                'fitness_administration_url' : window.app.recipe_items_model.attributes.fitness_frontend_url,
                'comment_obj' : {'user_name' : window.app.recipe_items_model.attributes.user_name, 'created' : "", 'comment' : ""},
                'db_table' : window.app.recipe_items_model.attributes.recipe_comments_db_table,
                'read_only' : true,
                'anable_comment_email' : false
            }
            var comments = $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            $("#comments_wrapper").html(comments_html);
        },

        close :function() {
            $(this.el).unbind();
            //$(this.el).remove();
        }
    });
            
    return view;
});