define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'views/status/index',
	'text!templates/nutrition_plan/nutrition_guide/frontend/menu_plan_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Menu_plan_model,
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
            "click .copy_menu_plan" : "onClickCopy",
        },

        onClickPreview : function(event) {
            var id = this.model.get('id');
            app.controller.navigate("!/menu_plan/" + id + "/" + this.options.nutrition_plan_id, true);
        },
        
        connectStatus : function(model) {
            app.options.menu_status_options.button_not_active = true;
            $(this.el).find(".status_container").html(new Status_view({
                model : model,
                settings : app.options.menu_status_options
            }).render().el);
        },
        
        onClickCopy : function() {
            var id = this.model.get('id');
            this.copy_menu_plan(id);
        },
        
        copy_menu_plan : function(id) {
            var menu_plan_model = new Menu_plan_model({id : id});
            var self = this;
 
            menu_plan_model.fetch({
                wait : true,
                success: function (model, response) {
                    model.set({
                        id : null, 
                        created_by : app.options.user_id,
                        submit_date : null,
                        status : '4',
                        assessed_by : null,
                    });
                    model.save(null, {
                        success: function (model, response) {
                            self.collection.add(model);
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    });
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
         },
    });
            
    return view;
});