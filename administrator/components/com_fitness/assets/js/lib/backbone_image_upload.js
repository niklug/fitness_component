/* html5 image upload backbone class
 * 
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    
    function BackboneImageUpload(options) {
    
        var Image_upload_model = Backbone.Model.extend({

            defaults : options
        });


        var Image_upload_view = Backbone.View.extend({

            template: _.template($('#image_upload_template').html()),

            initialize: function() {
                _.bindAll(this, 'render', 'drop_image', 'change_image', 'file_validation', 'save_image', 'clear_image');
                this.image_name  = this.model.get('image_name');
                
                this.readonly  = this.model.get('readonly') || false;
                this.model.set({readonly : this.readonly}); 
         
                this.render();
            },

            render: function(eventName) {
                $(this.el).html(this.template(this.model.toJSON()));
                
                this.preview_image = '.preview_image';
                
                //this.preview_image = $(self.el).find($(self.preview_image));
                
                return this;
            },

            events : {
                "click .save": "save_image", 

                "drop .preview_image" : "drop_image",

                "change #change_image": "change_image", 

                "click .clear_image" : "clear_image",

                "dragover .preview_image" : function(e) {
                    e.preventDefault();
                }

            },

            drop_image : function (event) {
                event.stopPropagation();
                event.preventDefault();
                
                if(this.readonly) return;

                var e = event.originalEvent;
                // The DataTransfer object holding the data.
                e.dataTransfer.dropEffect = 'copy';
                this.pictureFile = e.dataTransfer.files[0];
                
                if(!this.file_validation(this.pictureFile)) return false;

                // Read the image file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                var model = this.model;
                var filename = this.pictureFile.name;
                
                this.filetype = filename.split('.').pop();
                
                var self = this;
                reader.onloadend = function () {
                    $(self.el).find($(self.preview_image)).css('background-image',"url(" + reader.result +")");
                    $(self.el).find($(self.preview_image)).attr('data-imagepath', model.get('img_path') + '/' + self.image_name + '.' + self.filetype);
                };
                reader.readAsDataURL(this.pictureFile);
                return false;
            },

            change_image: function(event) {
                
                if(this.readonly) return;
                
                // Prevents the event from bubbling up the DOM tree.
                event.stopPropagation();
                // To prevent the browser default handling of the data: 
                // default is open as link on drop.
                event.preventDefault();

                var file = event.target.files[0];
                
                if(!this.file_validation(file)) return false;
                
                this.pictureFile = file;
                // Read the image file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                var model = this.model;
   
                
                var self = this;
                reader.onloadend = function() {
                    $(self.el).find($(self.preview_image)).css('background-image',"url(" + reader.result +")");
                    $(self.el).find($(self.preview_image)).attr('data-imagepath', model.get('img_path') + '/' + file.name);
                };
                reader.readAsDataURL(this.pictureFile);
             
                return false;
            },
            
            file_validation : function(file) {
                var imageType = /image.*/;  
  		// check file type
		if (!file.type.match(imageType)) {  
		  alert("File \""+file.name+"\" is not a valid image file.");
		  return false;	
		} 
		// check file size
		if (parseInt(file.size / 1024) > 1024) {  
		  alert("File \""+file.name+"\" is too big.");
		  return false;	
		} 
                
                return true;
            },

            save_image : function () {
                if(this.readonly) return;
                
                var self = this;
                if (this.pictureFile) {
                    // append photo into FormData object 
                    var fileData = new FormData();
                    fileData.append('file', this.pictureFile);
                    
                    var filename = this.pictureFile.name;
                    
                    this.filetype = filename.split('.').pop();
                    
                    this.model.set("picture", this.image_name + '.' + this.filetype);
                    
                    var url = this.model.get("url");
                    
                    var upload_folder = encodeURIComponent(this.model.get("upload_folder"));
                    
                    var data = {
                        image_name : self.image_name,
                        upload_folder : upload_folder
                    };


                    var data_encoded = JSON.stringify(data);
                    
                    url = url + '&data_encoded=' + data_encoded;
                    
                    
                    var ajax_load_html= '<div style="width:100%;text-align:center;margin-top:80px;margin-left: 28px;"><div class="ajax_loader"></div></div>';
                    
                    $(this.el).find($(this.preview_image)).html(ajax_load_html);
                    
                    // upload FormData object by XMLHttpRequest
                    $.ajax({
                            url:  url,
                            type: 'POST',
                            data: fileData,
                            processData: false,
                            cache: false,
                            contentType: false
                    })
                    .done(function () {
                            self.onSave();
 
                    })
                    .fail(function (response) {
                            alert(response.responseText)
                            return false;
                    });
                };
            },
            
            onSave : function() {
                console.log(this.pictureFile.name + ' uploaded successfully !' );
                $(this.el).find($(this.preview_image)).html('');
                $(this.el).find($(this.preview_image)).attr('data-imagepath', this.model.get('img_path') + '/' + this.image_name + '.' + this.filetype);
                $(this.el).find($(this.preview_image)).css('background-image',"url(" + this.model.get('base_url') + this.model.get('img_path') + '/' + this.image_name + '.' + this.filetype +")");
                this.render();
            },

            clear_image : function() {
                if(this.readonly) return;
                
                var url = this.model.get("url");
                    
                var upload_folder = encodeURIComponent(this.model.get("upload_folder"));
                
                var filename = this.model.get("picture");
                
                var data = {
                    method : 'clear',
                    format : 'text',
                    filename : filename,
                    upload_folder : upload_folder
                };

                var data_encoded = JSON.stringify(data);
                
                // upload FormData object by XMLHttpRequest
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                        data_encoded : data_encoded
                    },
                    dataType : 'text',
                    success : function(response) {
                        console.log(response);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert(textStatus);
                    }
                }); 
                    
                this.pictureFile = null;
                this.model.set({"picture" : ''});
                $(this.el).find($(this.preview_image)).css('background-image',"url(" + this.model.get("default_image") +")");
                $(this.el).find($(this.preview_image)).attr('data-imagepath', '');
                
                this.render();
            }

         });

        return new Image_upload_view({el : options.el, model: new Image_upload_model()});
    }
    
    $.backbone_image_upload = function(options) {

        var constr =  BackboneImageUpload(options);

        return constr;
    };

}));

