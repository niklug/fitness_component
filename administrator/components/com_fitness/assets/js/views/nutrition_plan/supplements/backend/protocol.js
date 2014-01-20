define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/supplements/supplements',
        'models/nutrition_plan/supplements/supplement',
        'views/nutrition_plan/supplements/backend/supplement',
	'text!templates/nutrition_plan/supplements/backend/protocol_item.html'
], function ( $, _, Backbone, app, Supplements_collection, Supplement_model, Supplement_view, template ) {

     var view = Backbone.View.extend({

        template:_.template(template),

        initialize: function(){
            _.bindAll(this, 'onClickSaveProtocol','close', 'render');
            this.model.on("destroy", this.close, this);
        },

        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);

            this.supplements_list_el = this.$el.find(".supplements_list");

            this.supplements_collection = new Supplements_collection();

            this.protocol_id = this.model.get('id');

            if(this.protocol_id ){ 
                this.supplements_collection.fetch({
                    data: {nutrition_plan_id : app.options.nutrition_plan_id, protocol_id : this.protocol_id},
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }

            var self = this;

            this.supplementListItemViews = {};

            this.supplements_collection.on("add", function(supplement) {
                app.views.supplement = new Supplement_view({collection : this, model : supplement}); 
                self.supplements_list_el.append(app.views.supplement.render().el );
                self.supplementListItemViews[ supplement.cid ] = app.views.supplement;
            });

            this.supplements_collection.on("remove", function(supplement, options) {
                self.supplementListItemViews[ supplement.cid ].close();
                delete self.supplementListItemViews[ supplement.cid ];
            });
            
            this.connectComments();

            return this;
        },

        connectComments : function() {
            var comment_options = {
                'item_id' : this.model.get('nutrition_plan_id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_nutrition_plan_supplements_comments',
                'read_only' : true,
            };

            var comments = $.comments(comment_options, comment_options.item_id, this.model.get('id'));
            
            var comments_html = comments.run();

            this.$el.find(".comments_wrapper").html(comments_html);
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
            app.views.supplement = new Supplement_view({collection : this.supplements_collection, model : new Supplement_model({protocol_id : this.model.get('id')}) }); 
            this.supplements_list_el.append(app.views.supplement.render().el );
        }
    });
            
    return view;
});