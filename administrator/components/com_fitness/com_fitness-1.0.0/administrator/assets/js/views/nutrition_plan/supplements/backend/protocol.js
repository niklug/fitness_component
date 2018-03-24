define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/supplements/supplements',
        'models/nutrition_plan/supplements/supplement',
        'views/nutrition_plan/supplements/backend/supplement',
        'views/comments/index',
	'text!templates/nutrition_plan/supplements/backend/protocol_item.html'
], function (
            $,
            _,
            Backbone, 
            app,
            Supplements_collection,
            Supplement_model,
            Supplement_view,
            Comments_view,
            template
    ) {

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
                var self = this;
                this.supplements_collection.fetch({
                    data: {nutrition_plan_id : self.options.nutrition_plan_id, protocol_id : this.protocol_id},
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
        
        events: {
            "click .save_protocol" : "onClickSaveProtocol",
            "click .delete_protocol" : "onClickDeleteProtocol",
            "click .add_supplement" : "onClickAddSupplement",
        },

        connectComments :function() {
            var comment_options = {
                'item_id' :  this.options.nutrition_plan_id,
                'item_model' : this.model,
                'sub_item_id' :  this.model.get('id'),
                'db_table' : 'fitness_nutrition_plan_supplements_comments',
                'read_only' : false,
                'anable_comment_email' : true,
                'comment_method' : 'SupplementComment'
            }
             
            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find(".comments_wrapper").html(comments_html);
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
                this.model.set({created_by : app.options.user_id});
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
            app.views.supplement = new Supplement_view({collection : this.supplements_collection, model : new Supplement_model({protocol_id : this.model.get('id')}), nutrition_plan_id : this.options.nutrition_plan_id }); 
            this.supplements_list_el.append(app.views.supplement.render().el );
        }
    });
            
    return view;
});