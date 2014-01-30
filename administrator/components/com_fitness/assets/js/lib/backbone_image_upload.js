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
    
        window.Image_upload_model = Backbone.Model.extend({

            defaults : options
        });


        window.Image_upload_view = Backbone.View.extend({

            template: _.template($('#image_upload_template').html()),

            initialize: function() {
                _.bindAll(this, 'render', 'drop_image', 'change_image', 'file_validation', 'save_image', 'clear_image');
                this.model.on("destroy", this.close, this);
                this.image_name  = this.model.get('image_name');
            },

            render: function(eventName) {
                $(this.el).html(this.template(this.model.toJSON()));
                return this;
            },

            events : {
                "click .save": "save_image", 

                "drop #preview_image" : "drop_image",

                "change #change_image": "change_image", 

                "click .clear_image" : "clear_image",

                "dragover #preview_image" : function(e) {
                    e.preventDefault();
                }

            },

            drop_image : function (event) {
                event.stopPropagation();
                event.preventDefault();

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
                    $('#preview_image').css('background-image',"url(" + reader.result +")");
                    $('#preview_image').attr('data-imagepath', model.get('img_path') + '/' + self.image_name + '.' + self.filetype);
                };
                reader.readAsDataURL(this.pictureFile);
                return false;
            },

            change_image: function(event) {
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
                    $('#preview_image').css('background-image',"url(" + reader.result +")");
                    $('#preview_image').attr('data-imagepath', model.get('img_path') + '/' + file.name);
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
                var self = this;
                if (this.pictureFile) {
                    // append photo into FormData object 
                    var fileData = new FormData();
                    fileData.append('file', this.pictureFile);
                    
                    var filename = this.pictureFile.name;
                    
                    this.filetype = filename.split('.').pop();
                    
                    this.model.set("picture", this.image_name + '.' + this.filetype);
                    
                    var url = this.model.get("url");
                    
                    var upload_folder = this.model.get("upload_folder");
                    
                    url = url +'&upload_folder=' + upload_folder +'&image_name=' + self.image_name;
                    
                    var ajax_load_html= '<div style="width:100%;text-align:center;margin-top:80px;margin-left: 28px;"><div class="ajax_loader"></div></div>';
                    
                    $('#preview_image').html(ajax_load_html);
                    
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
                            console.log(self.pictureFile.name + ' uploaded successfully !' );
                            $('#preview_image').html('');
                            $('#preview_image').attr('data-imagepath', self.model.get('img_path') + '/' + self.image_name + '.' + self.filetype);
                            $('#preview_image').css('background-image',"url(" + self.model.get('base_url') + self.model.get('img_path') + '/' + self.image_name + '.' + self.filetype +")");
                    })
                    .fail(function (response) {
                            alert(response.responseText)
                            return false;
                    });
                };
            },

            clear_image : function() {
                var url = this.model.get("url");
                    
                var upload_folder = this.model.get("upload_folder");

                url = url +'&upload_folder=' + upload_folder;

                var filename = this.model.get("picture");
                
                // upload FormData object by XMLHttpRequest
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                        view : '',
                        method : 'clear',
                        format : 'text',
                        filename : filename
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
                $('#preview_image').css('background-image',"url(" + this.model.get("default_image") +")");
                $('#preview_image').attr('data-imagepath', '');
            }

         });

        return new Image_upload_view({el : options.el, model: new Image_upload_model()});
    }
    
    $.backbone_image_upload = function(options) {

        var constr =  BackboneImageUpload(options);

        return constr;
    };

}));

