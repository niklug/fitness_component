define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/items',
        'models/assessments/request_params_items',
        'views/nutrition_plan/backend/targets/step1_assessment_item',
        'views/nutrition_plan/backend/targets/step2',
	'text!templates/nutrition_plan/backend/targets/step1.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Assessments_collection,
        Request_params_assessments_model,
        Step1_assessment_item_view,
        Step2_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize: function(){
            $(document).tooltip({
                content: function () {
                    return $(this).prop('title');
                }
            });
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
            var data = {item : this.model.toJSON()};
            console.log(data);
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
                
                if(self.model.get('id')) {
                    self.setChosenValues();
                }
                
                if(self.model.get('step2_fat_loss')) {
                    self.showStep2(self.model);
                }
                
            });
        },
        
        events : {
            "click #search_assessments" : "search_assessments",
            "click #clear_assessments" : "clear_assessments",
            "change .assessment_item" : "onSelectAssessment",
            "click #step1_claculate" : "onCalculate",
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
            this.clearSearchValidation();
            
            var date_from_field =  $(this.el).find("#date_from");
            var date_to_field = $(this.el).find("#date_to");
            
            var date_from = date_from_field.val();
            var date_to = date_to_field.val();
 
            if(!date_from) {
                date_from_field.addClass("red_style_border");
                return;
            }
            
            if(!date_to) {
                date_to_field.addClass("red_style_border");
                return;
            }
            
            app.models.request_params_assessments.set({
                date_from : date_from,
                date_to : date_to,
                uid : app.getUniqueId()
            });
        },
        
        clear_assessments : function(){
            this.clearSearchValidation();
            $(this.el).find("#assessments_list_wrapper").empty();
            $(this.el).find("#date_from, #date_to").val('');
        },
        
        clearSearchValidation : function() {
            $(this.el).find("#date_from, #date_to").removeClass("red_style_border");
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
        },
        
        onCalculate : function() {
            this.gender = $(this.el).find("input[name=step1_gender]:checked").val();
            this.formula = $(this.el).find("input[name=step1_formula]:checked").val();
            
            this.age = $(this.el).find("#step1_age").val();
            this.height = $(this.el).find("#step1_height").val();
            this.weight = $(this.el).find("#step1_weight").val();
            this.body_fat = $(this.el).find("#step1_body_fat").val();
            this.exercise_level = $(this.el).find("#exercise_level").find(":selected").val();
            this.exercise_level_water = $(this.el).find("#exercise_level").find(":selected").attr('data-water');
            this.climate = $(this.el).find("#climate").find(":selected").val();
            
            if(!this.validate()) {
                return;
            }
            
            this.BMR = this.calculate_BMR(this.gender, this.formula);
            this.TDEE = this.BMR * this.exercise_level;
            
            $(this.el).find("#step1_bmr").val(this.BMR.toFixed(0) );
            $(this.el).find("#step1_tdee").val(this.TDEE.toFixed(0) );
            
            this.goStep2();
        },
        
        calculate_BMR : function(gender, formula) {
            if(gender == 'male' && formula == 'lean' ) {
                return this.calculate_BMR_male_lean();
            }
            
            if(gender == 'male' && formula == 'overweight' ) {
                return this.calculate_BMR_male_overweight();
            }
            
            if(gender == 'female'  && formula == 'lean'  ) {
                return this.calculate_BMR_female_lean();
            }
            
            if(gender == 'female'  && formula == 'overweight'  ) {
                return this.calculate_BMR_female_overweight();
            }
        },
        
        calculate_BMR_male_lean : function() {
            var BMR = 10*this.weight + 6.25*this.height - 5*this.age + 5;
            return BMR;
        },
        
        calculate_BMR_male_overweight : function() {
            var BMR = 370 + (21.6*this.weight * ((100 - this.body_fat)/100));
            return BMR;
        },
        
        calculate_BMR_female_lean : function() {
            var BMR = 10*this.weight + 6.25*this.height - 5*this.age - 161;
            return BMR;
        },
        
        calculate_BMR_female_overweight : function() {
            return this.calculate_BMR_male_overweight();
        },
        
        validate : function() {
            var age_field =  $(this.el).find("#step1_age");
            
            var height_field = $(this.el).find("#step1_height");
            
            var weight_field = $(this.el).find("#step1_weight");
            
            var body_fat_field =  $(this.el).find("#step1_body_fat");
            
            $(this.el).find("#step1_age, #step1_height, #step1_weight, #step1_body_fat").removeClass("red_style_border");
            
            if(!this.digits(age_field.val())) {
                age_field.addClass("red_style_border");
                return false;
            }
            
            if(!this.number(height_field.val())) {
                height_field.addClass("red_style_border");
                return false;
            }
            
            if(!this.number(weight_field.val())) {
                weight_field.addClass("red_style_border");
                return false;
            }
            
            
            if(!this.number(body_fat_field.val()) && this.formula != 'lean') {
                body_fat_field.addClass("red_style_border");
                return false;
            }
            return true;
        },
        
        digits: function(value) {
            return /^\d+$/.test(value);
        },
        
        number: function(value) {
            return  /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value);
        },
        
        goStep2 : function() {
            this.model.set({
                gender : this.gender,
                formula : this.formula,
                age : this.age,
                height : this.height,
                weight : this.weight,
                body_fat : this.body_fat,
                exercise_level : this.exercise_level,
                exercise_level_water : this.exercise_level_water,
                BMR : this.BMR.toFixed(0),
                TDEE : this.TDEE.toFixed(0),
                climate : this.climate
            });
            //console.log(this.model.toJSON());
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    self.showStep2(model);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        showStep2 : function(model) {
            //console.log(model.toJSON());
            $("#step2_fieldset").show();
            $("#step2_wrapper").html(new Step2_view({model : model}).render().el);
        },
        
        setChosenValues : function() {
            $(this.el).find("#step1_gender_" + this.model.get('gender')).prop('checked',true);
            
            $(this.el).find("#step1_formula_" + this.model.get('formula')).prop('checked',true);
            
            $(this.el).find("#exercise_level").val(this.model.get('exercise_level'));
            
            $(this.el).find("#climate").val(this.model.get('climate'));
        }
    });
            
    return view;
});