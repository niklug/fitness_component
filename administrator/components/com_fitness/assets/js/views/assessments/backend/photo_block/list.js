define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/assessment_photos',
        'models/assessments/assessment_photos',
        'views/assessments/backend/photo_block/item',
	'text!templates/assessments/backend/photo_block/list.html'

], function ( $, _, Backbone, app, Items_collection, Item_model, Item_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize : function() {
            
            this.collection = new Items_collection();
            
            var self = this;
            
            this.collection.fetch({
                data : {item_id : this.model.get('id'), db_table : app.options.db_table_photos},
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                    self.render();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })

        },
        
        render: function(){
            var data = {data : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.container_el = this.$el.find("#photos_wrapper");
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
            
            return this;
        },
        
        events: {
            "click #add_pthoto" : "onClickAdd",
        },

        addItem : function(model) {
             this.container_el.append(new Item_view({model : model}).render().el); 
        },

        onClickAdd : function() {
            var model = new Item_model({
                item_id : this.model.get('id')
            });
           
            var self = this;
            this.collection.create(model, {
                wait: true,
                success: function (model, response) {
                    self.addItem(model);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            })
        }

    });
            
    return view;
});