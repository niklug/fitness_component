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
    $params = array('method' => 'EmailPdfNutritionPlanSupplements', 'id' => $id, 'client_id' => $client_id, 'layout' => $layout);

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
                <h2 style="margin: 0; padding: 0;">SUPPLEMENTATION PLAN</h2>
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
        
        <?php
            foreach ($data->item->protocols as $protocol) {

                $html  .= '<h3 style="margin: 20px 0 0; padding: 0;">PROTOCOL NAME: ' .  $protocol->name . '</h3>';
                $html  .= '<hr style="width:100%;">';
                $html  .= '<div style="width: 800px;margin-top: 20px;">';

                foreach ($protocol->supplements as $supplement) {
                    $html .= '<div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;margin-top:20px;">';
                    
                    $html .= '<table width="100%">';
                    $html .= '<tr>';
                    $html .= '<td>';
                    //
                    $html .= '<table width="100%">';
                    $html .= '<tr>';
                    $html .= '<td  width="200">';
                    $html .= 'Supplement Name';
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $supplement->name;
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td>';
                    $html .= 'Supplement Description';
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $supplement->description ? $supplement->description : '';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td>';
                    $html .= 'Recommended Usage ';
                    $html .= '</td>';
                    $html .= '<td>';
                    $html .= $supplement->comments ? $supplement->comments : '';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                    //
                    $html .= '<td>';
                    $html .= '<td width="100">';
                    if($supplement->image) {
                    $html .= '<img style="float: right; width:100px; height: 100px; background-size: 100px auto;" src="' . $supplement->image . '">';
                    }
                    $html .= '<a style="font-size:12px;" target="_blank" href="' . $supplement->url . '">[view product]</a>';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $html .= '</table>';
                    
                    $html .= '</div>';
                }
                
                $html  .= '</div>';
            }
            echo $html;
        ?>
            
            
        </div>
    </body>
</html>


