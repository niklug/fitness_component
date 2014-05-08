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
            <h3 style="margin: 0; padding: 0;">BIOSIGNATURE RESULTS</h3>
            <hr style="width:100%;">
                <table width="100%">
                    <tr>
                        <td width="50%" style="vertical-align: top;padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                            <h4 style="margin: 0; padding: 0;">Age, Height & Weight</h4>
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
                                    <td>
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="vertical-align: top;padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                            <h4 style="margin: 0; padding: 0;">BioSignature Calculations</h4>
                            <table border="0" width="100%">
                                <tr>
                                    <td>TOTAL BODY FAT (%)</td>
                                    <td>
                                        <?php echo $data->item->body_fat; ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td>LEAN MASS (kg)</td>
                                    <td>
                                        <?php echo $data->item->lean_mass; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SUM 10 (mm)</td>
                                    <td>
                                        <?php echo $data->item->sum_10; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>QUADS & HAM (mm)</td>
                                    <td>
                                        <?php echo $data->item->quads_ham; ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <br/>
                <div style="padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                    <h4 style="margin: 0; padding: 0;">Priorities</h4>
                    <table border="0" width="100%" style="color:red;">
                        <?php
                        $priorities = FitnessHelper::assessmentPriorities();
                        ?>
                        <tr>
                            <td width="33%">
                                1. <?php echo $priorities[$data->item->priority_1]; ?>
                            </td>
                            <td width="33%">
                                2. <?php echo $priorities[$data->item->priority_2]; ?>
                            </td>
                            <td>
                                3. <?php echo $priorities[$data->item->priority_3]; ?>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <br/>
                <div style="padding: 5px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                    <h4 style="margin: 0; padding: 0;">Body Fat (Skin Fold) Measurements</h4>
                    <table width="100%">
                        <tr>
                            <td width="33%" style="vertical-align: top;">
                                <table border="0" width="100%">
                                    <tr>
                                        <td >CHIN (mm)</td>
                                        <td>
                                            <?php echo $data->item->chin; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >CHEEK (mm)</td>
                                        <td>
                                            <?php echo $data->item->cheek; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>PECTORAL (mm)</td>
                                        <td>
                                            <?php echo $data->item->pectorial; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>TRICEPS (mm)</td>
                                        <td>
                                            <?php echo $data->item->triceps; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="33%" style="vertical-align: top;">
                                <table border="0" width="100%">
                                    <tr>
                                        <td>SUB-SCAPULARIS (mm)</td>
                                        <td>
                                            <?php echo $data->item->sub_scapularis; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>MID-AXILLARY (mm)</td>
                                        <td>
                                            <?php echo $data->item->midaxillary; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SUPRASPINATUS (mm)</td>
                                        <td>
                                            <?php echo $data->item->supraspinatus; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>UMBILICAL (mm)</td>
                                        <td>
                                            <?php echo $data->item->umbilical; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align: top;">
                                <table border="0" width="100%">
                                    <tr>
                                        <td>KNEE (mm)</td>
                                        <td>
                                            <?php echo $data->item->knee; ?>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>CALF (mm)</td>
                                        <td>
                                            <?php echo $data->item->calf; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>QUADRICEP (mm)</td>
                                        <td>
                                            <?php echo $data->item->quadricep; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>HAMSTRING (mm)</td>
                                        <td>
                                            <?php echo $data->item->hamstring; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
        </div>
        
        
        <div style="margin-top: 20px;">
            <h3 style="margin: 0; padding: 0;">OTHER ASSESSMENT RESULTS</h3>
            <hr style="width:100%;">
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                <tr>
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
                                </tr>
                                
                                <tr>
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
    </body>
</html>


