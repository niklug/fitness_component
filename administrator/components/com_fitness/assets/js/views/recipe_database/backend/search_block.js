define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'views/programs/select_element',
        'views/exercise_library/select_filter',
	'text!templates/recipe_database/backend/search_block.html'
], function (
        $,
        _,
        Backbone,
        app, 
        Business_profiles_collection,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Select_element_view,
        Select_filter_fiew,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {

        },
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.$el.find("#start_from, #start_to, #end_from, #end_to").datepicker({ dateFormat: "yy-mm-dd"});
            
            this.connectStatusFilter();
            this.connectPublishedFilter();
            this.connectBusinessFilter();
            this.connectRecipeTypesFilter();
            this.connectRecipeVariationsFilter();

            return this;
        },
        
        events : {
            "click #add_item" : "onClickAddItem",
            "click #trash_delete_selected" : "onClickTrashDeleteSelected",
            "click #publish_selected" : "onClickPublish",
            "click #search" : "search",
            'keypress input[type=text]': 'filterOnEnter',
            "click #clear_all" : "clearAll",
            "change #state_filter" : "onChangeState",
        },
        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
 
        search : function() {
            var start_from = this.$el.find("#start_from").val();
            var start_to = this.$el.find("#start_to").val();
            var end_from = this.$el.find("#end_from").val();
            var end_to = this.$el.find("#end_to").val();

            this.model.set({start_from : start_from, start_to : start_to, end_from : end_from, end_to : end_to});
        },
        
        clearAll : function(){
            var form = $("#header_wrapper");
            form.find(".filter_select").val(0);
            form.find("input[type=text]").val('');
            form.find("#state_select").val('*');
            this.model.set(
                {
                    start_from : '',
                    start_to : '',
                    end_from : '',
                    end_to : '',
                    status : ''
                }
            );
        },

        onClickAddItem : function() {
            app.controller.navigate("!/form_primary/0", true);
        },
        
        connectStatusFilter : function() {
            var collection = new Backbone.Collection();
            _.each(app.options.statuses, function(status) {
                var model = new Backbone.Model(status);
                collection.add(model);
            });
          
             new Select_element_view({
                model : this.model,
                el : $(this.el).find("#status_wrapper"),
                collection : collection,
                first_option_title : '-Select Status-',
                class_name : 'filter_select',
                id_name : 'status_select',
                model_field : ''
            }).render();
        },
        
        connectPublishedFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Published'},
                {id : '0', name : 'Unpublished'},
                {id : '-2', name : 'Trashed'},
                {id : '*', name : 'All Recipes'}
            ]);           
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#state_wrapper"),
                collection : collection,
                first_option_title : '-Published-',
                class_name : 'filter_select',
                id_name : 'state_select',
                model_field : ''
            }).render();
        },
        
        connectBusinessFilter : function() {
            if(app.collections.business_profiles) {
                this.loadBusinessSelect(app.collections.business_profiles);
                return;
            }
            var self = this;
            app.collections.business_profiles = new Business_profiles_collection();
            app.collections.business_profiles.fetch({
                success : function (collection, response) {
                    self.loadBusinessSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadBusinessSelect : function(collection) {
            var business_name_collection = new Backbone.Collection;
            
            var element_disabled = '';
            
            if(app.options.is_trainer) {
                business_name_collection.add(collection.where({id : this.business_profile_id}));
                element_disabled = 'disabled';
            }
            
            if(app.options.is_superuser) {
                business_name_collection = collection;
            }
            
             new Select_element_view({
                model : new Backbone.Model({business_profile_id : this.business_profile_id}),
                el : $(this.el).find("#business_profile_select"),
                collection : business_name_collection,
                first_option_title : '- Business profile-',
                id_name : 'business_profile_id',
                model_field : 'business_profile_id',
                element_disabled : element_disabled

            }).render();
        },
        
        connectRecipeTypesFilter : function() {
            if(app.collections.recipe_types) {
                this.loadRecipeTypesSelect(app.collections.recipe_types );
                return;
            }
            var self = this;
            app.collections.recipe_types = new Recipe_types_collection();
            app.collections.recipe_types.fetch({
                success : function (collection, response) {
                    self.loadRecipeTypesSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadRecipeTypesSelect : function(collection) {
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_type_wrapper"),
                collection : collection,
                title : 'FILTER CATEGORIES',
                first_option_title : 'ALL CATEGORIES',
                class_name : '',
                id_name : '',
                select_size : 15,
                model_field : 'filter_options'
            }).render();  
        },
        
        connectRecipeVariationsFilter : function() {
            if(app.collections.recipe_variations) {
                this.loadRecipeVariationsSelect(app.collections.recipe_variations );
                return;
            }
            var self = this;
            app.collections.recipe_variations = new Recipe_variations_collection();
            app.collections.recipe_variations.fetch({
                success : function (collection, response) {
                    self.loadRecipeVariationsSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadRecipeVariationsSelect : function(collection) {
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_variation_wrapper"),
                collection : collection,
                title : 'RECIPE VARIATIONS',
                first_option_title : 'ALL VARIATIONS',
                class_name : '',
                id_name : '',
                select_size : 15,
                model_field : 'recipe_variations_filter_options'
            }).render(); 
        },
        
        onClickTrashDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var current_page = this.model.get('current_page');
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    if(current_page == 'trash_list') {
                        self.deleteItem(item);
                    } else {
                       self.trashItem(item); 
                    }
                });
            }
            $("#select_trashed").prop("checked", false);
        },

        
        trashItem : function(id) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({id : id, published : '-2'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        deleteItem : function(id) {
            var model = this.collection.get(id);
            var self = this;
            model.destroy({
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
    });
            
    return view;
});