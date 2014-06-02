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

            this.status_obj = $.status(app.options.status_options);
        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.primary_goal = this.model.toJSON();
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.onRender();
          
            return this;
        },
        
        events: {
            "click .new_mini_goal" : "onClickNewMiniGoal",
            "click .edit_mini_goal" : "onClickEditMiniGoal",
            "click .finalise_mini_goals" : "onClickFinalise",
            "click .submit_mini_goals" : "onClickSubmit"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
            });
        },
        
        loadItems : function() {
              var self = this;
            _.each(this.collection.models, function(model) {
                 self.addItem(model);
            });
        },
        
        
        addItem : function(model) {
            var primary_goal_id = this.model.get('id');
            if(primary_goal_id == model.get('primary_goal_id')) {
                $(this.el).find(".minigoals_container").append(new List_item_mini_view({model : model, primary_goal_model : this.model}).render().el); 
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
        
        onClickFinalise : function() {
            var self = this;
            this.model.save({minigoals_status : '1'}, {
                success: function (model, response) {
                    app.collections.primary_goals.add(model);
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickSubmit : function() {
            var self = this;
            this.model.save({minigoals_status : '2'}, {
                success: function (model, response) {
                    app.collections.primary_goals.add(model);

                    //app.options.statuses.EVELUATING_GOAL_STATUS.id;
                    //self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        

        
        
        
     
    });
            
    return view;
});