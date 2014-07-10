define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/targets/step1',
	'text!templates/nutrition_plan/backend/targets/targets_container.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Step1_view,
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
                self.loadStep1();
            });
        },
        
        loadStep1 : function() {
            app.views.targets_step1 = new Step1_view({model : this.model});
            $(this.el).find("#step1_wrapper").html(app.views.targets_step1.render().el);
        }

   
    });
            
    return view;
});