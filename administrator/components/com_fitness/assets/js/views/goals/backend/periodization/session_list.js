define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/goals/periodization/sessions',
        'models/goals/periodization/session',
        'views/goals/backend/periodization/session_list_item',
	'text!templates/goals/backend/periodization/session_list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Sessions_collection,
        Session_model,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {

        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));

            this.container_el = this.$el.find(".sessions_container");
            
            this.onRender();

            return this;
        },
        
        events: {
            "click .add_session" : "onClickNewSession",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
     
            });
        },
        
        loadItems :function() {
            var period_id = this.model.get('id');
            this.collection = new Sessions_collection();
            var self = this;      
            this.collection.fetch({
                data : {period_id : period_id},
                success : function (collection, response) {
                    self.populateItems();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateItems : function() {
            var self = this;
            _.each(this.collection .models, function(model) {
                self.addItem(model, false);
            });
        },
        
        
        addItem : function(model, editable) {
            this.container_el.append(new List_item_view({collection : this.collection, model : model, editable : editable}).render().el); 
        },
        
        onClickNewSession : function() {
            var period_id = this.model.get('id');
            var model = new Session_model({ period_id : period_id});
            this.addItem(model, true);
        }
        
        
    });
            
    return view;
});