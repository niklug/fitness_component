define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/items',
        'models/assessments/request_params_items',
        'views/nutrition_plan/backend/targets/step1_assessment_item',
	'text!templates/nutrition_plan/backend/targets/step1.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Assessments_collection,
        Request_params_assessments_model,
        Step1_assessment_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize: function(){
            app.collections.assessments = new Assessments_collection();
            
            app.models.request_params_assessments = new Request_params_assessments_model({
                client_id : this.model.get('client_id'),
                appointment_types : '5',
                frontend_published : '2',
                published : '*',
                
                
            });
            app.models.request_params_assessments.bind("change", this.get_assessments, this);
        },
            
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
            });
        },
        
        events : {
            "click #search_assessments" : "search_assessments",
            "click #clear_assessments" : "clear_assessments",
            "change .assessment_item" : "onSelectAssessment",
        },
        
        get_assessments : function() {
            var params = app.models.request_params_assessments.toJSON();
            app.collections.assessments.reset();
            var self = this;
            app.collections.assessments.fetch({
                data : params,
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                    self.onLoadAssessments(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        search_assessments : function() {
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();
            app.models.request_params_assessments.set({
                date_from : date_from,
                date_to : date_to,
            });
        },
        
        clear_assessments : function(){
            $(this.el).find("#assessments_list_wrapper").empty();
            $(this.el).find("#date_from, #date_to").val('');
            app.models.request_params_assessments.set(
                {
                    date_from : '',
                    date_to : '',
                }
            );
        },
        
        onLoadAssessments : function(collection) {
            //console.log(collection.toJSON());
            $(this.el).find("#assessments_list_wrapper").empty();
            
            var self = this;
            if(collection.length) {
                _.each(collection.models, function(model) {
                    self.addItem(model);
                });
            }
        },
        
        addItem : function(model) {
            $(this.el).find("#assessments_list_wrapper").append(new Step1_assessment_item_view({model : model}).render().el);
        },
        
        onSelectAssessment : function(event) {
            var id = $(event.target).val();
            var  assessment_model = app.collections.assessments.get(id);
            
            $(this.el).find("#step1_age").val(assessment_model.get('age'));
            $(this.el).find("#step1_height").val(assessment_model.get('height'));
            $(this.el).find("#step1_weight").val(assessment_model.get('weight'));
            $(this.el).find("#step1_body_fat").val(assessment_model.get('body_fat'));
        }
    });
            
    return view;
});