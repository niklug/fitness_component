/*
 * 
 */
(function($, Backbone) {
    function NutritionGuide() {
        
        window.app = window.app || {};
        Backbone.emulateHTTP = true ;
        Backbone.emulateJSON = true;

        window.app.Nutrition_guide_menu = Backbone.View.extend({

            render:function () {
                var template = _.template($('#nutrition_guide_menu_frontend_template').html());
                this.$el.html(template);
                return this;
            },

            events:{
                "click .example_day_link": "onChooseDay",
            },

            onChooseDay:function (event) {
                $(".example_day_link").removeClass("active");
                var day = $(event.target).attr('data-id');
                $(event.target).addClass("active");
                window.app.controller.navigate("!/example_day/" + day, true);
            },


        });
        
        window.app.Email_pdf_header_template = Backbone.View.extend({

            render: function(){
                var template = _.template( $("#email_pdf_header_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },

            events:{
                "click .pdf_button" : "onClickPdf",
                "click .email_button" : "onClickEmail",
            },


            onClickPdf : function(event) {
                var id = $(event.target).attr('data-id');
                var client_id = this.model.get('client_id');
                var htmlPage = window.fitness_helper.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_nutrition_guide&id=' + id + '&client_id=' + client_id;
                window.fitness_helper.printPage(htmlPage);
            },
            
            onClickEmail : function(event) {
                var data = {};
                data.url = window.app.example_day_options.fitness_frontend_url;
                data.view = '';
                data.task = 'ajax_email';
                data.table = '';

                data.id = $(event.target).attr('data-id');
                data.view = 'NutritionPlan';
                data.method = 'email_pdf_nutrition_guide';
                window.fitness_helper.sendEmail(data);
            },

        });
        
        window.app.Example_day_view = Backbone.View.extend({
            render: function(){
                var template = _.template( $("#nutrition_plan_example_day_frontend_template").html());
                this.$el.html(template);
                
                var self = this;
                
		this.mealListItemViews = {};

		this.collection.on("add", function(meal) {
                    window.app.nutrition_plan_example_day_meal_view = new window.app.Nutrition_plan_example_day_meal_view({collection : this,  model : meal}); 
                    self.$el.find("#example_day_meal_list").append( window.app.nutrition_plan_example_day_meal_view.render().el );
		});
               
                return this;
            },
        });
        
        window.app.Nutrition_plan_example_day_meal_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this,'close', 'render');
                this.model.on("destroy", this.close, this);
                
                this.recipes_collection = new window.app.Nutrition_guide_recipes_collection();
                
                this.recipes_collection.bind("add", this.addRecipe, this);
                
                var self = this;
                this.recipes_collection.fetch({
                    data: {
                        meal_id : self.model.get('id')
                    },
                    wait : true,
                    success : function(collection, response) {
                        //console.log(collection);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
               
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_example_day_item_frontend_template").html(), this.model.toJSON());
                this.$el.html(template);
               
                this.connectComments();
                
                return this;
            },
            
            addRecipe : function(model) {
                this.item_view = new window.app.Nutrition_guide_recipe_view({collection : this.recipes_collection, model : model}); 
                this.$el.find(".meal_recipes").append( this.item_view.render().el );
            },
            
            connectComments : function() {
                var meal_id = this.model.get('id');
                var comment_options = {
                    'item_id' : window.app.example_day_options.nutrition_plan_id,
                    'fitness_administration_url' : window.app.example_day_options.fitness_frontend_url,
                    'comment_obj' : {'user_name' : window.app.example_day_options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : window.app.example_day_options.example_day_meal_comments_db_table,
                    'read_only' : true,
                }
                var comments = $.comments(comment_options, comment_options.item_id, meal_id).run();
                this.$el.find(".comments_wrapper").html(comments);
            },
           
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            },
 
        });
        
        
        window.app.Nutrition_guide_recipe_view = Backbone.View.extend({
            render:function () {
                var template = _.template( $("#nutrition_guide_recipe_frontend_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click .view_recipe" : "onClickViewRecipe",
            },
            
            onClickViewRecipe : function(event) {
                var url = window.app.example_day_options.base_url + 'index.php?option=com_fitness&view=recipe_database&Itemid=1002#!/nutrition_database/nutrition_recipe/' + this.model.get('original_recipe_id');
                window.open(url);
            }, 
            
        });
        
        
        //MODELS
        window.app.Example_day_meal_model = Backbone.Model.extend({
            urlRoot : window.app.example_day_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_exercie_day_meal&',
            
            defaults : {
                id : null,
                description : null,
                nutrition_plan_id : window.app.example_day_options.nutrition_plan_id,
                example_day_id : null,
                meal_time : null,
            },
        });
        
        
         // COLLECTIONS
        window.app.Example_day_meals_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_exercie_day_meal&',
            model: window.app.Example_day_meal_model
        });
        
        window.app.Nutrition_guide_recipes_collection = Backbone.Collection.extend({
            url : window.app.example_day_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_guide_recipes&'
        });
    }

    // Add the  function to the top level of the jQuery object
    $.NutritionGuide = function(options) {

        var constr = new NutritionGuide();

        return constr;
    };
        
})(jQuery, Backbone);



