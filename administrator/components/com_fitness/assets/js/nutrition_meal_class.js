/*
 * generate meals blocks
 */
function NutritionMeal(options) {
    this.options = options;
}


NutritionMeal.prototype.run = function() {
    var activity_level = this.options.activity_level;
    if ($(activity_level +":checked").val()) {
        this.options.add_meal_button.show();
    }
    this.setEventListeners();
}

NutritionMeal.prototype.setEventListeners = function() {
    var self = this;
    // on add meal click
    $("#add_plan_meal").on('click', function() {
        var meal_html = self.generateHtml(self.options.meal_obj);
        self.options.main_wrapper.append(meal_html);
        self.attachDateTimeListener();
    })

    // on Level of activity  choose
    $(this.options.activity_level).on('click', function() {
        self.options.add_meal_button.show();
    })


    // on save meal click
    $(".save_plan_meal").live('click', function() {
        var closest_table = $(this).closest("table");
        var data =  self.validateFields(closest_table);

        if(!data) return;

        data.id = closest_table.attr('data-id');
        data.nutrition_plan_id = self.options.nutrition_plan_id;
        //console.log(data);
        self.savePlanMeal(data, function(output) {
            closest_table.attr('data-id', output.inserted_id);
            data.id = output.inserted_id;
            var html = self.generateHtml(data);
            //console.log(html);
            closest_table.parent().replaceWith(html);
            self.attachDateTimeListener();
            self.CalculateTotalsWithDelay([output.inserted_id]);
        });
    })

    // on delete meal click
    $(".delete_plan_meal").live('click', function() {
        var closest_table = $(this).closest("table");
        var id = closest_table.attr('data-id');
        self.deletePlanMeal(id, function(output) {
            closest_table.parent().remove();
        });
    })

    // populate meals html on document load
    this.papulateMealsLogic();

    //
    
    $(".meal_quantity_input").live('focusout', function() {
        var closest_table = $(this).closest("table");
        var meal_id = closest_table.parent().attr('data-id');
        self.CalculateTotalsWithDelay([meal_id]);
    })


}

NutritionMeal.prototype.papulateMealsLogic = function() {
    var self = this;
    this.populatePlanMeal(function(output) {
        if(!output) return;
        var html = '';
        var meal_ids = [];
        var i = 0;
        output.each(function(meal){
            html += self.generateHtml(meal);
            meal_ids[i] = meal.id;
            i++;
        });
        self.options.main_wrapper.html(html);

        self.attachDateTimeListener();

        // calculate totals on load
        setInterval(function() {
            self.CalculateTotalsWithDelay(meal_ids);
        }, 2000);
    });
}

NutritionMeal.prototype.generateHtml = function(o) {
    var meal_id = o.id;
    var html = '';
    html += '<div data-id="' + meal_id + '" id="meal_wrapper_' + meal_id + '">';
    html += '<hr>';
    html += '<table data-id="' + meal_id + '" width="100%">';
    html += '<tr>';

    html += '<td>';
    html += 'MEAL TIME';
    html += '</td>';

    html += '<td>';
    html += '<input  size="5" type="text"  class="meal_date required" value="' + (o.meal_time).substring(0, 10) + '" readonly>';
    html += '<input  size="4" type="text"  class="meal_time required " value="' + (o.meal_time).substring(11, 16) + '">';
    html += '</td>';

    html += '<td>';
    html += 'How much water did you drink only with THIS meal?';
    html += '</td>';

    html += '<td>';
    html += '<input  size="5" type="text"  class="required water" value="' + o.water + '">';
    html += '</td>';

    html += '<td>';
    html += 'millilitres';
    html += '</td>';

    html += '<td>';
    html += '<input title="Save/Update Meal" class="save_plan_meal " type="button"  value="Save">';
    html += '</td>';

    html += '<td>';
    html += '<a href="javascript:void(0)" class="delete_plan_meal" title="Delete Meal"></a>';
    html += '</td>';
    html += '</tr>';

    html += '<tr>';
    html += '<td>';
    html += '</td>';

    html += '<td>';
    html += '</td>';

    html += '<td>';
    html += 'How much water did you drink between (before) this meal and your last meal? (workout/training inclusive)';
    html += '</td>';

    html += '<td>';
    html += '<input  size="5" type="text"  class="required previous_water" value="' + o.previous_water + '"> ';
    html += '</td>';


    html += '<td>';
    html += 'millilitres';
    html += '</td>';

    html += '<td class="error_wrapper" style="color:red" colspan="2">';
    html += '</td>';

    html += '</tr>';
    html += '</table>';


    if(meal_id) {
        html += new ItemDescription(item_description_options, 'meal', 'MEAL ITEM DESCRIPTION', meal_id).run();
        html += new ItemDescription(item_description_options, 'supplement', 'SUPPLEMENT ITEM DESCRIPTION', meal_id).run();
        html += new ItemDescription(item_description_options, 'drinks', 'DRINKS & LIQUIDS ITEM DESCRIPTION', meal_id).run();

        html += '<table id="totals_' + meal_id + '" width="100%">';
        html += '<tr style="text-align:center;">';
        html += '<th width="515"></th>';
        html += '<th></th>';
        html += '<th>PRO (g)</th>';
        html += '<th>FAT (g)</th>';
        html += '<th>CARB (g)</th>';
        html += '<th>CALS</th>';
        html += '<th>ENRG (kJ)</th>';
        html += '<th>FAT, SAT (g)</th>';
        html += '<th>SUG (g)</th>';
        html += '<th>SOD (mg)</th>';
        html += '<th></th>';
        html += '</tr>';
        html += '<tr >';
        html += '<td width="515" ></td>'
        html += '<td><b>TOTALS</b></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_protein_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_fats_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_carbs_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_calories_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_energy_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_saturated_fat_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_total_sugars_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text"  id="meal_sodium_input_total_' + meal_id + '" value=""></td>';
        html += '<td width="40px"></td>';
        html += '</tr>';
        html += '</table>';
        html += '<div class="clr"></div>';
        html += '<br/>';
    
        html += new NutritionComment(nutrition_comment_options, this.options.nutrition_plan_id, meal_id).run();

    }
    html += '<div class="clr"></div>';
    html += '<input id="add_comment_' + meal_id + '" class="" type="button" value="Add Comment" >';
    html += '<div class="clr"></div>';
    html += '</div>'; 

    return html;
}


NutritionMeal.prototype.savePlanMeal = function(data, handleData) {
    var meal_encoded = JSON.stringify(data);
    var url = this.options.fitness_administration_url;
    $.ajax({
        type : "POST",
        url : url,
        data : {
            view : 'nutrition_plan',
            format : 'text',
            task : 'savePlanMeal',
            meal_encoded : meal_encoded
        },
        dataType : 'json',
        success : function(response) {
            if(!response.status.IsSuccess) {
                alert(response.status.Msg);
                return;
            }
            handleData(response);
          },
        error: function(XMLHttpRequest, textStatus, errorThrown)
        {
            alert("error savePlanMeal");
        }
    }); 
}

NutritionMeal.prototype.deletePlanMeal = function(id, handleData) {
    var url = this.options.fitness_administration_url;
    $.ajax({
        type : "POST",
        url : url,
        data : {
            view : 'nutrition_plan',
            format : 'text',
            task : 'deletePlanMeal',
            id : id
        },
        dataType : 'json',
        success : function(response) {
            if(!response.status.IsSuccess) {
                alert(response.status.Msg);
                return;
            }
            handleData(response);
          },
        error: function(XMLHttpRequest, textStatus, errorThrown)
        {
            alert("error deletePlanMeal");
        }
    }); 
}


NutritionMeal.prototype.populatePlanMeal =  function(handleData) {
    var url = this.options.fitness_administration_url;
    var nutrition_plan_id = this.options.nutrition_plan_id;
    if(!nutrition_plan_id) return;
    $.ajax({
        type : "POST",
        url : url,
        data : {
            view : 'nutrition_plan',
            format : 'text',
            task : 'populatePlanMeal',
            nutrition_plan_id : nutrition_plan_id
        },
        dataType : 'json',
        success : function(response) {
            if(!response.status.IsSuccess) {
                alert(response.status.Msg);
                return;
            }
            handleData(response.data);
            },
        error: function(XMLHttpRequest, textStatus, errorThrown)
        {
            alert("error populatePlanMeal");
        }
    }); 
}

NutritionMeal.prototype.validateTime = function(time) {
    var result = false, m;
    var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
    if ((m = time.match(re))) {
        result = (m[1].length == 2 ? "" : "0") + m[1] + ":" + m[2];
    }
    return result;
}

NutritionMeal.prototype.validateFloat = function(value) {
    return (value.match(/^-?\d*(\.\d+)?$/));
}

NutritionMeal.prototype.validateFields = function(closest_table) {
    var result = true;
    var error_wrapper = closest_table.find(".error_wrapper");
    error_wrapper.html('');

    var date = closest_table.find(".meal_date").val();
    var time = closest_table.find(".meal_time").val();
    var meal_time = date + ' ' + time;
    var water = closest_table.find(".water").val();
    var previous_water = closest_table.find(".previous_water").val();

    var data = {
        'meal_time' : meal_time,
        'water' : water,
        'previous_water' : previous_water
    }

    if(!date) {
        error_wrapper.html('Dete is empty!');
        result = false;               
    }

    if(!this.validateTime(time)) {
        error_wrapper.html('Wrong Meal Time!');
        result = false;
    }

    if(!water) {
        error_wrapper.html('Water Value Empty!');
        result = false;
    }

    if(!this.validateFloat(water)) {
        error_wrapper.html('Wrong Water Value!');
        result = false;
    }

    if(!previous_water) {
        error_wrapper.html('Previous Water Value Empty!');
        result = false;
    }

    if(!this.validateFloat(previous_water)) {
        error_wrapper.html('Wrong Previous Water Value!');
        result = false;
    }

    if(result) {
        result = data;
    }
    return result;
}


NutritionMeal.prototype.attachDateTimeListener = function() {
    $('.meal_time').timepicker({ 'timeFormat': 'H:i', 'step': 15 });
    $( ".meal_date" ).datepicker({ dateFormat: "yy-mm-dd" });
}


NutritionMeal.prototype.calculate_totals = function(meal_id) {


   this.set_item_total(this.get_item_total('meal_protein_input_' + meal_id), 'meal_protein_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_fats_input_' + meal_id), 'meal_fats_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_carbs_input_' + meal_id), 'meal_carbs_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_calories_input_' + meal_id), 'meal_calories_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_energy_input_' + meal_id), 'meal_energy_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_saturated_fat_input_' + meal_id), 'meal_saturated_fat_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_total_sugars_input_' + meal_id), 'meal_total_sugars_input_total_' + meal_id);

   this.set_item_total(this.get_item_total('meal_sodium_input_' + meal_id), 'meal_sodium_input_total_' + meal_id);

}

NutritionMeal.prototype.get_item_total = function(element) {
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


NutritionMeal.prototype.set_item_total = function(value, element) {
    $("#" + element).val(value);
}

NutritionMeal.prototype.round_2_sign = function(value) {
    return Math.round(value * 100)/100;
}


NutritionMeal.prototype.CalculateTotalsWithDelay = function(meal_ids) {
    var self = this;
    console.log(meal_ids);
    setTimeout(
        function(){
            meal_ids.each(function(meal_id){
                self.calculate_totals(meal_id);
            });
        },
        2000
    );
}
