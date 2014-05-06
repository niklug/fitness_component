/* html5 video upload backbone class
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
    
    function BackboneVideoUpload(options) {
    
        var Video_upload_model = Backbone.Model.extend({

            defaults : options
        });


        var Video_upload_view = Backbone.View.extend({

            template: _.template($('#video_upload_template').html()),

            initialize: function() {
                _.bindAll(this, 'render', 'drop_video', 'change_video', 'file_validation', 'save_video', 'clear_video', 'connectPlayer');
                this.video_name  = this.model.get('video_name');
                this.readonly  = this.model.get('readonly') || false;
                this.model.set({readonly : this.readonly}); 
                this.render();
            },
            
            connectPlayer : function() {
                this.loadVideoPlayer(this.model.get("base_url") + this.model.get("video_path") + '/' + this.model.get('video'),  180, 250, 'preview_video');
            },

            render: function() {
                $(this.el).html(this.template(this.model.toJSON()));
                
                if(this.model.get('video')) {
                    this.onRender(this.connectPlayer);
                }
            },
            
            onRender : function(func) {
                $(this.el).show('0', function() {
                    func();
                });
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
                
                if(this.readonly) return;

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
                   
                this.filetype = filename.split('.').pop();
                
                var self = this;
                reader.onloadend = function () {
                    $('#preview_video').css('background-image', 'none');
                    $('#preview_video').html('<div style="margin-top:80px;">' + filename + '</div>');
                    $('#video_container').attr('data-videopath', model.get('video_path') + '/' + self.video_name + '.' + self.filetype );
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
                
                if(this.readonly) return;

                var file = event.target.files[0];

                this.videoFile = file;
                
                if(!this.file_validation(this.videoFile)) return false;
                
                // Read the video file from the local file system 
                // and display it in the img tag.
                var reader = new FileReader();
                var model = this.model;
                            
                var self = this;
                reader.onloadend = function() {
                    $('#preview_video').css('background-image', 'none');
                    $('#preview_video').html('<div style="margin-top:80px;">' +  file.name+ '</div>');
                    $('#video_container').attr('data-videopath', model.get('video_path') + '/' + file.name);
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
                if(this.readonly) return;
                
                var self = this;
                if (this.videoFile) {

                    // append  into FormData object 
                    var fileData = new FormData();
                    fileData.append('file', this.videoFile);
                    
                    var filename = this.videoFile.name;
                    
                    this.filetype = filename.split('.').pop();
                    
                    this.model.set("video", this.video_name + '.' + this.filetype);
                    
                    var url = this.model.get("url");
                    
                    var upload_folder = encodeURIComponent(this.model.get("upload_folder"));
          
                    var data = {
                        video_name : self.video_name,
                        upload_folder : upload_folder
                    };


                    var data_encoded = JSON.stringify(data);
                    
                    url = url + '&data_encoded=' + data_encoded;
                
                    
                    var ajax_load_html= '<div style="width:100%;text-align:center;margin-top:80px;margin-left: 28px;"><div class="ajax_loader"></div></div>';
                    
                    $('#preview_video').html(ajax_load_html);
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
            
            

            clear_video : function() {
                if(this.readonly) return;
                
                var url = this.model.get("url");
                    
                var upload_folder = encodeURIComponent(this.model.get("upload_folder"));

                var filename = this.model.get("video");
                
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
                    
                this.videoFile = null;
                this.model.set({"video" : ''});
                $('#preview_video').css('background-image', 'url(' +  this.model.get("default_video_image") + ')');
                $('#video_container').attr('data-videopath', '');
                $('#preview_video').html('');
                
                this.render();
            },
            
            onSave : function() {
                console.log(this.videoFile.name + ' uploaded successfully !' );
                var videopath = this.model.get('video_path') + '/' + this.video_name + '.' + this.filetype;
                
                $('#video_container').attr('data-videopath', videopath);
                
                this.model.set({video : this.video_name + '.' + this.filetype});
                
                this.render();
            },
            
            loadVideoPlayer : function(video_path, height, width, container) {
                    
                var no_video_image_big = this.model.get("default_video_image");

                var imageType = /no_video_image.*/; 

                var image = video_path.split('.')[0] + '.jpg';

                if (video_path && !video_path.match(imageType) && video_path) {  

                    jwplayer(container).setup({
                        file: video_path,
                        image:  image,
                        height: height,
                        width: width
                   });
                } else {
                    $("#" + container).css('background-image', 'url(' +  no_video_image_big + ')');
                }
            },

         });

        return new Video_upload_view({el : options.el, model: new Video_upload_model()});
    }
    
    $.backbone_video_upload = function(options) {

        var constr =  BackboneVideoUpload(options);

        return constr;
    };

}));

