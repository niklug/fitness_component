/*
 * Class provide adding Daily Targed block
 */
// Constructor
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    function MacronutrientTargets(options, type, title) {
        this.options = options;
        this.type = type;
        this.title = title;
        this.options.main_wrapper = $(this.options.targets_main_wrapper);
    }

    // Controller
    MacronutrientTargets.prototype.run = function() {
        if(! this.options.nutrition_plan_id) return;
        var self = this;
        this.getTargetsData(function(output) {
            if(!output) {
                output = self.options.empty_html_data;
            }
            var html = self.generateHtml(output);
            self.options.main_wrapper.append(html);
            self.setEventListeners();
            self.calculateProteinFields(self._protein);
            self.calculateFatsFields(self._fats);
            self.calculateCarbsFields(self._carbs);
            self.calculate_totals();
        });


    }

    MacronutrientTargets.prototype.setEventListeners = function() {

        this._calories = $("#" + this.type + "_calories");
        this._water = $("#" + this.type + "_water");
        this._protein = $("#" + this.type + "_protein");
        this._protein_grams = $("#" + this.type + "_protein_grams");
        this._protein_cals = $("#" + this.type + "_protein_cals");
        this._fats = $("#" + this.type + "_fats");
        this._fats_grams = $("#" + this.type + "_fats_grams");
        this._fats_cals = $("#" + this.type + "_fats_cals");
        this._carbs = $("#" + this.type + "_carbs");
        this._carbs_grams = $("#" + this.type + "_carbs_grams");
        this._carbs_cals = $("#" + this.type + "_carbs_cals");

        var self = this;

        // calculate grams and cals values
        $(this._calories).on('focusout', function(){
            self.calculateProteinFields(self._protein);
            self.calculateFatsFields(self._fats);
            self.calculateCarbsFields(self._carbs);
        });


        $(this._protein).on('focusout', function(){
            self.calculateProteinFields($(this));
        });

        $(this._fats).on('focusout', function(){
            self.calculateFatsFields($(this));
        });

        $(this._carbs).on('focusout', function(){
            self.calculateCarbsFields($(this));
        });
    }

    MacronutrientTargets.prototype.saveTargetsData = function(handleData) {
        var data = {
            'nutrition_plan_id' : this.options.nutrition_plan_id,
            'type' : this.type,
            'calories' : this._calories.val(),
            'water' : this._water.val(),
            'protein' : this._protein.val(),
            'fats' : this._fats.val(),
            'carbs' : this._carbs.val()
        };

        var data_encoded = JSON.stringify(data);
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                 view : 'nutrition_plan',
                 format : 'text',
                 task : 'saveTargetsData',
                 data_encoded : data_encoded
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response.status.success);
               },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error saveTargetsData");
            }
        });
    }

    MacronutrientTargets.prototype.getTargetsData = function(handleData) {
        var data = {
            'nutrition_plan_id' : this.options.nutrition_plan_id,
            'type' : this.type
        };

        var data_encoded = JSON.stringify(data);
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                 view : 'nutrition_plan',
                 format : 'text',
                 task : 'getTargetsData',
                 data_encoded : data_encoded
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response.data);
               },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error getTargetsData");
            }
        });
    }

    MacronutrientTargets.prototype.calculateProteinFields = function(o) {
        this.calculateTargetGrams(o, this._protein_grams, this.options.protein_grams_coefficient);
        this.calculateTargetCals(o, this._protein_cals);
        this.validateSum100();
        this.calculate_totals();
    }

    MacronutrientTargets.prototype.calculateFatsFields = function(o) {
        this.calculateTargetGrams(o, this._fats_grams, this.options.fats_grams_coefficient);
        this.calculateTargetCals(o, this._fats_cals);
        this.validateSum100();
        this.calculate_totals();
    }


    MacronutrientTargets.prototype.calculateCarbsFields = function(o) {
        this.calculateTargetGrams(o, this._carbs_grams, this.options.carbs_grams_coefficient);
        this.calculateTargetCals(o, this._carbs_cals);
        this.validateSum100();
        this.calculate_totals();
    }


    MacronutrientTargets.prototype.validateSum100 = function() {
        var protein = this._protein.val();
        var fats = this._fats.val();
        var carbohydrate  = this._carbs.val();

        var sum = parseFloat(protein) + parseFloat(fats) + parseFloat(carbohydrate);

        if((sum != 100) && sum) {
            $("#" + this.type +  "sum_100_error").html('Protein, Fats & Carbs MUST equal (=) 100%');
            return false;
        } else {
            $("#" + this.type +  "sum_100_error").html('');
            return true;
        } 
    }

    MacronutrientTargets.prototype.calculateTargetGrams = function(o, destination, coefficient) {
        var calories = this._calories.val();
        if(!calories) return;
        var target_percent = parseFloat(o.val()) * 0.01;
        if(!target_percent) return;
        var grams_value = parseFloat(calories) * (target_percent/coefficient);
        destination.val(this.round_2_sign(grams_value));
    }


    MacronutrientTargets.prototype.calculateTargetCals = function(o, destination) {
        var calories = this._calories.val();
        if(!calories) return;
        var target_percent = parseFloat(o.val()) * 0.01;
        if(!target_percent) return;
        var cals_value = parseFloat(calories) * target_percent;
        destination.val(this.round_2_sign(cals_value));
    }


    MacronutrientTargets.prototype.calculate_totals = function() {
        this.set_item_total(this.get_item_total(this.type + '_percent_value'), this.type + '_total');
        this.set_item_total(this.get_item_total(this.type + '_grams_value'), this.type + '_total_grams');
        this.set_item_total(this.get_item_total(this.type + '_cals_value'), this.type + '_total_cals');
    }

    MacronutrientTargets.prototype.get_item_total = function(element) {
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


    MacronutrientTargets.prototype.set_item_total = function(value, element) {
        $("#" + element).val(value);
    }


    MacronutrientTargets.prototype.generateHtml = function(o) {
        
        var anable_readonly = '';
        if(this.options.readonly) {
            anable_readonly = 'readonly';
        }
        
        var html = '<fieldset id="' + this.type + 'fieldset"  class="adminform">';
        html += '<legend>' + this.title + '</legend>';
        html += '<table class="nutrition_targets_table" width="100%">';

        html += '<tbody>';
        html += '<tr>';
        html += '<td>';
        html += 'Calorie Target';
        html += '</td>';

        html += '<td>';
        html += '<input ' + anable_readonly + ' type="text" value="' + o.calories + '" id="' + this.type + '_calories" class="required  validate-numeric" />';
        html += '</td>'

        html += '<td>';
        html += 'Calories';
        html += '</td>';

        html += '<td>';
        html += 'Water Target';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + '  type="text" value="' + o.water + '" id="' + this.type + '_water" class="required  validate-numeric" />';
        html += '</td>';

        html += '<td>';
        html += 'millilitres';
        html += '</td>';

        html += '<td colspan="3">';
        html += '</td>';
        html += '</tr>';


        html += '<tr>';
        html += '<td>';
        html += 'Macronutrients Targets';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + ' type="text" value="' + o.protein + '" id="' + this.type + '_protein" class="required  validate-numeric ' + this.type + '_percent_value" />';
        html += '</td>'

        html += '<td>';
        html += '(%) Protein';
        html += '</td>';

        html += '<td>';
        html += 'Macronutrients Targets';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + '  type="text" value="" id="' + this.type + '_protein_grams" readonly class="' + this.type + '_grams_value" />';
        html += '</td>';

        html += '<td>';
        html += '(grams) Protein';
        html += '</td>';

        html += '<td>';
        html += 'Macronutrients Targets';
        html += '</td>';
        html += '<td>';
        html += '<input  ' + anable_readonly + '  type="text" value="" id="' + this.type + '_protein_cals" readonly class="' + this.type + '_cals_value" />';
        html += '</td>'

        html += '<td>';
        html += '(cals) Protein';
        html += '</td>'
        html += '</tr>';


        html += '<tr>';
        html += '<td>';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + '  type="text" value="' + o.fats + '" id="' + this.type + '_fats" class="required  validate-numeric ' + this.type + '_percent_value" />';
        html += '</td>'

        html += '<td>';
        html += '(%) Fats';
        html += '</td>';

        html += '<td>';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + '  type="text" value="" id="' + this.type + '_fats_grams" readonly class="' + this.type + '_grams_value" />';
        html += '</td>';

        html += '<td>';
        html += '(grams) Fats';
        html += '</td>';

        html += '<td>';
        html += '</td>';
        html += '<td>';
        html += '<input  ' + anable_readonly + '  type="text" value="" id="' + this.type + '_fats_cals"readonly class="' + this.type + '_cals_value" />';
        html += '</td>'

        html += '<td>';
        html += '(cals) Fats';
        html += '</td>'
        html += '</tr>';


        html += '<tr>';
        html += '<td>';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + ' type="text" value="' + o.carbs + '" id="' + this.type + '_carbs" class="required  validate-numeric ' + this.type + '_percent_value" />';
        html += '</td>'

        html += '<td>';
        html += '(%) Carbohydrates';
        html += '</td>';

        html += '<td>';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + ' type="text" value="" id="' + this.type + '_carbs_grams" readonly class="' + this.type + '_grams_value" />';
        html += '</td>';

        html += '<td>';
        html += '(grams) Carbohydrates';
        html += '</td>';

        html += '<td>';
        html += '</td>';
        html += '<td>';
        html += '<input  ' + anable_readonly + ' type="text" value="" id="' + this.type + '_carbs_cals" readonly class="' + this.type + '_cals_value" />';
        html += '</td>'

        html += '<td>';
        html += '(cals) Carbohydrates';
        html += '</td>'
        html += '</tr>';
        html += '</tbody>';


        html += '<tfoot>';
        html += '<tr>';
        html += '<td>';
        html += '</td>';

        html += '<td>';
        html += '<input  ' + anable_readonly + ' type="text" value="" id="' + this.type + '_total" readonly />';
        html += '</td>'

        html += '<td>';
        html += '(%) TOTAL';
        html += '</td>';

        html += '<td>';
        html += '</td>';

        html += '<td>';
        html += '<input ' + anable_readonly + '  type="text" value="" id="' + this.type + '_total_grams" readonly />';
        html += '</td>';

        html += '<td>';
        html += '(grams) TOTAL';
        html += '</td>';

        html += '<td>';
        html += '</td>';
        html += '<td>';
        html += '<input  ' + anable_readonly + ' type="text" value="" id="' + this.type + '_total_cals" readonly />';
        html += '</td>'

        html += '<td>';
        html += '(cals) TOTAL';
        html += '</td>'
        html += '</tr>';      
        html += '</tfoot>';

        html += '</table>';
        html += '<div style="color:red;" id="' + this.type + 'sum_100_error"></div>';
        html += '</fieldset>'; 
        return html;
    }


    MacronutrientTargets.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }
    
        // Add the  function to the top level of the jQuery object
    $.macronutrientTargets = function(options, type, title) {

        var constr = new MacronutrientTargets(options, type, title);

        return constr;
    };
}));