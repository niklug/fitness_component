define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/supplements/supplements',
        'views/nutrition_plan/supplements/frontend/supplement',
        'views/comments/index',
	'text!templates/nutrition_plan/supplements/frontend/protocol_item.html'
], function (
        $, 
        _, 
        Backbone,
        app,
        Supplements_collection,
        Supplement_view,
        Comments_view, 
        template 
    ) {

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
        
        connectComments :function() {
            var comment_options = {
                'item_id' :  this.model.get('nutrition_plan_id'),
                'item_model' : this.model,
                'sub_item_id' :  this.model.get('id'),
                'db_table' : 'fitness_nutrition_plan_supplements_comments',
                'read_only' : true,
                'anable_comment_email' : true,
                'comment_method' : 'SupplementComment'
            }
             
            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find(".comments_wrapper").html(comments_html);
        },
    });
            
    return view;
});