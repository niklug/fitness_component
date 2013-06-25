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

                    <label style="display:none" class="checkp">
                        <input id="IsAllDayEvent" name="IsAllDayEvent" type="checkbox" value="1" <?php if (isset($event) && $event->isalldayevent != 0 || JRequest::getVar("isallday") == "1") {
                    echo "checked";
                } ?>/><span id="s_all_day_event" class="inl">All Day Event</span>
                    </label>  
                    <div>  
                    </div>  
                </div>  
            </label>  

            <hr>