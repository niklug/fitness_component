/* html5 image upload backbone class
 * 
 */
(function($) {
    
    function BackboneImageUpload(options) {
        
        //alert('test');
        console.log(options);
    
        window.Image_upload_model = Backbone.Model.extend({

            defaults : options
        });


        window.Image_upload_view = Backbone.View.extend({

            template: _.template($('#image_upload_template').html()),

            initialize: function() {
                this.model.on("destroy", this.close, this);
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

                // Read the image file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                reader.onloadend = function () {
                    $('#preview_image').attr('src', reader.result);
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
                
                var imageType = /image.*/;  
  		// check file type
		if (!file.type.match(imageType)) {  
		  alert("File \""+file.name+"\" is not a valid image file.");
		  return false;	
		} 
		// check file size
		if (parseInt(file.size / 1024) > 524) {  
		  alert("File \""+file.name+"\" is too big.");
		  return false;	
		} 
                
                this.pictureFile = file;
                // Read the image file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                reader.onloadend = function() {
                    $('#preview_image').attr('src', reader.result);
                };
                reader.readAsDataURL(this.pictureFile);
             
                return false;
            },

            save_image : function () {
                var self = this;
                if (this.pictureFile) {
                    //console.log( this.pictureFile.name);
                    this.model.set("picture", this.pictureFile.name);

                    // append photo into FormData object 
                    var fileData = new FormData();
                    fileData.append('file', this.pictureFile);
                    
                    var url = this.model.get("url");
                    
                    var upload_folder = this.model.get("upload_folder");
                    
                    url = url +'&upload_folder=' + upload_folder;


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
                $('#preview_image').attr('src', this.model.get("default_image"));
            }

         });

        return new Image_upload_view({el : options.el, model: new Image_upload_model()});
    }
    
    $.backbone_image_upload = function(options) {

        var constr =  BackboneImageUpload(options);

        return constr;
    };

})($js);

