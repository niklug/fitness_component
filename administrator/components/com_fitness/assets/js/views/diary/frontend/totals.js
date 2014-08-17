define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/totals.html'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize: function(){
            app.collections.meal_ingredients.bind("sync", this.render, this);
            app.collections.meal_ingredients.bind("add", this.render, this);
            app.collections.meal_ingredients.bind("remove", this.render, this);

            this.model.set({
                daily_protein_grams : '0',
                daily_fats_grams : '0',
                daily_carbs_grams : '0',
                daily_saturated_fat_grams : '0',
                daily_total_sugars_grams : '0',
                daily_sodium_grams : '0',

            });

        },
        
        template : _.template(template),

        render : function () {
            this.setBaseValues();
            var data = {item : this.model.toJSON()};
            $(this.el).html(this.template(data));
            
            this.setVarianceGrams();
            this.setVariancePercents();
            
            return this;
        },
        
        setBaseValues : function() {
            this.setDailyTotalsGrams();
            this.setDailyTargetGrams();
            
        },
        
        setDailyTotalsGrams : function() {
            var daily_protein_grams = this.getCollectionNameAmount('protein');
            var daily_fats_grams = this.getCollectionNameAmount('fats');
            var daily_carbs_grams = this.getCollectionNameAmount('carbs');
            var daily_saturated_fat_grams = this.getCollectionNameAmount('saturated_fat');
            var daily_total_sugars_grams = this.getCollectionNameAmount('total_sugars');
            var daily_sodium_grams = this.getCollectionNameAmount('sodium');

            this.model.set({
                daily_protein_grams : daily_protein_grams,
                daily_fats_grams : daily_fats_grams,
                daily_carbs_grams : daily_carbs_grams,
                daily_saturated_fat_grams : daily_saturated_fat_grams,
                daily_total_sugars_grams : daily_total_sugars_grams,
                daily_sodium_grams : daily_sodium_grams,
            });
        },

        getCollectionNameAmount : function( name) {
            var value =  app.collections.meal_ingredients.reduce(function(memo, value) { return parseFloat(memo) + parseFloat(value.get(name)) }, 0);
            return value.toFixed(2);
        },
        
        setDailyTargetGrams : function() {
            var nutrition_plan_id = this.model.get('nutrition_plan_id');
            //console.log(this.model.toJSON());
            
            //console.log(app.models.target.toJSON());
            var daily_target_protein_grams = app.models.target.get('protein');
            var daily_target_fats_grams = app.models.target.get('fats');
            var daily_target_carbs_grams = app.models.target.get('carbs');
            
            this.model.set({
                daily_target_protein_grams : daily_target_protein_grams,
                daily_target_fats_grams : daily_target_fats_grams,
                daily_target_carbs_grams : daily_target_carbs_grams
            });
        },
        
        setVarianceGrams : function() {
            //protein
            var variance_protein_grams_element = $(this.el).find("#variance_protein_grams");
            
            this.variance_protein_grams_value = this.calculateVarianceGrams(this.model.get('daily_protein_grams'), this.model.get('daily_target_protein_grams'));
            
            variance_protein_grams_element.html(this.variance_protein_grams_value);
            
            this.variance_protein_percents_value = this.calculateVariancePercents(this.variance_protein_grams_value, this.model.get('daily_target_protein_grams'));
            
            this.setVarianceRangeStylePRO_FATS_CARBS(variance_protein_grams_element, this.variance_protein_percents_value);
            
            //fats
            var variance_fats_grams_element = $(this.el).find("#variance_fats_grams");
            
            this.variance_fats_grams_value = this.calculateVarianceGrams(this.model.get('daily_fats_grams'), this.model.get('daily_target_fats_grams'));
            
            variance_fats_grams_element.html(this.variance_fats_grams_value);
            
            this.variance_fats_percents_value = this.calculateVariancePercents(this.variance_fats_grams_value, this.model.get('daily_target_fats_grams'));
            
            this.setVarianceRangeStylePRO_FATS_CARBS(variance_fats_grams_element, this.variance_fats_percents_value);
            
            //carbs
            var variance_carbs_grams_element = $(this.el).find("#variance_carbs_grams");
            
            this.variance_carbs_grams_value = this.calculateVarianceGrams(this.model.get('daily_carbs_grams'), this.model.get('daily_target_carbs_grams'));
            
            variance_carbs_grams_element.html(this.variance_carbs_grams_value);
            
            this.variance_carbs_percents_value = this.calculateVariancePercents(this.variance_carbs_grams_value, this.model.get('daily_target_carbs_grams'));
            
            this.setVarianceRangeStylePRO_FATS_CARBS(variance_carbs_grams_element, this.variance_carbs_percents_value);
        },
        
        setVarianceRangeStylePRO_FATS_CARBS : function(element, value) {
            var abs_value = Math.abs(value); 
            var input_class = '';
            element.removeClass('green_style orange_style red_style');
            if((abs_value >= 0) && (abs_value <= 15)) {
                input_class = 'green_style'; 
            }

            if((abs_value > 15) && (abs_value <= 40)) {
                input_class = 'orange_style'; 
            }

            if(abs_value > 40) {
                input_class = 'red_style'; 
            }
            element.addClass(input_class);
        },
        
        round_2_sign : function(value) {
            return Math.round(value * 100)/100;
        },
        
        calculateVarianceGrams : function(total, target) {
            return this.round_2_sign(total - target);
        },
    
        calculateVariancePercents : function(variance, target) {
            return this.round_2_sign((variance / target) * 100);
        },
        
        setVariancePercents : function() {
            
        }
        

    });
            
    return view;

});