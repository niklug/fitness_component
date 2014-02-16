define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'views/exercise_library/select_element',
	'text!templates/exercise_library/backend/search_block.html'
], function (
        $,
        _,
        Backbone,
        app, 
        Business_profiles_collection, 
        Select_element_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.business_profiles = new Business_profiles_collection();
            var self = this;
            app.collections.business_profiles.fetch({
                success : function (collection, response) {
                    self.connectBusinessFilter(collection);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })

            
        },
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
           
            return this;
        },
        
        events : {
            "click #search_by_name" : "search",
            "click #clear_all" : "clearAll",
            "change #state_filter" : "onChangeState",
            "click #add_item" : "onClickAddItem",
            "click #trash_selected" : "onClickTrashSelected",
            "click #delete_selected" : "onClickDeleteSelected",
        },
        
        connectBusinessFilter : function(collection) {
            if(!app.options.is_superuser) {
                return false;
            }
             new Select_element_view({
                model : this.model,
                el : $("#business_profile_filter"),
                collection : collection,
                first_option_title : '-Global Business Permission-',
                class_name : '',
                id_name : 'business_profile_select',
                model_field : 'business_profiles'
            }).render();
            
            
        },
        
        search : function() {
            var exercise_name = this.$el.find("#exercise_name").val();
            var client_name = this.$el.find("#client_name").val();
            this.model.set({exercise_name : exercise_name, client_name: client_name});
        },
        
        clearAll : function(){
            var form = $("#header_wrapper");
            form.find(".filter_select").val(0);
            form.find("input[type=text]").val('');
            
            this.model.set(
                {
                    exercise_name : '',
                    client_name : '',
                    exercise_type : '',
                    force_type : '',
                    mechanics_type : '',
                    body_part : '',
                    target_muscles : '', 
                    equipment_type : '',
                    difficulty : '',
                }
            );
        },
        
        onChangeState : function(event) {
            var value = $(event.target).val();
            
            if(parseInt(value)) {
                app.controller.navigate("!/list_view", true);
            } else {
                app.controller.navigate("!/trash_list", true);
            }
        },
        
        onClickTrashSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.trashItem(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        trashItem : function(id) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({id : id, state : '-2'}, {
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.deleteItem(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        deleteItem : function(id) {
            var model = this.collection.get(id);
            var self = this;
            model.destroy({
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        hide_items : function(items) {
            var self = this;
            var items = items.split(",");
            _.each(items, function(item, key){ 
                $(".item_row[data-id=" + item + "]").fadeOut();
            });
        },
        
        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
        }
    });
            
    return view;
});