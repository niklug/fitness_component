define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/backend/menu_plan_list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render: function(){
            var template = _.template(this.template({items : this.collection.toJSON(), $ : $}));
            this.$el.html(template);
            this.connectStatus();
            return this;
        },

        events: {
            "click .preview" : "onClickPreview",
            "click .delete" : "onClickDelete",
            "click .copy_menu_plan" : "onClickCopy",
        },

        onClickPreview : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/menu_plan/" + id + "/" + this.options.nutrition_plan_id, true);
        },
        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            this.model = this.collection.get(id);
            this.model.destroy({
                success : function() {
                    $(event.target).parent().parent().fadeOut();
                },
                error : function(response) {
                    alert(response.responseText);
                }
            });
        },
        connectStatus : function() {
            var status = $.status(app.options.status_options);
            status.run();
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.copy_menu_plan(id);
        }
    });
            
    return view;
});