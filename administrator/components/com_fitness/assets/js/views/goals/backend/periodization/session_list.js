define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/periodization/sessions',
        'models/goals/periodization/session',
        'views/goals/backend/periodization/session_list_item',
	'text!templates/goals/backend/periodization/session_list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Periods_collection,
        Period_model,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {

        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));

            this.container_el = this.$el.find("#items_container");
            
            this.onRender();

            return this;
        },
        
        events: {
            "click #new_period" : "onClickNewPeriod",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
     
            });
        },
        
        loadItems :function() {
            var mini_goal_id = this.options.mini_goal_id;
            app.collections.periods = new Periods_collection();
            var self = this;      
            app.collections.periods.fetch({
                data : {mini_goal_id : mini_goal_id},
                success : function (collection, response) {
                    self.populateItems();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateItems : function() {
            var self = this;
            _.each(app.collections.periods.models, function(model) {
                self.addItem(model);
            });
        },
        
        
        addItem : function(model) {
            this.container_el.append(new List_item_view({collection : app.collections.periods, model : model}).render().el); 
        },
        
        onClickNewPeriod : function() {
            var model = new Period_model({ mini_goal_id : this.options.mini_goal_id});
            this.addItem(model);
        }
        
        
    });
            
    return view;
});