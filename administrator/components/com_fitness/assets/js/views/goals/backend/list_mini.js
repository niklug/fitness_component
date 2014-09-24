define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/goals/backend/list_item_mini',
	'text!templates/goals/backend/list_mini.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        List_item_mini_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.primary_goal = this.model.toJSON();
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.loadItems();
          
            return this;
        },
        
        events: {
            "click .new_mini_goal" : "onClickNewMiniGoal",
            "click .edit_mini_goal" : "onClickEditMiniGoal",
            "click .view_mini" : "onClickView",
            
            "click .trash_mini" : "onClickTrash",
            "click .restore_mini" : "onClickRestore",
            "click .delete_mini" : "onClickDelete",
            
            "click .publish_mini" : "onClickPublish",
        },

        
        loadItems : function() {
              var self = this;
            _.each(this.collection.models, function(model) {
                 self.addItem(model);
            });
        },
        
        
        addItem : function(model) {
            //console.log(model.toJSON());
            var readonly_allowed = app.controller.readonly_allowed(model);
            model.set({readonly_allowed : readonly_allowed});
            var primary_goal_id = this.model.get('id');
            if(primary_goal_id == model.get('primary_goal_id')) {
                $(this.el).find(".minigoals_container").append(new List_item_mini_view({model : model, primary_goal_model : this.model}).render().el);
                $(this.el).find(".minigoals_container").append('<hr>');
            }
            
        },
        
        clearItems : function() {
            $(this.el).find(".minigoals_container").empty();
        },
        
        onClickNewMiniGoal : function() {
            app.controller.navigate("!/form_mini/0/" + this.model.get('id'), true);
        },
        
        onClickEditMiniGoal : function(event) {
            var id = $(event.target).attr('data-id');
            var primary_goal_id = this.model.get('id');
            app.controller.navigate("!/form_mini/" + id + '/' + primary_goal_id, true);
        },
        
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            var primary_goal_id = this.model.get('id');
            app.controller.navigate("!/form_mini/" + id + '/' + primary_goal_id, true);
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : '-2'}, {
                success: function (model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickRestore : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self = this;
            model.save({state : '1'}, {
                success: function (model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self = this;
            model.destroy({
                success: function (model) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickPublish : function(event) {
            var id = $(event.target).attr('data-id');
            var state = $(event.target).attr('data-state');
            
            var published = 1;
            
            if(parseInt(state) == '1') {
                published = 0;
            }
            
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : published}, {
                success: function (model, response) {
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
     
    });
            
    return view;
});