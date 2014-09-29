define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'collections/ingredients/recipe_ingredients',
        'models/ingredients/recipe_ingredient',
        'views/exercise_library/select_filter',
        'views/ingredients/ingredients_container',
        'views/comments/index',
	'text!templates/recipe_database/backend/form.html',
        'jquery.backbone_image_upload',
        'jquery.backbone_video_upload'
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Recipe_ingredients_collection,
        Recipe_ingredient_model,
        Select_filter_fiew,
        Ingredients_container_view,
        Comments_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.edit_allowed = this.model.get('edit_allowed');
        },

        template:_.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            var template = _.template(this.template(data));
        
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectRecipeTypesFilter();
                self.connectRecipeVariationsFilter();

                self.connectImageUpload();
                self.connectVideoUpload();

                if(self.model.get('id')) {
                    self.connectIngredients();
                    self.connectComments();
                }
                
                app.controller.connectStatus(self.model, $(self.el));
                
                $(self.el).find("#instructions").cleditor({width:'100%', height:150, useCSS:true})[0];
                
                if(!self.edit_allowed) {
                    $(self.el).find("#recipe_name, #number_serves, #published").attr('disabled', 'disabled');
                    
                    var element = $(self.el).find("#instructions").cleditor()[0];
                    if(element) {
                        element.disable(true);
                    }
                }
            });
        },

        connectComments :function() {
            var comment_options = {
                'item_id' :  this.model.get('id'),
                'item_model' :  this.model,
                'sub_item_id' :  '0',
                'db_table' : 'fitness_nutrition_recipes_comments',
                'read_only' : !this.edit_allowed,
                'anable_comment_email' : true,
                'comment_method' : 'RecipeComment'
            }
            
            if(app.options.is_backend) {
                comment_options.read_only = false;
            }
            
            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find("#comments_wrapper").html(comments_html);
        },

        connectImageUpload : function() {
            var imagepath = '';
            if(this.model.get('id')) {
                imagepath = this.model.get('image');
            }
            var filename = '';
            if(typeof imagepath !== 'undefined') {
                var fileNameIndex = imagepath.lastIndexOf("/") + 1;
                filename = imagepath.substr(fileNameIndex);
            }



            var image_upload_options = {
                'url' : app.options.fitness_frontend_url + '&view=recipe_database&task=uploadImage&format=text',
                'picture' : filename,
                'default_image' : app.options.default_image,
                'upload_folder' : app.options.upload_folder,
                'preview_height' : '180px',
                'preview_width' : '200px',
                'el' : $('#image_upload_content'),
                'img_path' : app.options.img_path,
                'base_url' : app.options.base_url,
                'image_name' : this.model.get('id'),
                'readonly' : !this.edit_allowed,
            };

            var image_upload = $.backbone_image_upload(image_upload_options); 
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
                'el' : $('#video_upload_content'),
                'video_path' : app.options.video_path,
                'base_url' : app.options.base_url,
                'video_name' : this.model.get('id'),
                'readonly' : !this.edit_allowed,
            };

            var video_upload = $.backbone_video_upload(video_upload_options); 
        },
        
        
        connectRecipeTypesFilter : function() {
            if(app.collections.recipe_types) {
                this.loadRecipeTypesSelect(app.collections.recipe_types );
                return;
            }
            var self = this;
            app.collections.recipe_types = new Recipe_types_collection();
            app.collections.recipe_types.fetch({
                success : function (collection, response) {
                    self.loadRecipeTypesSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadRecipeTypesSelect : function(collection) {
            var element_disabled = '';
            if(!this.edit_allowed ) {
                element_disabled = 'disabled';
            }
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_type_wrapper"),
                collection : collection,
                title : 'RECIPE TYPE',
                first_option_title : '-select-',
                class_name : '',
                id_name : 'recipe_type',
                select_size : 15,
                model_field : 'recipe_type',
                element_disabled : element_disabled
            }).render();  
        },
        
        connectRecipeVariationsFilter : function() {
            if(app.collections.recipe_variations) {
                this.loadRecipeVariationsSelect(app.collections.recipe_variations );
                return;
            }
            var self = this;
            app.collections.recipe_variations = new Recipe_variations_collection();
            app.collections.recipe_variations.fetch({
                success : function (collection, response) {
                    self.loadRecipeVariationsSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadRecipeVariationsSelect : function(collection) {
            var element_disabled = '';
            if(!this.edit_allowed ) {
                element_disabled = 'disabled';
            }
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_variation_wrapper"),
                collection : collection,
                title : 'RECIPE VARIATION',
                first_option_title : '-select-',
                class_name : '',
                id_name : 'recipe_variation',
                select_size : 15,
                model_field : 'recipe_variation',
                element_disabled : element_disabled
            }).render(); 
        },
        
        connectIngredients : function() {
            new Ingredients_container_view({
                el : $(this.el).find("#item_descriptions"),
                model : this.model,
                collection : new Recipe_ingredients_collection(),
                recipe_ingredients_collection : Recipe_ingredients_collection,
                request_data : {recipe_id : this.model.get('id')},
                edit_mode : true,
                ingredient_model : Recipe_ingredient_model,
                ingredient_model_data : {
                    recipe_id : this.model.get('id')
                }
            });
         },
        
    });
            
    return view;
});