define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/supplements/backend/ingredients_seaarch_results.html'
], function ( $, _, Backbone, app, template ) {

     var view = Backbone.View.extend({

        template:_.template(template),

        initialize : function () {
            _.bindAll(this, 'onClickOption', 'render');
        },

        render : function (eventName) {
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);

            return this;
        },

        events:{
            "click .supplement_name_results option": "onClickOption"
        },

        onClickOption : function (event) {
            var parent_view_el = this.options.parent_view_el;

            var ingredient_id = $(event.target).val();

            var self = this;
            this.getIngredientData(
                ingredient_id, 
                function(ingredient) {
                    if(!ingredient) return;
                    parent_view_el.find(".supplement_name").val(ingredient.ingredient_name);
                    parent_view_el.find(".supplement_description").val(ingredient.description);
                    self.close();
                }
            );
        },

        getIngredientData : function(id, handleData) {
            var url = app.options.ajax_call_url;
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
                    alert("error getIngredientData");
                }
            }); 
        },

        close : function() {
            $(this.el).unbind();
            $(this.el).remove();
        },
    });
            
    return view;
});