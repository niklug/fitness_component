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

class FitnessController extends JController
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/fitness.php';
 		$view		= JFactory::getApplication()->input->getCmd('view', 'dashboard');
                JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
        
        //------------------------------------------------------
	function setGoalStatus() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> setGoalStatus();
	}

        //clients view
        //------------------------------------------------------
	function getUsersByGroup() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> getUsersByGroup();
	}
        
        
        function getUsersByBusiness() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> getUsersByBusiness();
	}
        
        function getClientsByBusiness() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> getClientsByBusiness();
	}
        
        // programs view
        //------------------------------------------------------
	function setFrontendPublished() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> setFrontendPublished();
	}
        
        // Goals Graph
        //------------------------------------------------------
	function getGraphData() {
            
 		$view = $this -> getView('goals', 'json');
                $view->setModel($this->getModel('goals'));
   		$view -> getGraphData();
	}
        
        function getTrainingPeriod() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> getTrainingPeriod(); 
        }
        
        function populateGoals() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> populateGoals(); 
        }
        
        
        function primary_goals() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'goals.php';
            $model = new FitnessModelGoals();
            echo json_encode($model->primary_goals());
        }
        
        function mini_goals() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'goals.php';
            $model = new FitnessModelGoals();
            echo json_encode($model->mini_goals());
        }
        
        function addPlan() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> addPlan(); 
        }
        
        // Recipes
        //------------------------------------------------------
	function getSearchIngredients() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> getSearchIngredients();
	}

        
        function getIngredientData() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> getIngredientData();
	}
        
        function nutrition_database_ingredients() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> nutrition_database_ingredients();
	}
        
        function saveMeal() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> saveMeal();
	}
        
        function deleteMeal() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> deleteMeal();
	}
        
        function populateTable() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> populateTable();
	}
        
        function saveComment() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> saveComment();
	}
        
        function deleteComment() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> deleteComment();
	}
        
        function populateComments() {
            $view = $this -> getView('nutrition_recipe', 'json');
            $view->setModel($this->getModel('nutrition_recipe'));
            $view -> populateComments();
	}
        
        // nutrition plan
        function getClientPrimaryGoals() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getClientPrimaryGoals();
	}
        
        function getGoalData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getGoalData();
	}
        
        function resetAllForceActive() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> resetAllForceActive();
	}
        
        function saveTargetsData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> saveTargetsData();
	}
        
        function getTargetsData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getTargetsData();
	}
        
        function saveIngredient() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> saveIngredient();
	}
        
        function deleteIngredient() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deleteIngredient();
	}
        
        function populateItemDescription() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> populateItemDescription();
	}
        
        function savePlanMeal() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> savePlanMeal();
	}
        
        function deletePlanMeal() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deletePlanMeal();
	}
        
        function populatePlanMeal() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> populatePlanMeal();
	}
        
        function savePlanComment() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> savePlanComment();
	}
        
        function deletePlanComment() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deletePlanComment();
	}
        
        function populatePlanComments() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> populatePlanComments();
	}
        
        function importRecipe() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> importRecipe();
	}
        
        function saveShoppingItem() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> saveShoppingItem();
	}
        
        function deleteShoppingItem() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> deleteShoppingItem();
	}
        
        function getShoppingItemData() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getShoppingItemData();
	}
        
        function copyExampleDay() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> copyExampleDay();
        }
        
        function nutrition_plans() {
            $view = $this -> getView('nutrition_plans', 'json');
            $view->setModel($this->getModel('nutrition_plans'));
            $view -> nutrition_plans();
	}
        
        function nutrition_plan_targets() {
            $view = $this -> getView('nutrition_plans', 'json');
            $view->setModel($this->getModel('nutrition_plans'));
            $view -> nutrition_plan_targets();
	}
        
        
        
        // nutrition diary
        
        function updateStatus() {
            $view = $this -> getView('nutrition_diary', 'json');
            $view->setModel($this->getModel('nutrition_diary'));
            $view -> updateStatus();
	}
        
        // Goal view
        function checkOverlapDate() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> checkOverlapDate();
	}

        
        // business profile view
        function checkUniqueGroup() {
            $view = $this -> getView('business_profile', 'json');
            $view->setModel($this->getModel('business_profile'));
            $view -> checkUniqueGroup();
	}
        
        // user group view
        function onBusinessNameChange() {
            $view = $this -> getView('user_group', 'json');
            $view->setModel($this->getModel('user_group'));
            $view -> onBusinessNameChange();
	}
        
        
        public function ajax_email(){
            $view = $this -> getView('email', 'json');
            $view -> run(); 
        }
        
        public function nutrition_plan_protocol(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> nutrition_plan_protocol();
        }
        
        public function nutrition_plan_supplement(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> nutrition_plan_supplement();
        }
        
        
        public function nutrition_plan_example_day_meal(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> nutrition_plan_example_day_meal();
        }

        
        public function recipe_types(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> getRecipeTypes();
        }
        
        public function nutrition_guide_recipes(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> nutrition_guide_recipes();
        }
        
        
        function recipe_variations() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> recipe_variations(); 
        }
        
        function remote_images() {
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> remote_images(); 
        }
        
        public function nutrition_plan_menu(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> nutrition_plan_menu(); 
        }
        
        public function nutrition_database_categories(){
            require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
            $helper = new FitnessHelper();
            echo json_encode($helper->nutrition_database_categories());
        }
        
        
        public function shopping_list_ingredients(){
            $view = $this -> getView('nutrition_plan', 'json');
            $view->setModel($this->getModel('nutrition_plan'));
            $view -> shopping_list_ingredients(); 
        }
        
        function recipes() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'recipe_database.php';
            $recipe_database_model = new FitnessModelrecipe_database();
            echo json_encode($recipe_database_model->recipes());
        }
        
        function copyRecipe() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'recipe_database.php';
            $recipe_database_model = new FitnessModelrecipe_database();
            $table = JRequest::getVar('table');
            $data_encoded = JRequest::getVar('data_encoded','','POST');
            echo json_encode($recipe_database_model->copyRecipe($table, $data_encoded));
        }
        
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
        
        public function batch_copy(){
            require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
            $helper = new FitnessHelper();
            echo json_encode($helper->batch_copy());
        }
        
        function programs() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> programs(); 
        }
        
        function event_exercises() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> event_exercises(); 
        }

        function copyEvent() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> copyEvent(); 
        }
        
        function get_trainers() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> get_trainers(); 
        }
        
        function get_trainer_clients() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> get_trainer_clients(); 
        }
        
        
        
        function event_clients() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> event_clients(); 
        }
        
        function copyProgramExercises() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> copyProgramExercises(); 
        }
        
        function rest_data() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> rest_data(); 
        }
        
        function saveAsTemplate() {
            $view = $this -> getView('programs', 'json');
            $view->setModel($this->getModel('programs'));
            $view -> saveAsTemplate(); 
        }
        
        
        // programs templates
        function programs_templates() {
            $view = $this -> getView('programs_templates', 'json');
            $view->setModel($this->getModel('programs_templates'));
            $view -> programs_templates(); 
        }
        
        function copyProgramTemplate() {
            $view = $this -> getView('programs_templates', 'json');
            $view->setModel($this->getModel('programs_templates'));
            $view -> copyProgramTemplate(); 
        }
        
        function pr_temp_clients() {
            $view = $this -> getView('programs_templates', 'json');
            $view->setModel($this->getModel('programs_templates'));
            $view -> pr_temp_clients(); 
        }
        
        function pr_temp_exercises() {
            $view = $this -> getView('programs_templates', 'json');
            $view->setModel($this->getModel('programs_templates'));
            $view -> pr_temp_exercises(); 
        }
        
        function import_pr_temp() {
            $view = $this -> getView('programs_templates', 'json');
            $view->setModel($this->getModel('programs_templates'));
            $view -> import_pr_temp(); 
        }
        
        function assessment_photos() {
            $view = $this -> getView('assessments', 'json');
            $view->setModel($this->getModel('assessments'));
            $view -> assessment_photos(); 
        }
        
        //TRAINING PERIODIZATION /
        function training_periods() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> training_periods();
	}
        
        function training_sessions() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> training_sessions();
	}
        
        function scheduleSession() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> scheduleSession();
	}
        
        function copySessionPeriod() {
            $view = $this -> getView('goals', 'json');
            $view->setModel($this->getModel('goals'));
            $view -> copySessionPeriod();
	}
        
        //diaries
        function diaries() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_diaries.php';
            $nutrition_diaries_model = new FitnessModelNutrition_diaries();
            echo json_encode($nutrition_diaries_model -> diaries()); 
        }
        
        function meal_entries() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_diaries.php';
            $nutrition_diaries_model = new FitnessModelNutrition_diaries();
            echo json_encode($nutrition_diaries_model -> meal_entries()); 
        }

        function diary_meals() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_diaries.php';
            $nutrition_diaries_model = new FitnessModelNutrition_diaries();
            echo json_encode($nutrition_diaries_model -> diary_meals()); 
        }

        function meal_ingredients() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_diaries.php';
            $nutrition_diaries_model = new FitnessModelNutrition_diaries();
            echo json_encode($nutrition_diaries_model -> meal_ingredients()); 
        }
        
        function getPlanDataByDiary() {
            require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';
            $helper = new FitnessHelper();
            $diary_id = JRequest::getVar('diary_id');
            echo json_encode($helper -> getPlanDataByDiary($diary_id)); 
        }
        
        function copyMealEntry() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_diaries.php';
            $nutrition_diaries_model = new FitnessModelNutrition_diaries();
            $data_encoded = JRequest::getVar('data_encoded','','POST');
            echo json_encode($nutrition_diaries_model -> copyMealEntry($data_encoded)); 
        }

        function copyDiaryMeal() {
            require_once  JPATH_SITE . DS . 'components' . DS . 'com_fitness' . DS .'models' . DS . 'nutrition_diaries.php';
            $nutrition_diaries_model = new FitnessModelNutrition_diaries();
            $data_encoded = JRequest::getVar('data_encoded','','POST');
            echo json_encode($nutrition_diaries_model -> copyDiaryMeal($data_encoded)); 
        }

}
