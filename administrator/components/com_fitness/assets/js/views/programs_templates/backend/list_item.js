define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs_templates/backend/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            return this;
        },
    });
            
    return view;
});