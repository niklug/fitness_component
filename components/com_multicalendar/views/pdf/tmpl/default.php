<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body onload="javascript:print()">
        <?php
        defined('_JEXEC') or die('Restricted access');
        require_once( JPATH_COMPONENT.'/DC_MultiViewCal/php/functions.php' );
        require_once( JPATH_BASE.'/components/com_multicalendar/DC_MultiViewCal/php/list.inc.php' );

        $event_id = &JRequest::getVar('event_id');

        $event_data = getEmailPdfData($event_id);
        
        $client_id = &JRequest::getVar('client_id');      
        if(!$client_id) $client_id = $event_data->client_id;


        $exercises = getExercises($event_id);
        ?>   
        <div  style="position: absolute;width:800px;border:4px solid #000000; background-color: #ECEBE9;border-radius: 10px; padding: 10px;">
            <a href="http://www.elitefit.com.au">
                <img style="float: right; width:265px; height: 70px; background-size: 265px auto;" src="<?php echo JUri::base() ?>components/com_multicalendar/DC_MultiViewCal/css/images/elite_logo.png">
            </a>
            <br/>
            <br/>
            <table border="0">
                <tr>
                    <td width="70%">
                        <div style="width: 600px;">
                            <h3 style="margin: 0; padding: 0; ">SESSION DETAILS</h3>
                            <hr style="width:100%;">
                                <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;" border="0">
                                    <tbody>
                                        <tr>
                                            <td>Start Date:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    $date = JFactory::getDate($event_data->starttime);
                                                    echo $date->toFormat('%A, %d %b %Y');
                                                    ?>
                                                </i>
                                            </td>
                                            <td>Finish Date:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    $date = JFactory::getDate($event_data->endtime);
                                                    echo $date->toFormat('%A, %d %b %Y');
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Start Time:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    $date = JFactory::getDate($event_data->starttime);
                                                    echo $date->format('H:i:s');
                                                    ?>
                                                </i>
                                            </td>
                                            <td>Finish Time:</td>
                                            <td>
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

                                <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;"  border="0">
                                    <tbody>
                                        <tr>
                                            <td width="25%">Appointment:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    echo $event_data->title;
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Session Type:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    echo $event_data->session_type;
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Session Focus:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    echo $event_data->session_focus;
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;"  border="0">
                                    <tbody>
                                        <tr>
                                            <td width="25%">Client Name:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    $user = &JFactory::getUser($client_id);
                                                    echo $user->name;
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Trainer Name:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    $user = &JFactory::getUser($event_data->trainer_id);
                                                    echo $user->name;
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Location:</td>
                                            <td>
                                                <i>
                                                    <?php
                                                    echo $event_data->location;
                                                    ?>
                                                </i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                        </div>
                    </td>
                        <td style="vertical-align: top;" width="30%" align="right">
                            <div style="float: right; position: absolute; right: 20px; text-align: right; top: 70px;">
                                <span>www.elitefit.com.au</span><br/>
                                <span>info@elitefit.com.au</span><br/>
                                <span>0422 050 590</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>


            
            


            <div style="width: 784px;margin-top: 20px;">
                <h3 style="margin: 0; padding: 0;">WORKOUT / PROGRAM DETAILS</h3>
                <hr style="width:100%;">
                <h4 style="margin: 0; padding: 0;">Trainer Instructions / Comments</h4>
                <div style="padding: 5px;width: 770px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;">
                    <?php
                    echo $event_data->description;
                    ?>
                </div>
                
                <table width="100%" style="border:1px solid #000000;margin-top: 10px;border-collapse: collapse;"  border="0" >
                    <tr style="background-color: #989898;">
                        <th style="border:1px solid #000000;padding:5px;" width="40%">Exercise/Description/Notes</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Speed</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Weight</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Reps</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Time</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Sets</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Rest</th>
                    </tr>
                    <tbody style="background-color: #ffffff;">
                    <?php
                       foreach ($exercises as $exercise) {
                           echo '<tr>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->title.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->speed.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->weight.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->reps.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->time.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->sets.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->rest.'</td>
                                </tr>';
                       }
                    ?>
                    </tbody>
                </table>
            </div>
            
                    
                    
 
        </div>
        
    </body>
</html>


