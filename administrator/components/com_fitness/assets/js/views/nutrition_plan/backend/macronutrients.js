define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/menus/macronutrients_menu',
	'text!templates/nutrition_plan/backend/macronutrients.html'
], function ( $, _, Backbone, app, Form_menu_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadFormMenu();
                
                self.connectEditor("#allowed_proteins");
                self.connectEditor("#allowed_fats");
                self.connectEditor("#allowed_carbs");
                self.connectEditor("#allowed_liquids");
                self.connectEditor("#other_recommendations");
                
                self.connectComments();
            });
        },
        
        connectEditor : function(target) {
            $(this.el).find(target).cleditor({width:'100%', height:150, useCSS:true})[0];
        },
        
        connectComments :function() {
             var comment_options = {
                'item_id' :  this.model.get('id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_nutrition_plan_macronutrients_comments',
                'read_only' : false,
                'anable_comment_email' : true,
                'comment_method' : 'MacrosComment'
            }
            var comments = $.comments(comment_options, comment_options.item_id, 1);

            var comments_html = comments.run();
            $(this.el).find("#macronutrients_comments_wrapper").html(comments_html);
        },
        
        loadFormMenu : function() {
            $(this.el).find("#form_menu").html(new Form_menu_view({model : this.model}).render().el);
        },

    });
            
    return view;
});