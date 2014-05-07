define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/assessments/assessment_photos',
	'text!templates/assessments/frontend/photo_block/item.html',
        'jquery.backbone_image_upload'

], function ( $, _, Backbone, app, Item_model, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        

        
        render: function(){
            var data = {data : this.model.toJSON()};
            
            this.readonly = this.options.readonly || false;
            
            data.readonly = this.readonly;
            
            var template = _.template(this.template(data));
            this.$el.html(template);

            this.connectImageUpload();
            
            $(this.el).find(".photo_trainer_comments").attr('readonly', true);
 
            if(this.readonly) {
                $(this.el).find("input, textarea").attr('readonly', true);
   
                this.setClientCommentsEdit(this.model);
            }
            
            return this;
        },
        
        events: {
            "click .save_photo" : "onClickSave",
            "click .close_photo" : "onClickClose",
        },
        
        setClientCommentsEdit : function(model) {
            if(!model.get('client_comments')) {
                $(this.el).find(".photo_client_comments").attr('readonly', false);
            } else {
                $(this.el).find(".photo_client_comments").attr('readonly', true);
            }
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
                'el' : $(this.el).find('.photo_item_wrapper'),
                'img_path' : app.options.img_path,
                'base_url' : app.options.base_url,
                'image_name' : this.model.get('id'),
                'readonly' : this.readonly

            };

            var image_upload = $.backbone_image_upload(image_upload_options); 
         },
        
        onClickSave : function(event) {
            var id = $(event.target).attr('data-id');
            
            this.model = new Item_model({
                id : id,
                image : $(this.el).find(".preview_image").attr('data-imagepath'),
                description : $(this.el).find(".photo_description").val(),
                client_comments : $(this.el).find(".photo_client_comments").val(),
                trainer_comments : $(this.el).find(".photo_trainer_comments").val()
            });
            
            var self = this;
            this.model.save(null, {
                success : function(model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickClose : function(event) {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });
            
    return view;
});