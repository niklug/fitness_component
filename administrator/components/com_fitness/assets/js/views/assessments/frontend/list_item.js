define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/frontend/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.app = app;
            data.current_page = app.models.request_params.get('current_page');
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            app.controller.connectStatus(this.model, this.$el);
            return this;
        },

    });
            
    return view;
});