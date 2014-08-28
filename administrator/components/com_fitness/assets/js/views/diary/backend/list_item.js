define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/status/index',
	'text!templates/diary/backend/list_item.html'
], function ( $, _, Backbone, app, Status_view, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            this.connectStatus(this.model, "#status_wrapper_" + this.model.get('id'));
            
            return this;
        },
        
        connectStatus : function(model, target) {
            $(this.el).find(target).html(new Status_view({
                model : model,
                settings : app.options.status_options
            }).render().el);
        },
    });
            
    return view;
});