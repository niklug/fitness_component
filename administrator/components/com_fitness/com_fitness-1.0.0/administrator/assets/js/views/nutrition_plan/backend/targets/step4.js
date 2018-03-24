define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/targets/step4.html'
], function (
        $,
        _, 
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize: function(){
            this.TDEE = this.model.get('step4_calories');
            this.weight = this.model.get('weight');
        },
        
        template:_.template(template),
            
        render: function(){
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events : {
            "click #step4_reset" : "onReset",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                if($("#common_profiles").find(":selected").attr('data-name') == 'iifym') {
                    self.setFields_iifym();
                } else {
                    self.setFields();
                }
                self.setWater();
                self.saveData();
            });
        },
        //
        setFields : function() {
            this.setProtein();
            this.setFat();
            this.setCarbs();
        },
        //
        setProtein : function() {
            this.setProteinGrams();
            this.setProteinCalories();
            this.setProteinPercent();
        },
        
        setFat : function() {
            this.setFatGrams();
            this.setFatCalories();
            this.setFatPercent();
        },
        
        setCarbs : function() {
            this.setCarbsGrams();
            this.setCarbsCalories();
            this.setCarbsPercent();
        },
        //
        setProteinGrams : function() {
            var value = ((this.TDEE * this.model.get('protein_percent')) / 4).toFixed(0);
            console.log(this.model.get('protein_percent'));
            $(this.el).find("#step4_protein_grams").val(value);
        },
        
        setFatGrams : function() {
            var value = ((this.TDEE * this.model.get('fat_percent')) / 9).toFixed(0);
            $(this.el).find("#step4_fat_grams").val(value);
        },
        
        setCarbsGrams : function() {
            var value = ((this.TDEE * this.model.get('carbs_percent')) / 4).toFixed(0);
            $(this.el).find("#step4_carbs_grams").val(value);
        },
        //
        setProteinCalories : function() {
            var value = (this.TDEE * this.model.get('protein_percent')).toFixed(0);
            $(this.el).find("#step4_protein_calories").val(value);
        },
        
        setFatCalories : function() {
            var value = (this.TDEE * this.model.get('fat_percent')).toFixed(0);
            $(this.el).find("#step4_fat_calories").val(value);
        },
        
        setCarbsCalories : function() {
            var value = (this.TDEE * this.model.get('carbs_percent')).toFixed(0);
            $(this.el).find("#step4_carbs_calories").val(value);
        },
        //
        setProteinPercent : function() { 
            $(this.el).find("#step4_protein_percent").val(this.model.get('protein_percent') * 100);
        },
        
        setFatPercent : function() {
            $(this.el).find("#step4_fat_percent").val(this.model.get('fat_percent') * 100);
        },
        
        setCarbsPercent : function() {
            $(this.el).find("#step4_carbs_percent").val(this.model.get('carbs_percent') * 100);
        },
        
        /* */
        setFields_iifym : function() {
            this.setProtein_iifym();
            this.setFat_iifym();
            this.setCarbs_iifym();
        },
        //
        setProtein_iifym : function() {
            this.setProteinGrams_iifym();
            this.setProteinCalories_iifym();
            this.setProteinPercent_iifym();
        },
        
        setFat_iifym : function() {
            this.setFatGrams_iifym();
            this.setFatCalories_iifym();
            this.setFatPercent_iifym();
        },
        
        setCarbs_iifym : function() {
            this.setCarbsGrams_iifym();
            this.setCarbsCalories_iifym();
            this.setCarbsPercent_iifym();
        },
        //
        setProteinGrams_iifym : function() {
            var step3_protein = $(".step3_protein:checked").val();
            
            if(step3_protein == 'custom') {
                step3_protein = $("#step3_protein_custom").val();
            }
            
            var formula = this.model.get('formula');
            
            var koef = this.weight;
            
            if(formula == 'overweight') {
                var weight = parseFloat(this.model.get('weight'));
                var body_fat = parseFloat(this.model.get('body_fat'));
                
                var lean_mass = weight - (weight * body_fat/100);
                
                koef = lean_mass;
            }
            
            var value = (step3_protein * koef).toFixed(0);
            
            $(this.el).find("#step4_protein_grams").val(value);
            
            return value;
        },
        
        setFatGrams_iifym : function() {
            var step3_fat = $(".step3_fats:checked").val();
            
            if(step3_fat == 'custom') {
                step3_fat = $("#step3_fats_custom").val();
            }
            
            var formula = this.model.get('formula');
            
            var koef = this.weight;
            
            if(formula == 'overweight') {
                var weight = parseFloat(this.model.get('weight'));
                var body_fat = parseFloat(this.model.get('body_fat'));
                
                var lean_mass = weight - (weight * body_fat/100);
                
                koef = lean_mass;
            }
            
            var value = (step3_fat * koef).toFixed(0);
            
            $(this.el).find("#step4_fat_grams").val(value);
            
            return value
        },
        //
        setProteinCalories_iifym : function() {
            var value = (this.setProteinGrams_iifym() * 4).toFixed(0);
            
            $(this.el).find("#step4_protein_calories").val(value);
            
            return value;
        },
        
        setFatCalories_iifym : function() {
            var value = (this.setFatGrams_iifym() * 9).toFixed(0);
            
            $(this.el).find("#step4_fat_calories").val(value);
            
            return value;
        },
        //
        
        setProteinPercent_iifym : function() {
            var value = (this.setProteinCalories_iifym() / this.TDEE * 100).toFixed(0);
            
            $(this.el).find("#step4_protein_percent").val(value);
            
            return value;
        },
        
        setFatPercent_iifym : function() {
            var value = (this.setFatCalories_iifym() / this.TDEE * 100).toFixed(0);
            //console.log(value);
            $(this.el).find("#step4_fat_percent").val(value);
            
            return value;
        },
        //
        setCarbsGrams_iifym : function() {
            var value = (this.setCarbsCalories_iifym() / 4).toFixed(0);
            
            $(this.el).find("#step4_carbs_grams").val(value);
            
            return value;
        },
        
        setCarbsCalories_iifym : function() {
            var value = (this.TDEE - this.setProteinCalories_iifym() - this.setFatCalories_iifym()).toFixed(0);
            
            $(this.el).find("#step4_carbs_calories").val(value);
            
            return value;
        },
        
        setCarbsPercent_iifym : function() {
            var value = (100 - this.setProteinPercent_iifym() - this.setFatPercent_iifym()).toFixed(0);
            
            $(this.el).find("#step4_carbs_percent").val(value);
            
            return value;
        },
        
        onReset : function() {
            var id = this.model.get('nutrition_plan_id');
            this.model.destroy({
                success: function (model, response) {
                    app.controller.navigate("");
                    app.controller.navigate("!/targets/" + id, true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        saveData : function() {
            this.model.set({
                calories : this.TDEE,
                protein : $("#step4_protein_grams").val(),
                fats : $("#step4_fat_grams").val(),
                carbs : $("#step4_carbs_grams").val(),
                water : $("#step4_water").val(),
                
                step4_protein_percent : $("#step4_protein_percent").val(),
                step4_fat_percent : $("#step4_fat_percent").val(),
                step4_carbs_percent : $("#step4_carbs_percent").val(),
            });
            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        setWater : function() {
            var climate_variable = parseFloat(this.model.get('climate'));
            var activity_level = parseFloat(this.model.get('exercise_level_water'));
            var body_weight = parseFloat(this.model.get('weight'));
            
            var water = (((body_weight * 0.67) + (body_weight * activity_level)) / 0.029) + climate_variable;
            
            water = (parseInt(water)).toFixed(0);
            
            //WATER = ((("Body Weight" x "0.67(kg)") + ("Body Weight" x "Activity Level" )) / 0.029) + climate_variable
            
            $(this.el).find("#step4_water").val(water);
        }
        
        
   
    });
            
    return view;
});