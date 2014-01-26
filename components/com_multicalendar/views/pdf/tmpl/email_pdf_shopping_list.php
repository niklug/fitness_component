<html xmlns="http://www.w3.org/1999/xhtml" >
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  </head>
    <body>
<?php
    require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS . 'helpers' . DS . 'email_templates_data.php';

    $id = &JRequest::getVar('id');
    $client_id = JRequest::getVar('client_id') ? JRequest::getVar('client_id') : JFactory::getUser()->id;
    $checked = &JRequest::getVar('checked');
    $layout = JRequest::getVar('layout');
    // 
    $params = array('method' => 'EmailPdfShoppingList', 'id' => $id, 'client_id' => $client_id, 'layout' => $layout, 'checked' => $checked);

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
                <h2 style="margin: 0; padding: 0;">SHOPPING LIST</h2>
                <br/>
                <table border="0">
                    <tbody>
                        <tr>
                            <td><span style="margin-right:10px;font-weight:bold;">MENU NAME:</span></td>
                            <td>
                                <b>
                                    <?php echo $data->item->name;?>
                                </b>
                            </td>
                            <td>
                                <i style="margin-left: 100px;">
                                    <?php echo $data->start_date ;?>
                                </i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <br/>
                <div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;overflow: auto;" border="0">
                    <?php
                    $ingredients_caregories_ids = array();
                   
                    foreach ($data->item->ingredients as $ingredient) {
                        $ingredients_caregories_ids[] = $ingredient->category;
                    }
                    $ingredients_caregories_ids= array_filter($ingredients_caregories_ids);
     
                    $count = 0;
                    foreach ($data->item->categories as $category) {
                        if(in_array($category->id, $ingredients_caregories_ids)) {
                            $html .= '<div class="internal_wrapper" style=" margin:5px; width:390px;float:left;">';
                            $html .= '<span class="orange_text">CATEGORY: </span>';
                            $html .= '<span style="font-weight: bold;margin-left:10px;">' . $category->name  . '</span>';
                            $html .= '<hr>';

                            foreach ($data->item->ingredients as $ingredient) {
                                if($ingredient->category == $category->id) {
                                    $html .= '<table width="100%">';
                                    $html .= '<tr>';
                                    $html .= '<td>';
                                    $checked = '';
                                    $text_style = '';
                                    if(in_array($ingredient->id, $data->checked)) {
                                        $checked = ' checked="checked" ';
                                        $text_style = ' style="color:#ccc;text-decoration: line-through;" ';
                                    }
                                    $html .= '<input' . $checked . ' type="checkbox" data-id="' . $ingredient->id . '">';
                                    $html .= '</td>';
                                    $html .= '<td width="350">';
                                    $html .= '<span' . $text_style . '>' . $ingredient->meal_name . '</span>';
                                    $html .= '</td>';
                                    $html .= '<td width="100">';
                                    $html .= '<span' . $text_style . '>' . $ingredient->quantity . ' ' . $ingredient->measurement . '</span>';
                                    $html .= '</td>';
                                    $html .= '</tr>';
                                    $html .= '</table>';
                                }
                            }
                            $html .= '</div>';

                            if($count%2 == 1) { 
                                $html .= '<div style="clear: both; height: 0;overflow: hidden;"></div>';
                            }
                            $count++;
                        }
                    }
                    echo $html;
                    ?>
                </div>
            </div>

        </div>
    </body>
</html>


