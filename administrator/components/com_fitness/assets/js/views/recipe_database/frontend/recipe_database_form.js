define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'views/exercise_library/select_filter',
	'text!templates/recipe_database/frontend/recipe_database_form.html',
        'jquery.itemDescription',
        'jquery.backbone_image_upload',
        'jquery.backbone_video_upload'
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Select_filter_fiew,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
            this.render();
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
                self.connectImageUpload();
                self.connectVideoUpload();

                if(self.model.get('id')) {
                    self.connect_item_description();
                    self.connectComments();
                }
                
                self.connectEditor($(self.el), "#instructions", false);
                
                self.connectRecipeTypesFilter();
                self.connectRecipeVariationsFilter();
                
                self.controller.connectStatus(self.model, $(self.el));
            });
        },
        
        connectEditor : function(element, selector, disabled) {
            element.find(selector).cleditor({width:'98%', height:300, useCSS:true})[0];

            element.find("iframe").contents().find("body").css('color', '#fff');

            element.find(".cleditorMain").css('background-color', 'rgba(255, 255, 255, 0.1)');


            var element = element.find(selector).cleditor()[0];
            if(element) {
                element.disable(disabled);
            }
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
                'image_name' : this.model.get('id')

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
                'video_name' : this.model.get('id')

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
            
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_type_wrapper"),
                collection : collection,
                title : 'RECIPE TYPE',
                first_option_title : '-select-',
                class_name : ' dark_input_style ',
                id_name : 'recipe_type',
                select_size : 17,
                model_field : 'recipe_type',
                element_disabled : ''
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
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_variation_wrapper"),
                collection : collection,
                title : 'RECIPE VARIATION',
                first_option_title : '-select-',
                class_name : ' dark_input_style ',
                id_name : 'recipe_variation',
                select_size : 17,
                model_field : 'recipe_variation',
                element_disabled : ''
            }).render(); 
        },

        close :function() {
            $(this.el).remove();
        }
    });
            
    return view;
});