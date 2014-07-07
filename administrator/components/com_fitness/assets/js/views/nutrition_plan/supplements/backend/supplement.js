define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/supplements/remote_images',
        'collections/nutrition_plan/supplements/remote_images_filtered',
        'views/nutrition_plan/supplements/backend/thumbnail_slide',
        'views/nutrition_plan/supplements/backend/ingredients_search_results',
	'text!templates/nutrition_plan/supplements/backend/supplement.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Remote_images_collection,
        Remote_images_filtered_collection,
        Thumbnail_slide_view,
        Ingredients_search_results_view,
        template
     ) {

     var view = Backbone.View.extend({
         
        template:_.template(template),
         
        initialize: function(){
            _.bindAll(this, 'onClickSaveSupplement', 'onInputSupplementName', 'onInputSupplementUrl', 'onGetRemoteImages', 'connectThumbnailSlide');
            this.remote_images_collection = new Remote_images_collection();
            this.remote_images_filtered_collection = new Remote_images_filtered_collection();
        },

        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);

            this.connectThumbnailSlide();

            return this;
        },

        connectThumbnailSlide : function() {
            app.views.thumbnail_slide = new  Thumbnail_slide_view({collection : this.remote_images_filtered_collection});
            this.$el.find(".shop_url_slider").append(app.views.thumbnail_slide.render().el);
        },

        events: {
            "submit form" : "onClickSaveSupplement",
            "click .delete_supplement" : "onClickDeleteSupplement",
            "input .supplement_name" : "onInputSupplementName",
            "focusout .supplement_url" : "onInputSupplementUrl"
        },

        onClickSaveSupplement : function(event) {
            event.preventDefault();
            var data = Backbone.Syphon.serialize(this);
            
            data.nutrition_plan_id = this.options.nutrition_plan_id;

            this.model.set(data);

            //validation
            var supplement_name_field = this.$el.find('.supplement_name');
            supplement_name_field.removeClass("red_style_border");
            var supplement_url_field = this.$el.find('.supplement_url');
            supplement_url_field.removeClass("red_style_border");
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'name') {
                    supplement_name_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'url') {
                    supplement_url_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }

            }

            var self = this;
            if (this.model.isNew()) {
                this.collection.create(this.model, {
                    wait: true,
                    success: function (model, response) {
                        self.close();
                        //console.log(self.collection);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
            } else {
                this.model.save(null, {
                    success: function (model, response) {
                        //console.log(self.collection);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }

        },

        onClickDeleteSupplement : function(event) {
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

        onInputSupplementName : function(event) {
            var o = $(event.target);
            var typingTimer;
            var search_text = o.val();

            var id = this.model.get('id');

            $(".select_meal_form").parent().remove();
            app.views.ingredients_search_results = new Ingredients_search_results_view({model : this.model, 'parent_view_el' : this.$el});
            app.views.ingredients_search_results.render().el;

            o.parent().append(app.views.ingredients_search_results.render().el);


            clearTimeout(typingTimer);
            var self = this;
            if (search_text) {
                typingTimer = setTimeout(
                    function() {
                        self.getSearchIngredients(
                            search_text,
                            function(output) {
                                app.views.ingredients_search_results.$el.find(".results_count").html('Search returned ' + output.count + ' results.');
                                app.views.ingredients_search_results.$el.find(".supplement_name_results").html(output.html);
                                app.views.ingredients_search_results.$el.find(".supplement_name_results").find(":odd").css("background-color", "#F0F0EE")
                            })
                    },
                    1000
                );
            }

        },

        getSearchIngredients : function(search_text, handleData) {
            var url = app.options.ajax_call_url;
            $.ajax({
                type : "POST",
                url : url,
                data : {
                    view : 'nutrition_recipe',
                    format : 'text',
                    task : 'getSearchIngredients',
                    search_text : search_text
                  },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.message);
                        return;
                    }
                    handleData(response);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error getSearchIngredients");
                }
            });
        },

        onInputSupplementUrl : function(event) {
            this.remote_images_filtered_collection.reset();

            var o = $(event.target);
            var typingTimer;
            var url = o.val();

            var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
            if (!url || !regexp.test(url) ) {
              return;
            }
            var self = this;
            this.remote_images_collection.fetch({
                data: {url : url},
                wait : true,
                success : function(collection, response) {
                    self.onGetRemoteImages(response);
                },
                error: function (model, response) {
                   alert(response.responseText);
                }
            });
        },

        onGetRemoteImages : function(image_urls) {
            var images = [];
            var self = this;
            _.each(image_urls, function(image_url) {
                var img = new Image();
                img.src = image_url;

                img.onload = function() {
                    if(
                        img.width > 100 &&
                        img.height > 100 &&
                        img.width < 400 &&
                        img.height < 400
                      ) {
                        images.push(img);
                        self.remote_images_filtered_collection.add([{src : img.src}])
                    }
                };
            });
        },

        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },
    });

    return view;
});