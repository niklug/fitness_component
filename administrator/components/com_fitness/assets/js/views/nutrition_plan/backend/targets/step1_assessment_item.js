define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/targets/step1_assessment_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        tagName : "tr",
        
        render : function(){
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.append(template);
            return this;
        },
    });
            
    return view;
});