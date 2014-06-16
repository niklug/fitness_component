<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body>
<?php
    require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email_templates_data.php';

    $id = &JRequest::getVar('id');
    $client_id = JRequest::getVar('client_id') ? JRequest::getVar('client_id') : JFactory::getUser()->id;
    $layout = JRequest::getVar('layout');
    // 
    $params = array('method' => 'EmailPdfPeriod', 'id' => $id, 'client_id' => $client_id, 'layout' => $layout);

    try {
        $obj = EmailTemplateData::factory($params);

        $data = $obj->processing();
    } catch (Exception $exc) {
        echo $exc->getMessage();
        die();
    }
    //var_dump($data->item->mini_goal);
    include __DIR__ . DS . 'email_pdf_header.php';
?>
        
        <div style="width: 784px;">
            <h3 style="margin: 0; padding: 0; ">TRAINING PERIOD OVERVIEW</h3>
            <hr style="width:100%;">
            
            <h4 style="margin: 0; padding: 0;">CLIENT & TRAINER/S</h4>
            <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;"  border="0">
                <tbody>
                    <tr>
                        <td width="25%">Client Name:</td>
                        <td>
                            <i>
                                <?php echo $data->client_name ;?>
                            </i>
                        </td>
                        <td>Trainer Name:</td>
                        <td>
                            <i>
                                <?php echo $data->item->primary_trainer->name ;?>
                            </i>
                        </td>
                    </tr>
                    <tr>
                        
                        <td>
                            Email Address:
                        </td>
                        <td>
                            <i>
                                <?php echo $data->client_email;?>
                            </i>
                        </td>
                        <td>
                            Secondary Trainer/s:
                        </td>
                        <td>
                            <?php
                            foreach ($data->item->secondary_trainers AS $trainer) {
                                echo "<i>" .  $trainer->name . "</i><br/>";
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <br/>
            <h4 style="margin: 0; padding: 0;">CLIENT GOALS</h4>
            <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;"  border="0">
                <tbody>
                    <tr>
                        <td width="25%">Primary Goal:</td>
                        <td>
                            <i>
                                <?php echo $data->item->primary_goal->category_name ;?>
                            </i>
                        </td>

                    </tr>
                    <tr>
                        <td>
                            Start Date 

                        </td>
                        <td>
                            <?php echo $data->start_date_primary ;?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Achieve By 
                        </td>
                        <td>
                            <?php echo $data->deadline_primary ;?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Goal Details 
                        </td>
                        <td>
                            <?php echo $data->item->primary_goal->details  ? $data->item->primary_goal->details : '-' ;?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <br/>
                
            <table style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;"  border="0">
                <tbody>
                    <tr>
                        <td width="25%">Mini Goal:</td>
                        <td>
                            <i>
                                <?php echo $data->item->mini_goal->category_name ;?>
                            </i>
                        </td>

                    </tr>
                    <tr>
                        <td>
                            Start Date 

                        </td>
                        <td>
                            <?php echo $data->start_date_mini ;?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Achieve By 
                        </td>
                        <td>
                            <?php echo $data->deadline_mini ;?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Goal Details 
                        </td>
                        <td>
                            <?php echo $data->item->mini_goal->details  ? $data->item->mini_goal->details : '-' ;?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <br/>
            
            <h3 style="margin: 0; padding: 0; ">TRAINING PERIOD PLANNING & SCHEDULING</h3>
            <hr style="width:100%;">
            <span style="font-size: 12px;">* Note: This is only a proposed schedule and is therefore subject to change.
                <br/>
                Please refer to your online Calendar, Programs and Assessments for up to date information and scheduling!
            </span>
                
            <br/><br/>
            <h4 style="margin: 0; padding: 0;">TRAINING PERIOD FOCUS</h4>
            <div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;padding: 5px;">
                <?php echo $data->item->period_focus ?>
            </div>
            
            <br/>
            <h4 style="margin: 0; padding: 0;">TRAINER NOTES</h4>
            <div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;padding: 5px;">
                <?php echo $data->item->comments ? $data->item->comments : '-'; ?>
            </div>
            
            <br/>
            <h4 style="margin: 0; padding: 0;">WORKOUT SCHEDULE</h4>
            <div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;padding: 5px;">
                <table width="100%">
                    <?php
                    $html = '';
                       foreach ($data->item->period_sessions as $session) {
    
                        $html .= "<tr>";
                        //
                        $html .= "<td>";
                        $html .= appointmentImage($session->appointment_type_id,  $data);
                        $html .= "</td>";
                        //
                        $html .= "<td>";
                        $starttime = JFactory::getDate($session->starttime);
                        $html .= $starttime->toFormat('%A, %d %b %Y');
                        $html .= "</td>";
                        //
                        $html .= "<td>";
                        $html .= $starttime->format('H:i');
                        $html .= "</td>";
                        //
                        $html .= "<td>";
                        $html .= $session->appointment_name;
                        $html .= "</td>";
                        //
                        $html .= "<td>";
                        $html .= $session->session_type_name;
                        $html .= "</td>";
                        //
                        $html .= "<td>";
                        $html .= $session->session_focus_name;
                        $html .= "</td>";
                        $html .= "</tr>";
                    }
                    echo $html;
                    ?>
                </table>
            </div>
                
        </div>
    </body>
</html>


<?php

function appointmentImage($id, $data) {
    switch ($id) {
        case '1':
            $image = 'schedule_personal_training.png';
            break;
        case '2':
            $image = 'schedule_semi-private_training.png';
            break;
        case '3':
            $image = 'schedule_resistance_workout.png';
            break;
        case '4':
            $image = 'schedule_cardio_workout.png';
            break;
        default:
            break;
    }
    
    $html = '<img src="' . $data->path . $image . '" width="50" height="50">';

    return $html;
}



?>

