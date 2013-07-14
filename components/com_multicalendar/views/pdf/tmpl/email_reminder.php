<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body>
        <?php
        defined('_JEXEC') or die('Restricted access');
        require_once( JPATH_COMPONENT.'/DC_MultiViewCal/php/functions.php' );
        require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

        $event_id = &JRequest::getVar('event_id');
        
        $client_id = &JRequest::getVar('client_id');

        $event_data = getEmailPdfData($event_id);
        
        if(!$client_id) $client_id = $event_data->client_id;

        $width = '900px';
        
        $height = '1500px';
        ?>   
        <div  style="
              background-image: url('<?php echo JUri::base() ?>components/com_multicalendar/DC_MultiViewCal/css/images/email_reminder_bgr.png');
              position: absolute;
              width:<?php echo $width; ?>;
              height:<?php echo $height; ?>;
              background-size:<?php echo $width; ?>;
              background-repeat: no-repeat;
              font-family: Times New Roman;
              background-size: 900px auto !important;
              ">

           
            <div style="margin-left: 40px; margin-top: 0; padding: 10px; width: 793px;font-size:16px;">
                  
              
                <table style="width: 100%; margin-top: 380px;"  border="0">
                    <tr>
                        <td align="right">
                            <a target="_blank" href="<?php echo JUri::base() ?>index.php?option=com_multicalendar&task=confirm_email&event_id=<?php echo base64_encode($event_id) ?>" 
                                 style="
                              background-image: url('<?php echo JUri::base() ?>components/com_multicalendar/DC_MultiViewCal/css/images/confirm_email_button.png');
                              background-repeat: no-repeat;
                              display: block;
                              height: 85px;
                              width: 250px;
                              "></a>  
                        </td>
                    </tr>
                </table>
                
                 <table style="width: 500px;margin-top: 25px"  border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;" width="150px">Client Name:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php
                                $user = &JFactory::getUser($client_id);
                                echo $user->name;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Trainer Name:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php
                                $user = &JFactory::getUser($event_data->trainer_id);
                                echo $user->name;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Location:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php
                                echo $event_data->location;
                                ?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                    
                 <table style="width: 750px; margin-top: 25px;" border="0">
                    <tbody>
                        <tr>
                            <td style="width: 150px;color:#ffffff;">Start Date:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php 
                                $date = JFactory::getDate($event_data->starttime);
                                echo $date->toFormat('%A, %d %b %Y');
                                ?>
                                </i>
                            </td>
                            <td style="padding-left: 50px;color:#ffffff;">Finish Date:</td>
                            <td style="font-weight: bold;width:200px;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php 
                                $date = JFactory::getDate($event_data->endtime);
                                echo $date->toFormat('%A, %d %b %Y');
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Start Time:</td>
                            <td style="font-weight: bold;width:200px;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php 
                                $date = JFactory::getDate($event_data->starttime);
                                echo $date->format('H:i:s'); 
                                ?>
                                </i>
                            </td>
                            <td style="padding-left: 50px;color:#ffffff;">Finish Time:</td>
                            <td style="font-weight: bold;width:200px;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php 
                                $date = JFactory::getDate($event_data->endtime);
                                echo $date->format('H:i:s'); 
                                ?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                <table style="width: 500px; margin-top: 25px;"  border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;" width="150px;">Appointment:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php
                                echo $event_data->title;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Session Type:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php
                                echo $event_data->session_type;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Session Focus:</td>
                            <td style="font-weight: bold;color:#D14F16;background-color:#5c5c5c; border-radius:7px;padding-left: 10px;">
                                <i>
                                <?php
                                echo $event_data->session_focus;
                                ?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                
                <span></span>
            </div>
            <div style="font-size: 14px;  color: #FFFFFF;  margin-left: 40px; margin-top: 35px;font-size: 14px; padding: 10px; width: 792px;">
                (please arrive at least 5 mins before your scheduled appointment time. If you are training or doing anything physical in your appointment, please be sure to do a warm-up by walking, jogging or rowing for 5-10 mins - unless otherwise instructed not to do so)
            </div>
            
            <div style="  color: #FFFFFF;  margin-left: 40px; margin-top: 60px;font-size: 14px; padding: 10px; width: 792px;">
                <p style="color:#D14F16;">CANCELLATION POLICY</p>
                <p>24 hours notice is required to reschedule or cancel any training session or appointment.</p>
                <p>If less than 24 hours notice is given, FULL FEES will apply for that appointment, unless...</p>
                <p> - your appointment time can be filled by another client.<br /> - your appointment is able to be rescheduled to another time on the same day (availability depending).</p>
                <p>If you are late to your appointment, your appointment will still end at the scheduled time.<br /><br />If you do no show up to your appointment or cancel last minute, FULL FEES apply.</p>
                <p>If you are training early to mid-morning and you need to cancel or reschedule your appointment, you must do so BEFORE 3pm the preceding day to allow time to fill your appointment. Failure to do so will incur FULL SESSION FEES if you cancel too late!</p>
                <p style="color:#D14F16;">3 ‘STRIKES’ POLICY</p>
                <p>If you break any of the before mentioned policies, one ‘Strike' will be recorded against you.</p>
                <p>Each 'Strike' is cumulative and will remain on your record for a period of 1 month.</p>
                <p>Gaining a 3rd ’Strike' will result in your training being suspended for a length of time determined by your trainer. If this continues, you will no longer be able to continue training with Elite Fitness.</p>
            </div>
        </div>
        
    </body>
</html>


