/*
 * 
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    function CalculateSummary(options) {
        this.options = options;
        this._activity_level = this.options.activity_level;
        this._activity_level_value = $(this._activity_level +":checked").val();
    }



    CalculateSummary.prototype.run = function() {
        var self = this;
        this.interval = setInterval(function() {
            self.calculateFields();
        }, 2000);
    }

    CalculateSummary.prototype.calculateFields = function() {
        this.setDailyTotalsGrams();
        this.setDailyTotalsPercents();
        this.setVarianceGrams();
        this.setVariancePercents();
        this.setVariance();
        this.setCaloriesVariancePercents();
        this.setEnergyVariancePercents();
        this.setDailyTotalWater();
        this.setDailyVarianceWater();
        this.setDailyTargetGrams();
        this.setDailyTargetPercents();
        this.setDailyTargetEnergy();
        this.setDailyTargetCalories();
        this.setDailyTargetWater();
        
        this.setCalorieTotal();
        this.setWaterTotal();
        
        this.setCalorieScore();
        this.setWaterScore();

        if(this.options.draw_chart) {
            this.connectMacronutrientScores();
            this.setScores(this.options.chart_container);
        }

    }
    
    CalculateSummary.prototype.setCalorieTotal = function() {
        var calories_total = this.get_item_total('meal_calories_total');
        var target_kind = this.getDayKindPrefix();
        var dayly_target_calories = this.getTargetPercentsValue('calories', target_kind);
        
        var calorieTotal = this.round_2_sign(100 * (calories_total / dayly_target_calories));

        $("#calories_total").html(calorieTotal + '%');
    }
    
    CalculateSummary.prototype.setWaterTotal = function() {
        var daily_total_water  = this.getDailyTotalWater();
        
        var target_kind = this.getDayKindPrefix();
        var dayly_target_water = this.getTargetPercentsValue('water', target_kind);
        
        var waterTotal = this.round_2_sign(100 * (daily_total_water / dayly_target_water));

        $("#water_total").html(waterTotal + '%');
    }
    
    CalculateSummary.prototype.setCalorieScore = function() {
        var calories_variance_percents = this.getCaloriesVariancePercents('calories');
        var cvp = calories_variance_percents;
        
        var calorie_score = 0;
        
        if(cvp < 200) {
            calorie_score = 100 + Math.abs(cvp) - ((1 + (100/200)) * Math.abs(cvp));
        } else {
            calorie_score = 100 + cvp;
        }
        
        calorie_score = this.round_2_sign(calorie_score);
        
        $("#calorie_score").html(calorie_score + '%');

    }
    
    CalculateSummary.prototype.setWaterScore = function() {

        var daily_total_water  = this.getDailyTotalWater();
        
        var target_kind = this.getDayKindPrefix();
        var dayly_target_water = this.getTargetPercentsValue('water', target_kind);
        
        var water_score = this.round_2_sign(100 * (daily_total_water / dayly_target_water));
        
        water_score = this.round_2_sign(water_score);
        
        $("#water_score").html(water_score + '%');

    }
    
    CalculateSummary.prototype.setScores = function(container) {
        var protein_variance = $("#variance_protein_percents").val();
        var carbs_variance = $("#variance_carbs_percents").val();
        var fats_variance = $("#variance_fats_percents").val();
        
        
        var protein_totals = $("#daily_protein_percents").val();
        var carbs_totals = $("#daily_carbs_percents").val();
        var fats_totals = $("#daily_fats_percents").val();

        
        var protein= this.calculateScores(protein_variance);
        var carbs = this.calculateScores(carbs_variance);
        var fats = this.calculateScores(fats_variance);
        
        $("#protein_score").html(protein + '%');
        $("#carbs_score").html(carbs + '%');
        $("#fats_score").html(fats + '%');
        
        var total_score = this.calculateTotalScore(protein, carbs, fats);
        $("#final_score").html(total_score + '%');
        
        this.setVarianceRangeFinalScore($("#final_score"), total_score);
        
        var status = $(".status_button").attr('data-status_id');
        
        if((status == '3') || (status == '4') || (status == '5')) {
            $("#score_input").val(total_score);
        }
        
        
        var target_kind = this.getDayKindPrefix();
        var dayly_target_protein = this.getTargetPercentsValue('protein', target_kind);
        var dayly_target_carbs = this.getTargetPercentsValue('carbs', target_kind);
        var dayly_target_fats = this.getTargetPercentsValue('fats', target_kind);

        var data = [
            {label: "Protein: <br/>"  + protein_totals + '%' , data: [[1, dayly_target_protein]]},
            {label: "Carbs: <br/>" + carbs_totals + '%', data: [[1, dayly_target_carbs]]},
            {label: "Fat: <br/>" + fats_totals + '%', data: [[1, dayly_target_fats]]}
        ];
        
        
        var targets_pie = $.drawPie(data, container, {'no_percent_label' : true});

        targets_pie.draw();  
    }
    
    CalculateSummary.prototype.calculateTotalScore = function(protein, carbs, fats) {
        var sum = protein + carbs + fats;
       
        return this.round_2_sign(sum / 3);
    }
    
    CalculateSummary.prototype.calculateScores = function(value) {
        var value = parseFloat(value);
        if(value < 0) {
            var score = 100 + value;
        } else {
            var score = 100 - value;
        }
        score = Math.abs(score);
        return this.round_2_sign(score);
    }


    CalculateSummary.prototype.setVarianceRangeStylePRO_FATS_CARBS = function(element, value) {

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
    }
    
    
    CalculateSummary.prototype.setVarianceRangeFinalScore = function(element, value) {
        var abs_value = Math.abs(value); 
        var input_class = '';
        element.removeClass('yellow_style_total green_style_total orange_style_total red_style_total');
        if((abs_value >= 0) && (abs_value <= 40)) {
            input_class = 'red_style_total'; 
        }
        
        if((abs_value > 40) && (abs_value <= 55)) {
            input_class = 'orange_style_total'; 
        }

        if((abs_value > 55) && (abs_value <= 79)) {
            input_class = 'yellow_style_total'; 
        }
        
        if((abs_value > 79) && (abs_value <= 93)) {
            input_class = 'green_style_total'; 
        }

        if(abs_value > 93) {
            input_class = 'blue_style_total'; 
        }
       
        element.addClass(input_class);
    }


    CalculateSummary.prototype.setVarianceRangeStyleCalories = function(element, value) {
        var abs_value = Math.abs(value); 
        var input_class = '';
        element.removeClass('green_style orange_style red_style');
        if((abs_value >= 0) && (abs_value <= 30)) {
            input_class = 'green_style'; 
        }

        if((abs_value > 30) && (abs_value <= 50)) {
            input_class = 'orange_style'; 
        }

        if(abs_value > 50) {
            input_class = 'red_style'; 
        }
        element.addClass(input_class);
    }


    CalculateSummary.prototype.setVarianceRangeWater = function(element, value) {
        var abs_value = Math.abs(value); 
        var input_class = '';
        element.removeClass('green_style orange_style red_style');
        if((abs_value >= 0) && (abs_value <= 250)) {
            input_class = 'green_style'; 
        }

        if((abs_value > 250) && (abs_value <= 350)) {
            input_class = 'orange_style'; 
        }

        if(abs_value > 350) {
            input_class = 'red_style'; 
        }
        element.addClass(input_class);
    }



    CalculateSummary.prototype.setDailyVarianceWater = function() {
        var daily_total_water  = this.getDailyTotalWater();
        var target_kind = this.getDayKindPrefix();
        var dayly_target_water = this.getTargetPercentsValue('water', target_kind);
        var variance_water = this.round_2_sign(daily_total_water - dayly_target_water);


        var variance_daily_total_water_element = $("#variance_daily_total_water");
        variance_daily_total_water_element.val(variance_water);
        this.setVarianceRangeWater(variance_daily_total_water_element, variance_water);

    }

    CalculateSummary.prototype.setDailyTotalWater = function() {
        this.set_item_total(this.getDailyTotalWater(), 'daily_total_water');
    }

    CalculateSummary.prototype.getDailyTotalWater = function() {
        return (this.get_item_total('water') + this.get_item_total('previous_water'));
    }

    CalculateSummary.prototype.setVariance = function() {
        var variance_calories_element = $("#variance_calories");
        var variance_calories_value = this.calculateVariance('calories');
        variance_calories_element.val(variance_calories_value);

        this.setVarianceRangeStyleCalories(variance_calories_element, this.getCaloriesVariancePercents('calories'));
        this.setVarianceRangeStyleCalories($("#variance_calories_percents"), this.getCaloriesVariancePercents('calories'));

        $("#variance_energy").val(this.calculateVarianceEnergy());
    }

    CalculateSummary.prototype.setEnergyVariancePercents = function() {
        $("#variance_energy_percents").val(this.getEnergyVariancePercents());
    }

    CalculateSummary.prototype.setCaloriesVariancePercents = function() {
        $("#variance_calories_percents").val(this.getCaloriesVariancePercents('calories'));
    }

    CalculateSummary.prototype.getCaloriesVariancePercents = function(name) {
        var variance_calories = this.calculateVariance(name);
        var target_kind = this.getDayKindPrefix();
        var dayly_target_calories = this.getTargetPercentsValue(name, target_kind);

        return this.round_2_sign((variance_calories / dayly_target_calories)*100);
    }



    CalculateSummary.prototype.getEnergyVariancePercents = function(name) {
        var variance_energy = this.calculateVarianceEnergy();
        var daily_target_energy = this.getDailyTargetEnergy();
        return this.round_2_sign((variance_energy / daily_target_energy)*100);
    }



    CalculateSummary.prototype.calculateVarianceEnergy = function() {
        var daily_target_energy = this.getDailyTargetEnergy();
        var daily_totals_energy = this.get_item_total('meal_energy_total');
        return this.round_2_sign(daily_totals_energy - daily_target_energy);
    }


    CalculateSummary.prototype.getDailyTargetEnergy = function() {
        var target_kind = this.getDayKindPrefix();
        var cals_total = $("#" + target_kind + "total_cals").val();
        return cals_total * 4.184;
    }


    CalculateSummary.prototype.setVarianceGrams = function() {
        var variance_protein_grams_element = $("#variance_protein_grams");
        var variance_protein_grams_value = this.calculateVarianceGrams('protein');
        variance_protein_grams_element.val(variance_protein_grams_value);
        this.setVarianceRangeStylePRO_FATS_CARBS(variance_protein_grams_element, this.calculateVariancePercents('protein'));

        var variance_fats_grams_element = $("#variance_fats_grams");
        var variance_fats_grams_value = this.calculateVarianceGrams('fats');
        variance_fats_grams_element.val(variance_fats_grams_value);
        this.setVarianceRangeStylePRO_FATS_CARBS(variance_fats_grams_element, this.calculateVariancePercents('fats'));
        

        var variance_carbs_grams_element = $("#variance_carbs_grams");
        var variance_carbs_grams_value = this.calculateVarianceGrams('carbs');
        variance_carbs_grams_element.val(variance_carbs_grams_value);
        this.setVarianceRangeStylePRO_FATS_CARBS(variance_carbs_grams_element, this.calculateVariancePercents('carbs'));
        
    }


    CalculateSummary.prototype.setVariancePercents = function() {
        $("#variance_protein_percents").val(this.calculateVariancePercents('protein'));
        this.setVarianceRangeStylePRO_FATS_CARBS($("#variance_protein_percents"), this.calculateVariancePercents('protein'));
        
        $("#variance_fats_percents").val(this.calculateVariancePercents('fats'));
        this.setVarianceRangeStylePRO_FATS_CARBS($("#variance_fats_percents"), this.calculateVariancePercents('fats'));
        
        $("#variance_carbs_percents").val(this.calculateVariancePercents('carbs'));
        this.setVarianceRangeStylePRO_FATS_CARBS($("#variance_carbs_percents"), this.calculateVariancePercents('carbs'));
    }

    CalculateSummary.prototype.calculateVarianceGrams = function(name) {
        var total = this.get_item_total('meal_' + name + '_total');
        var target_kind = this.getDayKindPrefix();
        var target = this.getTargetGramsValue(name, target_kind);
        return this.round_2_sign(total - target);
    }

    CalculateSummary.prototype.calculateVariancePercents = function(name) {
        var grams_value = this.calculateVarianceGrams(name)
        var target_kind = this.getDayKindPrefix();
        var target = this.getTargetGramsValue(name, target_kind);
        return this.round_2_sign((grams_value / target) * 100);
    }

    CalculateSummary.prototype.calculateVariance = function(name) {
        var total = this.get_item_total('meal_' + name + '_total');
        var target_kind = this.getDayKindPrefix();
        var target = this.getTargetPercentsValue(name, target_kind);
        return this.round_2_sign(total - target);
    }



    CalculateSummary.prototype.setDailyTotalsGrams = function() {
        this.set_item_total(this.get_item_total('meal_protein_total'), 'daily_protein_grams');
        this.set_item_total(this.get_item_total('meal_fats_total'), 'daily_fats_grams');
        this.set_item_total(this.get_item_total('meal_carbs_total'), 'daily_carbs_grams');
        this.set_item_total(this.get_item_total('meal_calories_total'), 'daily_calories');
        this.set_item_total(this.get_item_total('meal_energy_total'), 'daily_energy');
        this.set_item_total(this.get_item_total('meal_saturated_fat_total'), 'daily_saturated_fat_grams');
        this.set_item_total(this.get_item_total('meal_sugars_total'), 'daily_total_sugars_grams');
        this.set_item_total(this.get_item_total('meal_sodium_total'), 'daily_sodium_grams');

    }
    
    CalculateSummary.prototype.setDailyTargetGrams = function() {
        
        var target_kind = this.getDayKindPrefix();
        this.setTargetGramsValue('daily_target_protein_grams', this.getTargetGramsValue('protein', target_kind));
        this.setTargetGramsValue('daily_target_fats_grams', this.getTargetGramsValue('fats', target_kind));
        this.setTargetGramsValue('daily_target_carbs_grams', this.getTargetGramsValue('carbs', target_kind));
    }
    
    
    CalculateSummary.prototype.setDailyTargetPercents = function() {
        var target_kind = this.getDayKindPrefix();
        this.setTargetPercentsValue('daily_target_protein_percents', this.getTargetPercentsValue('protein', target_kind));
        this.setTargetPercentsValue('daily_target_fats_percents', this.getTargetPercentsValue('fats', target_kind));
        this.setTargetPercentsValue('daily_target_carbs_percents', this.getTargetPercentsValue('carbs', target_kind));
    }
    
    CalculateSummary.prototype.setDailyTargetEnergy = function() {
        $("#daily_target_energy").val(this.getDailyTargetEnergy());
    }
    
    CalculateSummary.prototype.setDailyTargetCalories= function() {
        var target_kind = this.getDayKindPrefix();
        var dayly_target_calories = this.getTargetPercentsValue('calories', target_kind);
        $("#daily_target_calories").val(dayly_target_calories);
    }
    
    CalculateSummary.prototype.setDailyTargetWater= function() {
        var target_kind = this.getDayKindPrefix();
        var dayly_target_water = this.getTargetPercentsValue('water', target_kind);
        $("#daily_target_water").val(dayly_target_water);
    }


    CalculateSummary.prototype.setDailyTotalsPercents = function() {

        this.setUpDailyTotalValue('protein');
        this.setUpDailyTotalValue('fats');
        this.setUpDailyTotalValue('carbs');
    }

    CalculateSummary.prototype.setUpDailyTotalValue = function(name) {
        this.setDailyTotalValue(name, this.calculateDailyTotalValue(name));
    }

    CalculateSummary.prototype.setDailyTotalValue = function(name, value) {
        $("#daily_" + name + "_percents").val(value)
    }

    CalculateSummary.prototype.calculateDailyTotalValue = function(name) {
        var target_kind = this.getDayKindPrefix();
        var target_protein_grams = this.getTargetGramsValue(name, target_kind);
        var target_protein_percents= this.getTargetPercentsValue(name, target_kind);
        var daily_totals_grams = $("#daily_" + name + "_grams").val();

        var daily_total_percent_value = target_protein_percents / target_protein_grams * daily_totals_grams;
        return this.round_2_sign(daily_total_percent_value); 
    }


    CalculateSummary.prototype.getTargetGramsValue = function(name, target_kind) {
        return $("#" + target_kind + name + "_grams").val();
    }
    
    CalculateSummary.prototype.setTargetGramsValue = function(name, value) {
        return $("#"  + name ).val(value);
    }
    
        
    
    CalculateSummary.prototype.getTargetPercentsValue = function(name, target_kind) {
        return $("#" + target_kind + name).val();
    }
    
    CalculateSummary.prototype.setTargetPercentsValue = function(name, value) {
        return $("#"  + name ).val(value);
    }


    CalculateSummary.prototype.getDayKindPrefix = function() {
        var prefix;
        switch(this._activity_level_value) {
            case '1' :
                prefix = 'heavy_';
                break;
            case '2' :
                prefix = 'light_';
                break;
            case '3' :
                prefix = 'rest_';
                break;
                default :
                    break;
        }
        return prefix;
    }


    CalculateSummary.prototype.get_item_total = function(element) {
       var item_array = $("." +element);
       var sum = 0;
       item_array.each(function(){
           var value = parseFloat($(this).val());
           if(value > 0) {
              sum += parseFloat(value); 
           }

       });

       return this.round_2_sign(sum);
    }


    CalculateSummary.prototype.set_item_total = function(value, element) {
        $("#" + element).val(value);
    }

    CalculateSummary.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }
    
    
    CalculateSummary.prototype.connectMacronutrientScores = function() {
        
        var vpp = parseFloat($("#variance_protein_percents").val());
        var protein_graph_score = this.calculateGraphScore(vpp);
        
        
        var vcp = parseFloat($("#variance_carbs_percents").val());
        var carbs_graph_score = this.calculateGraphScore(vcp);
        
        var vfp = parseFloat($("#variance_fats_percents").val());
        var fats_graph_score = this.calculateGraphScore(vfp);
        
        

        var data = {};
        
        
        
        data.el = "#protein_score_graph";
        data.title = 'PROTEIN SCORE';
        data.width = '250px';
        data.level =  protein_graph_score + '%';
        $.gredient_graph(data);
        
        
        data.el = "#fat_score_graph";
        data.title = 'FATS SCORE';
        data.width = '250px';
        data.level = fats_graph_score + '%';
        $.gredient_graph(data);
        
        data.el = "#carbs_score_graph";
        data.title = 'CARBOHYDRATE SCORE';
        data.width = '250px';
        data.level = carbs_graph_score + '%';
        $.gredient_graph(data);
    }
    
    
    CalculateSummary.prototype.calculateGraphScore = function(vpp) {
  
        var result = 0;
        
        if(vpp < 200) {
            if(vpp > 0) {
                result = 100 + Math.abs(vpp) - ((1.5) * Math.abs(vpp));
            } else {
                result = 100 + vpp;
            }
        }
        
        return this.round_2_sign(result);
    }

    
    // Add the  function to the top level of the jQuery object
    $.calculateSummary = function(options) {

        var constr = new CalculateSummary(options);

        return constr;
    };

}));



