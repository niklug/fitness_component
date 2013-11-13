/*
 * 
 */
(function($) {
    function Status(options) {
        this.options = options;
    }


    Status.prototype.run = function() {
        this.setEventListeners();
    }

    Status.prototype.setEventListeners = function() {
        var self = this;

        $("." + this.options.status_button).die().live('click', function() {
            $("#" + self.options.dialog_status_wrapper).remove();
            var item_id = $(this).attr('data-item_id');
            var status_id = $(this).attr('data-status_id');
            var dialog_html = self.generateDialogHtml(item_id, status_id);

            $("body").append(dialog_html);
            var position = $(this).position();
            var top = position.top;
            var left = position.left;
            $("#" + self.options.dialog_status_wrapper).css('top', top + 'px');
            $("#" + self.options.dialog_status_wrapper).css('left', left + 'px');
        
        })

        $("." + this.options.hide_image_class).die().live('click', function() {
             self.closeDialog();
        })

        $("." + this.options.status_button_dialog).die().live('click', function() {
            var item_id = $(this).attr('data-item_id');
            var status = $(this).attr('data-status_id');
            var data = {
                'status' : status,
                'id' : item_id
            };

            self.ajaxCall(data, self.options.fitness_administration_url, 'nutrition_diary', 'updateStatus', self.options.db_table, function(output) {
                self.emailLogic(item_id, status);
                var status_button_html = self.statusButtonHtml(item_id, status);
                $(self.options.status_button_place + '' + item_id).html(status_button_html);
                self.closeDialog();
                if((self.options.set_updater !== 'undefined') && (self.options.set_updater == true)) {
                    self.setUpdaterId(item_id);
                }
            });
        })

    }
    
    
    Status.prototype.setUpdaterId = function(item_id) {
        var user_id = this.options.user_id;
        var data = {
                'reviewed_by' : user_id,
                'id' : item_id
        };
        
        var self = this;

        this.ajaxCall(data, self.options.fitness_administration_url, 'nutrition_diary', 'updateStatus', self.options.db_table, function(output) {

        });
    }



    Status.prototype.generateDialogHtml = function(item_id, status_id) {
        var statuses = this.options.setStatuses(item_id);
        var variables = { 'statuses' : statuses,
            'item_id' : item_id,
            'status_id' : status_id,
            'close_image' : this.options.close_image,
            'wrapper' : this.options.dialog_status_wrapper,
            'hide_image_class' : this.options.hide_image_class,
            'status_button_dialog' : this.options.status_button_dialog,
            'show_send_email' : this.options.show_send_email
        };
        var template = _.template($(this.options.dialog_status_template).html(), variables);
        return template
    }

    Status.prototype.closeDialog = function() {
        $("#" + this.options.dialog_status_wrapper).remove();
    }

    Status.prototype.ajaxCall = function(data, url, view, task, table, handleData) {
        var data_encoded = JSON.stringify(data);
            $.ajax({
                type : "POST",
                url : url,
                data : {
                    view : view,
                    task : task,
                    format : 'text',
                    data_encoded : data_encoded,
                    table : table
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.status.success) {
                        alert(response.status.Msg);
                        return;
                    }
                    handleData(response.data);

                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert( task + " error");
                }
            }); 
    }

    Status.prototype.statusButtonHtml = function(item_id, status_id) {
        var statuses = this.options.setStatuses(item_id);
        var variables = { 'statuses' : statuses,
            'item_id' : item_id,
            'status_id' : status_id,
            'close_image' : this.options.close_image,
            'wrapper' : this.options.dialog_status_wrapper,
            'hide_image_class' : this.options.hide_image_class,
            'status_button_dialog' : this.options.status_button_dialog,
            'status_button' : this.options.status_button
        };
        var template = _.template( $(this.options.status_button_template).html(), variables );
        return template
    }
    
    Status.prototype.emailLogic = function(item_id, status_id) {
        var statuses = this.options.setStatuses(item_id);
        
        var method = statuses[status_id].email_alias;
        var send_email = $("#send_diary_email").is(':checked');
        if(method && send_email) {
            this.sendEmail(item_id, method);
        }
     
    }
    
    Status.prototype.sendEmail = function(id, method) {
        var data = {};
        var url = this.options.fitness_administration_url;
        var view = '';
        var task = 'ajax_email';
        var table = '';
        
        data.id = id;
        data.view = this.options.view;
        data.method = method;
        
        
        $.AjaxCall(data, url, view, task, table, function(output){
            console.log(output);
            var emails = output.split(',');
            var message = 'Emails were sent to: ' +  "</br>";
            $.each(emails, function(index, email) { 
                message += email +  "</br>";
            });
            $("#emais_sended").append(message);
        });
    }
    

    
    // Add the  function to the top level of the jQuery object
    $.status = function(options) {

        var constr = new Status(options);

        return constr;
    };
        
})(jQuery);



