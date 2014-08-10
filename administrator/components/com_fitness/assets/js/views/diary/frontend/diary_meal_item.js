define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/menu_descriptions',
        'views/programs/select_element',
	'text!templates/diary/frontend/diary_meal_item.html'
], function (
        $,
        _,
        Backbone,
        app,
        Menu_descriptions_collection,
        Select_element_view,
        template 
    ) {

    var view = Backbone.View.extend({
            initialize: function(){
                this.edit_mode();
            },
            
            template:_.template(template),
            
            render: function(){
                var data = {item : this.model.toJSON()};
                //console.log(this.model.toJSON());
                var template = _.template(this.template(data));
                this.$el.html(template);
                
                this.loadDescriptions();
                
                return this;
            },
            
            events : {
                "click .save_diary_meal" : "onClickSave",
                "click .edit_diary_meal" : "onClickEdit",
                "click .cancel_diary_meal" : "onClickCancel",
                "click .delete_diary_meal" : "onClickDelete",
            },
            
            onClickSave :function() {
                var description_field = this.$el.find('.diary_item_description');

                description_field.removeClass("red_style_border");

                var description = description_field.find(":selected").val();

                if(!description) {
                    description_field.addClass("red_style_border");
                    return false;
                }
                
                this.model.set({
                    description : description,
                });
                
                console.log(this.model.toJSON());
                
                if (!this.model.isValid()) {
                    var validate_error = this.model.validationError;

                    if(validate_error == 'description') {
                        description_field.addClass("red_style_border");
                        return false;
                    } else {
                        alert(this.model.validationError);
                        return false;
                    }
                }

                var self = this;
                this.model.save(null, {
                    success : function (model, response) {
                        self.model.set({edit_mode : false});
                        self.render();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            onClickEdit : function() {
                this.model.set({edit_mode : true});
                this.render();
            },

            onClickCancel : function(event) {
                this.model.set({edit_mode : false});
                this.render();
            },
            
            onClickDelete : function() {
                var self = this;
                this.model.destroy({
                    success: function (model) {
                        self.close();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            edit_mode : function() {
                var edit_mode = false;
                
                if(this.model.get('edit_mode')) {
                    return true;
                }

                this.model.set({edit_mode : edit_mode});
            },
            
            loadDescriptions : function() {
                var collection = Menu_descriptions_collection;
                var description_id = this.model.get('description');

                if(description_id) {
                    var model = collection.get(description_id);

                    if(model) {
                        var name = model.get('name');
                        var image = model.get('image');

                        if(image) {
                            this.$el.find(".description_image").css('background-image', 'url(' + image + ')');
                        }
                        this.$el.find(".description_select").html(name);
                    }
                }

                if(this.model.get('edit_mode')) {
                    new Select_element_view({
                        model : this.model,
                        el : this.$el.find(".description_select"),
                        collection : collection,
                        first_option_title : '-Select-',
                        class_name : 'diary_item_description dark_input_style',
                        id_name : 'description',
                        model_field : 'description',
                        element_disabled :  ""
                    }).render();
                }
            },

            onClickEdit : function() {
                this.model.set({edit_mode : true});
                this.render();
            },
            

            close :function() {
                $(this.el).unbind();
                $(this.el).remove();
            },
  
        });
            
    return view;
});