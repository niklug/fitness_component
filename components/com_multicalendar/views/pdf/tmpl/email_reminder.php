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

        $event_data = getEmailPdfData($event_id);
        
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
              ">
           
           
            <div style="background-color: #35322F;border: 2px solid #FFFFFF; border-radius: 10px 10px 10px 10px;  color: #FFFFFF;
                 margin-left: 40px; margin-top: 490px; padding: 10px; width: 600px;">
                         
                 <table style="width: 100%;"  border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;" width="25%">Client Name:</td>
                            <td style="color:#D14F16;">
                                <i>
                                <?php
                                $user = &JFactory::getUser($event_data->client_id);
                                echo $user->name;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Trainer Name:</td>
                            <td style="color:#D14F16;">
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
                            <td style="color:#D14F16;">
                                <i>
                                <?php
                                echo $event_data->location;
                                ?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                    
                 <table style="width: 100%; margin-top: 30px;" border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;">Start Date:</td>
                            <td style="color:#D14F16;">
                                <i>
                                <?php 
                                $date = JFactory::getDate($event_data->starttime);
                                echo $date->toFormat('%A, %d %b %Y');
                                ?>
                                </i>
                            </td>
                            <td style="color:#ffffff;">Finish Date:</td>
                            <td style="color:#D14F16;">
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
                            <td style="color:#D14F16;">
                                <i>
                                <?php 
                                $date = JFactory::getDate($event_data->starttime);
                                echo $date->format('H:i:s'); 
                                ?>
                                </i>
                            </td>
                            <td style="color:#ffffff;">Finish Time:</td>
                            <td style="color:#D14F16;">
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
                    
                <table style="width: 100%; margin-top: 30px;"  border="0">
                    <tbody>
                        <tr>
                            <td style="color:#ffffff;" width="25%">Appointment:</td>
                            <td style="color:#D14F16;">
                                <i>
                                <?php
                                echo $event_data->title;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Session Type:</td>
                            <td style="color:#D14F16;">
                                <i>
                                <?php
                                echo $event_data->session_type;
                                ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#ffffff;">Session Focus:</td>
                            <td style="color:#D14F16;">
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
            <div style="background-color: #35322F;border: 2px solid #FFFFFF; border-radius: 10px 10px 10px 10px;  color: #FFFFFF;
                 margin-left: 40px; margin-top: 30px; padding: 10px; width: 600px;">
                
            </div>
        </div>
        
    </body>
</html>


