define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/menus/submenu_exercise_database.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },
        
        events : {
            "click #search_by_name" : "search",
            "click #clear_all" : "clearAll",
            "click #view_trash" : "onClickViewTrash",
            "click #close_trash_list" : "onClickCloseTrashList",
            "click #add_item" : "onClickAddItem",
        },
        
        search : function() {
            var exercise_name = this.$el.find("#exercise_name").val();
            this.model.set({exercise_name : exercise_name});
        },
        
        clearAll : function(){
            var form = $("#filters_container");
            form.find(".filter_select").val(0);
            this.$el.find("#exercise_name").val('');
            
            this.model.set(
                {
                    exercise_name : '',
                    client_name : '',
                    exercise_type : '',
                    force_type : '',
                    mechanics_type : '',
                    body_part : '',
                    target_muscles : '', 
                    equipment_type : '',
                    difficulty : '',
                }
            );
        },
        
        onClickViewTrash : function() {
            app.controller.navigate("!/trash_list", true);
        },
        
        onClickCloseTrashList : function(){
            app.controller.navigate("!/my_exercises", true);
        },
        
        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
        }
    });
            
    return view;
});