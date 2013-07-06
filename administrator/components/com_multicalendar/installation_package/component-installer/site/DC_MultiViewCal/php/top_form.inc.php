<script type="text/javascript">  
$(document).ready(function() {
        var DATA_FEED_URL = "<?php echo $datafeed?>&calid=<?php echo $_GET["calid"]?>";
            /** set up appointment color
             *  npkorban
             */
            $('#Subject').change(function(){
               var id = $(this).find(':selected')[0].id;
               var catid = $(this).find(':selected').data('catid');
               $('#colorvalue').val(id);
               
               generateFormHtml(catid);
               // get session focus by category (appointment)
               setupSessionType(catid);
            });
            
            
            $('#session_type').change(function(){
                var catid = $('#Subject').find(':selected').data('catid');
                var session_type = $(this).find(':selected').data('session_type');
                setupSessionFocus(catid, session_type);

            });
            
            function setupSessionTypeOnLoad() {
               var id = $('#Subject').find(':selected')[0].id;
               var catid = $('#Subject').find(':selected').data('catid');
               $('#colorvalue').val(id);
               // get session focus by category (appointment)
               setupSessionType(catid);
            }
            setupSessionTypeOnLoad();
            /**
             * 
             * @param {type} catid
             * @returns {undefined}
             */
            function setupSessionType(catid) {
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
                        $('#session_type').html('');
                        $.each(message, function(index, value) {
                            if(session_type == value) {
                                var selected = 'selected';
                            } else {
                                selected = '';
                            }
                            $('#session_type').append('<option ' + selected + ' data-session_type="' + index + '" value="' + value + '">' + value + '</option>');
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
                        $('#session_focus').html('');
                        $.each(message, function(index, value) {
                            if(session_focus == value) {
                                var selected = 'selected';
                            } else {
                                selected = '';
                            }
                            $('#session_focus').append('<option ' + selected + ' value="' + value + '">' + value + '</option>');
                        });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
            }
            
            
            
           /** client onchange select
             *  npkorban
             */
            $('#client').change(function(){
               var client_id = $(this).find(':selected')[0].id;
               setTrainerSelect(client_id);
            });
            
            /** client onload select
             *  npkorban
             */
            function setTrainerSelectOnLoad() {
               var client_id = '<?php echo $event->client_id; ?>';
               setTrainerSelect(client_id);
            }
            setTrainerSelectOnLoad();
           /** client select
             *  npkorban
             */
            function setTrainerSelect(client_id) {
               var url = DATA_FEED_URL+ "&method=get_trainers";
                $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                       client_id : client_id
                    },
                    dataType : 'json',
                    success : function(message) {
                        $('#trainer').html('');
                        $.each(message, function(index, value) {
                            var client_id = '<?php echo $event->trainer_id; ?>';
                            if(client_id == index) {
                               var selected = 'selected';
                            } else {
                                selected = '';
                            }
                            if(index) {
                                $('#trainer').append('<option ' + selected + ' value="' + index + '">' + value + '</option>');
                            }
                        });
                     
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
            }

            /********************/ 
            /**
            * 

             * @param {type} form_id
             * @returns {undefined}             */
            function generateFormHtml(form_id) {
                //window.parent.$jc('#editEvent').dialog('close');
                //window.parent.$jc('#editEvent').dialog('open');
               
               
            /*
                 var url = DATA_FEED_URL+ "&method=generateFormHtml";
                 $.ajax({
                    type : "POST",
                    url : url,
                    data : {
                       form_id : form_id
                    },
                    dataType : 'text',
                    success : function(message) {
                        //$(".multicalendar").html(message);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("error");
                    }
                });
                */
            }
            
            
                    
             /* EVENT STATUS */
            $(".open_status").live('click', function(e) {
                var event_status = $(this).data('status');
                openSetEventStatusBox(event_status);
            });
            
            $(".set_status").live('click', function(e) {
                var event_status = $(this).data('status');
                eventSetStatus(event_status);
            });
            
            $(".hideimage").live('click', function(e) {
                hide_event_status_wrapper();
            });
            
            function openSetEventStatusBox(event_status) {
                 $(".event_status_wrapper").show();
                 $(".event_status__button").show();
                 if(event_status == 1)  $(".event_status_wrapper .event_status_pending").hide();
                 if(event_status == 2)  $(".event_status_wrapper .event_status_attended").hide();
                 if(event_status == 3)  $(".event_status_wrapper .event_status_cancelled").hide();
                 if(event_status == 4)  $(".event_status_wrapper .event_status_latecancel").hide();
                 if(event_status == 5)  $(".event_status_wrapper .event_status_noshow").hide();
            } 
            
            
            function hide_event_status_wrapper() {
                $(".event_status_wrapper").hide();
            }
            
            
                    
            function eventSetStatus(event_status) {
                var event_id = '<?php echo $event->id; ?>';
                var url = DATA_FEED_URL+ "&method=set_event_status";
                   $.ajax({
                        type : "POST",
                        url : url,
                        data : {
                            event_id : event_id,
                            event_status : event_status
                        },
                        dataType : 'text',
                        success : function(event_status) {
                            hide_event_status_wrapper();
                            $("#event_status").html( 'Appointment status' + event_status_html(event_status) );
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown)
                        {
                            alert("error");
                        }
                });

            }

            function event_status_html(event_status) {
                 if(event_status == 1)  return '<a data-status="' + event_status + '" class="open_status event_status_pending event_status__button" href="javascript:void(0)">pending</a>';
                 if(event_status == 2)  return '<a data-status="' + event_status + '"   class="open_status event_status_attended event_status__button" href="javascript:void(0)">attended</a>';
                 if(event_status == 3)  return '<a data-status="' + event_status + '"  class="open_status event_status_cancelled event_status__button" href="javascript:void(0)">cancelled</a>';
                 if(event_status == 4)  return '<a data-status="' + event_status + '" class="open_status event_status_latecancel event_status__button" href="javascript:void(0)">late cancel</a>';
                 if(event_status == 5)  return '<a data-status="' + event_status + '"  class="open_status event_status_noshow event_status__button" href="javascript:void(0)">no show</a>';

            }
            /* END EVENT STATUS */
            
           
        });  
        
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
    <h3 id="appointment_title">Add/Edit Appointment</h3>

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
                <?php
                if (isset($event->status)) {
                    ?>
                    <div id="event_status">
                        Appointment status
                    <?php
                    echo event_state_html($event->status);
                    ?>
                    </div>  
                        <?php
                    }
                    
                    ?>

                <div> 
                    <input MaxLength="10" class="required date" id="stpartdate" name="stpartdate" type="text" value="<?php echo $stpartdate; ?>" />
                    <input MaxLength="7" class="required time" id="stparttime" name="stparttime" style="width:52px;" type="text" value="<?php echo $stparttime; ?>" /><span id="s_to1" class="inl">&nbsp;&nbsp;&nbsp;</span>
                    <input MaxLength="10" class="required date" id="etpartdate" name="etpartdate" type="text" value="<?php echo $etpartdate; ?>" />
                    <input MaxLength="7" class="required time" id="etparttime" name="etparttime" style="width:52px;" type="text" value="<?php echo $etparttime; ?>" />
                    <input MaxLength="10" id="stpartdatelast" name="stpartdatelast" type="hidden" value="" />
                    <input MaxLength="10" id="etpartdatelast" name="etpartdatelast" type="hidden" value="" />
                    <input MaxLength="10" id="stparttimelast" name="stparttimelast" type="hidden" value="" />
                    <input MaxLength="10" id="etparttimelast" name="etparttimelast" type="hidden" value="" />

                    <label  class="checkp">
                        <input id="IsAllDayEvent" name="IsAllDayEvent" type="checkbox" value="1" <?php if (isset($event) && $event->isalldayevent != 0 || JRequest::getVar("isallday") == "1") {
                    echo "checked";
                } ?>/><span id="s_all_day_event" class="inl">All Day Event</span>
                    </label>  
                    <div>  
                    </div>  
                </div>  
            </label>  

            <hr>