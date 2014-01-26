define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/nutrition_guide/shopping_list_item',
	'text!templates/nutrition_plan/nutrition_guide/shopping_list.html'
], function ( $, _, Backbone, app, Shopping_list_item_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render:function () {
            $(this.el).html(this.template(this.model.toJSON()));
            
            var container = this.$el.find("#shopping_list_container");
            
            var categories_collection = this.options.categories_collection;
            var ingredients_collection = this.options.ingredients_collection;
            
            var count = 0;
            categories_collection.each(function(model) {
                var ingredients = ingredients_collection.where({category : model.get('id')});
                if(ingredients.length > 0) {
                    //console.log(ingredients);
                    model.set({'ingredients' : ingredients, count : count});
                    var item_view = new Shopping_list_item_view({el : container, model : model});
                    item_view.render();
                    count++;
                }
            });
            return this;
        },

        events: {
            "click .pdf_button" : "onClickPdf",
            "click .email_button" : "onClickEmail",
        },

        onClickPdf : function(event) {
            
            var checked = $('div#shopping_list_container input:checked').map(function() { return this.value}).get().join(',');
            //console.log(checked);
      
            var id = $(event.target).attr('data-id');
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_shopping_list&id=' + id +'&client_id=' + app.options.client_id + '&checked=' + checked;
            $.fitness_helper.printPage(htmlPage);
          
        },

        onClickEmail : function(event) {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id = $(event.target).attr('data-id');
            data.checked = $('div#shopping_list_container input:checked').map(function() { return this.value}).get().join(',');
            data.view = 'NutritionPlan';
            data.method = 'email_pdf_shopping_list';
            $.fitness_helper.sendEmail(data);
        },
    });
            
    return view;
});