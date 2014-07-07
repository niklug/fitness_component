define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/shopping_list_item.html',
], function (
        $,
        _,
        Backbone,
        app, 
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render:function () {
            var data = this.model.toJSON();
            data.$ = $;
            $(this.el).append(this.template(data));
            return this;
        },
    });
            
    return view;
});