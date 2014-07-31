define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/nutrition_plan/backend/search_header.html'

], function (
        $,
        _,
        Backbone,
        app,
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
        },

        
        template:_.template(template),
        
        render: function(){
            var data = {item : {}};
            //console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events : {
            "click #search" : "search",
            'keypress input[type=text]': 'filterOnEnter',
            "click #clear_all" : "clearAll",
            "click #publish_selected" : "onClickPublishAll",
            "click #unpublish_selected" : "onClickUnpublishAll",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.$el.find("#active_start_from, #active_start_to, #active_finish_from, #active_finish_to").datepicker({ dateFormat: "yy-mm-dd"});
               
                self.connectForceActiveFilter();
                
                self.connectPublishedFilter();
                
            });
        },
        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
                        
        search : function() {
            var active_start_from = this.$el.find("#active_start_from").val();
            var active_start_to = this.$el.find("#active_start_to").val();
            var active_finish_from = this.$el.find("#active_finish_from").val();
            var active_finish_to = this.$el.find("#active_finish_to").val();
            var business_profile_id = this.$el.find("#business_profile_id").val();
            var trainer_id = this.$el.find("#trainer_id").val();
            var client_id = this.$el.find("#client_id").val();
            var force_active = this.$el.find("#force_active_select").val();
            var client_name = this.$el.find("#client_name").val();

            this.model.set({
                active_start_from : active_start_from,
                active_start_to : active_start_to,
                active_finish_from : active_finish_from,
                active_finish_to : active_finish_to,
                business_profile_id : business_profile_id,
                trainer_id : trainer_id,
                client_id : client_id,
                force_active : force_active,
                client_name : client_name
            });
        },
        
        clearAll : function(){
            $(this.el).find("#business_profile_id, #trainer_id, #client_id, #state_select, #force_active_select, #client_name").val('');
            $(this.el).find("#active_start_from, #active_start_to, #active_finish_from, #active_finish_to").val('');
            this.model.set(
                {
                    active_start_from : '',
                    active_start_to : '',
                    active_finish_from : '',
                    active_finish_to : '',
                    business_profile_id : '',
                    trainer_id : '',
                    client_id : '',
                    status : '',
                    force_active : '',
                    client_name : ''
                }
            );
        },
        
        connectPublishedFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Published'},
                {id : '0', name : 'Unpublished'},
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
        
        connectActivePlanFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Active Plan'},
                {id : '0', name : 'Inactive Plan '},
             ]);           
          
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#active_plan_wrapper"),
                collection : collection,
                first_option_title : '-Active Plan-',
                class_name : 'filter_select',
                id_name : 'active_plan_select',
                model_field : 'active_plan'
            }).render();
        },
        
        connectForceActiveFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Force Active'},
                {id : '0', name : 'Force Inactive'},
             ]);           
          
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#force_active_wrapper"),
                collection : collection,
                first_option_title : '-Force Active-',
                class_name : 'filter_select',
                id_name : 'force_active_select',
                model_field : 'force_active'
            }).render();
        },
        
        onClickPublishAll: function() {
            var selected = new Array();
            $('.item_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publishUnpublishItem(item, '1');
                });
            }
            $("#select_all_checkbox").prop("checked", false);
        },
        
        onClickUnpublishAll: function() {
            var selected = new Array();
            $('.item_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publishUnpublishItem(item, '0');
                });
            }
            $("#select_all_checkbox").prop("checked", false);
        },
        
         
        publishUnpublishItem : function(id, state) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : state}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }
        
    });
            
    return view;
});