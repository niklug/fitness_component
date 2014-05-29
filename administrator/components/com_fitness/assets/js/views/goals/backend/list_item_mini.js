define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/goals/backend/list_item_mini.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.connectStatus(this.model.get('id'), this.model.get('status'));
            
            return this;
        },
        
        connectStatus : function(id, status) {
            var status_obj = $.status(app.options.status_options);
              
            var html =  status_obj.statusButtonHtml(id, status);

            this.$el.find("#status_button_place_" + id).html(html);

            //status_obj.run();
        },
    });
            
    return view;
});