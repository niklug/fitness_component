define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/ingredients_search_results.html'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
            
            template:_.template(template),
            
            render: function(){
                var data = {};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                return this;
            },
            
            events : {

            },
        

            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
  
        });
            
    return view;
});