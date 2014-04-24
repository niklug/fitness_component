define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/frontend/menus/submenu_list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            
            this.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
            
            return this;
        },
        
        events : {
            "click #search" : "search",
            "click #clear_all" : "clearAll",
            "click #view_trash" : "onClickViewTrash",
            "click #close_trash_list" : "onClickCloseTrashList",
            "click #new_item" : "onClickAddItem",
            "change #sort_filter" : "onChangeSortFilter",
        },
        
        search : function() {
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();
            this.model.set({date_from : date_from, date_to : date_to});
        },
        
        clearAll : function(){
            var form = $("#filters_container");
            form.find(".filter_select").val(0);
            $("#date_from, #date_to").val('');
           
            this.model.set(
                {
                    date_from : '',
                    date_to : '',
                    client_name : '',
                    trainer_name : '',
                    created_by_name : '',
                    title : '',
                    location : '',
                    session_type : '',
                    session_focus : '',
                }
            );
        },
        
        onClickViewTrash : function() {
            app.controller.navigate("!/trash_list", true);
        },
        
        onClickCloseTrashList : function(){
            app.controller.navigate("!/my_workouts", true);
        },
        
        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
        },

    });
            
    return view;
});