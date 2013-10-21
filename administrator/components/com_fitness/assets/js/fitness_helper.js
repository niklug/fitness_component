(function($) {
    
    function FitnessHelper(options) {
         //// Helper Model
        Helper_model = Backbone.Model.extend({
            defaults: {
                
            },

            initialize: function(){
                
            },
            
            ajaxCall : function(data, url, view, task, table, handleData) {
                return $.AjaxCall(data, url, view, task, table, handleData);
            },
            
            getClientsByBusiness : function(view, business_profile_id) {
                var data = {};
                var url = this.get('ajax_call_url');
                var view = view;
                var task = 'getClientsByBusiness';
                var table = '';
                data.business_profile_id = business_profile_id;
                
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("clients", output);
                });
            },
            
            getTrainersByBusiness : function(view, business_profile_id) {
                var data = {};
                var url = this.get('ajax_call_url');
                var view = view;
                var task = 'onBusinessNameChange';
                var table = '#__fitness_business_profiles';
                data.business_profile_id = business_profile_id;
                
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("trainers", output);
                });
            },
            
            populateSelect : function(data, target, selected_value) {
                var html = '<option  value="">-Select-</option>';
                $.each(data, function(index, value) {
                    if(index) {
                        var selected = '';
                        if(selected_value == index) {
                            selected = 'selected';
                        }
                        html += '<option ' + selected + ' value="' + index + '">' +  value + '</option>';
                    }
                });
                $(target).html(html);
                return html;
            },
            
            populateClientsSelectOnBusiness : function(model, business_profile_id, target, selected) {
                this.getClientsByBusiness(model, business_profile_id);
                this.on('change:clients', function(model, items) {
                    model.populateSelect(items, target, selected);
                });
            },
            
            populateTrainersSelectOnBusiness : function(model, business_profile_id, target, selected) {
                this.getTrainersByBusiness(model, business_profile_id);
                this.on('change:trainers', function(model, items) {
                    model.populateSelect(items, target, selected);
                });
            },
            
            hideSelectOption : function(value, element, all_options) {
                $(element).html(all_options);
                $(element + " option[value=" + value + "]").remove();
            },
            
            excludeSelectOption : function(select1, select2) {
                var self = this;
                $(select1).live('change', function() {
                    var all_options_select2 = $(select2).html();
                    var value = $(this).val();
                    self.hideSelectOption(value, select2, all_options_select2);
                });
                //on load
                var value = $(select1).val();
                if(value) {
                    var all_options_select2 = $(select2).html();
                    this.hideSelectOption(value, select2, all_options_select2);
                }
            }

        });
        
        return new Helper_model(options);
    }


    $.fitness_helper = function(options) {

        var constr = new FitnessHelper(options);

        return constr;
    };


})(jQuery);