define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/supplements/backend/thumbnail_slide.html'
], function ( $, _, Backbone, app, template ) {

     var view = Backbone.View.extend({

        template:_.template(template),

        initialize: function(){
            this.collection.on('add', this.addImage, this);
            this.collection.on('reset', this.resetImages, this);
        },

        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            this.wrapper = this.$el.find(".images_wrapper");
            return this;
        },

        events:{
            "click .button_prev": "onClickPrev",
            "click .button_next": "onClickNext",
        },

        addImage : function(model) {
            var image_url = model.get('src');
            var background_image = 'url(' + "'" + image_url  + "')" ;
            this.wrapper.append('<div class="thumbnail_slide_image" style="background-image:' + background_image + '"></div>');

            this.connectSliding();
        },

        resetImages : function() {
            this.wrapper.html('');
            this.$el.find('.slider_nav').hide();
            this.$el.parent().find('.thumbnail_slide_image_saved').remove();
        },

        connectSliding : function () {
            this.$el.parent().find('.thumbnail_slide_image_saved').remove();

            this.wrapper.children().first().addClass('active');
            this.wrapper.children().hide();    
            this.wrapper.find('.active').show();

            var items_number = this.collection.length;

            this.$el.find('.slide_images_total').text(items_number);
            if(items_number >1) {
                this.$el.find('.slider_nav').show();
            }
            this.$el.parent().find('.supplement_image').val(this.collection.first().get('src'));

        },

        onClickPrev : function() {
            this.$el.find('.active').removeClass('active').addClass('oldActive');    
            if (this.$el.find('.oldActive').is(':first-child')) {
                this.$el.find('.thumbnail_slide_image').last().addClass('active');
            } else {
                this.$el.find('.oldActive').prev().addClass('active');
            }
            this.$el.find('.oldActive').removeClass('oldActive');
            this.$el.find('.thumbnail_slide_image').hide();
            this.$el.find('.active').show();

            var current_image_index = this.$el.find('.active').index();

            this.$el.parent().find('.supplement_image').val(this.collection.models[current_image_index].get('src'));

            this.$el.find('.slide_current_image').text(current_image_index + 1);  

        },

        onClickNext : function() {
            this.$el.find('.active').removeClass('active').addClass('oldActive');   

            if (this.$el.find('.oldActive').is(':last-child')) {
                this.$el.find('.thumbnail_slide_image').first().addClass('active');
            } else {
                this.$el.find('.oldActive').next().addClass('active');
            }
            this.$el.find('.oldActive').removeClass('oldActive');
            this.$el.find('.thumbnail_slide_image').hide();
            this.$el.find('.active').show();

            var current_image_index = this.$el.find('.active').index();

            this.$el.parent().find('.supplement_image').val(this.collection.models[current_image_index].get('src'));

            this.$el.find('.slide_current_image').text(current_image_index + 1);        

        },

        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },
    });
            
    return view;
});