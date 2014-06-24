define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'views/programs/select_element',
	'text!templates/client_progress/frontend/search_header.html'

], function (
        $,
        _,
        Backbone,
        app,
        Select_filter_collection,
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            if(app.collections.session_focuses) {
                this.render();
                return;
            }

            app.collections.session_focuses = new Select_filter_collection();
            
            var self = this;
            
            $.when (
                app.collections.session_focuses.fetch({
                    data : {table : app.options.db_table_session_focuses, category_id : 5},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
            ).then (function(response) {
                self.render();
            })
            
            this.collection.on("sync", app.controller.load_sub_search, this);
        },

        
        template:_.template(template),
        
        render: function(){
            var data = {item : {}};
            //console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events : {
            "click #search" : "search",
            "click #clear_all" : "clearAll",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
                
                self.loadSessionFocus('25');
            });
        },
        
        loadSessionFocus : function(id) {
            var session_focus_collection = new Backbone.Collection;
            
            session_focus_collection.add(app.collections.session_focuses.where({session_type_id : id}));

            new Select_element_view({
                model : '',
                el : this.$el.find("#session_focus_select"),
                collection : session_focus_collection,
                first_option_title : '-Select-',
                class_name : ' required dark_input_style',
                id_name : 'session_focus',
                model_field : 'session_focus',
                element_disabled :  ''
            }).render();
        },
        
        search : function() {
            $("#main_container, #progress_graph_container, #sub_search_wrapper").empty();
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();
            var client_id = app.options.client_id;
            var session_focus = this.$el.find("#session_focus").val();
            this.model.set({current_page : '', date_from : date_from, date_to : date_to, client_id : client_id, session_focus : session_focus, limit : 100, published : '1',  frontend_published : '1', uid : app.getUniqueId()});
        },
        
        clearAll : function(){
            this.collection.reset();
            $("#session_focus, #date_from, #date_to").val('');
            $("#main_container, #progress_graph_container, #sub_search_wrapper").empty();
        },
        
    });
            
    return view;
});