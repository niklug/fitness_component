define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/exercise_library/exercise_library_item',
        'views/exercise_library/select_filter_block',
        'views/exercise_library/backend/menus/main_menu',
        'views/exercise_library/backend/exercise_details',
], function (
        $,
        _,
        Backbone,
        app,
        Exercise_library_item_model,
        Select_filter_block_view,
        Main_menu_view,
        Exercise_details_view
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            //unique id
            app.getUniqueId = function() {
                return new Date().getUTCMilliseconds();
            }
                        
            app.models.exercise_library_item = new Exercise_library_item_model();
        },

        routes: {
            "": "form_view", 
            "!/form_view": "form_view", 
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },

        form_view : function() {
            app.models.exercise_library_item.fetch({
                data : {},
                success: function (model, response) {
                    $("#main_menu").html(new Main_menu_view({model : app.models.exercise_library_item}).render().el);
                    $("#exercise_details_wrapper").html(new Exercise_details_view({model : app.models.exercise_library_item}).render().el);
                    $("#select_filter_wrapper").html(new Select_filter_block_view({model : app.models.exercise_library_item, block_width : '140px'}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        }
    });

    return Controller;
});