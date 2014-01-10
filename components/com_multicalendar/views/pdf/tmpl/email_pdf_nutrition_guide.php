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
    $params = array('method' => 'EmailPdfNutritionGuide', 'id' => $id, 'client_id' => $client_id, 'layout' => $layout);

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
                <h2 style="margin: 0; padding: 0;">DAILY MENU & NUTRITION GUIDE</h2>
                <br/>
                
                <?php
                $i = 1;
                foreach ($data->item as $meals) {
                    $html  .= '<br/>';
                    $html  .= '<h3 style="margin: 0; padding: 0;">EXAMPLE DAY ' . $i . ' </h3>';
                    $html  .= '<hr style="width:100%;">';


                    foreach ($meals as $meal) {
                        $html .= '<div style="width: 100%;background-color: #ffffff;border: 2px solid #000000; border-radius: 10px;margin-top:20px;">';
                        $html .= '<table width="100%">';
                        $html .= '<tr>';
                        $html .= '<td width="200">';
                        $html .= 'MEAL DESCRIPTION';
                        $html .= '</td>';
                        $html .= '<td width="450">';
                        $html .= $meal->description;
                        $html .= '</td>';
                        $html .= '<td width="120">';
                        $html .= 'MEAL TIME';
                        $html .= '</td>';
                        $html .= '<td>';
                        $html .= $meal->meal_time;
                        $html .= '</td>';
                        $html .= '</tr>';
                        $html .= '</table>';
                        $html  .= '<hr style="width:100%;margin:0;padding:0;">';
                            
                        foreach ($meal->recipes as $recipe) {
                            $html .= '<table width="100%">';
                            $html .= '<tr>';
                            $html .= '<td style="vertical-align:top;" width="100">';
                            if($recipe->image) {
                            $html .= '<img style="float: left; width:100px; height: 100px; background-size: 100px auto;" src="' . JUri::root() . $recipe->image . '">';
                            $html  .= '<br/>';
                            }
                            $open_link = JUri::root() . 'index.php/contact/nutrition-database#!/nutrition_database/nutrition_recipe/' . $recipe->id;
                            $html .= '<a style="font-size:12px;" target="_blank" href="' .$open_link . '">[view recipe]</a>';
                            $html .= '</td>';
                            
                            $html .= '<td width="350" style="vertical-align:top;">';
                            $html .= '<table width="100%">';
                            $html .= '<tr>';
                            $html .= '<td style="vertical-align:top;" width="120">';
                            $html .= 'Recipe Name';
                            $html .= '</td>';
                            $html .= '<td>';
                            $html .= $recipe->recipe_name;
                            $html .= '</td>';
                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<td style="vertical-align:top;">';
                            $html .= 'Recipe Type';
                            $html .= '</td>';
                            $html .= '<td>';
                            
                            foreach ($recipe->recipe_types_names as $recipe_type_name) {
                                $html .= $recipe_type_name . "<br/>";
                            }
                            
                            $html .= '</td>';

                            $html .= '</tr>';
                            $html .= '<tr>';
                            $html .= '<td style="vertical-align:top;">';
                            $html .= 'Variations';
                            $html .= '</td>';
                            $html .= '<td>';
                            
                            foreach ($recipe->recipe_variations_names as $recipe_variations_names) {
                                $html .= $recipe_variations_names . "<br/>";
                            }
                            
                            $html .= '</td>';
                            $html .= '</tr>';
                            
                            $html .= '</table>';
                            $html .= '</td>';
                            
                            $html .= '<td style="vertical-align:top;">';
                            $html .= '<table width="100%">';
                            $html .= '<tr>';
                            $html .= '<th>';
                            $html .= 'Meal Item/Description';
                            $html .= '</th>';
                            $html .= '<th width="50">';
                            $html .= 'Quantity';
                            $html .= '</th>';
                            $html .= '</tr>';
                            foreach ($recipe->ingredients as $ingredient) {
                                $html .= '<tr>';
                                $html .= '<td>';
                                $html .= $ingredient->meal_name;
                                $html .= '</td>';
                                $html .= '<td >';
                                if($ingredient->measurement == 'grams') {
                                    $measurement = 'g';
                                } else if ($ingredient->measurement == 'millilitres'){
                                    $measurement = 'ml';
                                }
                                $html .= $ingredient->quantity . ' ' . $measurement;
                                $html .= '</td>';
                                $html .= '</tr>';
                            }

                            $html .= '</table>';
                            $html .= '</td>';
                            
                            $html .= '</tr>';
                            $html .= '</table>';
                            $html  .= '<br/>';
                            $html  .= '<hr style="width:100%;">';
                        }

                        $html  .= '</div>';
                        
                    }
                    
           
                    $i++;
                }
                echo $html;
                ?>
            </div>
        
        
        </div>
    </body>
</html>


