define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/list_item.html'
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
            this.connectStatus();
            return this;
        },
  
        connectStatus : function() {
            var id = this.model.get('id');
            if(id) {
                var status = $.status(app.options.status_options);
            }
        },
    });
            
    return view;
});