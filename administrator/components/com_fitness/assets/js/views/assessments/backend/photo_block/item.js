define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/backend/photo_block/item.html',
        'jquery.backbone_image_upload'

], function ( $, _, Backbone, app,  template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        

        
        render: function(){
            var data = {data : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            this.connectImageUpload();
            
            return this;
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
                'image_name' : this.model.get('id')

            };
            
            console.log(image_upload_options);

            var image_upload = $.backbone_image_upload(image_upload_options); 
            image_upload.render();
        },

    });
            
    return view;
});