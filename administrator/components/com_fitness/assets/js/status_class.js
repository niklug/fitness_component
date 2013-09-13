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

        $(this.options.status_button).live('click', function() {
            var item_id = $(this).attr('data-item_id');
            var status_id = $(this).attr('data-status_id');
            var dialog_html = self.generateDialogHtml(item_id, status_id);

            $("body").append(dialog_html);
        })

        $("." + this.options.hide_image_class).live('click', function() {
             self.closeDialog();
        })

        $("." + this.options.status_button_dialog).live('click', function() {
            var item_id = $(this).attr('data-item_id');
            var status = $(this).attr('data-status_id');
            var data = {
                'status' : status,
                'id' : item_id
            };


            self.ajaxCall(data, self.options.fitness_administration_url, 'nutrition_diary', 'updateDiaryStatus', self.options.db_table, function(output) {
                self.emailLogic(item_id, status);
                var status_button_html = self.statusButtonHtml(item_id, status);
                $(self.options.status_button_place + '' + item_id).html(status_button_html)
                self.closeDialog();
            });
        })

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
                    if(!response.status.IsSuccess) {
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

        var variables = { 'statuses' : this.options.statuses,
            'item_id' : item_id,
            'status_id' : status_id,
            'close_image' : this.options.close_image,
            'wrapper' : this.options.dialog_status_wrapper,
            'hide_image_class' : this.options.hide_image_class,
            'status_button_dialog' : this.options.status_button_dialog
        };
        var template = _.template( $(this.options.status_button_template).html(), variables );
        return template
    }
    
    Status.prototype.emailLogic = function(item_id, status_id) {
        if(this.options.statuses[status_id].email_alias !== 'undefined') {
            var method = this.options.statuses[status_id].email_alias;
            var send_email = $("#send_diary_email").is(':checked');
            if(method && send_email) {
                this.sendEmail(item_id, method);
            }
        }
    }
    
    Status.prototype.sendEmail = function(id, method) {
        var url = this.options.calendar_frontend_url;
        $.ajax({
                type : "POST",
                url : url,
                data : {
                    id : id,
                    method : 'send' + method + 'Email'
                },
                dataType : 'json',
                success : function(response) {
                    if(response.IsSuccess) {
                        var emails = response.Msg.split(',');

                        var message = 'Emails were sent to: ' +  "</br>";
                        $.each(emails, function(index, email) { 
                            message += email +  "</br>";
                        });
                        $("#emais_sended").append(message);

                    } else {
                        alert(response.Msg);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error sendEmail");
                }
        });
    }
    
    // Add the  function to the top level of the jQuery object
    $.status = function(options) {

        var constr = new Status(options);

        return constr;
    };
        
})(jQuery);



