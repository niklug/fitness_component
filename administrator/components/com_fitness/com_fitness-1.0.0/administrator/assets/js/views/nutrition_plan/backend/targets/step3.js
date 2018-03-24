define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/targets/step4',
	'text!templates/nutrition_plan/backend/targets/step3.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Step4_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            //console.log(this.model.toJSON());
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                if(self.model.get('id')) {
                    self.setChosenValues();
                }
                
                self.setLayout($(self.el).find("#common_profiles"));

                if(self.model.get('calories') && self.model.get('calories') != '0') {
                    self.showStep4(self.model);
                }
            });
        },
        
        events : {
            "change #common_profiles" : "onChangeCommonProfiles",
            "click #step3_claculate" : "onCalculate",
        },
        
        onChangeCommonProfiles : function(event){
            this.setLayout($(event.target));
        },
        
        setLayout : function(field) {
            $(this.el).find("#step3_protein, #step3_fats, #step3_carbs").removeClass("red_style_border");
            var value = field.find(":selected").attr('data-name');
            if(value == 'iifym') {
                $(this.el).find("#iifym_block").show();
                $(this.el).find("#other_common_profiles_block").hide();
            } else if(value == 'custom_ratios') {
                $(this.el).find("#iifym_block").hide();
                $(this.el).find("#other_common_profiles_block").show();
                this.preFillFieldsCustom(field);
            } else {
                $(this.el).find("#iifym_block").hide();
                $(this.el).find("#other_common_profiles_block").show();
                this.preFillFields(field);
            }
        },
        
        preFillFields : function(field) {
            var protein = field.find(":selected").attr('data-protein');
            var fat = field.find(":selected").attr('data-fat');
            var carbs = field.find(":selected").attr('data-carbs');
            
            $(this.el).find("#step3_protein").val(protein);
            $(this.el).find("#step3_fats").val(fat);
            $(this.el).find("#step3_carbs").val(carbs);
            
            this.model.set({
                protein_percent : protein/100,
                fat_percent : fat/100,
                carbs_percent : carbs/100,
            });
        },
        
        preFillFieldsCustom : function(field) {
            var protein = parseFloat($(this.el).find("#step3_protein").val());
            var fats =  parseFloat($(this.el).find("#step3_fats").val());
            var carbs =  parseFloat($(this.el).find("#step3_carbs").val());

            this.model.set({
                protein_percent : protein/100,
                fat_percent : fats/100,
                carbs_percent : carbs/100,
            });
        },
        
        onCalculate : function() {
            if(!this.validate() && $(this.el).find("#common_profiles").find(":selected").attr('data-name') != 'iifym') {
                return;
            }
            
            this.goStep4();
        },
        
        validate : function() {
            var step3_protein_field =  $(this.el).find("#step3_protein");
            
            var step3_fats_field = $(this.el).find("#step3_fats");
            
            var step3_carbs_field = $(this.el).find("#step3_carbs");
            
            $(this.el).find("#step3_protein, #step3_fats, #step3_carbs").removeClass("red_style_border");

            
            if(!this.number(step3_protein_field.val()) || parseInt(step3_protein_field.val()) > 100) {
                step3_protein_field.addClass("red_style_border");
                return false;
            }
            
            if(!this.number(step3_fats_field.val()) || parseInt(step3_fats_field.val()) > 100) {
                step3_fats_field.addClass("red_style_border");
                return false;
            }
            
            
            if(!this.number(step3_carbs_field.val()) || parseInt(step3_carbs_field.val()) > 100) {
                step3_carbs_field.addClass("red_style_border");
                return false;
            }
            return true;
        },
        
        number: function(value) {
            return  /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value);
        },
        
        goStep4 : function() {
            var common_profiles =  $(this.el).find("#common_profiles").find(":selected").attr('data-name');
            
            
            this.model.set({
                common_profiles : common_profiles,
                step3_protein : $(this.el).find(".step3_protein:checked").val(),
                step3_fats : $(this.el).find(".step3_fats:checked").val(),
                step3_protein_custom : $(this.el).find("#step3_protein_custom").val(),
                step3_fats_custom : $(this.el).find("#step3_fats_custom").val()
            });
            
            if(common_profiles == 'custom_ratios') {
                var protein = parseFloat($(this.el).find("#step3_protein").val());
                var fats =  parseFloat($(this.el).find("#step3_fats").val());
                var carbs =  parseFloat($(this.el).find("#step3_carbs").val());
                
                this.model.set({
                    step3_protein : protein,
                    step3_fats : fats,
                    step3_carbs : carbs,
                    protein_percent : protein/100,
                    fat_percent : fats/100,
                    carbs_percent : carbs/100,
                });
            }
            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    //console.log(model.toJSON());
                    self.showStep4(model);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        showStep4 : function(model) {
            $("#step4_fieldset").show();

            $("#step4_wrapper").html(new Step4_view({model : this.model}).render().el);
        },
        
        setChosenValues : function() {
            var common_profiles = this.model.get('common_profiles');
            
            if(common_profiles) {
                $('#common_profiles option').attr('selected', false);
                $('#common_profiles option[data-name="' + common_profiles + '"]').attr('selected', true);
            }
            
            if(common_profiles == 'iifym') {
                $(this.el).find(".step3_protein[value='" + this.model.get('step3_protein') + "']").prop('checked',true);
                $(this.el).find(".step3_fats[value='" + this.model.get('step3_fats') + "']").prop('checked',true);
            }
        }
   
    });
            
    return view;
});