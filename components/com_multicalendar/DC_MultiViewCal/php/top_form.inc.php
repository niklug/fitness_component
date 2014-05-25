<script type="text/javascript">  
    (function($){
        $(document).ready(function() {
            /** set up appointment color
             *  
             */
            $('#Subject').change(function(){
               var id = $(this).find(':selected')[0].id;
               var catid = $(this).find(':selected').data('catid');
               setEndInterval(catid);
               $('#colorvalue').val(id);

               generateFormHtml(catid);
               // get session focus by category (appointment)
               setupSessionType(catid);

            });
            
            var catid = $(this).find(':selected').data('catid');
            generateFormHtml(catid);
            
            buildClientsSelect();

            $('#session_type').change(function(){
                var catid = $('#Subject').find(':selected').data('catid');
                var session_type = $(this).find(':selected').data('session_type');
                setupSessionFocus(catid, session_type);

            });

 
            setupSessionTypeOnLoad();
            /**
            *  delete group client
            */
            $(".delete_group_client").live('click', function() {
                deleteGroupClient($(this));
            });


           /** trainers onchange select
             *  
             */
            $('#add_client_button').click(function(){
               var trainer_id = $('#trainer').val();
               setClientsSelect(trainer_id);
            });



            $(".clients").live('change', function() {
                updateGroupClient($(this));
            });
            
            $("#trainer, #business_profile_id").live('change', function() {
                deleteEventClients();
            });
            
            

            $("#go_to_app").live('click', function() {
                goToAppointment();
            });
            /********************/ 
        });  



        // FUNCTIONS 
        
        function goToAppointment() {
            var event_id = '<?php echo $event->id; ?>';
            var appointment_id = $("#Subject").val();
            var appointment_type = 'programs';
            var is_client = '<?php echo $is_client ?>';
            
            if(appointment_id == '5') {
                appointment_type = 'assessments';
            }
            
            var url = '<?php echo JURI::base();?>' + 'administrator/index.php?option=com_fitness&view=' + appointment_type + '#!/form_view/' + event_id;
     
            if(parseInt(is_client)) {
                url = '<?php echo JURI::base();?>' + 'index.php?option=com_fitness&view=' + appointment_type + '#!/item_view/' + event_id;
            }

            window.open(url, '_blank');
        }
        
        function deleteGroupClient(this_object) {
            var id = this_object.closest('tr').find('select').data('id');
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
            var url = DATA_FEED_URL+ "&method=delete_group_client";
            this_object.closest("tr").remove();
            $.ajax({
                type : "POST",
                url : url,
                data : {
                   id : id
                },
                dataType : 'json',
                success : function(response) {
                    if(!response.success) {
                       alert(response.message);
                    }

                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    //alert("error");
                }
            }); 
        }

        function updateGroupClient(this_object) {
            var DATA_FEED_URL = "<?php echo $datafeed ?>&calid=<?php echo $_GET["calid"] ?>";
            var url = DATA_FEED_URL + "&method=add_update_group_client";
            var event_id = '<?php echo $event->id; ?>';
            var client_id = this_object.find(':selected').val();
            var this_select = this_object.closest('tr').find('select');
            
            this_select.attr('disabled', true);

            var id = this_object.closest('tr').find('select').data('id') || '';

            if (client_id) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        id: id,
                        event_id: event_id,
                        client_id: client_id
                    },
                    dataType: 'json',
                    success: function(response) {

                        if (response.success) {
                            this_select.attr('data-id', response.id);
                        } else {
                            this_select.attr('disabled', false);
                            this_select.val('');
                            alert(response.message);
                        }

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
            }
        }

        function setupSessionTypeOnLoad() {
           var id = $('#Subject').find(':selected')[0].id;
           var catid = $('#Subject').find(':selected').data('catid');
           $('#colorvalue').val(id);
           // get session focus by category (appointment)
           setupSessionType(catid);
        }

        /**
         * 
         * @param {type} catid
         * @returns {undefined}
         */
        function setupSessionType(catid) {
           var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
           var url = DATA_FEED_URL+ "&method=get_session_type";
           $.ajax({
                type : "POST",
                url : url,
                data : {
                   catid : catid,
                },
                dataType : 'json',
                success : function(message) {
                    var session_type = '<?php echo $event->session_type; ?>';
                    $('#session_type').html('<option value="" >-Select-</option>');
                    $.each(message, function(value, index) {
                        if(session_type == index) {
                            var selected = 'selected';
                        } else {
                            selected = '';
                        }
                        $('#session_type').append('<option ' + selected + ' data-session_type="' + index + '" value="' + index + '">' + value + '</option>');
                    });
                    var session_type = $('#session_type').find(':selected').data('session_type');
                    setupSessionFocus(catid, session_type);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }

         /**
         * 
         * @param {type} catid
         * @returns {undefined}
         */
        function setupSessionFocus(catid, session_type) {
           var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
           var url = DATA_FEED_URL+ "&method=get_session_focus";
           $.ajax({
                type : "POST",
                url : url,
                data : {
                   catid : catid,
                   session_type :session_type
                },
                dataType : 'json',
                success : function(message) {
                    var session_focus = '<?php echo $event->session_focus; ?>';
                    $('#session_focus').html('<option value="" >-Select-</option>');
                    $.each(message, function(value, index) {
                        if(session_focus == index) {
                            var selected = 'selected';
                        } else {
                            selected = '';
                        }
                        $('#session_focus').append('<option ' + selected + ' value="' + index + '">' + value + '</option>');
                    });
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }

        
        function buildClientsSelect() {
            var event_id = '<?php echo $event->id; ?>';
            var trainer_id = $('#trainers').val();
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
            var url = DATA_FEED_URL+ "&method=get_semi_clients";
            $.ajax({
                type : "POST",
                url : url,
                data : {
                   event_id : event_id
                },
                dataType : 'json',
                success : function(message) {
                    var html = '';

                    for( var i = 0; i < message.clients.length; i++) {
                        html += '<tr>';
                        html +='<td>';
                        html += '<select disabled="disabled" data-id="' + message.ids[i]  + '" class="inputtext clients"  name="clients[]">';
                        html += '<option  value="' + message.clients[i]  + '">' +  message.clients_name[i] + '</option>';
                        html += '</select>';
                        html +='</td>';
                        html +='<td>';
                        html += "<a href='#' data-id='" + message.ids[i] + "' class='delete_group_client'></a>";
                        html +='</td>';
                        html += '</tr>';

                    }
                    $("#clients_html").html(html);


                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }
        
        function deleteEventClients() {
            var event_id = '<?php echo $event->id; ?>';
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
            var url = DATA_FEED_URL+ "&method=delete_event_clients";
            $.ajax({
                type : "POST",
                url : url,
                data : {
                   event_id : event_id
                },
                dataType : 'json',
                success : function(message) {
                    $("#clients_html").empty();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }
        
        
        function setClientsSelect(trainer_id) {
            var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>"; 
            var url = DATA_FEED_URL+ "&method=get_clients";
           
            var added_clients = $(".clients").map(function() {return this.value});
           
            var empty_select = $(".clients").last().val();
           
            if(empty_select == ''){
                return;
            }
       

            $.ajax({
                type : "POST",
                url : url,
                data : {
                   trainer_id : trainer_id
                },
                dataType : 'json',
                success : function(response) {
                    //console.log(response.status.success);
                    if(!response.status.success) {
                        alert(response.status.message);
                        return;
                    }
                    
                    var html = '';
                    html += '<tr>';
                    html +='<td>';
                    html += '<select class="inputtext clients"   name="clients">';
                    html += '<option  value="">-Select-</option>';
                    $.each(response.data, function(value, index) {
                        
                        if($.inArray(index, added_clients) == '-1') {
                            html += '<option  value="' + index + '">' +  value + '</option>';
                        }
                    });

                    html += '</select>';
                    html +='</td>';
                    html +='<td>';
                    html += "<a href='#' class='delete_group_client'></a>";
                    html +='</td>';
                    html +='<td>';
                    html += '';
                    html +='</td>';
                    html += '</tr>';
                    $("#clients_html").append(html);

                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("error");
                }
            });
        }



        function generateFormHtml(form_id) {
            switch(form_id) {
                case 1:
                   personalTrainingForm();
                   break;
                case 2:
                   semiPrivateForm();
                   break;
                case 3:
                   resistanceWorkoutForm();
                   break;
                case 4:
                   cardioWorkoutForm();
                   break;
                case 5:
                   assessmentForm();
                   break;
                case 6:
                   consultationForm();
                   break;
                case 7:
                   specialEventForm();
                   break;
                case 8:
                   availableForm();
                   break;
                case 9:
                   unavailableForm();
                   break;
                default :
                   //personalTrainingForm(); 
            }
        }



        function personalTrainingForm() {
            showCommentsBlock(false, false);
        }

        function semiPrivateForm() {
            showCommentsBlock(false, false);
        }


        function resistanceWorkoutForm() {
            showCommentsBlock(false, false);
        }

        function cardioWorkoutForm() {
            showCommentsBlock(false, false);
        }

        function assessmentForm() {
            showCommentsBlock(false, false);
        }

        function consultationForm() {
            showCommentsBlock(true, true);
        }

        function specialEventForm() {
            showCommentsBlock(true, true);
        }

        function availableForm() {
            showCommentsBlock(true, false);
        }

        function unavailableForm() {
            showCommentsBlock(true, false);
        }
        
        function showCommentsBlock(show, readonly) {
            var comments_wraper = $("#comments_wrapper");
            var  frontend_published = parseInt($("#frontend_published").val());
            //console.log(frontend_published);
            if(show && frontend_published) {
                comments_wraper.show();
            } else {
                comments_wraper.hide();
            }
            if(readonly) {
                var element = $("#trainer_comments").cleditor()[0];
                if(element) {
                    element.disable(true);
                }
            }
                
        }
        
        

        function setEndInterval(form_id) {
            var endInterval;
            switch(form_id) {
                case 1:
                   endInterval = 45;
                   break;
                case 2:
                   endInterval = 30;
                   break;
                case 3:
                   endInterval = 45;
                   break;
                default :
                   endInterval = 60; 
            }
            set_etparttime(endInterval);
        }


        function set_etparttime(minutes) {
            var stparttime = $("#stparttime").val();
            var stparttime_part = stparttime.split(":");
            var date = new Date();
            date.setHours(stparttime_part[0]);
            date.setMinutes(stparttime_part[1]);
            var newdate = addMinutes(date, minutes);
            var hours = newdate.getHours();
            var minutes = newdate.getMinutes();
            $("#etparttime").val(pad(hours) + ':' + pad(minutes));
        }

        function addMinutes(inDate, inMinutes) {
            var newdate = new Date();
            newdate.setTime(inDate.getTime() + inMinutes * 60000);
            return newdate;
        }

        function pad(d) {
            return (d < 10) ? '0' + d.toString() : d.toString();
        }
    })($);
</script>  


<style type="text/css">  

    #repeatsave a,#repeatdelete a{width:150px;text-align:center;display:block;float:left;margin:3px 10px 20px 0px}
    .ui-dialog{ position: absolute;  }
    .ui-widget-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    .ui-widget-overlay { background: #eeeeee ; opacity: .80;filter:Alpha(Opacity=80); }

    .ui-datepicker-trigger     {
        width:23px;  
        height:23px;  
        border:none;  
        cursor:pointer;  
        background:url("<?php echo $path; ?>css/images/cal.gif") no-repeat center center;
        margin-left:5px; 
    }  
    #repeat,#repeatsave,#repeatdelete{display:none;font-family: "Lucida Grande","Lucida Sans Unicode",Arial,Verdana,sans-serif;font-size: 12px;}

    #repeat div{padding:2px;}
    #repeat label{width:100px;float:left}
    #repeat .fl{float:left}  
    #repeat .clear{clear:both}

    #repeat.ui-dialog-content{display:block}
</style>  
</head>  
<body class="multicalendar calendaredition">
    <table width="100%">
        <tr>
            <td style="text-align: left;">
                <h3 id="appointment_title">Add/Edit Appointment</h3>
            </td>
            <?php if($event->id) { ?>
            <td style="text-align: right;">
                <a style="font-size:12px;" id="go_to_app" href="javascript:void(0)">[GO TO APPOINTMENT]</a>
            </td>
            <?php } ?>
        </tr>
    </table>
    

    <div class="infocontainer ui-widget-content" >
        <hr>
        <form action="<?php echo $datafeed ?>&calid=<?php echo $_GET["calid"]; ?>&month_index=<?php echo JRequest::getVar("month_index"); ?>&method=adddetails<?php echo isset($event) ? "&id=" . $event->id : ""; ?>" class="fform" id="fmEdit" method="post">

            <?php
            if (isset($event) && ($event->rrule == "")) {  //no recurrent events
                $sarr = explode(" ", php2JsTime(mySql2PhpTime($event->starttime)));
                $earr = explode(" ", php2JsTime(mySql2PhpTime($event->endtime)));
                $shm = explode(":", $sarr[1]);
                $ehm = explode(":", $earr[1]);
                $stpartdate = $sarr[0];
                $stparttime = fomartTimeAMPM(intval($shm[0]), intval($shm[1]));
                $etpartdate = $earr[0];
                $etparttime = fomartTimeAMPM(intval($ehm[0]), intval($ehm[1]));
            } else if (JRequest::getVar("start") != "" && JRequest::getVar("end") != "") {
                $sarr = explode(" ", JRequest::getVar("start"));
                $earr = explode(" ", JRequest::getVar("end"));
                $shm = explode(":", $sarr[1]);
                $ehm = explode(":", $earr[1]);
                $stpartdate = $sarr[0];
                $stparttime = fomartTimeAMPM(intval($shm[0]), intval($shm[1]));
                $etpartdate = $earr[0];
                $etparttime = fomartTimeAMPM(intval($ehm[0]), intval($ehm[1]));
            } else {
                $stpartdate = "";
                $stparttime = "";
                $etpartdate = "";
                $etparttime = "";
            }
            if (JRequest::getVar("month_index") == "1" && $stpartdate != "" && $etpartdate != "") {
                $sarr = explode("/", $stpartdate);
                $stpartdate = $sarr[1] . "/" . $sarr[0] . "/" . $sarr[2];
                $earr = explode("/", $etpartdate);
                $etpartdate = $earr[1] . "/" . $earr[0] . "/" . $earr[2];
            }
        
            ?>  

            <label>  
                <div style="float:left;" > Start Date </div>
                <div style="float:left;margin-left:50px;<?php if ($stparttime == '00:00') echo 'visibility:hidden;' ?>"> Start Time </div>
                <div style="float:left;margin-left:16px;"> End Date </div>
                <div style="display: inline;float: none;margin-left: 54px;<?php if ($stparttime == '00:00') echo 'visibility:hidden;' ?>"> End Time </div>

                <div> 
                    <input <?php echo $readonly_attr; ?>  MaxLength="10" class="required date" id="stpartdate" name="stpartdate" type="text" value="<?php echo $stpartdate; ?>" />
                    <input <?php echo $readonly_attr; ?>  MaxLength="7" class="required time" id="stparttime" name="stparttime" style="width:52px;" type="text" value="<?php echo $stparttime; ?>" /><span id="s_to1" class="inl">&nbsp;&nbsp;&nbsp;</span>
                    <input <?php echo $readonly_attr; ?> MaxLength="10" class="required date" id="etpartdate" name="etpartdate" type="text" value="<?php echo $etpartdate; ?>" />
                    <input <?php echo $readonly_attr; ?> MaxLength="7" class="required time" id="etparttime" name="etparttime" style="width:52px;" type="text" value="<?php echo $etparttime; ?>" />
                    <input MaxLength="10" id="stpartdatelast" name="stpartdatelast" type="hidden" value="" />
                    <input MaxLength="10" id="etpartdatelast" name="etpartdatelast" type="hidden" value="" />
                    <input MaxLength="10" id="stparttimelast" name="stparttimelast" type="hidden" value="" />
                    <input MaxLength="10" id="etparttimelast" name="etparttimelast" type="hidden" value="" />
                    
                    <label  class="checkp">
                        <input <?php echo $readonly_attr; ?>  id="IsAllDayEvent" name="IsAllDayEvent" type="checkbox" value="1" <?php if (isset($event) && $event->isalldayevent != 0 ) {
                    echo "checked";
                } ?>/><span style="font-size:10px;font-weight:normal;" id="s_all_day_event" class="inl">All Day Event</span>
                    </label>  
                    <div>  
                    </div>  
                </div>  
            </label>  

            <hr>
