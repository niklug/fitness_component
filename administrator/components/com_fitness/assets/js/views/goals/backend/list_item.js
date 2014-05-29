define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/mini_goals',
        'views/goals/backend/list_mini',
	'text!templates/goals/backend/list_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Mini_goals_collection,
        List_mini_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectStatus(self.model.get('id'), self.model.get('status'));
                
                self.connectMiniGoals();
     
            });
        },
        
        connectStatus : function(id, status) {
            var status_obj = $.status(app.options.status_options);
              
            var html =  status_obj.statusButtonHtml(id, status);

            this.$el.find("#status_button_place_" + id).html(html);

            //status_obj.run();
        },
        
        connectMiniGoals : function() {
            app.collections.mini_goals = new Mini_goals_collection();
            var self = this;
            app.collections.mini_goals.fetch({
                data : {primary_goal_id : this.model.get('id')},
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                    $(self.el).find(".minigoals_wrapper").html(new List_mini_view({collection : collection, model : self.model}).render().el);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 
        },
        
    });
            
    return view;
});