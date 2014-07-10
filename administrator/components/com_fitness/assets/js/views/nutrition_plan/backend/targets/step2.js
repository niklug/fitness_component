define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/targets/step2.html'
], function (
        $,
        _, 
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            this.calcutateFieldsValues();
            
            var data = {item : this.model.toJSON()};
            console.log(data);
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {

            });
        },
        
        calcutateFieldsValues : function() {
            var TDEE = this.model.get('TDEE');
            this.model.set({
                step2_fat_loss : (TDEE - (TDEE * 15/100)).toFixed(0),
                step2_maintain : TDEE.toFixed(0),
                step2_bulking : (TDEE + (TDEE * 10/100)).toFixed(0),
            });
        }
   
    });
            
    return view;
});