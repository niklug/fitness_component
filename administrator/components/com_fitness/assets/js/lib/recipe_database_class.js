(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
    function RecipeDatabase(options) {
        this.options = options;
    }

    RecipeDatabase.prototype.run = function() {
        this.setEventListeners();

        this.check_specific_gravity();
    }

    RecipeDatabase.prototype.setEventListeners = function() {
        var self = this;
        // input focus out events
        $("#jform_calories").live('focusout', function() {
            self.calculate_energy();
        });

        $("#jform_energy").live('focusout', function() {
            self.calculate_calories();
        });

        $("#jform_saturated_fat").live('focusout', function() {
            self.validate_saturated_fat();
        });

        $("#jform_fats").live('focusout', function() {
            self.validate_saturated_fat();
            self.validate_sum_100();
        });


        $("#jform_protein").live('focusout', function() {
            self.validate_sum_100();
        });

        $("#jform_carbs").live('focusout', function() {
            self.validate_sum_100();
            self.validate_sugars();
        });

        $("#jform_total_sugars").live('focusout', function() {
            self.validate_sugars();
        });


        $("#jform_measurement_unit").live('change', function() {
            var measurement_unit = $(this).find(':selected').val();
            self.set_measurement_unit(measurement_unit);
        });   

        $("#jform_specific_gravity").live('focusout', function() {
            var specific_gravity = self.parse_comma_number($(this).val());
            self.specific_gravity_set_grams(specific_gravity);
        });


        $("#enter_energy").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_energy');
        });

        $("#enter_protein").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_protein');
            self.validate_sum_100();
        });

        $("#enter_fats").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_fats');
            self.validate_saturated_fat();
            self.validate_sum_100();
        });


        $("#enter_saturated_fat").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_saturated_fat');
            self.validate_saturated_fat();
        });

        $("#enter_carbs").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_carbs');
            self.validate_sum_100();
            self.validate_sugars();
        });

        $("#enter_total_sugars").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_total_sugars');
            self.validate_sugars();
        });


        $("#enter_sodium").live('focusout', function() {
            self.set_converted_value($(this).val(), 'jform_sodium');
        });

        $("#enter_energy").live('focusout', function() {
            self.calculate_calories();
        });

        $("#jform_specific_gravity").live('focusout', function() {
            self.on_specific_gravity_change();
        });     
    }


    RecipeDatabase.prototype.validate_form = function() {
        var saturated_fat_error = this.validate_saturated_fat();
        var sum_100_error = this.validate_sum_100();
        var sugars_error = this.validate_sugars();
        if(saturated_fat_error && sum_100_error && sugars_error) {
            return true;
        }
        return false;
    }

    RecipeDatabase.prototype.validate_saturated_fat = function() {
        var saturated_fat = this.parse_comma_number($("#jform_saturated_fat").val());
        var total_fat = this.parse_comma_number($("#jform_fats").val());

        if(parseFloat(saturated_fat) > parseFloat(total_fat)) {
            $("#saturated_error").html('Saturated fat value must be less than or equal to the total fat value.')
            return false;
        } else {
            $("#saturated_error").html('');
            return true;
        } 
    }


    RecipeDatabase.prototype.validate_sum_100 = function() {
        var protein = this.parse_comma_number($("#jform_protein").val());
        var total_fat = this.parse_comma_number($("#jform_fats").val());
        var carbohydrate  = this.parse_comma_number($("#jform_carbs").val());
        var sum = parseFloat(protein) + parseFloat(total_fat) + parseFloat(carbohydrate);
        if(sum > 100) {
            $("#sum_100_error").html('Sum of proximates cannot exceed 100g.')
            return false;
        } else {
            $("#sum_100_error").html('');
            return true;
        } 
    }

    RecipeDatabase.prototype.validate_sugars = function() {
        var sugars = this.parse_comma_number($("#jform_total_sugars").val());
        var carbs = this.parse_comma_number($("#jform_carbs").val());
        if(parseFloat(sugars) > parseFloat(carbs)) {
            $("#sugars_error").html('Sugar value must be less than or equal to the carbohydrate value.')
            return false;
        } else {
            $("#sugars_error").html('');
            return true;
        }  
    }

    RecipeDatabase.prototype.check_specific_gravity = function() {
        var specific_gravity = this.parse_comma_number(this.options.specific_gravity);
        var measurement_unit =  $("#jform_measurement_unit").val();
        this.hide_left_column();
        $("#right_title").html("<b>Enter Nutrition Info</b><br/>(as on product label: “average per 100g”) ");
        if(specific_gravity || (measurement_unit == '2')) {
            $("#jform_measurement_unit").val('2');
            $("#jform_specific_gravity").val(specific_gravity);
            $("#measurement_unit_wrapper").show();
            this.set_measurement_unit('2');
            this.specific_gravity_set_grams(specific_gravity);
            this.show_left_column();
        }
    }

    RecipeDatabase.prototype.specific_gravity_set_grams = function(specific_gravity) {
        if(specific_gravity == 0) return;
        $("#specific_gravity_grams").val(this.round_2_sign(parseFloat(100/specific_gravity)));
    }

    RecipeDatabase.prototype.set_measurement_unit = function(measurement_unit) {
        $("#jform_calories, #jform_energy, #jform_protein, #jform_fats, #jform_saturated_fat, #jform_carbs, #jform_total_sugars, #jform_sodium, #enter_energy, #enter_protein, #enter_fats, #enter_saturated_fat, #enter_carbs, #enter_total_sugars, #enter_sodium").val('');
        if(measurement_unit == '2') {
            $("#measurement_unit_wrapper").show();
            this.show_left_column();
            $("#right_title").html("<b>Values as 100g Edible Portion (EP)</b><br/>(stored in nutrition database) ");
            $(".main_fields_wrapper").show();
            
            $("#jform_calories, #jform_energy, #jform_protein, #jform_fats, #jform_saturated_fat, #jform_carbs, #jform_total_sugars, #jform_sodium").attr('readonly','readonly');
            
        } else if(measurement_unit == '1') {
            $("#jform_specific_gravity").val('');
            $("#specific_gravity_grams").val('');
            $("#measurement_unit_wrapper").hide();
            this.hide_left_column();
            $("#right_title").html("<b>Enter Nutrition Info</b><br/>(as on product label: “average per 100g”) ");
            $(".main_fields_wrapper").show();
            
            $("#jform_calories, #jform_energy, #jform_protein, #jform_fats, #jform_saturated_fat, #jform_carbs, #jform_total_sugars, #jform_sodium").removeAttr('readonly');
        } else {
            $(".main_fields_wrapper").hide();
        }
    }

    RecipeDatabase.prototype.parse_comma_number = function(str) {
        return str.replace(',' ,'.');
    }

    RecipeDatabase.prototype.calculate_energy = function() {
        var calories = this.parse_comma_number($("#jform_calories").val());
        var energy = calories * 4.184;
        energy = this.round_2_sign(energy);
        $("#jform_energy").val(energy);
    }

    RecipeDatabase.prototype.calculate_calories = function(type) {
        var energy = this.parse_comma_number($("#jform_energy").val());;
        var calories = energy / 4.184;
        calories = this.round_2_sign(calories);
        $("#jform_calories").val(calories);
    }

    RecipeDatabase.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }
    //////////////////////////////

    RecipeDatabase.prototype.set_converted_value = function(value, field_id) {
        var specific_gravity_field = $("#jform_specific_gravity");
        
        specific_gravity_field.removeClass("red_style_border");
        
        var specific_gravity_grams = $("#specific_gravity_grams").val();
        if(!specific_gravity_grams) {
            specific_gravity_field.addClass("red_style_border");
            return;
        }
        var convertedValue = (value / 100) * specific_gravity_grams;
        convertedValue = this.round_2_sign(convertedValue);
        $("#" + field_id).val(convertedValue);
    }

    RecipeDatabase.prototype.hide_left_column = function() {
        $(".millilitres_column").hide();
    }

    RecipeDatabase.prototype.show_left_column = function() {
        $(".millilitres_column").show();
    }

    RecipeDatabase.prototype.on_specific_gravity_change = function() {
        this.set_converted_value($("#enter_energy").val(), 'jform_energy');

        this.set_converted_value($("#enter_protein").val(), 'jform_protein');

        this.set_converted_value($("#enter_fats").val(), 'jform_fats');

        this.set_converted_value($("#enter_saturated_fat").val(), 'jform_saturated_fat');

        this.set_converted_value($("#enter_carbs").val(), 'jform_carbs');

        this.set_converted_value($("#enter_total_sugars").val(), 'jform_total_sugars');

        this.set_converted_value($("#enter_sodium").val(), 'jform_sodium');

        this.calculate_calories();
    }

    // Add the  function to the top level of the jQuery object
    $.recipe_database = function(options) {
        
        return new RecipeDatabase(options);
    };
    
        
}));
