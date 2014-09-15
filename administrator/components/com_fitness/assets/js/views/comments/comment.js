define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/comments/comment.html',
        'jquery.cleditor'
        
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.edit_mode = this.options.edit_mode || false;
        },
        
        template : _.template(template),

        render : function () {
            var data = {
                item : this.model.toJSON(),
                options : this.options,
                edit_mode : this.edit_mode
            };
            data.$ = $;
            $(this.el).html(this.template(data));
            
            this.onRender();

            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                if(self.options.show_editor && self.edit_mode) {
                    self.connectEditor($(self.el), ".comment_textarea", false);
                }
            });
        },
        
        events: {
            "click .save_parent_comment": "onClickSaveComment",
            "click .edit_parent_comment": "onClickEditComment",
        },
        
        onClickEditComment : function() {
            this.edit_mode = true;
            this.render();
        },
        
        connectEditor : function(element, selector, disabled) {
            element.find(selector).cleditor({width:'98%', height:100, useCSS:true})[0];

            element.find("iframe").contents().find("body").css('color', '#fff');

            element.find(".cleditorMain").css('background-color', 'rgba(255, 255, 255, 0.1)');
            
            var element = element.find(selector).cleditor()[0];
            if(element) {
                element.disable(disabled);
            }
        },
        
        onClickSaveComment : function() {
            this.edit_mode = false;
            
            var comment = $(this.el).find(".comment_textarea").val();
             
            if(typeof comment !== 'undefined') {
                comment = encodeURIComponent(comment);
            } else {
                comment = '';
            }
            
            this.model.set({comment : comment});
            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                alert(this.model.validationError);
                return;
            }
            var self = this;
            this.model.save(null, {
                success: function(model, response) {
                    self.render();
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        }
        
    });
            
    return view;

});