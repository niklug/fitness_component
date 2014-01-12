define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/supplements/supplements',
        'views/nutrition_plan/supplements/frontend/supplement',
	'text!templates/nutrition_plan/supplements/frontend/protocol_item.html'
], function ( $, _, Backbone, app, Supplements_collection, Supplement_view, template ) {

     var view = Backbone.View.extend({

        template:_.template(template),

        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            
            this.$el.html(template);

            this.supplements_list_el = this.$el.find(".supplements_list");

            app.collections.supplements = new Supplements_collection();

            this.protocol_id = this.model.get('id');

            if(this.protocol_id ){ 
                app.collections.supplements.fetch({data: {nutrition_plan_id : this.model.get('nutrition_plan_id'), protocol_id : this.protocol_id}});
            }

            var self = this;

            this.supplementListItemViews = {};

            app.collections.supplements.on("add", function(supplement) {
                var supplement_view = new Supplement_view({collection : this, model : supplement}); 
                self.supplements_list_el.append(supplement_view.render().el );
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
    });
            
    return view;
});