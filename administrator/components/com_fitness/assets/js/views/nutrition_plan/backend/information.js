define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/menus/information_menu',
	'text!templates/nutrition_plan/backend/information.html'
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
                
                self.connectEditor("#information");
            });
        },
        
        connectEditor : function(target) {
            $(this.el).find(target).cleditor({width:'100%', height:500, useCSS:true})[0];
        },
        
        loadFormMenu : function() {
            $(this.el).find("#form_menu").html(new Form_menu_view({model : this.model}).render().el);
        },

    });
            
    return view;
});