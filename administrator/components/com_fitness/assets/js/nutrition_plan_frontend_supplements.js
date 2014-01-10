/*
 * 
 */
(function($, Backbone) {
    function NutritionPlanSupplements() {
        
        window.app = window.app || {};
        Backbone.emulateHTTP = true ;
        Backbone.emulateJSON = true;
        
        
       //VIEWS
        window.app.Nutrition_plan_protocols_view = Backbone.View.extend({
            
            initialize: function(){
                
            },
            
            render: function(){
                this.nutrition_plan_id = this.model.get('id');
                var template = _.template( $("#nutrition_plan_frontend_supplements_template").html(), {'id' : this.nutrition_plan_id});
                this.$el.html(template);

                var self = this;
                
		this.protocolListItemViews = {};

		this.collection.on("add", function(protocol) {
                    var comment_options = {
                        'item_id' : self.nutrition_plan_id,
                        'fitness_administration_url' : window.app.protocol_options.fitness_frontend_url,
                        'comment_obj' : {'user_name' : window.app.protocol_options.user_name, 'created' : "", 'comment' : ""},
                        'db_table' : window.app.protocol_options.protocol_comments_db_table,
                        'read_only' : true,
                    };
                    
                    var comments = $.comments(comment_options, comment_options.item_id, protocol.id);
                    window.app.nutrition_plan_protocol_view = new window.app.Nutrition_plan_protocol_view({collection : this,  model : protocol, 'comments' : comments}); 
                    self.$el.find(".protocols_wrapper").append( window.app.nutrition_plan_protocol_view.render().el );
                    self.protocolListItemViews[ protocol.cid ] = window.app.nutrition_plan_protocol_view;
		});
 
                return this;
            },
            
            events: {
                "click #pdf_button_supplements" : "onClickPdf",
                "click #email_button_supplements" : "onClickEmail",
            },
            
            onClickPdf : function(event) {
                var id = $(event.target).attr('data-id');
                var htmlPage = window.fitness_helper.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_nutrition_plan_supplements&id=' + id + '&client_id=' + this.model.get('client_id');
                window.fitness_helper.printPage(htmlPage);
            },
            
            onClickEmail : function(event) {
                var data = {};
                data.url = window.fitness_helper.ajax_call_url
                data.view = '';
                data.task = 'ajax_email';
                data.table = '';

                data.id = $(event.target).attr('data-id');
                data.view = 'NutritionPlan';
                data.method = 'email_pdf_nutrition_plan_supplements';
                window.fitness_helper.sendEmail(data);
            },

        });
        
        
        window.app.Nutrition_plan_protocol_view = Backbone.View.extend({
           
            initialize: function(){
                _.bindAll(this,'close', 'render', 'connectComments');
                this.model.on("destroy", this.close, this);
            },
            
            render: function(){
                var template = _.template( $("#nutrition_plan_frontend_protocol_item_template").html(), this.model.toJSON());
                this.$el.html(template);

                this.supplements_list_el = this.$el.find(".supplements_list");
                
                this.supplement_collection = new  window.app.Supplements_collection();
  
                this.protocol_id = this.model.get('id');
                
                if(this.protocol_id ){ 
                    this.supplement_collection.fetch({data: {nutrition_plan_id : this.model.get('nutrition_plan_id'), protocol_id : this.protocol_id}});
                }

                var self = this;
                
		this.supplementListItemViews = {};

		this.supplement_collection.on("add", function(supplement) {
                    window.app.nutrition_plan_supplement_view = new window.app.Nutrition_plan_supplement_view({collection : self.supplement_collection, model : supplement}); 
                    self.supplements_list_el.append( window.app.nutrition_plan_supplement_view.render().el );
                    self.supplementListItemViews[ supplement.cid ] = window.app.nutrition_plan_supplement_view;
		});
                
                this.connectComments();
                
                return this;
            },
            
            connectComments : function() {
                if(typeof this.options.comments !== 'undefined') {
                    var comments = this.options.comments.run();
                    this.$el.find(".comments_wrapper").html(comments);
                }
            },

            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            },

        });
        
        window.app.Nutrition_plan_supplement_view = Backbone.View.extend({

            render: function(){
                var template = _.template( $("#nutrition_plan_frontend_supplement_template").html(), this.model.toJSON());
                this.$el.html(template);
                return this;
            },
                    
            events: {
                "click .view_product": "onClickViewProduct"
            },

            onClickViewProduct : function(event) {
                var url = $(event.target).attr('data-url');
                window.open(url);
            },
            
            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
            
        });
        
         //MODELS
        
        window.app.Protocol_model = Backbone.Model.extend({
            urlRoot : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.app.protocol_options.nutrition_plan_id,
                name : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.name) {
                  return 'Protocol Name is empty';
                }
            }
        });
        
        window.app.Supplement_model = Backbone.Model.extend({
            urlRoot : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            
            defaults : {
                id : null,
                nutrition_plan_id : window.app.protocol_options.nutrition_plan_id,
                protocol_id : null,
                name : null,
                description : null,
                comments : null,
                url : null,
            },

        });
        
        // COLLECTIONS
        window.app.Protocols_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_protocol&',
            model: window.app.Protocol_model
        });

        
        
        window.app.Supplements_collection = Backbone.Collection.extend({
            url : window.app.protocol_options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            model: window.app.Supplement_model
        });
 
    }

    // Add the  function to the top level of the jQuery object
    $.NutritionPlanSupplements = function(options) {

        var constr = new NutritionPlanSupplements();

        return constr;
    };
        
})(jQuery, Backbone);



