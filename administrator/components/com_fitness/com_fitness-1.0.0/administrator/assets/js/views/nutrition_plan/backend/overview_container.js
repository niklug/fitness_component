define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/menus/overview_menu',
        
        'views/nutrition_plan/backend/client_trainers_block',
        'views/nutrition_plan/backend/goals_periods_block',
        'views/nutrition_plan/backend/nutrition_focus_block',
	'text!templates/nutrition_plan/backend/overview_container.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Form_menu_view,
        
        Client_trainers_block_view,
        Goals_periods_block_view,
        Nutrition_focus_block_view,
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
                self.loadFormMenu();
                self.loadClientTrainersBlock();
                self.loadGoalsPeriodsBlock();
                self.loadNutritionFocusBlock();
            });
        },
        
        loadFormMenu : function() {
            $(this.el).find("#form_menu").html(new Form_menu_view({model : this.model}).render().el);
            $("#overview_link").addClass("active_link");
        },
        
        loadClientTrainersBlock : function() {
            $(this.el).find("#client_trainers_container").html(new Client_trainers_block_view({model : this.model}).render().el);
        },
        
        loadGoalsPeriodsBlock : function() {
            $(this.el).find("#goals_periods_wrapper").html(new Goals_periods_block_view({model : this.model}).render().el);
        },
        
        loadNutritionFocusBlock : function() {
            $(this.el).find("#nutrition_focus_container").html(new Nutrition_focus_block_view({model : this.model}).render().el);
        }
    });
            
    return view;
});