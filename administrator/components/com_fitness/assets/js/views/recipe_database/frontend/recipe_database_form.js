define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
	'text!templates/recipe_database/frontend/recipe_database_form.html',
        'jquery.itemDescription'
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
            app.collections.recipe_types = new Recipe_types_collection();
            app.collections.recipe_variations = new Recipe_variations_collection();
            var self = this;
            
            $.when(
                app.collections.recipe_types.fetch({
                    wait : true,
                    success: function (collection, response) {
                        //console.log(response);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })

            ,
                app.collections.recipe_variations.fetch({
                    wait : true,
                    success: function (collection, response) {
                        //console.log(response);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })

            ).then(function() {
                self.render();
            });
        },

        template:_.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            var template = _.template(this.template(data));
        
            this.$el.html(template);
            
            if(this.model.get('id')) {
                this.connect_item_description();
                this.connectComments();
            }
            /*
            this.connect_file_upload();
            this.connect_video_upload();
            if(data.recipe.id) {
                this.connect_item_description();
                this.connect_comments();
            }
            */
            
            return this;
        },
        
        connect_item_description : function() {
            var item_description_options = {
                'nutrition_plan_id' : this.model.get('id'),
                'fitness_administration_url' : app.options.fitness_frontend_url,
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
        
        connectComments : function() {
            var comment_options = {
                'item_id' : this.model.get('id'),
                'fitness_administration_url' : app.options.fitness_frontend_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : app.options.recipe_comments_db_table,
                'read_only' : true,
                'anable_comment_email' : false
            }
            var comments = $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            $("#comments_wrapper").html(comments_html);
        },


        /*
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
        */
        close :function() {
            $(this.el).remove();
        }
    });
            
    return view;
});