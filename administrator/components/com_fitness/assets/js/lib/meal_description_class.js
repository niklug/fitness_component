/*
 * Provides search ingredients in database and calculations
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
    function ItemDescription(options, type, title, meal_id) {
        this.options = options;
        this._type = type;
        this._title = title;
        this._meal_id = meal_id;

        this._description_id = '_' + this._type + '_' + this._meal_id;

        this._doneTypingInterval = 1000;
        
        this.ingredient_model = function() {
            var ingredient_model = '';
            if(typeof this.options.ingredient_model !== 'undefined') {
                ingredient_model = this.options.ingredient_model;
            }
            return ingredient_model;
        };

    }

    ItemDescription.prototype.run = function() {
        
        var item_description_html = this.generateHtml();
        //this.options.main_wrapper.append(item_description_html);
        this.setEventListeners();

        return item_description_html;
        
        
    }

    ItemDescription.prototype.setEventListeners = function() {
        var self = this;
        //$("#add_item"+ self._description_id).die()
        if(this.options.read_only == false) {
            $("#add_item"+ self._description_id).die().live('click', function() {
                var tr_html = self.createIngredientTR(self.options.ingredient_obj);
                $("#meals_content" + self._description_id).append(tr_html);
                $("#meals_content" + self._description_id).find("tr:last td:first input").focus();

            });

            $("#add_recipe"+ self._description_id).die().live('click', function() {
                var recipesListHtml = self.recipesListHtml();
                $("body").append(recipesListHtml);
                $("body").css('overflow', 'hidden');
            });
            
            $("#save_as_recipe"+ self._description_id).die().live('click', function() {
                var meal_id = $(this).attr('data-meal_id');
                
                var data = {};
                
                data.meal_id = meal_id;
                
                data.description_id = self._description_id;
                
                data.type = self._type;
                
                self.getRecipeTypesAndVariations(data);

            });
            
            
            $("#save_as_recipe_cancel_"+ self._description_id).die().live('click', function() {
                $("#save_as_recipe_form_" + self._description_id).empty();
            });
            
            $("#save_as_recipe_save_"+ self._description_id).die().live('click', function() {
                $("#save_as_recipe_serves_" + self._description_id).removeClass("red_style_border");
                $("#save_as_recipe_recipe_name_" + self._description_id).removeClass("red_style_border");
                $("#save_as_recipe_recipe_type_" + self._description_id).removeClass("red_style_border");
                
                var meal_id = $(this).attr('data-meal_id');
                var type = $(this).attr('data-type');
                
                
                var recipe_name = $("#save_as_recipe_recipe_name_" + self._description_id).val();
                
                if(!recipe_name) {
                    $("#save_as_recipe_recipe_name_" + self._description_id).addClass("red_style_border");
                    return false;
                }
                
                
                var number_serves = parseInt($("#save_as_recipe_serves_" + self._description_id).val());

                if(!number_serves) {
                    $("#save_as_recipe_serves_" + self._description_id).addClass("red_style_border");
                    return false;
                }
               
                
                var recipe_type = $("#save_as_recipe_recipe_type_" + self._description_id).val();
                
                if(!recipe_type) {
                    $("#save_as_recipe_recipe_type_" + self._description_id).addClass("red_style_border");
                    return false;
                }
                
                var data = {};
                
                data.recipe_name = recipe_name;
                data.number_serves = number_serves;
                data.recipe_type = $("#save_as_recipe_recipe_type_" + self._description_id).find(':selected').map(function(){ return this.value }).get().join(",");
                data.recipe_variation = $("#save_as_recipe_recipe_variation_" + self._description_id).find(':selected').map(function(){ return this.value }).get().join(",");
                data.meal_id = meal_id;
                data.type = type;
                
                self.save_as_recipe(data);
                $("#save_as_recipe_form_" + self._description_id).empty();
            });
            
            

            $("#close_recipe_list").die().live('click', function() {
                $("#recipes_list_wrapper").remove();
                $("body").css('overflow', 'auto');
            });


            $(".meal_name_input").die().live('input', function() {
                self.populateSearchResults($(this));
            });

            $(".ingredients_results option").die().live('click', function() {
                var closest_TR = $(this).closest("tr");
                self.setupTrDataId($(this));
                self.setIngredientData($(this));
                self.close_popup($("#select_meal_form" + self._description_id));
                var selected_ingredient_name = $(this).text();
                closest_TR.find(".meal_name_input").val(selected_ingredient_name);
                closest_TR.find(".meal_quantity_input").focus();
            });

            $("#meal_quantity_input"+ this._description_id).die().live('focusout', function(e){
                self.onQuantityInput($(this));

                self.CalculateTotalsWithDelay([self._meal_id]);
            });
            


            $(".delete_meal").die().live('click', function() {
                var closest_TR = $(this).closest("tr");
                var id = closest_TR.attr('data-id');
                self.deleteIngredient(id, function(id) {
                    closest_TR.remove();;
                })
            });
        }
        this.populateItemDescription(function(output) {
            if(!output) return;
            var html = '';
            output.each(function(ingredient){
                html += self.createIngredientTR(ingredient);
            });
            $("#meals_content" + self._description_id).html(html);
        });
        
   
        
        if(this.ingredient_model() == 'recipe_database') {
            
            // clear previous interval
            if(typeof window.calculate_totals_interval !== 'undefined') {
                clearInterval(window.calculate_totals_interval);
            }
            
            // calculate totals on load
            window.calculate_totals_interval = setInterval(function() {
                self.CalculateTotalsWithDelay([self._meal_id]);
            }, 2000);
        }
    }


    ItemDescription.prototype.generateHtml = function() {
        var html = '<table class="meal_table" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th class="ingredient_title">' + this._title + '</th>';
        html += '<th>QUANTITY</th>';
        html += '<th class="ingredient_cell">PRO</th>';
        html += '<th class="ingredient_cell">FAT</th>';
        html += '<th class="ingredient_cell">CARB</th>';
        html += '<th class="ingredient_cell">CALS</th>';
        html += '<th class="ingredient_cell">ENRG (kJ)</th>';
        html += '<th class="ingredient_cell">FAT, SAT</th>';
        html += '<th class="ingredient_cell">SUG</th>';
        html += '<th class="ingredient_cell">SOD (mg)</th>';
        
        html += '<th class="ingredient_cell_delete"></th>';

        html += '</tr>';
        html += '</thead>';
        html += '<tbody id="meals_content' + this._description_id + '">';
        html += '</tbody>';
        html += '<tfoot>';
        html += '<tr id="totals_row' + this._description_id + '">';
        if(this.options.read_only == false) {
            html += '<td><input  type="button" id="add_item' + this._description_id + '" value="Add New Item">';

            if(this.ingredient_model() != 'recipe_database') {
                html += '<input  type="button" id="add_recipe' + this._description_id + '" value="My Favourites">';
                html += '<input data-meal_id="' + this._meal_id  + '"  type="button" id="save_as_recipe' + this._description_id + '" value="Save As Recipe"></td>'; 
                html += '</tr>';
                
                
                html += '<tr>';
                html += '<td colspan="11">';
                html += '<div class="clr"></div>';
                html += '<div id="save_as_recipe_form_' + this._description_id + '"></div>';
                html += '<div class="clr"></div>';
                html += '</td>';
                html += '</tr>';
            }
        }
        
        html += '</tr>';
        html += '</tfoot>';
        html += '</table>';
        
        if(this.ingredient_model() == 'recipe_database') {
            html += this.totalsHtml(this._meal_id);
        }

        return html;
    }

    ItemDescription.prototype.createIngredientTR = function(calculatedIngredient) {

        var read_only_attr = '';
        if(this.options.read_only == true) {
            read_only_attr = 'readonly';
        }

        var html = '<tr data-ingredient_id="' + calculatedIngredient.ingredient_id + '"  data-id="' + calculatedIngredient.id + '">'

        html += '<td>';
        html += '<input ' + read_only_attr + ' size="60" type="text"  class="meal_name_input" value="' + calculatedIngredient.meal_name + '">';
        html += '</td>';

        html += '<td>';
        html += '<input ' + read_only_attr + ' size="5" type="text"  class="meal_quantity_input" id="meal_quantity_input' + this._description_id + '" value="' + calculatedIngredient.quantity + '">';
        html += '<span class="grams_mil">' + calculatedIngredient.measurement + '</span>';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_protein_input_' + this._meal_id + '" value="' + calculatedIngredient.protein + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_fats_input_' + this._meal_id + '" value="' + calculatedIngredient.fats + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_carbs_input_' + this._meal_id + '" value="' + calculatedIngredient.carbs + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_calories_input_' + this._meal_id + '" value="' + calculatedIngredient.calories + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_energy_input_' + this._meal_id + '" value="' + calculatedIngredient.energy + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_saturated_fat_input_' + this._meal_id + '" value="' + calculatedIngredient.saturated_fat + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_total_sugars_input_' + this._meal_id + '" value="' + calculatedIngredient.total_sugars + '">';
        html += '</td>';

        html += '<td>';
        html += '<input readonly size="5" type="text"  class="number_input meal_sodium_input_' + this._meal_id + '" value="' + calculatedIngredient.sodium + '">';
        html += '</td>';

        html += '<td class="ingredient_cell_delete">';
        if(this.options.read_only == false) {
            html += '<a href="javascript:void(0)" class="delete_meal" title="delete"></a>';
        }
        html += '</td>';
        

        html += '</tr>';

        return html;
    }

    ItemDescription.prototype.totalsHtml = function(meal_id) {
        var html = '';
        html += '<table class="meal_table" id="totals_' + meal_id + '" width="100%">';
        html += '<thead>';
        html += '<tr style="text-align:left;">';
        html += '<th class="totals_pagging meal_invisible_cell"></th>';
        html += '<th  class="meal_invisible_cell" ></th>';
        html += '<th class="ingredient_cell">PRO</th>';
        html += '<th class="ingredient_cell">FAT</th>';
        html += '<th class="ingredient_cell">CARB</th>';
        html += '<th class="ingredient_cell">CALS</th>';
        html += '<th class="ingredient_cell">ENRG (kJ)</th>';
        html += '<th class="ingredient_cell">FAT, SAT</th>';
        html += '<th class="ingredient_cell">SUG </th>';
        html += '<th class="ingredient_cell">SOD (mg)</th>';
        html += '<th class="ingredient_cell_delete"></th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        html += '<tr >';
        html += '<td class="totals_pagging meal_invisible_cell" ></td>'
        html += '<td style="text-align:right;" class="meal_invisible_cell" ><b>TOTALS</b></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_protein_total" id="meal_protein_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_fats_total" id="meal_fats_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_carbs_total" id="meal_carbs_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_calories_total" id="meal_calories_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_energy_total" id="meal_energy_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_saturated_fat_total" id="meal_saturated_fat_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row"><input readonly size="5" type="text" class="meal_sugars_total" id="meal_total_sugars_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="totals_row "><input readonly size="5" type="text" class="meal_sodium_total" id="meal_sodium_input_total_' + meal_id + '" value=""></td>';
        html += '<td class="ingredient_cell_delete">';
        html += '</td>';
        html += '</tr>';
        html += '</tbody>';
        html += '</table>';
        return html;
    }
    
    ItemDescription.prototype.searchResultsTemplate = function() {
        var html = '<div class="select_meal_form" id="select_meal_form' + this._description_id + '">';
        html += '<span id="results_count' + this._description_id + '"></span>';
        html += '<select size="25" class="ingredients_results" id="ingredients_results' + this._description_id + '"></select>';
        html += '</div>';
        return html;
    }

    ItemDescription.prototype.getSearchIngredients = function(search_text, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_recipe',
                format : 'text',
                task : 'getSearchIngredients',
                search_text : search_text
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error Get Ingredients");
            }
        });
    }

    ItemDescription.prototype.populateSearchResults = function(o) {
        var typingTimer;
        var search_text = o.val();
        if($('#select_meal_form' + this._description_id).length == 0) {
            o.parent().append(this.searchResultsTemplate());
        }
        clearTimeout(typingTimer);
        var self = this;
        if (search_text) {
            typingTimer = setTimeout(
                function() {
                    self.getSearchIngredients(
                        search_text,
                        function(output) {
                            //console.log(output);
                            $("#results_count"+ self._description_id).html('Search returned ' + output.count + ' ingredients.');
                            $("#ingredients_results" + self._description_id).html(output.html);
                            $("#ingredients_results" + self._description_id).find(":odd").css("background-color", "#F0F0EE")
                        })
                    },
                self.options.doneTypingInterval
            );
        }
    }

    ItemDescription.prototype.getIngredientData = function(id, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_recipe',
                format : 'text',
                task : 'getIngredientData',
                id : id
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response.ingredient);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
            }
        }); 
    }

    ItemDescription.prototype.setIngredientData = function(o) {
        var ingredient_id = o.val();
        var selected_ingredient_name = o.text();
        var closest_TR = o.closest("tr");
        var self = this;
        this.getIngredientData(
            ingredient_id, 
            function(ingredient) {
                if(!ingredient) return;
                var measurement = self.getMeasurement(ingredient.specific_gravity);
                closest_TR.find(".grams_mil").html(measurement);
                //console.log(ingredient);
            }
        );
    }

    ItemDescription.prototype.getMeasurement = function(specific_gravity) {
        if(parseFloat(specific_gravity) > 0) {
            return 'millilitres';
        } 
        return 'grams';
    }


    ItemDescription.prototype.close_popup = function(element) {
        element.remove();
    }

    ItemDescription.prototype.setupTrDataId = function(o) {
        o.closest("tr").attr('data-ingredient_id', o.val());
    }

    ItemDescription.prototype.onQuantityInput = function(o) {
        var quantity = o.val();
        var closest_TR = o.closest("tr");
        var self = this;
        var ingredient_id = closest_TR.attr('data-ingredient_id');
        this.getIngredientData(
            ingredient_id, 
            function(ingredient) {
                if(!ingredient) return;
                if(quantity) {
                    var calculatedIngredient = self.calculatedIngredientItems(ingredient, quantity);

                    calculatedIngredient.nutrition_plan_id = self.options.nutrition_plan_id;
                    calculatedIngredient.meal_id = self._meal_id;
                    var id = closest_TR.attr('data-id');
                    calculatedIngredient.id = id; 
                    calculatedIngredient.type = self._type;
                    self.saveIngredient(calculatedIngredient, function(inserted_id) {
                         if(inserted_id) {
                            calculatedIngredient.id = inserted_id;
                            var TR_html = self.createIngredientTR(calculatedIngredient);
                            closest_TR.replaceWith(TR_html);
                         }
                    });
                }

            }
        );
    }


    ItemDescription.prototype.calculatedIngredientItems = function(ingredient, quantity) {
        var calculated_ingredient = {};
        var specific_gravity = ingredient.specific_gravity;
        //quantity = 100;
        //specific_gravity = 1.03;
        //ingredient.protein = 3.2;
        calculated_ingredient.ingredient_id = ingredient.id;

        calculated_ingredient.meal_name = ingredient.ingredient_name;

        calculated_ingredient.quantity = quantity;

        calculated_ingredient.measurement = this.getMeasurement(ingredient.specific_gravity);

        calculated_ingredient.protein = this.calculateDependsOnGravity(ingredient.protein, quantity, specific_gravity);

        calculated_ingredient.fats = this.calculateDependsOnGravity(ingredient.fats, quantity, specific_gravity);

        calculated_ingredient.carbs = this.calculateDependsOnGravity(ingredient.carbs, quantity, specific_gravity);

        calculated_ingredient.calories = this.calculateDependsOnGravity(ingredient.calories, quantity, specific_gravity);

        calculated_ingredient.energy = this.calculateDependsOnGravity(ingredient.energy, quantity, specific_gravity);

        calculated_ingredient.saturated_fat = this.calculateDependsOnGravity(ingredient.saturated_fat, quantity, specific_gravity);

        calculated_ingredient.total_sugars = this.calculateDependsOnGravity(ingredient.total_sugars, quantity, specific_gravity);

        calculated_ingredient.sodium = this.calculateDependsOnGravity(ingredient.sodium, quantity, specific_gravity);

        //console.log(ingredient.specific_gravity);
        //console.log(ingredient);
        //console.log(calculated_ingredient);

        return calculated_ingredient;
    }


    ItemDescription.prototype.calculateDependsOnGravity =  function(value, quantity, specific_gravity) {
        var calculated_value;
        if(parseFloat(specific_gravity) > 0) {
            calculated_value = this.millilitresFormula(value, quantity, specific_gravity);
        } else {
            calculated_value = this.gramsFormula(value, quantity);
        }
        return calculated_value;
    }

    ItemDescription.prototype.gramsFormula = function(value, quantity) {
        return this.round_2_sign (value / 100 * quantity );
    }

    ItemDescription.prototype.millilitresFormula = function(value, quantity, specific_gravity) {
        return this.round_2_sign (value / 100 * quantity * specific_gravity );
    }

    ItemDescription.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }


    ItemDescription.prototype.saveIngredient = function(calculatedIngredient, handleData) {
        var table = this.options.db_table;
        

        
        if(this.ingredient_model() == 'recipe_database') {
            calculatedIngredient.recipe_id = calculatedIngredient.nutrition_plan_id
            delete calculatedIngredient.nutrition_plan_id;
            delete calculatedIngredient.meal_id;
            delete calculatedIngredient.type;
        }
        
        var ingredient_encoded = JSON.stringify(calculatedIngredient);
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'saveIngredient',
                ingredient_encoded : ingredient_encoded,
                table : table
            },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response.inserted_id);
              },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error saveIngredient");
            }
        }); 
     }


     ItemDescription.prototype.deleteIngredient = function(id, handleData) {
        var table = this.options.db_table;
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'deleteIngredient',
                id : id,
                table : table
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.success) {
                    alert(response.status.message);
                    return;
                }
                handleData(response.id);
                },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error deleteIngredient");
            }
        }); 
    }


    ItemDescription.prototype.populateItemDescription =  function(handleData) {
        var table = this.options.db_table;
        var url = this.options.fitness_administration_url;
        
        var nutrition_plan_id = this.options.nutrition_plan_id;
        var meal_id = this._meal_id;
        var type = this._type;
        
        var data = {
            nutrition_plan_id : nutrition_plan_id,
            meal_id : meal_id,
            type : type,
        }
        
        
        if(this.ingredient_model() == 'recipe_database') {
            data = {
                recipe_id : nutrition_plan_id,
            }
        }
        
        if(!nutrition_plan_id) return;
        
        var data_encoded = JSON.stringify(data);
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                task : 'populateItemDescription',
                format : 'text',
                data_encoded : data_encoded,
                table : table
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
                alert("error populateItemDescription");
            }
        }); 
    }



    ItemDescription.prototype.recipesListHtml =  function() {
        if(this.options.logged_in_admin) {
            return this.recipesListHtml_backend();
        }
        var html = '';
        html += '<div id="recipes_list_wrapper">';
        html += '<div id="close_recipe_list_frontend_wpapper"><a href="javascript:void(0)" id="close_recipe_list" title="Close">[CLOSE]</a></div>';
        html += ' <iframe scrolling="auto" style="overflow-y: auto;overflow-x: hidden;" width="100%" height="100%"';
        html += 'src="' + this.options.fitness_frontend_url + '&view=recipe_database&tmpl=component&nutrition_plan_id=';
        html += this.options.nutrition_plan_id +'&meal_id=' + this._meal_id + '&type=' + this._type +'&parent_view=' + this.options.parent_view + '#!/my_favourites">'
        html += '</iframe> ';
        html += '</div>';

        return html;
    }
    
    ItemDescription.prototype.recipesListHtml_backend =  function() {
        var html = '';
        html += '<div id="recipes_list_wrapper">';
        html += '<a href="javascript:void(0)" id="close_recipe_list" title="Close"></a>';
        html += ' <iframe scrolling="auto" style="overflow-y: auto;overflow-x: hidden;" width="100%" height="100%"';
        html += 'src="' + this.options.fitness_administration_url + '&view=nutrition_recipes&tmpl=component&layout=popup_view&nutrition_plan_id=';
        html += this.options.nutrition_plan_id +'&meal_id=' + this._meal_id + '&type=' + this._type +'&parent_view=' + this.options.parent_view + '">'
        html += '</iframe> ';
        html += '</div>';

        return html;
    }
    
    
    
    
    
    
    
    
    
    
    ItemDescription.prototype.calculate_totals = function(meal_id) {

       this.set_item_total(this.get_item_total('meal_protein_input_' + meal_id), 'meal_protein_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_fats_input_' + meal_id), 'meal_fats_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_carbs_input_' + meal_id), 'meal_carbs_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_calories_input_' + meal_id), 'meal_calories_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_energy_input_' + meal_id), 'meal_energy_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_saturated_fat_input_' + meal_id), 'meal_saturated_fat_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_total_sugars_input_' + meal_id), 'meal_total_sugars_input_total_' + meal_id);

       this.set_item_total(this.get_item_total('meal_sodium_input_' + meal_id), 'meal_sodium_input_total_' + meal_id);

    }

    ItemDescription.prototype.get_item_total = function(element) {
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


    ItemDescription.prototype.set_item_total = function(value, element) {
        $("#" + element).val(value);
    }

    ItemDescription.prototype.round_2_sign = function(value) {
        return Math.round(value * 100)/100;
    }


    ItemDescription.prototype.CalculateTotalsWithDelay = function(meal_ids) {
        var self = this;
        //console.log(meal_ids);
        setTimeout(
            function(){
                meal_ids.each(function(meal_id){
                    self.calculate_totals(meal_id);
                });
            },
            2000
        );
    }
    
    
    ItemDescription.prototype.save_as_recipe = function(data) {
        var url = this.options.fitness_administration_url;
        
        var view = 'nutrition_diaries'
        var task = 'saveAsRecipe';
        var table = '#__fitness_nutrition_diary_ingredients';
        
        $.AjaxCall(data, url, view, task, table, function(output) {
            console.log(output);
        });
    }
    
    
    ItemDescription.prototype.load_save_as_recipe_form = function(o, recipe_types, recipe_variations) {
        var html = '';
        html += '<table style="width:400px;border: 1px solid #FFFFFF !important;">';
        html += '<tr>';
        
        html += '<td>';
        html += 'RECIPE NAME';
        html += '</td>';
        html += '<td>';
        html += '<input maxlength="100" style="width:250px;" type="text" id="save_as_recipe_recipe_name_' + o.description_id + '"/>';
        html += '</td>';
        html += '</tr>';
        
        html += '<tr>';
        html += '<td>';
        html += '# OF SERVES';
        html += '</td>';
        html += '<td>';
        html += '<input maxlength="3" style="width:20px;" type="text" id="save_as_recipe_serves_' + o.description_id + '"/>';
        html += '</td>';
        html += '</tr>';
        
        html += '<tr>';
        html += '<td>';
        html += 'RECIPE TYPE';
        html += '</td>';
        html += '<td>';

        html += '<select class="dark_input_style" multiple size="5" id="save_as_recipe_recipe_type_' + o.description_id + '"  style="width:200px;">';
            recipe_types.each(function(item){
                html += '<option value="' +  item.id + '">' + item.name + '</option>';
            });
        html += '</select>';

        html += '</td>';
        html += '</tr>';
        
         html += '<tr>';
        html += '<td>';
        html += 'VARIATION';
        html += '</td>';
        html += '<td>';

        html += '<select class="dark_input_style" multiple size="5" id="save_as_recipe_recipe_variation_' + o.description_id + '"  style="width:200px;">';
            recipe_variations.each(function(item){
                html += '<option value="' +  item.id + '">' + item.name + '</option>';
            });
        html += '</select>';

        html += '</td>';
        html += '</tr>';
        
        html += '<tr>';
        html += '<td>';
        html += '</td>';
        html += '<td style="text-align:left;">';
        html += '<a style="cursor: pointer;" id="save_as_recipe_cancel_' + o.description_id + '" onclick="javascript:void(0)">[CANCEL]</a>';
        html += '<a data-meal_id="' + o.meal_id + '" data-type="' + o.type + '" style="cursor: pointer;margin-left:20px;" id="save_as_recipe_save_' + o.description_id + '" onclick="javascript:void(0)">[SAVE]</a>';
        html += '</td>'
        
        html += '</tr>';
        html += '</table>';
        html += '<br/>';
        html += '<div class="clr"></div>';
        
        return html;
    }
    
    
    
    ItemDescription.prototype.getRecipeTypesAndVariations = function(o) {

        var url = this.options.fitness_administration_url;
        var self = this;
        var obj = o;
        
        $.when( 
                
            $.ajax({
                type : "POST",
                url : url,
                data : {
                    view : 'recipe_database',
                    task : 'getRecipeTypes',
                    format : 'text',
                    table : '#__fitness_recipe_types'
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.message);
                        return;
                    }
                    //console.log(response.data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert('getRecipeTypes error');
                }
            }), 
            
            $.ajax({
                type : "POST",
                url : url,
                data : {
                    view : 'recipe_database',
                    task : 'recipe_variations',
                    format : 'text',
                    table :  '#__fitness_recipe_variations'
                },
                dataType : 'json',
                success : function(response) {
                    //console.log(response);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert('getRecipeTypes error');
                }
            })
        )

        .then( function( data1, data2 ) {
            var recipe_types = data1[0]['data'];
            var recipe_variations = data2[0];
           
            var form_html = self.load_save_as_recipe_form(obj, recipe_types, recipe_variations);
                
            $("#save_as_recipe_form_" + obj.description_id).html(form_html);
        });
    }

    
    
    // Add the  function to the top level of the jQuery object
    $.itemDescription = function(options, type, title, meal_id) {

        var constr = new ItemDescription(options, type, title, meal_id);

        return constr;
    };

}));