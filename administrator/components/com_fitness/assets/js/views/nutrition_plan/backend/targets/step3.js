define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/targets/step4',
	'text!templates/nutrition_plan/backend/targets/step3.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Step4_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            //console.log(this.model.toJSON());
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.setLayout($(self.el).find("#common_profiles"));
            });
        },
        
        events : {
            "change #common_profiles" : "onChangeCommonProfiles",
            "click #step3_claculate" : "onCalculate",
        },
        
        onChangeCommonProfiles : function(event){
            this.setLayout($(event.target));
        },
        
        setLayout : function(field) {
            $(this.el).find("#step3_protein, #step3_fats, #step3_carbs").removeClass("red_style_border");
            var value = field.find(":selected").attr('data-name');
            if(value == 'iifym') {
                $(this.el).find("#iifym_block").show();
                $(this.el).find("#other_common_profiles_block").hide();
            } else {
                $(this.el).find("#iifym_block").hide();
                $(this.el).find("#other_common_profiles_block").show();
                this.preFillFields(field);
            }
        },
        
        preFillFields : function(field) {
            var protein = field.find(":selected").attr('data-protein');
            var fat = field.find(":selected").attr('data-fat');
            var carbs = field.find(":selected").attr('data-carbs');
            
            $(this.el).find("#step3_protein").val(protein);
            $(this.el).find("#step3_fats").val(fat);
            $(this.el).find("#step3_carbs").val(carbs);
            
            this.model.set({
                protein_percent : protein/100,
                fat_percent : fat/100,
                carbs_percent : carbs/100,
            });
        },
        
        onCalculate : function() {
            if(!this.validate() && $(this.el).find("#common_profiles").find(":selected").attr('data-name') != 'iifym') {
                return;
            }
            
            this.goStep4();
        },
        
        validate : function() {
            var step3_protein_field =  $(this.el).find("#step3_protein");
            
            var step3_fats_field = $(this.el).find("#step3_fats");
            
            var step3_carbs_field = $(this.el).find("#step3_carbs");
            
            $(this.el).find("#step3_protein, #step3_fats, #step3_carbs").removeClass("red_style_border");

            
            if(!this.number(step3_protein_field.val()) || parseInt(step3_protein_field.val()) > 100) {
                step3_protein_field.addClass("red_style_border");
                return false;
            }
            
            if(!this.number(step3_fats_field.val()) || parseInt(step3_fats_field.val()) > 100) {
                step3_fats_field.addClass("red_style_border");
                return false;
            }
            
            
            if(!this.number(step3_carbs_field.val()) || parseInt(step3_carbs_field.val()) > 100) {
                step3_carbs_field.addClass("red_style_border");
                return false;
            }
            return true;
        },
        
        number: function(value) {
            return  /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value);
        },
        
        goStep4 : function() {
            $("#step4_fieldset").show();

            $("#step4_wrapper").html(new Step4_view({model : this.model}).render().el);
        },
   
    });
            
    return view;
});