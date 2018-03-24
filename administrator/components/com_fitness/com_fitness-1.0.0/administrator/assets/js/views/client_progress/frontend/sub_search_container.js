define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/client_progress/frontend/sub_search_item',
        'views/graph/progress_graph',
        'views/client_progress/frontend/data_item',
	'text!templates/client_progress/frontend/sub_search_container.html'

], function (
        $,
        _,
        Backbone,
        app,
        List_item_view,
        Progress_graph_view,
        Data_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
        },

        
        template:_.template(template),
        
        render: function(){
            var data = {item : {}};
            //console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.container_el = this.$el.find("#sub_search_items");
            
            this.onRender();
            
            return this;
        },
        
        events : {
            "click #search_sub" : "onClickSearch",
            "click #clear_sub" : "clear",
            "click #select_all" : "selectAll",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
            });
        },
        
        loadItems : function() {
            var self = this;
            _.each(this.collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem : function(model) {
            this.container_el.append(new List_item_view({ model : model}).render().el); 
        },
        
        onClickSearch : function() {
            var collection = this.checkedAppointmentsCollection(this.collection.models);
            this.connectGraph(collection);
            
            this.loadDataItems(collection);
        },
        
        checkedAppointmentsCollection : function(original_collection) {
            var collection = new Backbone.Collection;
            
            var ids = $(this.el).find(".sub_search_item:checked").map(function(){return $(this).val();}).get();
            
            _.each(original_collection, function(model) {
                var id =  model.get('id');
                if(ids.indexOf(id) != '-1') {
                    collection.add(model);
                }
            });
            
            return collection;
        },
        
     
        connectGraph : function(collection) {
            this.progress_graph = new Progress_graph_view({
                head_title : 'BODY FAT COMPARISON CHART',
                el : "#progress_graph_container",
                collection : collection,
                style : 'dark',
                color : "#287725",
                data_field_x : 'starttime',
                data_field_y : 'body_fat',
                y_title : 'Body Fat (%)',
                tooltip : true,
                setTooltipHtml : this.setTooltipHtml
            });
        },
        
        setTooltipHtml : function(html, model) {
           html +=  "DATE: " +  moment(new Date(Date.parse(model.get('starttime')))).format("ddd, D MMM  YYYY, hh:mm") + "</br>";
           html +=  "AGE (YRS): " +  model.get('age') + "</br>";
           html +=  "WEIGHT (KG): " +  model.get('weight') + "</br>";
           html +=  "BODY FAT (%): " +  model.get('body_fat') + "</br>";
           html +=  "LEAN MASS (KG): " +  model.get('lean_mass') + "</br>";
           return html;
        },
        
        clear : function(){
            $(this.el).find(".sub_search_item").attr('checked', false);
            $("#main_container, #progress_graph_container").empty();
        },
        
        loadDataItems : function(collection) {
            var container = $("#main_container");
            container.empty();
            var self = this;
            _.each(collection.models, function(model) {
                container.append(new Data_item_view({ model : model}).render().el); 
            });

        },
        
        selectAll : function() {
            $(this.el).find(".sub_search_item").attr('checked', true);
        }
  
  
    });
            
    return view;
});