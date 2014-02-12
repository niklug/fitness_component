define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/backend/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.app = app;
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
                status.run();
            }
        },

    });
            
    return view;
});