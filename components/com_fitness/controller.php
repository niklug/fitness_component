<?php

/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class FitnessController extends JController {

    public function __construct() {
        parent::__construct();

        //connect administrator models
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'nutrition_plan.php';
        $this->admin_nutrition_plan_model = new FitnessModelnutrition_plan();
        
        require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'nutrition_recipe.php';
        $this->admin_nutrition_recipe_model = new FitnessModelnutrition_recipe();
    }

    public function display($tpl = null) {
        $user = JFactory::getUser();
        
        $view = JRequest::getVar( 'view' );
        
        if($view == 'appointment_confirmed') {
            parent::display();
            return;
        }

        if ($user->guest) {
            $this->setRedirect(JRoute::_(JURI::base() . 'index.php', false));
            $this->setMessage('Login please to proceed');
            return false;
        }
        parent::display();
    }
    
    //nutrition_recipe
    function getSearchIngredients() {
        $search_text = JRequest::getVar('search_text');
        
        echo $this->admin_nutrition_recipe_model->getSearchIngredients($search_text);
    }


    function getIngredientData() {
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_recipe_model->getIngredientData($id);
    }

    function saveMeal() {
        $ingredient_encoded = JRequest::getVar('ingredient_encoded');
        
        echo $this->admin_nutrition_recipe_model->saveMeal($ingredient_encoded);
    }


     function deleteMeal() {
        $id= JRequest::getVar('id');
        
        echo $this->admin_nutrition_recipe_model->deleteMeal($id);
    }


     function populateTable() {
        $recipe_id = JRequest::getVar('recipe_id');
        
        echo $this->admin_nutrition_recipe_model->populateTable($recipe_id);
    }
    // end nutrition_recipe

    // nutrition plan

    function getTargetsData() {
        $data_encoded = JRequest::getVar('data_encoded');
        echo $this->admin_nutrition_plan_model->getTargetsData($data_encoded);
    }
    
    function nutrition_targets() {
        $view = $this -> getView('nutrition_planning', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> nutrition_targets(); 
    }
    
    function saveIngredient() {
        $table = JRequest::getVar('table');
        $ingredient_encoded = JRequest::getVar('ingredient_encoded');
        
        echo $this->admin_nutrition_plan_model->saveIngredient($ingredient_encoded, $table);
    }

    function deleteIngredient() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_plan_model->deleteIngredient($id, $table);
    }

    function populateItemDescription() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        echo $this->admin_nutrition_plan_model->populateItemDescription($data_encoded, $table);
    }

    function savePlanMeal() {
        $table = JRequest::getVar('table');
        $meal_encoded = JRequest::getVar('meal_encoded');
        
        echo $this->admin_nutrition_plan_model->savePlanMeal($meal_encoded, $table);
    }

    function deletePlanMeal() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_plan_model->deletePlanMeal($id, $table);
    }

    function populatePlanMeal() {
        $table = JRequest::getVar('table');
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        
        echo $this->admin_nutrition_plan_model->populatePlanMeal($nutrition_plan_id, $table);
    }

    function savePlanComment() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded','','POST','STRING',JREQUEST_ALLOWHTML);
        echo $this->admin_nutrition_plan_model->savePlanComment($data_encoded, $table);
    }

    function deletePlanComment() {
        $table = JRequest::getVar('table');
        $id = JRequest::getVar('id');
        echo $this->admin_nutrition_plan_model->deletePlanComment($id, $table);
    }

    function populatePlanComments() {
        $table = JRequest::getVar('table');
        $item_id = JRequest::getVar('item_id');
        $sub_item_id = JRequest::getVar('sub_item_id');
        echo $this->admin_nutrition_plan_model->populatePlanComments($item_id, $sub_item_id, $table);
    }

    function importRecipe() {
        $table = JRequest::getVar('table');
        $data_encoded = JRequest::getVar('data_encoded');
        
        echo json_encode($this->admin_nutrition_plan_model->importRecipe($data_encoded, $table));
    }

    function saveShoppingItem() {
        $data_encoded = JRequest::getVar('data_encoded');
        
        echo $this->admin_nutrition_plan_model->saveShoppingItem($data_encoded);
    }

    function deleteShoppingItem() {
        $id = JRequest::getVar('id');
        
        echo $this->admin_nutrition_plan_model->deleteShoppingItem($id);
    }

    function getShoppingItemData() {
        $nutrition_plan_id = JRequest::getVar('nutrition_plan_id');
        
        echo $this->admin_nutrition_plan_model->getShoppingItemData($nutrition_plan_id);
    }
    // end nutrition plan
    
    
    // goals
    function addGoal() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> addGoal();
    }
    
    function populateGoals() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> populateGoals(); 
    }
    
    function checkOverlapDate() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> checkOverlapDate(); 
    }
    
   function commentEmail() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> commentEmail();
    }
    
    function getClientsByBusiness() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> getClientsByBusiness();
    }
    
    function onBusinessNameChange() {
            $view = $this -> getView('goals_periods', 'json');
            $view->setModel($this->getModel('goals_periods'));
            $view -> onBusinessNameChange();
	}
    
    // nutrition plan
    function nutrition_plan() {
        $view = $this -> getView('nutrition_planning', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> nutrition_plan(); 
    }
    
    function getTrainingPeriod() {
        $view = $this -> getView('goals_periods', 'json');
        $view->setModel($this->getModel('goals_periods'));
        $view -> getTrainingPeriod(); 
    }
    
    // recipe database
    function getRecipes() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> getRecipes(); 
    }
    
    function recipes() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> recipes(); 
    }
    
    function ingredient_categories() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> ingredient_categories(); 
    }
    
    function getRecipe() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> getRecipe(); 
    }
    
        
    
    function getRecipeTypes() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> getRecipeTypes(); 
    }
    
    function recipe_variations() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> getRecipeVariations(); 
    }
    
    function copyRecipe() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> copyRecipe(); 
    }
    
    function favourite_recipe() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> favourite_recipe(); 
    }
    

    function uploadImage() {
        $filename = $_FILES['file']['name'];
        
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $data = json_decode(JRequest::getVar('data_encoded'));
        
        $upload_folder = urldecode($data->upload_folder);

        
        $image_name = $data->image_name;

        $filename =  $image_name . '.' . $ext;

        $task = $data->method;
        
        


        if($task == 'clear') {
            $filename = $data->filename;
            unlink($upload_folder . $filename);
            echo $filename;
            return false;
        }


        if($_FILES['file']['size']/1024 > 1024) {
            echo 'too big file'; 
            header("HTTP/1.0 404 Not Found");
            return false;
        }

        $fileType="";
        

        if(strstr($_FILES['file']['type'],"jpg")) $fileType="jpg";

        if(strstr($_FILES['file']['type'],"png")) $fileType="png";

        if(strstr($_FILES['file']['type'],"gif")) $fileType="gif";

        if(strstr($_FILES['file']['type'],"bmp")) $fileType="bmp";

        if(strstr($_FILES['file']['type'],"jpeg")) $fileType="jpeg";


        if (!$fileType) {
            echo  $_FILES['file']['type'] . 'Invalid file type';
            header("HTTP/1.0 404 Not Found");
            return false;
        } 
        
        unlink($upload_folder . $filename);

        if (file_exists($upload_folder .$filename) && $filename) {
            echo 'Crear existing image first!';
            header("HTTP/1.0 404 Not Found");
            return false;
         }
         
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_folder . $filename)) {
            echo "ok";
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }
    
    
    
    function uploadVideo() {
        $filename = $_FILES['file']['name'];
        
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        
        
        $data = json_decode(JRequest::getVar('data_encoded'));
        
        $upload_folder = urldecode($data->upload_folder);

        
        $video_name = $data->video_name;

        $filename =  $video_name . '.' . $ext;

        $task = $data->method;
        


        if($task == 'clear') {
            $filename = $_POST['filename'];
            unlink($upload_folder . $filename);
            
            $thumbnail_name = explode('.', $filename);
            $thumbnail = $upload_folder . $thumbnail_name[0] . '.jpg';
            unlink($thumbnail);
            echo $filename;
            return false;
        }


        if($_FILES['file']['size']/1024 > 10024) {
            echo 'too big file'; 
            header("HTTP/1.0 404 Not Found");
            return false;
        }
        


        $fileType="";

        if(strstr($_FILES['file']['type'],"flv")) $fileType="flv";

        if(strstr($_FILES['file']['type'],"avi")) $fileType="avi";

        if(strstr($_FILES['file']['type'],"mp4")) $fileType="mp4";

        if(strstr($_FILES['file']['type'],"mpg")) $fileType="mpg";
        
        if(strstr($_FILES['file']['type'],"mov")) $fileType="mov";
        
        if(strstr($_FILES['file']['type'],"wmv")) $fileType="wmv";
        
        if(strstr($_FILES['file']['type'],"asf")) $fileType="asf";
        
        if(strstr($_FILES['file']['type'],"swf")) $fileType="swf";
        
        if(strstr($_FILES['file']['type'],"mpeg")) $fileType="mpeg";
        
        if(strstr($_FILES['file']['type'],"mpeg4")) $fileType="mpeg4";
        
        if(strstr($_FILES['file']['type'],"vid")) $fileType="vid";


        if (!$fileType) {
            echo 'Invalid file type';
            header("HTTP/1.0 404 Not Found");
            return false;
        } 
        
        unlink($upload_folder . $filename);
        unlink($thumbnail);
        

        if (file_exists($upload_folder .$filename) && $filename) {
            echo 'Crear existing video first!';
            header("HTTP/1.0 404 Not Found");
            return false;
         }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_folder . $filename)) {
            $this->createThumbnail($upload_folder, $video_name, $ext) ;
            //echo "ok";
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }
    
    private function createThumbnail($upload_folder, $video_name, $ext) {
        $extension = "ffmpeg";
        $extension_soname = $extension . "." . PHP_SHLIB_SUFFIX;
        $extension_fullname = PHP_EXTENSION_DIR . "/" . $extension_soname;

        // load extension
        if(!extension_loaded($extension)) {
            die("Can't load extension $extension_fullname\n");
        }
        
        $video = $upload_folder . $video_name . '.' . $ext;

        $thumbnail = $upload_folder . $video_name . '.jpg';

        $second = 20;

        $command = "ffmpeg  -itsoffset -$second  -i $video -vcodec mjpeg -vframes 1 -an -f rawvideo -s 400x250 $thumbnail";
        shell_exec($command);
    }
    
    
    
    function ingredients() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> ingredients(); 
    }
    
    function updateIngredient() {
        $view = $this -> getView('recipe_database', 'json');
        $view->setModel($this->getModel('recipe_database'));
        $view -> updateIngredient(); 
    }
    
    public function ajax_email(){
        $view = $this -> getView('email', 'json');
        $view -> run(); 
    }
    
    // diary
    function diaries() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> diaries(); 
    }
    
    function updateDiary() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> updateDiary(); 
    }
    
    function deleteDiary() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> deleteDiary(); 
    }
    
    function getDiaryDays() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> getDiaryDays(); 
    }
    
    function getActivePlanData() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> getActivePlanData(); 
    }
    
    function getNutritionTarget() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> getNutritionTarget(); 
    }
    

    function updateDiaryItem() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> updateDiaryItem(); 
    }
    
    
    function getDiaryItem() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> getDiaryItem(); 
    }
    
    
    function saveAsRecipe() {
        $view = $this -> getView('nutrition_diaries', 'json');
        $view->setModel($this->getModel('nutrition_diaries'));
        $view -> saveAsRecipe(); 
    }
    
    public function nutrition_plan_protocol(){
        echo json_encode($this->admin_nutrition_plan_model->nutrition_plan_protocol());
    }

    public function nutrition_plan_supplement(){
        echo json_encode($this->admin_nutrition_plan_model->nutrition_plan_supplement());
    }
    
    public function nutrition_plan_example_day_meal(){
        echo json_encode($this->admin_nutrition_plan_model->nutrition_plan_example_day_meal());
    }
    
    public function nutrition_guide_recipes(){
        echo json_encode($this->admin_nutrition_plan_model->nutrition_guide_recipes());
    }
    
    public function nutrition_plan_menu(){
        echo json_encode($this->admin_nutrition_plan_model->nutrition_plan_menu());
    }

    public function recipe_types(){
        echo json_encode($this->admin_nutrition_plan_model->getRecipeTypes());
    }
    
    public function get_recipe(){
        echo json_encode($this->admin_nutrition_plan_model->getRecipe());
    }
    
    public function nutrition_database_categories(){
        require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
        $helper = new FitnessHelper();
        echo json_encode($helper->nutrition_database_categories());
    }
    
    public function shopping_list_ingredients(){
        echo json_encode($this->admin_nutrition_plan_model->shopping_list_ingredients());
    }
    
    //exercise library
    function select_filter() {
        $view = $this -> getView('exercise_library', 'json');
        $view->setModel($this->getModel('exercise_library'));
        $view -> select_filter(); 
    }

    function exercise_library() {
        $view = $this -> getView('exercise_library', 'json');
        $view->setModel($this->getModel('exercise_library'));
        $view -> exercise_library(); 
    }

    function business_profiles() {
        $view = $this -> getView('exercise_library', 'json');
        $view->setModel($this->getModel('exercise_library'));
        $view -> business_profiles(); 
    }

    function exercise_library_clients() {
        $view = $this -> getView('exercise_library', 'json');
        $view->setModel($this->getModel('exercise_library'));
        $view -> clients(); 
    }
    
    function favourite_exercise() {
        $view = $this -> getView('exercise_library', 'json');
        $view->setModel($this->getModel('exercise_library'));
        $view -> favourite_exercise(); 
    }
    
    //Programs
    function programs() {
        $view = $this -> getView('programs', 'json');
        $view -> programs(); 
    }

    function event_exercises() {
        $view = $this -> getView('programs', 'json');
        $view -> event_exercises(); 
    }

    function copyEvent() {
        $view = $this -> getView('programs', 'json');
        $view -> copyEvent(); 
    }

    function get_trainers() {
        $view = $this -> getView('programs', 'json');
        $view -> get_trainers(); 
    }

    function get_trainer_clients() {
        $view = $this -> getView('programs', 'json');
        $view -> get_trainer_clients(); 
    }

    function event_clients() {
        $view = $this -> getView('programs', 'json');
        $view -> event_clients(); 
    }
    
    function updateStatus() {
        $view = $this -> getView('nutrition_diary', 'json');
        $view -> updateStatus();
    }
    
    function favourite_event() {
        $view = $this -> getView('programs', 'json');
        $view->setModel($this->getModel('programs'));
        $view -> favourite_event(); 
    }
    
    function copyProgramExercises() {
        $view = $this -> getView('programs', 'json');
        $view->setModel($this->getModel('programs'));
        $view -> copyProgramExercises(); 
    }
    
    function assessment_photos() {
        $view = $this -> getView('assessments', 'json');
        $view -> assessment_photos(); 
    }
        
        
}