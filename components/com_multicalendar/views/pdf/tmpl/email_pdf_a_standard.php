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
                                    <?php echo $data->start_date; ?>
                                </i>
                            </td>
                            <td>Finish Date:</td>
                            <td>
                                <i>
                                    <?php echo $data->end_date; ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td>Start Time:</td>
                            <td>
                                <i>
                                    <?php echo $data->start_time; ?>
                                </i>
                            </td>
                            <td>Finish Time:</td>
                            <td>
                                <i>
                                    <?php echo $data->end_time; ?>
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
                                    <?php echo $data->item->appointment_name; ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td>Assessment Type:</td>
                            <td>
                                <i>
                                    <?php echo $data->item->session_type_name; ?> 
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td>Assessment Focus:</td>
                            <td>
                                <i>
                                    <?php echo $data->item->session_focus_name; ?>
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
                                    <?php echo $data->client_name; ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td>Trainer Name:</td>
                            <td>
                                <i>
                                    <?php echo $data->trainer_name; ?>
                                </i>
                            </td>
                        </tr>
                        <tr>
                            <td>Location:</td>
                            <td>
                                <i>
                                    <?php echo $data->item->location_name; ?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>
        
        <div style="margin-top: 20px;">
            <h3 style="margin: 0; padding: 0;">PHYSICAL ASSESSMENT DETAILS</h3>
            <hr style="width:100%;">
            <h4 style="margin: 0; padding: 0;">Assessment Instructions</h4>
            <div style="padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px; margin-top: 10px;">
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

        <?php
        $status = $data->item->client_item->status;
        //DISTINCTION, EXCELLENT, PASS, IMPROVEMENT, FAIL
        if(!in_array($status, array('3', '4', '5', '6', '7'))) {
            return;
        }
        ?>
        <!-- RESULTS -->
        <div style="margin-top: 20px;">
            <h3 style="margin: 0; padding: 0;">MY ASSESSMENT RESULTS</h3>
            <hr style="width:100%;">
                <table width="100%">
                    <tr>
                        <td width="50%" style="vertical-align: top;padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                            <h4 style="margin: 0; padding: 0;">Physical Measurements</h4>
                            <table border="0" width="100%">
                                <tr>
                                    <td  >AGE (years)</td>
                                    <td>
                                        <?php echo $data->item->age; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td >HEIGHT (cm)</td>
                                    <td>
                                        <?php echo $data->item->height; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>WEIGHT (kg)</td>
                                    <td>
                                        <?php echo $data->item->weight; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>LEAN MASS (kg)</td>
                                    <td>
                                        <?php echo $data->item->lean_mass; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>BODY FAT (%)</td>
                                    <td>
                                        <?php echo $data->item->body_fat; ?>
                                    </td>

                                </tr>
                                
                            </table>
                        </td>
                        <td style="vertical-align: top;padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                            <h4 style="margin: 0; padding: 0;">Test Scores</h4>
                            <table border="0" width="100%">
                                <tr>
                                    <td>BLOOD PRESSURE (mm/hg)</td>
                                    <td>
                                        <?php echo $data->item->blood_pressure; ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td>BODY COMPOSITION (bmi)</td>
                                    <td>
                                        <?php echo $data->item->body_composition; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>LUNG FUCTION (ml)</td>
                                    <td>
                                        <?php echo $data->item->lung_function; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FITNESS / VO2 MAX</td>
                                    <td>
                                        <?php echo $data->item->v02_max; ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
        </div>
        
        
        <div style="margin-top: 20px;">
            <table width="100%">
                <tr>
                    <td width="50%" style="vertical-align: top;padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                        <h4 style="margin: 0; padding: 0;">Anatomical Measurements</h4>
                        <table border="0" width="100%">
                            <tr>
                                <td >CHEST (cm)</td>
                                <td>
                                    <?php echo $data->item->chest; ?>
                                </td>
                                <td >WAIST (cm)</td>
                                <td>
                                    <?php echo $data->item->waist; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>HIPS (cm)</td>
                                <td>
                                    <?php echo $data->item->hips; ?>
                                </td>

                                <td>LEFT BICEP (cm)</td>
                                <td>
                                    <?php echo $data->item->left_bicep; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>RIGHT BICEP (cm)</td>
                                <td>
                                    <?php echo $data->item->right_bicep; ?>
                                </td>

                                <td >LEFT THIGH (cm) </td>
                                <td>
                                    <?php echo $data->item->left_thigh; ?>
                                </td>
                            </tr>

                            <tr>
                                <td>RIGHT THIGH (cm)</td>
                                <td>
                                    <?php echo $data->item->right_thigh; ?>
                                </td>

                                <td>LEFT CALF (cm)</td>
                                <td>
                                    <?php echo $data->item->left_calf; ?>
                                </td>
                            </tr>

                            <tr>
                                <td>RIGHT CALF (cm)</td>
                                <td>
                                    <?php echo $data->item->right_calf; ?>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </div>
        
        <br/>
        <div style="padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
            <h4 style="margin: 0; padding: 0;">Photo Assessments</h4>
            <?php
            $default_image = JURI::root(). 'administrator/components/com_fitness/assets/images/no_image.png';
            foreach ($data->item->photos as $photo) {
                $image = JURI::root(). $photo->image;
                if (!@getimagesize($image)) {
                    $image = $default_image;
                }
                ?>
                <table width="100%">
                    <tr>
                        <td width="30%">
                            <img style="float: right; width:200px; height: 200px; background-size: 200px auto;" src="<?php echo $image;?>">
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td width="150">
                                        <label>Description</label>
                                    </td>
                                    <td>
                                        <?php echo $photo->description; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>Client Comments</label>
                                    </td>
                                    <td>
                                        <?php echo $photo->client_comments; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label>Trainer Comments</label>
                                    </td>
                                    <td>
                                        <?php echo $photo->trainer_comments; ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <hr>
                <?php
            }
            ?>
        </div>
        <!-- END RESULTS -->
        
    </body>
</html>


