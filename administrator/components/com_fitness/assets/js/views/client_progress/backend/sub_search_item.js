define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/client_progress/backend/sub_search_item.html'

], function (
        $,
        _,
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
        },
        
        template:_.template(template),
        
        render: function(){
            var data = {item : this.model.toJSON()};
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
           
            return this;
        },
        
        events : {

        },
  
    });
            
    return view;
});