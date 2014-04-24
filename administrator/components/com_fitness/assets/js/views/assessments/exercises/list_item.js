define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/exercises/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            data.app = app;
            data.$ = $;
            data.readonly = this.options.readonly || false;
            var template = _.template(this.template(data));
            this.$el.append(template);
            
            this.setComments();
            
            return this;
        },
        
        setComments : function() {
            var exercise_comment_show =  localStorage.getItem("exercise_comment_show");

            if(parseInt(exercise_comment_show)) {
                this.$el.find(".comments").show();
            } else {
                this.$el.find(".comments").hide();
            }
       }
    });
            
    return view;
});