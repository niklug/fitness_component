define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/menus/main_menu',
        'views/nutrition_plan/backend/client_trainers_block',
	'text!templates/nutrition_plan/backend/overview_container.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Main_menu_view, 
        Client_trainers_block_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadMainMenu();
                self.loadClientTrainersBlock();
            });
        },
        
        loadMainMenu : function() {
            $(this.el).find("#plan_menu").html(new Main_menu_view({model : this.model}).render().el);
            $("#overview_link").addClass("active_link");
        },
        
        loadClientTrainersBlock : function() {
            $(this.el).find("#client_trainers_container").html(new Client_trainers_block_view({model : this.model}).render().el);
        }
    });
            
    return view;
});