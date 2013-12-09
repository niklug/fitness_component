/* html5 video upload backbone class
 * 
 */
(function($) {
    
    function BackboneVideoUpload(options) {
    
        window.Video_upload_model = Backbone.Model.extend({

            defaults : options
        });


        window.Video_upload_view = Backbone.View.extend({

            template: _.template($('#video_upload_template').html()),

            initialize: function() {
                this.model.on("destroy", this.close, this);
            },

            render: function(eventName) {
                $(this.el).html(this.template(this.model.toJSON()));
                return this;
            },

            events : {
                "click .save": "save_video", 

                "drop #preview_video" : "drop_video",

                "change #change_video": "change_video", 

                "click .clear_video" : "clear_video",

                "dragover #preview_video" : function(e) {
                    e.preventDefault();
                }

            },

            drop_video : function (event) {
                event.stopPropagation();
                event.preventDefault();

                var e = event.originalEvent;
                // The DataTransfer object holding the data.
                e.dataTransfer.dropEffect = 'copy';
                this.videoFile = e.dataTransfer.files[0];
                
                if(!this.file_validation(this.videoFile)) return false;

                // Read the video file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                var model = this.model;
                var filename = this.videoFile.name;
                reader.onloadend = function () {
                    $('#preview_video').css('background-image', 'none');
                    $('#preview_video').html('<div style="margin-top:80px;">' + filename + '</div>');
                    $('#preview_video').attr('data-videopath', model.get('video_path') + '/' + filename);
                };
                reader.readAsDataURL(this.videoFile);
                return false;
            },

            change_video: function(event) {
                // Prevents the event from bubbling up the DOM tree.
                event.stopPropagation();
                // To prevent the browser default handling of the data: 
                // default is open as link on drop.
                event.preventDefault();

                var file = event.target.files[0];

                this.videoFile = file;
                
                if(!this.file_validation(this.videoFile)) return false;
                
                // Read the video file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                var model = this.model;
                reader.onloadend = function() {
                    $('#preview_video').css('background-image', 'none');
                    $('#preview_video').html('<div style="margin-top:80px;">' + file.name + '</div>');
                    $('#preview_video').attr('data-videopath', model.get('video_path') + '/' + file.name);
                };
                reader.readAsDataURL(this.videoFile);
             
                return false;
            },
            
            file_validation : function(file) {
                var videoType = /video.*/;  
  		// check file type
		if (!file.type.match(videoType)) {  
		  alert("File \""+file.name+"\" is not a valid video file.");
		  return false;	
		} 
 
		// check file size
		if (parseInt(file.size / 1024) > 10024) {  
		  alert("File \""+file.name+"\" is too big.");
		  return false;	
		} 
                
                return true;
            },

            save_video : function () {
                var self = this;
                if (this.videoFile) {
                    //console.log( this.videoFile.name);
                    this.model.set("video", this.videoFile.name);

                    // append photo into FormData object 
                    var fileData = new FormData();
                    fileData.append('file', this.videoFile);
                    
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
                            console.log(self.videoFile.name + ' uploaded successfully !' );
                            $('#preview_video').attr('data-videopath', self.model.get('video_path') + '/' + self.videoFile.name);
                    })
                    .fail(function (response) {
                            alert(response.responseText)
                            return false;
                    });
                };
            },

            clear_video : function() {
                var url = this.model.get("url");
                    
                var upload_folder = this.model.get("upload_folder");

                url = url +'&upload_folder=' + upload_folder;

                var filename = this.model.get("video");
                
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
                    
                this.videoFile = null;
                this.model.set({"video" : ''});
                $('#preview_video').css('background-image', 'url(' +  this.model.get("default_video_image") + ')');
                $('#preview_video').attr('data-videopath', '');
                $('#preview_video').html('');
            }

         });

        return new Video_upload_view({el : options.el, model: new Video_upload_model()});
    }
    
    $.backbone_video_upload = function(options) {

        var constr =  BackboneVideoUpload(options);

        return constr;
    };

})($js);

