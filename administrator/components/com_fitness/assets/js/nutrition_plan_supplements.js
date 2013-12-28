/*
 * 
 */
(function($, Backbone) {
    function NutritionPlanSupplements() {
        
        window.app = window.app || {};
        Backbone.emulateHTTP = true ;
        Backbone.emulateJSON = true;
        
        
        window.app.Protocols_wrapper = Backbone.View.extend({

            template : _.template($('#nutrition_plan_protocol_template').html()),

            initialize:function () {
                this.render();
            },

            render:function (eventName) {
                $(this.el).html(this.template());
                return this;
            },

            events:{
                "click #add_protocol": "onAddProtocol"
            },

            onAddProtocol:function () {
                window.app.supplements_controller.navigate("!/supplements");
                window.app.supplements_controller.navigate("!/add_supplement_protocol", true);
            }

        });
        
        
        
        window.app.Nutrition_plan_protocols_view = Backbone.View.extend({
            
            initialize: function(){
                
                var self = this;
                
		this.protocolListItemViews = {};

		this.collection.on("add", function(protocol) {
                    var comment_options = {
                        'item_id' : window.app.protocol_options.nutrition_plan_id,
                        'fitness_administration_url' : window.app.protocol_options.fitness_backend_url,
                        'comment_obj' : {'user_name' : window.app.protocol_options.user_name, 'created' : "", 'comment' : ""},
                        'db_table' : window.app.protocol_options.protocol_comments_db_table,
                        'read_only' : false,
                    }
                    var comments = $.comments(comment_options, comment_options.item_id, protocol.id);

                    window.app.nutrition_plan_protocol_view = new window.app.Nutrition_plan_protocol_view({collection : this,  model : protocol, 'comments' : comments}); 
                    $(self.el).append( window.app.nutrition_plan_protocol_view.render().el );
                    self.protocolListItemViews[ protocol.cid ] = window.app.nutrition_plan_protocol_view;
		});
		
		this.collection.on("remove", function(protocol, options) {
                    self.protocolListItemViews[ protocol.cid ].close();
                    delete self.protocolListItemViews[ protocol.cid ];
		});
            },
            
            render : function () {
                
                _.each(this.collection.models, function (protocol) { 
                    $(this.el).append(new window.app.Nutrition_plan_protocol_view({model : protocol }).render().el); 
                    
                }, this);

                return this;
            },
                    
            events: {
                "click #add_protocol" : "onClickAddProtocol",
            },

        });
        
        
        window.app.Nutrition_plan_protocol_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this, 'onClickSaveProtocol','close', 'render');
                this.model.on("destroy", this.close, this);
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_protocol_item_template").html(), this.model.toJSON());
                this.$el.html(template);
                
                this.connectComments(this.$el);

                this.supplements_list_el = this.$el.find(".supplements_list");
                
                this.supplement_collection = new  window.app.Supplements_collection();
  
                this.protocol_id = this.model.get('id');
                
                if(this.protocol_id ){ 
  
                    this.supplement_collection.fetch({
                        data: {nutrition_plan_id : window.app.protocol_options.nutrition_plan_id, protocol_id : this.protocol_id},
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    });
                }
                
                
                var self = this;
                
		this.supplementListItemViews = {};

		this.supplement_collection.on("add", function(supplement) {
                    window.app.nutrition_plan_supplement_view = new window.app.Nutrition_plan_supplement_view({collection : self.supplement_collection, model : supplement}); 
                    self.supplements_list_el.append( window.app.nutrition_plan_supplement_view.render().el );
                    self.supplementListItemViews[ supplement.cid ] = window.app.nutrition_plan_supplement_view;
		});
		
		this.supplement_collection.on("remove", function(supplement, options) {
                    self.supplementListItemViews[ supplement.cid ].close();
                    delete self.supplementListItemViews[ supplement.cid ];
		});
                
                return this;
            },
            
            connectComments : function() {
                if(typeof this.options.comments !== 'undefined') {
                    var comments = this.options.comments.run();
                    this.$el.find(".comments_wrapper").html(comments);
                }
            },
                    
            events: {
                "click .save_protocol" : "onClickSaveProtocol",
                "click .delete_protocol" : "onClickDeleteProtocol",
                "click .add_supplement" : "onClickAddSupplement",
            },

            
            onClickSaveProtocol : function(event) {
                var protocol_name_field = this.$el.find('.protocol_name');
                var protocol_name = protocol_name_field.attr('value');
                
                protocol_name_field.removeClass("red_style_border");
               
                this.model.set({ name : protocol_name});
                
                if (!this.model.isValid()) {
                    protocol_name_field.addClass("red_style_border");
                    //console.log(this.model.validationError);
                    return false;
                }
                
                var self = this;
                if (this.model.isNew()) {
                    this.collection.create(this.model, {
                        wait: true,
                        success: function (model, response) {
                            self.close();
                            //console.log(model.toJSON());
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
             
             onClickDeleteProtocol : function(event) {
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
             

            onClickAddSupplement : function(event) {
                var nutrition_plan_supplement_view = new window.app.Nutrition_plan_supplement_view({collection : this.supplement_collection, model : new window.app.Supplement_model({protocol_id : this.model.get('id')}) }); 
                this.supplements_list_el.append(nutrition_plan_supplement_view.render().el );
            }
        });
        
        
        
        
        
        window.app.Nutrition_plan_supplement_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this, 'onClickSaveSupplement', 'onInputSupplementName', 'onInputSupplementUrl', 'onGetRemoteImages', 'connectThumbnailSlide');
                this.remote_images_collection = new window.app.Remote_images_collection();
                this.remote_images_filtered_collection = new window.app.Remote_images_filtered_collection();
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_supplement_template").html(), this.model.toJSON());
                this.$el.html(template);
                
                this.connectThumbnailSlide();
                
                return this;
            },
            
            connectThumbnailSlide : function() {
                this.thumbnail_slide_view = new  window.app.Thumbnail_slide_view({collection : this.remote_images_filtered_collection});
                this.$el.find(".shop_url_slider").append(this.thumbnail_slide_view.render().el);
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
                this.ingredients_search_results_view = new window.app.Ingredients_search_results_view({model : this.model, 'parent_view_el' : this.$el});
                this.ingredients_search_results_view.render().el;
                
                o.parent().append(this.ingredients_search_results_view.render().el);

                
                clearTimeout(typingTimer);
                var self = this;
                if (search_text) {
                    typingTimer = setTimeout(
                        function() {
                            self.getSearchIngredients(
                                search_text,
                                function(output) {
                                    self.ingredients_search_results_view.$el.find(".results_count").html('Search returned ' + output.count + ' results.');
                                    self.ingredients_search_results_view.$el.find(".supplement_name_results").html(output.html);
                                    self.ingredients_search_results_view.$el.find(".supplement_name_results").find(":odd").css("background-color", "#F0F0EE")
                                })
                        },
                        1000
                    );
                }
      
            },
            
            getSearchIngredients : function(search_text, handleData) {
                var url = window.app.protocol_options.fitness_backend_url;
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
        
        window.app.Thumbnail_slide_view = Backbone.View.extend({
           
            initialize: function(){
                this.collection.on('add', this.addImage, this);
                this.collection.on('reset', this.resetImages, this);
            },
            
            render: function(){
                var template = _.template( $("#thumbnail_slide_template").html());
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
                
                console.log(image_url);
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
                    this.$el.find('.supplement_image').val(this.collection.first().get('src'));
                }
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
        
        
        window.app.Ingredients_search_results_view = Backbone.View.extend({

            initialize : function () {
                _.bindAll(this, 'onClickOption', 'render');
            },

            render : function (eventName) {
                var template = _.template( $("#ingredients_seaarch_results_template").html(), this.model.toJSON());
                this.$el.html(template);
              
                return this;
            },

            events:{
                "click .supplement_name_results option": "onClickOption"
            },

            onClickOption : function (event) {
                var parent_view_el = this.options.parent_view_el;
  
                var ingredient_id = $(event.target).val();
                
                var self = this;
                this.getIngredientData(
                    ingredient_id, 
                    function(ingredient) {
                        if(!ingredient) return;
                        parent_view_el.find(".supplement_name").val(ingredient.ingredient_name);
                        parent_view_el.find(".supplement_description").val(ingredient.description);
                        self.close();
                    }
                );
            },
            
            getIngredientData : function(id, handleData) {
                var url = window.app.protocol_options.fitness_backend_url;
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                        view : 'nutrition_recipe',
                        format : 'text',
                        task : 'getIngredientData',
                        id : id
                      },
                    dataType : 'json',
                    success : function(response) {
                        if(!response.status.success) {
                            alert(response.status.message);
                            return;
                        }
                        handleData(response.ingredient);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error getIngredientData");
                    }
                }); 
            },
            
            close : function() {
                $(this.el).unbind();
                $(this.el).remove();
            },

        });
        
        //MODELS
        
        window.app.Protocol_model = Backbone.Model.extend({
            urlRoot : window.app.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.app.protocol_options.nutrition_plan_id,
                name : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.name) {
                  return 'Protocol Name is empty';
                }
                if (!attrs.nutrition_plan_id) {
                  return 'Nurtition Plan Id is not valid';
                }
            }
        });
        
        window.app.Supplement_model = Backbone.Model.extend({
            urlRoot : window.app.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.app.protocol_options.nutrition_plan_id,
                protocol_id : null,
                name : null,
                description : null,
                comments : null,
                url : null,
            },

            validate: function(attrs, options) {
                if (!attrs.nutrition_plan_id) {
                  return 'Nurtition Plan Id is not valid';
                }
                if (!attrs.protocol_id) {
                  return 'Protocol Id is not valid';
                }
                if (!attrs.name) {
                  return 'name';
                }

                var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
                if (attrs.url && !regexp.test(attrs.url) ) {
                  return 'url';
                }
            }
        });
        
        
        // COLLECTIONS
        window.app.Protocols_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            model: window.app.Protocol_model
        });

        
        
        window.app.Supplements_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            model: window.app.Supplement_model
        });
        
        
        window.app.Remote_images_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_backend_url + '&format=text&view=nutrition_plan&task=remote_images&'
        });
        
        window.app.Remote_images_filtered_collection = Backbone.Collection.extend({
        });
        
        
        //CONTROLLER
        
        var Controller = Backbone.Router.extend({
            routes: {
                "!/supplements": "supplements", 
                "!/add_supplement_protocol": "add_supplement_protocol",
            },
            
            initialize:function () {
                $('#protocols_wrapper').html(new window.app.Protocols_wrapper().render().el);
            },

            
            supplements: function () {
                $("#protocol_list").empty();
                 this.common_actions();
                 $("#supplements_wrapper").show();
                 $("#supplements_link").addClass("active_link");
                 
                 window.app.protocols = new window.app.Protocols_collection(); 
                 
                 window.app.protocols.fetch({
                     data: {nutrition_plan_id : window.app.protocol_options.nutrition_plan_id},
                     error: function (model, response) {
                        alert(response.responseText);
                     }
                 });
                 
                 window.app.nutrition_plan_protocols_view = new window.app.Nutrition_plan_protocols_view({el : $("#protocol_list"), collection : window.app.protocols}); 

            },
                    
            
            add_supplement_protocol : function() {
                this.nutrition_plan_protocol_view = new window.app.Nutrition_plan_protocol_view({model : new window.app.Protocol_model(), collection : window.app.protocols}); 
                $("#protocol_list").append(this.nutrition_plan_protocol_view.render().el );
            },
            
            common_actions : function() {
                $(".block, #close_tab").hide();
                $(".plan_menu_link").removeClass("active_link")
            },
                    
            
        });

        window.app.supplements_controller = new Controller(); 
 
    }

    // Add the  function to the top level of the jQuery object
    $.NutritionPlanSupplements = function(options) {

        var constr = new NutritionPlanSupplements();

        return constr;
    };
        
})(jQuery, Backbone);



