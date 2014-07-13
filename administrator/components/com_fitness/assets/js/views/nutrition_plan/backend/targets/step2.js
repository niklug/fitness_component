define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/targets/step3',
	'text!templates/nutrition_plan/backend/targets/step2.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Step3_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize: function(){
            this.TDEE = this.model.get('TDEE');
        },
            
        render: function(){
            this.calcutateFieldsValues();
            
            var data = {item : this.model.toJSON()};
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
        
        events : {
            "click #step2_claculate" : "onCalculate",
            "change .step2_fat_loss" : "onSelectFatLossPercent",
            "change .step2_bulking" : "onSelectBulkingPercent",
        },
        
        calcutateFieldsValues : function() {
            this.model.set({
                step2_fat_loss : (this.TDEE - (this.TDEE * 15/100)).toFixed(0),
                step2_maintain : this.TDEE.toFixed(0),
                step2_bulking : (this.TDEE + (this.TDEE * 10/100)).toFixed(0),
            });
        },
        
        onCalculate : function() {
            this.setCalories();
            this.goStep3();
        },
        
        setCalories : function() {
            var fat_loss_checked =  $(this.el).find(".step2_fat_loss").is(':checked');
            
            var maintain_checked =  $(this.el).find(".step2_maintain").is(':checked');

            var bulking_checked =  $(this.el).find(".step2_bulking").is(':checked');
            
            var custom_checked =  $(this.el).find(".step2_custom").is(':checked');
            
            var calories;
            
            if(fat_loss_checked) {
                calories = $(this.el).find("#step2_fat_loss").val();
                this.model.set({step4_calories : calories});
            }
            
            if(maintain_checked) {
                calories = $(this.el).find("#step2_maintain").val();
                this.model.set({step4_calories : calories});
            }
            
            if(bulking_checked) {
                calories = $(this.el).find("#step2_bulking").val();
                this.model.set({step4_calories : calories});
            }
            
            if(custom_checked) {
                calories = $(this.el).find("#step2_custom").val();
                this.model.set({step4_calories : calories});
            }
            
            //console.log(this.model.get('step4_calories'));
        },
        
        goStep3 : function() {
            $("#step3_fieldset").show();
            
            this.model.set({
                intensity : $(this.el).find("input[name=step2_radio]:checked").val()
            });

            $("#step3_wrapper").html(new Step3_view({model : this.model}).render().el);
        },
        
        onSelectFatLossPercent : function(event) {
            var percent = $(event.target).val();
            var value = this.TDEE + (this.TDEE * percent/100);
            $(this.el).find("#step2_fat_loss").val(value.toFixed(0));
        },
        
        onSelectBulkingPercent : function(event) {
            var percent = $(event.target).val();
            var value = this.TDEE + (this.TDEE * percent/100);
            $(this.el).find("#step2_bulking").val(value.toFixed(0));
        },
   
    });
            
    return view;
});