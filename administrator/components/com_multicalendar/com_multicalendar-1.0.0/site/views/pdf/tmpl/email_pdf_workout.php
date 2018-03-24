<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body>
<?php
    require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email_templates_data.php';

    $event_id = &JRequest::getVar('event_id');
    $client_id = JRequest::getVar('client_id') ? JRequest::getVar('client_id') : JFactory::getUser()->id;
    $layout = JRequest::getVar('layout');
    // 
    $params = array('method' => 'EmailPdfWorkout', 'id' => $event_id, 'client_id' => $client_id, 'layout' => $layout);

    try {
        $obj = EmailTemplateData::factory($params);

        $data = $obj->processing();
    } catch (Exception $exc) {
        echo $exc->getMessage();
        die();
    }
    
    include __DIR__ . DS . 'email_pdf_header.php';
?>
            <div style="width: 600px;">
                <h3 style="margin: 0; padding: 0; ">SESSION DETAILS</h3>
                <hr style="width:100%;">
                    <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;" border="0">
                        <tbody>
                            <tr>
                                <td>Start Date:</td>
                                <td>
                                    <i>
                                        <?php echo $data->start_date;?>
                                    </i>
                                </td>
                                <td>Finish Date:</td>
                                <td>
                                    <i>
                                        <?php echo $data->end_date;?>
                                    </i>
                                </td>
                            </tr>
                            <tr>
                                <td>Start Time:</td>
                                <td>
                                    <i>
                                        <?php echo $data->start_time ;?>
                                    </i>
                                </td>
                                <td>Finish Time:</td>
                                <td>
                                    <i>
                                        <?php echo $data->end_time ;?>
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
                                        <?php echo $data->item->appointment_name;?>
                                    </i>
                                </td>
                            </tr>
                            <tr>
                                <td>Session Type:</td>
                                <td>
                                    <i>
                                        <?php echo $data->item->session_type_name;?> 
                                    </i>
                                </td>
                            </tr>
                            <tr>
                                <td>Session Focus:</td>
                                <td>
                                    <i>
                                        <?php echo $data->item->session_focus_name;?>
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
                                        <?php echo $data->client_name ;?>
                                    </i>
                                </td>
                            </tr>
                            <tr>
                                <td>Trainer Name:</td>
                                <td>
                                    <i>
                                        <?php echo $data->trainer_name ;?>
                                    </i>
                                </td>
                            </tr>
                            <tr>
                                <td>Location:</td>
                                <td>
                                    <i>
                                        <?php echo $data->item->location_name;?>
                                    </i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        
        <?php 
        // Special Event Or Consultation
        if($data->item->title == '6' OR $data->item->title == '7' ) { ?>
            <div style="width: 784px;margin-top: 20px;">
                <h3 style="margin: 0; padding: 0;">APPOINTMENT DETAILS</h3>
                 <div style="padding: 5px;width: 770px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;">
                    <?php echo urldecode($data->item->comments);?>
                </div>
            </div>
        <?php } else { ?>
            <div style="width: 784px;margin-top: 20px;">
                <h3 style="margin: 0; padding: 0;">WORKOUT DETAILS</h3>
                <hr style="width:100%;">
                <h4 style="margin: 0; padding: 0;">Workout Instructions</h4>
                <div style="padding: 5px;width: 770px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;">
                    <?php echo urldecode($data->item->description);?>
                </div>
                
                <table width="100%" style="border:1px solid #000000;margin-top: 10px;border-collapse: collapse;"  border="0" >
                    <tr style="background-color: #989898;">
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Seq</th>
                        <th style="border:1px solid #000000;padding:5px;" width="40%">Exercise Name</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Speed</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Weight</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Reps</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Time</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Sets</th>
                        <th style="border:1px solid #000000;padding:5px;" width="10%">Rest</th>
                    </tr>
                    <tbody style="background-color: #ffffff;">
                    <?php
                       foreach ($data->exercises as $exercise) {
                           $html =  '<tr>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->sequence.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->title.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->speed.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->weight.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->reps.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->time.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->sets.'</td>
                                    <td style="border:1px solid #000000;padding:5px;">'.$exercise->rest.'</td>
                                </tr>';
                           
                           if($exercise->comments) {
                               $html .=  '<tr>
                                    <td colspan="8" style="border:1px solid #000000;padding:5px;color:#2c3993;">'.$exercise->comments.'</td>
                               </tr>';
                           }
                           
                           echo $html;
                       }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </body>
</html>


