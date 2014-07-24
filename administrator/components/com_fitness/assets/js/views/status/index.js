define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/status/dialog',
	'text!templates/status/button.html',
], function (
        $,
        _,
        Backbone,
        app,
        Dialog_view,
        template 
    ) {

    var view = Backbone.View.extend({
        template:_.template(template),
        
        initialize : function() {
            this.model.bind("change:status", this.onModelStatusChange, this);
        },
        
        render : function(){
            var data = this.model.toJSON();
            
            data.options = this.options.settings;
    
            var template = _.template(this.template(data));
        
            this.$el.html(template);
            
            return this;
        },
        
        events: {
            "click .status_button_element" : "onClickItem",
        },
        
        onClickItem : function(event) {
            if(typeof this.options.settings.button_not_active != "" && this.options.settings.button_not_active == true) {
                return false;
            }
            
            $(".dialog_status_wrapper").remove();
            
            var dialog_html = new Dialog_view({model : this.model, settings : this.options.settings}).render().el;

            $("body").append(dialog_html);
            
            var position = $(event.target).offset();

            var top = position.top;
            var left = position.left;
            $(".dialog_status_wrapper").css('top', top + 'px');
            $(".dialog_status_wrapper").css('left', left + 'px');
        },
        
        onModelStatusChange : function() {
            this.render();
        }
        
    });
            
    return view;
});