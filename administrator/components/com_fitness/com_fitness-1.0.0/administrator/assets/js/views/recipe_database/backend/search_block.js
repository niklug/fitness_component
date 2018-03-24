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
            
            this.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
            
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
            "click #unpublish_selected" : "onClickUnpublish",
            "click #copy_selected" : "onClickCopySelected",
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
            var recipe_name = this.$el.find("#recipe_name").val();
            var created_by_name = this.$el.find("#created_by_name").val();
            
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();

            this.model.set({recipe_name : recipe_name, created_by_name : created_by_name, date_from : date_from, date_to : date_to});
        },
        
        clearAll : function(){
            $(this.el).find("input[type=text],#business_profile_id").val('');
            $(this.el).find(".filter_select").val(0);
            $(this.el).find("#state_select").val('*');
            this.model.set(
                {
                    date_from : '',
                    date_to : '',
                    state : '*',
                    status : '',
                    recipe_name : '',
                    created_by_name : '',
                    filter_options : '',
                    recipe_variations_filter_options : '',
                    business_profile_id : ''
                }
            );
        },

        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
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
                model_field : 'status'
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
                model_field : 'state'
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
            if(!app.options.is_superuser) {
                return;
            }
            
             new Select_element_view({
                model : this.model,
                el : $(this.el).find("#business_profile_select"),
                collection : collection,
                first_option_title : '- Business profile-',
                id_name : 'business_profile_id',
                model_field : 'business_profile_id',

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
                title : 'RECIPE TYPE',
                first_option_title : 'ALL TYPES',
                class_name : '',
                id_name : 'recipe_type',
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
                title : 'RECIPE VARIATION',
                first_option_title : 'ALL VARIATIONS',
                class_name : '',
                id_name : 'recipe_variation',
                select_size : 15,
                model_field : 'recipe_variations_filter_options'
            }).render(); 
        },
        
        onClickTrashDeleteSelected : function() {
            var selected = new Array();
            var states = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
                states.push($(this).attr('data-state'));
            });

            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.trashItem(item);
                    if(states[key] == '-2') {
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
            model.save({id : id, state : '-2'}, {
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
        
        
        onClickPublish : function() {
            
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publish(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        publish : function(id) {
            var model = this.collection.get(id);
       
            var self  = this;
            model.save({id : id, state : '1'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickUnpublish : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.unpublish(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        unpublish : function(id) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({id : id, state : '0'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickCopySelected : function() {
            var selected = new Array();
            var states = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    app.controller.copy_recipe(item, true);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
    });
            
    return view;
});