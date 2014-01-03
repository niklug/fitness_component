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
    $params = array('method' => 'EmailPdfNutritionPlanMacros', 'id' => $id, 'client_id' => $client_id, 'layout' => $layout);

    try {
        $obj = EmailTemplateData::factory($params);

        $data = $obj->processing();
    } catch (Exception $exc) {
        echo $exc->getMessage();
        die();
    }
    
    include __DIR__ . DS . 'email_pdf_header.php';
?>
            <div style="width: 800px;">
                <h2 style="margin: 0; padding: 0;">NUTRITION PLAN</h2>
                <br/>
                <h3 style="margin: 0; padding: 0;">CLIENT DETAILS</h3>
                <hr style="width:100%;">
                <div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;">
                    <table width="100%">
                        <tr>
                            <td>
                                <table width="100%">
                                    <tr>
                                        <td>
                                            Client Name
                                        </td>
                                        <td>
                                            <?php echo $data->client_name; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Trainer Name
                                        </td>
                                        <td>
                                            <?php echo $data->trainer_name; ?>
                                        </td>
                                    </tr>
                                    <tr>  <td colspan="2" </td></tr>
                                    <tr>  <td colspan="2" </td></tr>
                                    <tr>  <td colspan="2" </td></tr>
                                    <tr>
                                        <td>
                                            Primary Goal
                                        </td>
                                        <td>
                                            <?php echo $data->item->primary_goal_name; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Start Date
                                        </td>
                                        <td>
                                            <?php echo $data->primary_goal_start_date; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Achieve By
                                        </td>
                                        <td>
                                            <?php echo $data->primary_goal_deadline; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align:bottom;">
                                <table width="100%">
                                    <tr>
                                        <td>
                                          
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                          
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Mini Goal
                                        </td>
                                        <td>
                                            <?php echo $data->item->mini_goal_name; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Start Date
                                        </td>
                                        <td>
                                            <?php echo $data->mini_goal_start_date; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Achieve By
                                        </td>
                                        <td>
                                            <?php echo $data->mini_goal_deadline; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="width: 800px;margin-top: 20px;">
                <h3 style="margin: 0; padding: 0;">MACRONUTRIENT DETAILS</h3>
                <hr style="width:100%;">
                    
                <h4>ALLOWED PROTEINS</h4>
                <div style="width: 790px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;padding:5px;">
                    <?php echo $data->item->allowed_proteins; ?>
                </div>
                
                <h4>ALLOWED FATS</h4>
                <div style="width: 790px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;padding:5px;">
                    <?php echo $data->item->allowed_fats; ?>
                </div>
                
                <h4>ALLOWED CARBOHYDRATES</h4>
                <div style="width: 790px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;padding:5px;">
                    <?php echo $data->item->allowed_carbs; ?>
                </div>
                
                <h4>ALLOWED LIQUIDS</h4>
                <div style="width: 790px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;padding:5px;">
                    <?php echo $data->item->allowed_liquids; ?>
                </div>
                
                <h4>OTHER RECOMMENDATIONS / INSTRUCTIONS</h4>
                <div style="width: 790px;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;padding:5px;">
                    <?php echo $data->item->other_recommendations; ?>
                </div>
            </div>
        </div>
    </body>
</html>


