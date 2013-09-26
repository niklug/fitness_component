/*
 * Provides search ingredients in database and calculations
 */
(function($) {
    function ItemDescription(options, type, title, meal_id) {
        this.options = options;
        this._type = type;
        this._title = title;
        this._meal_id = meal_id;

        this._description_id = '_' + this._type + '_' + this._meal_id;

        this._doneTypingInterval = 1000;
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
            });

            $("#close_recipe_list").die().live('click', function() {
                $(this).parent().remove();
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
    }


    ItemDescription.prototype.generateHtml = function() {
        var html = '<table width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th class="ingredient_title">' + this._title + '</th>';
        html += '<th>QUANTITY</th>';
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
        html += '</thead>';
        html += '<tbody id="meals_content' + this._description_id + '">';
        html += '</tbody>';
        html += '<tfoot>';
        html += '<tr id="totals_row' + this._description_id + '">';
        if(this.options.read_only == false) {
            html += '<td><input  type="button" id="add_item' + this._description_id + '" value="Add New Item">';
            html += '<input  type="button" id="add_recipe' + this._description_id + '" value="RECIPE"></td>';
        }
        html += '</tr>';
        html += '</tfoot>';
        html += '</table>';

        return html;
    }

    ItemDescription.prototype.createIngredientTR = function(calculatedIngredient) {

        var html = '<tr data-ingredient_id="' + calculatedIngredient.ingredient_id + '"  data-id="' + calculatedIngredient.id + '">'

        html += '<td>';
        html += '<input  size="60" type="text"  class="meal_name_input" value="' + calculatedIngredient.meal_name + '">';
        html += '</td>';

        html += '<td>';
        html += '<input size="5" type="text"  class="meal_quantity_input" id="meal_quantity_input' + this._description_id + '" value="' + calculatedIngredient.quantity + '">';
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

        html += '<td>';
        if(this.options.read_only == false) {
        html += '<a href="javascript:void(0)" class="delete_meal" title="delete"></a>';
        }
        html += '</td>';

        html += '</tr>';

        return html;
    }

    ItemDescription.prototype.searchResultsTemplate = function(calculatedIngredient) {
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
                    alert(response.status.Msg);
                    return;
                }
                handleData(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error");
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
                    alert(response.status.Msg);
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
                    alert(response.status.Msg);
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
                    alert(response.status.Msg);
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
        if(!nutrition_plan_id) return;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'populateItemDescription',
                nutrition_plan_id : nutrition_plan_id,
                meal_id : meal_id,
                type : type,
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
                alert("error populateItemDescription");
            }
        }); 
    }



    ItemDescription.prototype.recipesListHtml =  function() {
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
    
    // Add the  function to the top level of the jQuery object
    $.itemDescription = function(options, type, title, meal_id) {

        var constr = new ItemDescription(options, type, title, meal_id);

        return constr;
    };

})(jQuery);