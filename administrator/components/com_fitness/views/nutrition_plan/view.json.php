<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

if(!JSession::checkToken('get')) {
    $status['success'] = 0;
    $status['message'] = JText::_('JINVALID_TOKEN');
    $result = array( 'status' => $status);
    //echo  json_encode($result);
    //die();
}

//=======================================================
// AJAX View
//======================================================
class FitnessViewNutrition_plan extends JView {
    
    function getClientPrimaryGoals() {
        $client_id = JRequest::getVar('client_id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->getClientPrimaryGoals($client_id);
    }
    
    function getGoalData() {
        $id = JRequest::getVar('id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->getGoalData($id);
    }
    
    function resetAllForceActive() {
        $model = $this -> getModel("nutrition_plan");
        echo $model->resetAllForceActive();
    }
    
    function saveTargetsData() {
        $data_encoded = JRequest::getVar('data_encoded');
        $model = $this -> getModel("nutrition_plan");
        echo $model->saveTargetsData($data_encoded);
    }
    
        
    function getTargetsData() {
        $data_encoded = JRequest::getVar('data_encoded');
        $model = $this -> getModel("nutrition_plan");
        echo $model->getTargetsData($data_encoded);
    }
    
    function saveIngredient() {
        $table = JRequest::getVar('table');
        $ingredient_encoded = JRequest::getVar('ingredient_encoded');
        $model = $this -> getModel("nutrition_plan");
        echo $model->saveIngredient($ingredient_encoded, $table);
    }
    
        
    function deleteIngredient() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->deleteIngredient($id, $table);
    }
    
    function populateItemDescription() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        $model = $this -> getModel("nutrition_plan");
        echo $model->populateItemDescription($data_encoded, $table);
    }
    
    
    function savePlanMeal() {
        $table = JRequest::getVar('table');
        $meal_encoded = JRequest::getVar('meal_encoded');
        $model = $this -> getModel("nutrition_plan");
        echo $model->savePlanMeal($meal_encoded, $table);
    }
    
    function deletePlanMeal() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->deletePlanMeal($id, $table);
    }
    
    function populatePlanMeal() {
        $table = JRequest::getVar('table');
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->populatePlanMeal($nutrition_plan_id, $table);
    }
    
   function savePlanComment() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        $model = $this -> getModel("nutrition_plan");
        echo $model->savePlanComment($data_encoded, $table);
    }
    
    
    function deletePlanComment() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->deletePlanComment($id, $table);
    }
    
    function populatePlanComments() {
        $table = JRequest::getVar('table');
        $item_id = JRequest::getVar('item_id');
        $sub_item_id = JRequest::getVar('sub_item_id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->populatePlanComments($item_id, $sub_item_id, $table);
    }
    
    function importRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded');
        $model = $this -> getModel("nutrition_plan");
        echo json_encode($model->importRecipe($data_encoded, $table));
    }
    
    function saveShoppingItem() {
        $data_encoded = JRequest::getVar('data_encoded');
        $model = $this -> getModel("nutrition_plan");
        echo $model->saveShoppingItem($data_encoded);
    }
    
    
    function deleteShoppingItem() {
        $id = JRequest::getVar('id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->deleteShoppingItem($id);
    }
    
    
    function getShoppingItemData() {
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        $model = $this -> getModel("nutrition_plan");
        echo $model->getShoppingItemData($nutrition_plan_id);
    }
    
    
    function nutrition_plan_protocol() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->nutrition_plan_protocol());
    }
    
    function nutrition_plan_supplement() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->nutrition_plan_supplement());
    }
    
    function nutrition_plan_exercie_day_meal() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->nutrition_plan_exercie_day_meal());
    }
    
    function nutrition_guide_add_recipe_list() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->nutrition_guide_add_recipe_list());
    }
    
    function getRecipeTypes() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->getRecipeTypes());
    }
    
    function getRecipe() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->getRecipe());
    }
    
    function nutrition_guide_recipes() {
        $model = $this -> getModel("nutrition_plan");
        echo  json_encode($model->nutrition_guide_recipes());
    }
    
    function recipe_variations() {
        $model = $this -> getModel("nutrition_plan");
        echo json_encode($model->recipe_variations());
    }
    
    function remote_images() {
        $url = JRequest::getVar('url');
        $model = $this -> getModel("nutrition_plan");
        echo json_encode($model->getRemoteImages($url));
    }
    
}
