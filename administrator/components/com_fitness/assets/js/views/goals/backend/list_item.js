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
            
            if(app.collections.mini_goals) {
                this.onRender();
                return this;
            }
            app.collections.mini_goals = new Mini_goals_collection();
            var self = this;
            app.collections.mini_goals.fetch({
                wait : true,
                data : {user_id : app.options.user_id},
                success : function (collection, response) {
                    self.onRender();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 
            
            
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectStatus(self.model.get('id'), self.model.get('status'));
                
                self.loadMinigoalslist();
     
            });
        },
        
        connectStatus : function(id, status) {
            var status_obj = $.status(app.options.status_options);
              
            var html =  status_obj.statusButtonHtml(id, status);

            this.$el.find("#status_button_place_" + id).html(html);

            //status_obj.run();
        },
        

        
        loadMinigoalslist : function() {
            $(this.el).find(".minigoals_wrapper").html(new List_mini_view({collection : app.collections.mini_goals, model : this.model}).render().el);
        }
        
    });
            
    return view;
});