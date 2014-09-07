<?php

defined('_JEXEC') or die('Restricted access');
// import Joomla view library
jimport('joomla.application.component.view');

if(!JSession::checkToken('get')) {
    $status['success'] = 0;
    $status['message'] = JText::_('JINVALID_TOKEN');
    $result = array( 'status' => $status);
    echo  json_encode($result);
    die();
}

//=======================================================
// AJAX View
//======================================================
class FitnessViewNutrition_diaries extends JView {
    
    function diaries() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->diaries());
    }
    
    function meal_entries() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->meal_entries());
    }
    
    function diary_meals() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->diary_meals());
    }
    
    function meal_ingredients() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->meal_ingredients());
    }
    
    
    function updateDiary() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->updateDiary($table, $data_encoded));
    }
    
    function deleteDiary() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->deleteDiary($table, $data_encoded));
    }
    
    function getDiaryDays() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getDiaryDays($table, $data_encoded));
    }
    
    
    function getActivePlanData() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getActivePlanData());
    }
    
    function getNutritionTarget() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getNutritionTarget($table, $data_encoded));
    }
    
    
    function updateDiaryItem() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->updateDiaryItem($table, $data_encoded));
    }
    
    
    function getDiaryItem() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->getDiaryItem($table, $data_encoded));
    }
    
    function saveAsRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->saveAsRecipe($table, $data_encoded));
    }

    function copyMealEntry() {
        $model = $this -> getModel("nutrition_diaries");
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        echo json_encode($model->copyMealEntry($data_encoded));
    }
    
    function copyDiaryMeal() {
        $model = $this -> getModel("nutrition_diaries");
        $data_encoded = JRequest::getVar('data_encoded','','POST');
        echo json_encode($model->copyDiaryMeal($data_encoded));
    }
    
    function comments() {
        $model = $this -> getModel("nutrition_diaries");
        echo json_encode($model->comments());
    }
}
