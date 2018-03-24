/*
 * generate meals blocks
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
    function NutritionMeal(options, item_description_options, nutrition_comment_options) {
        this.options = options;
        this.item_description_options = item_description_options;
        this.nutrition_comment_options = nutrition_comment_options;
    }


    NutritionMeal.prototype.run = function() {
        var activity_level = this.options.activity_level;
        if ($(activity_level +":checked").val()) {
            this.options.add_meal_button.show();
        }
        this.setEventListeners();
    }
    
    NutritionMeal.prototype.CalculateTotalsWithDelay = function(meal_id) {
        var meal_description = $.itemDescription(this.item_description_options, 'meal', 'MEAL ITEMS', meal_id);
        return meal_description.CalculateTotalsWithDelay(meal_id);
    }
     

    NutritionMeal.prototype.setEventListeners = function() {
        var self = this;
        
        // on add meal click
        if(this.options.read_only == false) {
            $("#add_plan_meal").die().live('click', function() {
                var meal_html = self.generateHtml(self.options.meal_obj);
                self.options.main_wrapper.append(meal_html);
                self.attachDateTimeListener();
            })

            // on Level of activity  choose
            $(this.options.activity_level).die().live('click', function() {
                self.options.add_meal_button.show();
            })


            // on save meal click
            $(".save_plan_meal").die().live('click', function() {
                var closest_table = $(this).closest("table");
                var data =  self.validateFields(closest_table);
                
                if(self.options.import_date) {
                    var date_field_value = $(self.options.import_date_source).val();
                    closest_table.find('.meal_date').val(date_field_value);
                }
                
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
            $(".delete_plan_meal").die().live('click', function() {
                var closest_table = $(this).closest("table");
                var id = closest_table.attr('data-id');
                self.deletePlanMeal(id, function(output) {
                    closest_table.parent().remove();
                });
            })
        }
        // populate meals html on document load
        this.populateMealsLogic();

        //

        $(".meal_quantity_input").live('focusout', function() {
            var closest_table = $(this).closest("table");
            var meal_id = closest_table.parent().attr('data-id');
            self.CalculateTotalsWithDelay([meal_id]);
        })


    }

    NutritionMeal.prototype.populateMealsLogic = function() {
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
            
            // clear previous interval
            if(typeof window.meals_interval !== 'undefined') {
                clearInterval(window.meals_interval);
            }

            // calculate totals on load
            window.meals_interval = setInterval(function() {
                self.CalculateTotalsWithDelay(meal_ids);
            }, 2000);
        });
    }

    NutritionMeal.prototype.generateHtml = function(o) {
        var read_only_attr = '';
        if(this.options.read_only == true) {
            read_only_attr = 'readonly';
        }
        var meal_id = o.id;
        var html = '';
        html += '<div data-id="' + meal_id + '" id="meal_wrapper_' + meal_id + '">';
        html += '<hr>';
        html += '<table  data-id="' + meal_id + '" width="100%">';
        html += '<tr>';

        html += '<td style="text-align:right;">';
        html += '<h6 style="font-size:10px;display:inline-block;">WHAT TIME WAS THIS MEAL CONSUMED?</h6>';
        
        var date_field_type = 'text';
        var date_field_value = (o.meal_time).substring(0, 10);

        if(this.options.import_date) {
            date_field_value = $(this.options.import_date_source).val();
            date_field_type = 'hidden';
        }

        html += '</td>';
        html += '<td width="50" style="text-align:center;">';
        html += '<input  size="5" type="' + date_field_type + '"  class="meal_date required" value="' + date_field_value + '" readonly>';
        
        html += '<input style="width:43px;" ' + read_only_attr + ' size="5" type="text"  class="meal_time required " value="' + (o.meal_time).substring(11, 16) + '">';
        
        html += '</td>';
        
        html += '<td width="40">';
        html += '<span class="grey_title">hh:mm</span>';

        html += '</td>';
        html += '</tr>';
        
        html += '<tr>';
        html += '<td style="text-align:right;" class="meal_center_desc">';
        html += '<h6 style="font-size:10px;display:inline-block;">HOW MUCH WATER WAS CONSUMED WITH THIS MEAL?</h6>';

        html += '</td>';
        html += '<td style="text-align:center;">';
        html += '<input  ' + read_only_attr + ' size="5" type="text"  class="required water" value="' + o.water + '">';
        html += '</td>';
        
        html += '<td >';
        html += '<span class="grey_title">millilitres</span>';
        html += '</td>';
        html += '</tr>';

        html += '<tr>';
        html += '<td style="text-align:right;">';
        html += '<h6 style="font-size:10px;display:inline-block;">HOW MUCH WATER WAS CONSUMED BEFORE THIS MEAL - BUT AFTER THE PREVIOUS MEAL? (WORKOUT / TRAINING INCLUSIVE)</h6>';
        
        html += '</td>';
        html += '<td style="text-align:center;">';
        html += '<input ' + read_only_attr + '  size="5" type="text"  class="required previous_water" value="' + o.previous_water + '">';

        html += '</td>';
        
        html += '<td >';
        html += '<span class="grey_title">millilitres</span>';
        html += '</td>';
        html += '</tr>';
        
       
        if(this.options.read_only == false) {
            html += '<tr>';
            html += '<td style="text-align:right;">';
            html += '<h6 style="font-size:10px;display:inline-block;">SAVE THIS INFORMATION TO CONTINUE ... (or delete this entry)</h6>';
            html += '</td>';
            html += '<td style="text-align:center;">';
            html += '<input title="Save/Update Meal" class="save_plan_meal " type="button"  value="Save">';
            html += '</td>';
            html += '<td >';
            html += '<a href="javascript:void(0)" class="delete_plan_meal" title="Delete Meal"></a>';
            html += '</td>';
            html += '</tr>';
        }
        
        
        html += '<tr>';
        html += '<td style="text-align:right;"  class="error_wrapper" style="color:red">';
        html += '</td>';

        html += '</tr>';
        html += '</table>';

        if(meal_id) {
            var meal_description = $.itemDescription(this.item_description_options, 'meal', 'MEAL ITEMS', meal_id);
            html += meal_description.run();
            html += $.itemDescription(this.item_description_options, 'supplement', 'SUPPLEMENTS', meal_id).run();
            html += $.itemDescription(this.item_description_options, 'drinks', 'DRINKS & LIQUIDS', meal_id).run();
            
            html += meal_description.totalsHtml(meal_id);
            
            html += '<div class="clr"></div>';
            html += '<br/>';

            html += $.comments(this.nutrition_comment_options, this.nutrition_comment_options.item_id, meal_id).run();

        }
        html += '<div class="clr"></div>';
        if(meal_id){
            html += '<input id="add_comment_' + meal_id + '" class="" type="button" value="Add Comment" >';
        }
        html += '<div class="clr"></div>';
        html += '</div>'; 

        return html;
    }
    
    


    NutritionMeal.prototype.savePlanMeal = function(data, handleData) {
        var table = this.options.db_table;
        var meal_encoded = JSON.stringify(data);
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'savePlanMeal',
                meal_encoded : meal_encoded,
                table : table
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
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
        var table = this.options.db_table;
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'deletePlanMeal',
                id : id,
                table : table
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
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
        var table = this.options.db_table;
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
                nutrition_plan_id : nutrition_plan_id,
                table : table
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
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
        
        $(".meal_date, .meal_time, .water, .previous_water").removeClass("red_style_border");

        var data = {
            'meal_time' : meal_time,
            'water' : water,
            'previous_water' : previous_water
        }

        if(!date) {
            closest_table.find(".meal_date").addClass("red_style_border");
            //error_wrapper.html('Date is empty!');
            result = false;               
        }


        if(!this.validateTime(time)) {
            
            closest_table.find(".meal_time").addClass("red_style_border");
            //error_wrapper.html('Wrong Meal Time!');
            result = false;
        }


        if(!water) {
            closest_table.find(".water").addClass("red_style_border");
            //error_wrapper.html('Water Value Empty!');
            result = false;
        }

        if(!this.validateFloat(water)) {
            closest_table.find(".water").addClass("red_style_border");
            //error_wrapper.html('Wrong Water Value!');
            result = false;
        }
        
        if(!previous_water) {
             closest_table.find(".previous_water").addClass("red_style_border");
            //error_wrapper.html('Previous Water Value Empty!');
            result = false;
        }

        if(!this.validateFloat(previous_water)) {
            closest_table.find(".previous_water").addClass("red_style_border");
            //error_wrapper.html('Wrong Previous Water Value!');
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


    
        // Add the ItemDescription function to the top level of the jQuery object
    $.nutritionMeal = function(options, item_description_options, nutrition_comment_options) {

        var constr = new NutritionMeal(options, item_description_options, nutrition_comment_options);

        return constr;
    };

}));