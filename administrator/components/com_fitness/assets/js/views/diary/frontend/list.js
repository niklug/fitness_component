define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/diary/frontend/list_item',
	'text!templates/diary/frontend/list.html'
], function (
        $,
        _,
        Backbone,
        app,
        List_item_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            this.collection.bind("reset", this.clearItems, this);
        },
        
        template : _.template(template),

        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#diary_items");
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
            
            return this;
        },
        
        events: {
            "click #sort_entry_date" : "onClickSortEnryDate",
            "click #sort_status" : "onClickSortStatus",
            "click #sort_score" : "onClickSortScore",
            "click #sort_assessed_by" : "onClickAssessedBy",
            "click #sort_submit_date" : "onClickSubmitDate",
                        
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .preview" : "onClickPreview",
            "click #select_trashed" : "onClickSelectTrashed",
        },

        addItem : function(model) {
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
        },
        
        clearItems : function() {
            this.container_el.empty();
        },

        onClickSortEnryDate : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.entry_date'});
        },

        onClickSortStatus : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.status'});
        },

        onClickSortScore : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.score'});
        },
        
        onClickAssessedBy : function() {
            app.models.request_params_diaries.set({'sort_by' : 'assessed_by_name'});
        },

        onClickSubmitDate : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.submit_date'});
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            this.model = this.collection.get(id);
            var self  = this;
            this.model.save({state : '-2'}, {
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickRestore : function(event) {
            var id = $(event.target).attr('data-id');
            this.model = this.collection.get(id);
            var self = this;
            this.model.save({state : '1'}, {
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            this.model = this.collection.get(id);
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickSelectTrashed : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
        },

        onClickPreview : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/item_view/" + id, true);
        },
        
        hide_items : function(items) {
            var self = this;
            var items = items.split(",");
            _.each(items, function(item, key){ 
                self.container_el.find(".diary_row[data-id=" + item + "]").fadeOut();
            });
        },
        
        onClickSelectTrashed : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
        },

        close :function() {
            $(this.el).empty();
        }

    });
            
    return view;
});