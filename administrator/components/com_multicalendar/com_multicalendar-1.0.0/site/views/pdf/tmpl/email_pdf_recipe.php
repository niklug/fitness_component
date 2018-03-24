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
    $params = array('method' => 'EmailPdfRecipe', 'id' => $id, 'client_id' => $client_id, 'layout' => $layout);

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
                <h2 style="margin: 0; padding: 0;">RECIPE DATABASE</h2>
                <br/>
                <h3 style="margin: 0; padding: 0;"><?php echo $data->item->recipe_name;?></h3>
                <hr style="width:100%;">
                <div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;padding:2px;">
                    <table width="100%">
                        <tr>
                            <td>
                                <table width="100%">
                                    <tr>
                                        <td style="vertical-align:top;" width="150">
                                            Recipe Type 
                                        </td>
                                        <td>
                                            <?php echo $data->item->recipe_types_names ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align:top;">
                                            Variations 
                                        </td>
                                        <td>
                                            <?php echo $data->item->recipe_variations_names ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Serves 
                                        </td>
                                        <td>
                                            <?php echo $data->item->number_serves ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Author 
                                        </td>
                                        <td>
                                            <?php echo $data->created_by ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Status 
                                        </td>
                                        <td>
                                            <?php echo  $data->item->status_html; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Assessed By 
                                        </td>
                                        <td>
                                            <?php echo $data->assessed_by ?>
                                        </td>
                                    </tr>
                                </table>                                
                            </td>
                            <td style="vertical-align:top;" width="100">
                            <img style="float: left; width:100px; height: 100px; background-size: 100px auto;" src="<?php echo JUri::root() . $data->item->image ?>">
                            <br/>
                            <?php
                            $open_link = JUri::root() . 'index.php/contact/nutrition-database#!/nutrition_database/nutrition_recipe/' . $data->item->id;
                            ?>
                            <a style="font-size:12px;" target="_blank" href="<?php echo $open_link ?>">[view recipe]</a>
                            </td>
                        </tr>
                    </table>
                    
                    <br/>
                    <table style="font-size: 11px; border-collapse: collapse;" width="100%">
                        <thead  style="text-align: left;">
                            <th width="300">MEAL ITEM/DESCRIPTION</th>
                            <th>QUANTITY</th>
                            <th>Protein</th>
                            <th>Fat</th>
                            <th>Carbs</th>
                            <th>Cals</th>
                            <th>Energy</th>
                            <th>Fat, Sat</th>
                            <th>Sug</th>
                            <th>Sod</th>
                        </thead>
                        <tbody>
                        <?php 
                        foreach ($data->item->recipe_meals as $ingredient) {
                            $html .= '<tr>';
                            $html .= '<td>';
                            $html .= $ingredient->meal_name;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->quantity . ' ' . $ingredient->measurement;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->protein;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->fats;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->carbs;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->calories;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->energy;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->saturated_fat;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->total_sugars;
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $ingredient->sodium;
                            $html .= '</td>';
                            $html .= '</tr>';
                        }
                        echo $html;
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>

                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <b>TOTALS</b>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->protein; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->fats; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->carbs; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->calories; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->energy; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->saturated_fat; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->total_sugars; ?>
                                </td>
                                <td style="border-top:1px solid #ccc !important;">
                                    <?php echo $data->item->sodium; ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <br/>
                    Cooking Instructions / Method
                    <br/>
                    <?php echo $data->item->instructions ?>
                </div>
            </div>
    </body>
</html>


