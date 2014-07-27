define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/status/index',
	'text!templates/nutrition_plan/nutrition_guide/backend/menu_plan_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Status_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        tagName : "tr",
        
        template:_.template(template),

        render: function(){
            var template = _.template(this.template({item : this.model.toJSON(), $ : $}));
            this.$el.html(template);
            this.connectStatus(this.model);
            return this;
        },

        events: {
            "click .preview" : "onClickPreview",
            "click .delete" : "onClickDelete",
            "click .copy_menu_plan" : "onClickCopy",
        },

        onClickPreview : function(event) {
            var id = this.model.get('id');
            app.controller.navigate("!/menu_plan/" + id + "/" + this.options.nutrition_plan_id, true);
        },
        onClickDelete : function(event) {
            this.model.destroy({
                success : function() {
                    $(event.target).parent().parent().fadeOut();
                },
                error : function(response) {
                    alert(response.responseText);
                }
            });
        },
        
        connectStatus : function(model) {
            app.options.menu_status_options.button_not_active = false;
            $(this.el).find(".status_container").html(new Status_view({
                model : model,
                settings : app.options.menu_status_options
            }).render().el);
        },
        
        onClickCopy : function() {
            var id = this.model.get('id');
            app.controller.copy_menu_plan(id);
        }
    });
            
    return view;
});