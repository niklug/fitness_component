/*
 * 
 */
(function($) {
    function ShoppingList(options) {
        this.options = options;
    }


    ShoppingList.prototype.run = function() {
        var item_html = this.generateHtml();
        $("#shopping_list_wrapper").append(item_html);

        this.populate();

        this.setEventListeners();
    }

    ShoppingList.prototype.populate = function() {
        var self = this;
        this.getShoppingItemData(function(output) {
            if(!output) return;
            var html = '';
            output.each(function(item){
                html += self.generateItemTR(item);
            });
            $("#shopping_list_content").html(html);
        });
    }


    ShoppingList.prototype.setEventListeners = function() {
        var self = this;

        $("#add_shopping_item").on('click', function() {
            var item_html = self.generateItemTR(self.options.item_obj);
            $("#shopping_list_content").append(item_html);
        });

        $(".save_shopping_item").live('click', function() {
            var closest_tr = $(this).closest("tr");
            var data = {};

            data.id = closest_tr.attr('data-id');
            data.nutrition_plan_id = self.options.nutrition_plan_id;
            data.name = closest_tr.find(".shopping_name").val();
            data.usage = closest_tr.find(".shopping_usage").val();
            data.comments = closest_tr.find(".shopping_comments").val();
            data.url = closest_tr.find(".shopping_url").val();

            self.saveShoppingItem(data, function(output) {
                var html = self.generateItemTR(output);
                closest_tr.replaceWith(html);
            });
        });


        $(".delete_shopping_item").live('click', function() {
            var closest_tr = $(this).closest("tr");
            var id = closest_tr.attr('data-id');
            self.deleteShoppingItem(id, function(id) {
            closest_tr.remove();;
            });
        });
    }

    ShoppingList.prototype.generateHtml = function() {
        var html = '';
        html += '<table width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>';
        html += 'PRODUCT NAME';
        html += '</th>';
        html += '<th>';
        html += 'RECOMMENDED USAGE';
        html += '</th>';
        html += '<th>';
        html += 'TRAINER COMMENTS';
        html += '</th>';
        html += '<th>';
        html += 'SHOP URL';
        html += '</th>';
        html += '</tr>';
        html += '</thead>';

        html += '<tbody id="shopping_list_content">';

        html += '<tbody>';
        html += '</table>';

        return html;
    }

    ShoppingList.prototype.generateItemTR = function(o) {
        var html = '';
        html += '<tr data-id="' + o.id + '" >';
        html += '<td>';
        html += '<input  size="60" type="text"  class=" shopping_name " value="' + o.name + '"> ';
        html += '</td>';

        html += '<td>';
        html += '<input  size="50" type="text"  class=" shopping_usage" value="' + o.usage + '"> ';
        html += '</td>';

        html += '<td>';
        html += '<input  size="50" type="text"  class=" shopping_comments" value="' + o.comments + '"> ';
        html += '</td>';

        html += '<td>';
        html += '<input  size="30" type="text"  class=" shopping_url" value="' + o.url + '"> ';
        html += '</td>';

        html += '<td>';
        html += '<input title="Save/Update Shopping Item" class="save_shopping_item " type="button"  value="Save">';
        html += '</td>';

        html += '<td>';
        html += '<a href="javascript:void(0)" class="delete_shopping_item" title="Delete Shopping Item"></a>';
        html += '</td>';
        html += '</tr>';

        return html;
    }


    ShoppingList.prototype.saveShoppingItem = function(o, handleData) {
        if(o.id === 'undefined')  o.id = "";
        var data_encoded = JSON.stringify(o);

        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'saveShoppingItem',
                data_encoded : data_encoded
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
                alert("error saveShoppingItem");
            }
        }); 
    }

     ShoppingList.prototype.deleteShoppingItem = function(id, handleData) {
        var url = this.options.fitness_administration_url;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'deleteShoppingItem',
                id : id
              },
            dataType : 'json',
            success : function(response) {
                if(!response.status.IsSuccess) {
                    alert(response.status.Msg);
                    return;
                }
                handleData(response.id);
                },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                alert("error deleteShoppingItem");
            }
        }); 
    }



    ShoppingList.prototype.getShoppingItemData=  function(handleData) {
        var url = this.options.fitness_administration_url;
        var nutrition_plan_id = this.options.nutrition_plan_id;
        if(!nutrition_plan_id) return;
        $.ajax({
            type : "POST",
            url : url,
            data : {
                view : 'nutrition_plan',
                format : 'text',
                task : 'getShoppingItemData',
                nutrition_plan_id : nutrition_plan_id,
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
                alert("error getShoppingItemData");
            }
        }); 
    }

    
    // Add the  function to the top level of the jQuery object
    $.shoppingList = function(options) {

        var constr = new ShoppingList(options);

        return constr;
    };

})(jQuery);
