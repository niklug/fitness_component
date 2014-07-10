define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/assessment_photos',
	'text!templates/client_progress/frontend/data_item.html',
        'text!templates/client_progress/frontend/data_item_bio.html'

], function (
        $,
        _,
        Backbone,
        app,
        Assessment_photos_collection,
        template,
        template_bio
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
        },
        
        template : _.template(template),
        
        template_bio : _.template(template_bio),
        
        render: function(){
            var session_focus = this.model.get('session_focus_name');
            
            if(this.is_bio_assessment(session_focus)) {
                this.template = this.template_bio;
            }
            
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.priorities = JSON.parse(app.options.assessment_priorities);
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
           
            return this;
        },
        
        events : {
            "click .open_assessment" : "onClickOpen",
            "click .show_images" : "onClickShowImages",
            "click .hide_images" : "onClickHideImages",
        },
        
        is_bio_assessment : function(name) {
            var result = false;
            if(name && (name.toLowerCase().indexOf("bio") > -1)) {
                result = true;
            }
            return result;
        },
        
        onClickOpen : function() {
            var id = this.model.get('id');
            
            var url = app.options.base_url + 'administrator/index.php?option=com_fitness&view=assessments#!/form_view/' + id;
            
            if(!app.options.is_backend) {
                url = app.options.base_url + 'index.php?option=com_fitness&view=assessments#!/item_view/' + id;
            }
            
            window.open(url, '_blank');
        },
        
        onClickShowImages : function() {
            $(this.el).find(".show_images").hide();
            $(this.el).find(".hide_images").show();
            
            if(typeof this.assessment_photos_collection !== "undefined") {
                this.populatePhotos(this.assessment_photos_collection);
                return;
            }
            
            this.assessment_photos_collection = new Assessment_photos_collection()
            
            var self = this;
            
            this.assessment_photos_collection.fetch({
                data : {item_id : this.model.get('id'), db_table : app.options.db_table_photos},
                success : function (collection, response) {
                    self.populatePhotos(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        populatePhotos : function(collection) {
            var container = $(this.el).find(".photos_wraper");
            
            container.empty();
            
            var self = this;
            _.each(collection.models, function(model) {
                self.addPhoto(model, container);
            });
        },
        
        addPhoto : function(model, container) {
            var image = model.get('image');
            
            var path = app.options.base_url + image;
            
            var html = '<div  style="display:inline-block;background-image: url(' + path + '); margin: 5px;" class="recipe_database_image preview_image"> </div>'
            
            container.append(html);
            console.log(html);
        },
        
        onClickHideImages : function() {
            $(this.el).find(".show_images").show();
            $(this.el).find(".hide_images").hide();
            $(this.el).find(".photos_wraper").empty();
        }
        
  
    });
            
    return view;
});