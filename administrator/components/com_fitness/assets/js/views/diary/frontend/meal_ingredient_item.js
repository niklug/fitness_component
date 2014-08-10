define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/meal_ingredient_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
            tagName : "tr",
        
            initialize: function(){
                this.edit_mode();
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);

                return this;
            },
            
            events : {
                "click .delete_meal_ingredient" : "onClickDelete",
            },
            
            edit_mode : function() {
                this.model.set({edit_mode : true});
                return true;
                
                var edit_mode = false;
                
                if(this.model.get('edit_mode')) {
                    return true;
                }

                this.model.set({edit_mode : edit_mode});
            },
            
            onClickDelete : function() {
                this.close();
            },
            
            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
  
        });
            
    return view;
});