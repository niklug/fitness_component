define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/checkbox_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data = {item : this.model.toJSON()};
            data.$ = $;
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.checkbox = $(this.el).find(".checkbox_item");
            
            if(this.options.disabled) {
                this.checkbox.attr('disabled', true);
            } else {
                this.checkbox.attr('disabled', false);
            }
            
            if(this.options.checked) {
                this.checkbox.attr('checked', true);
            } else {
                this.checkbox.attr('checked', false);
            }

            return this;
        },
        
    });
            
    return view;
});