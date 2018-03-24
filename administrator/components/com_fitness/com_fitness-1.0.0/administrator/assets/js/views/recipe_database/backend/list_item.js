define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/backend/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        initialize : function() {
            this.render();
        },
        
        template : _.template(template),
        
        render : function(){
            var data = {item : this.model.toJSON()};
            //console.log(this.model.toJSON());
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            app.controller.connectStatus(this.model, $(this.el));
            
            return this;
        },
        


    });
            
    return view;
});