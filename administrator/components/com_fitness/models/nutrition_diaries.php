<?php

/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

/**
 * Methods supporting a list of Fitness records.
 */
class FitnessModelnutrition_diaries extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'entry_date', 'a.entry_date',
                'submit_date', 'a.submit_date',
                'client_id', 'u.name',
                'trainer_id', 'u.name',
                'assessed_by', 'u.name',
                'goal_category_id', 'gn.primary_goal_name',
                'nutrition_focus', 'a.nutrition_focus',
                'status', 'a.status',
                'activity_level', 'a.activity_level',
                'score', 'a.score',
                'trainer_comments', 'a.trainer_comments',
                'business_name', 'business_name',
                'state', 'a.state',

            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
                
        
               	$this->setState('filter.score.from', $app->getUserStateFromRequest($this->context.'.filter.score.from', 'filter_from_score', '', 'string'));
		$this->setState('filter.score.to', $app->getUserStateFromRequest($this->context.'.filter.score.to', 'filter_to_score', '', 'string'));

        
		//Filtering entry_date
		$this->setState('filter.entry_date.from', $app->getUserStateFromRequest($this->context.'.filter.entry_date.from', 'filter_from_entry_date', '', 'string'));
		$this->setState('filter.entry_date.to', $app->getUserStateFromRequest($this->context.'.filter.entry_date.to', 'filter_to_entry_date', '', 'string'));

		//Filtering submit_date
		$this->setState('filter.submit_date.from', $app->getUserStateFromRequest($this->context.'.filter.submit_date.from', 'filter_from_submit_date', '', 'string'));
		$this->setState('filter.submit_date.to', $app->getUserStateFromRequest($this->context.'.filter.submit_date.to', 'filter_to_submit_date', '', 'string'));

                

                // Filter by primary trainer
                $primary_trainer = $app->getUserStateFromRequest($this->context . '.filter.primary_trainer', 'filter_primary_trainer', '', 'string');
                $this->setState('filter.primary_trainer', $primary_trainer);
                
                // Filter by assessed by
                $assessed_by = $app->getUserStateFromRequest($this->context . '.filter.assessed_by', 'filter_assessed_by', '', 'string');
                $this->setState('filter.assessed_by', $assessed_by);
                
                // Filter by goal category
                $goal_category = $app->getUserStateFromRequest($this->context . '.filter.goal_category', 'filter_goal_category', '', 'string');
                $this->setState('filter.goal_category', $goal_category);

                
                // Filter by nutrition focus
                $nutrition_focus = $app->getUserStateFromRequest($this->context . '.filter.nutrition_focus', 'filter_nutrition_focus', '', 'string');
                $this->setState('filter.nutrition_focus', $nutrition_focus);
                
                // Filter by diary status
                $diary_status = $app->getUserStateFromRequest($this->context . '.filter.diary_status', 'filter_diary_status', '', 'string');
                $this->setState('filter.diary_status', $diary_status);
                
                // Filter by activity level
                $activity_level = $app->getUserStateFromRequest($this->context . '.filter.activity_level', 'filter_activity_level', '', 'string');
                $this->setState('filter.activity_level', $activity_level);
                
                // Filter by business profile
                $business_profile_id = $app->getUserStateFromRequest($this->context . '.filter.business_profile_id', 'filter_business_profile_id', '', 'string');
                $this->setState('filter.business_profile_id', $business_profile_id);

                
                
        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.entry_date', 'DESC');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*,
                            (SELECT name FROM #__fitness_goal_categories WHERE id=a.goal_category_id) primary_goal_name,
                            nf.name AS nutrition_focus_name,
                            bp.name AS business_name
                        '
                )
        );
        $query->from('`#__fitness_nutrition_diary` AS a');

        $query->leftJoin('#__users AS u ON u.id = a.client_id');
        
        $query->leftJoin('#__fitness_goals AS gc ON gc.id = a.goal_category_id');
        
        $query->leftJoin('#__fitness_goal_categories AS gn ON gn.id = a.goal_category_id');
        
        $query->leftJoin('#__fitness_nutrition_focus AS nf ON nf.id = a.nutrition_focus');
        
        $query->leftJoin('#__fitness_clients AS c ON c.user_id = a.client_id');
        
        $query->leftJoin('#__fitness_business_profiles AS bp ON bp.id = c.business_profile_id');
        
        
        $user = JFactory::getUser();
        
        if(FitnessHelper::is_primary_administrator($user->id) || FitnessHelper::is_secondary_administrator($user->id)) {
            $trainers_group_id = FitnessHelper::getTrainersGroupId();
            $query->where('bp.group_id = '.(int) $trainers_group_id);
        }
        
        // 
        
        
        if(!FitnessHelper::is_primary_administrator($user->id) && !FitnessHelper::is_secondary_administrator($user->id) && FitnessHelper::is_trainer($user->id)) {
            $query->where('(c.primary_trainer = ' . (int) $user->id . ' OR FIND_IN_SET(' . $user->id . ' , c.other_trainers) )');
        }

        
        
        // Filter by business profile
        $business_profile_id = $this->getState('filter.business_profile_id');
        if (is_numeric($business_profile_id)) {
            $query->where('c.business_profile_id = '.(int) $business_profile_id);
        } 
        
        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }


            // Filter by search in title
            $search = $this->getState('filter.search');
            if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                    $query->where('a.id = ' . (int) substr($search, 3));
                } else {
                    $search = $db->Quote('%' . $db->escape($search, true) . '%');
                    $query->where('( u.name LIKE '.$search.'  OR  u.username LIKE '.$search.' )');
                }
            }

                
                //Filtering score
		$filter_score_from = $this->state->get("filter.score.from");
		if ($filter_score_from) {
			$query->where("a.score >= '".$db->escape($filter_score_from)."'");
		}
		$filter_score_to = $this->state->get("filter.score.to");
		if ($filter_score_to) {
			$query->where("a.score <= '".$db->escape($filter_score_to)."'");
		}
            

		//Filtering entry_date
		$filter_entry_date_from = $this->state->get("filter.entry_date.from");
		if ($filter_entry_date_from) {
			$query->where("a.entry_date >= '".$db->escape($filter_entry_date_from)."'");
		}
		$filter_entry_date_to = $this->state->get("filter.entry_date.to");
		if ($filter_entry_date_to) {
			$query->where("a.entry_date <= '".$db->escape($filter_entry_date_to)."'");
		}

		//Filtering submit_date
		$filter_submit_date_from = $this->state->get("filter.submit_date.from");
		if ($filter_submit_date_from) {
			$query->where("a.submit_date >= '".$db->escape($filter_submit_date_from)."'");
		}
		$filter_submit_date_to = $this->state->get("filter.submit_date.to");
		if ($filter_submit_date_to) {
			$query->where("a.submit_date <= '".$db->escape($filter_submit_date_to)."'");
		}
                
                
                // Filter by primary trainer
                $primary_trainer = $this->getState('filter.primary_trainer');
                if (is_numeric($primary_trainer)) {
                    $query->where('a.trainer_id = '.(int) $primary_trainer);
                } 
                
                
                // Filter by assessed by
                $assessed_by = $this->getState('filter.assessed_by');
                if (is_numeric($assessed_by)) {
                    $query->where('a.assessed_by = '.(int) $assessed_by);
                } 
                
                
                // Filter by goal category
                $goal_category = $this->getState('filter.goal_category');
                if (is_numeric($goal_category)) {
                    $query->where('gn.id = '.(int) $goal_category);
                }  

                
                // Filter by nutrition focus
                $nutrition_focus = $this->getState('filter.nutrition_focus');
                if (is_numeric($nutrition_focus)) {
                    $query->where('a.nutrition_focus = '.(int) $nutrition_focus);
                }  
                
                
                // Filter by diary status
                $diary_status = $this->getState('filter.diary_status');

                if ($diary_status) {
                    $query->where('a.status = ' . (int) $diary_status);
                    if($diary_status == '1') {
                        $query->where('a.status = ' . (int) $diary_status . ' OR a.status="0"');
                    }
                } 
                
                          
                 // Filter by activitylevel
                $activity_level= $this->getState('filter.activity_level');
                
                if ($activity_level) {
                    $query->where('a.activity_level = ' . (int) $activity_level);
                } 



        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
        return $items;
    }
    
    private function getUserGroup($user_id) {
        $db = JFactory::getDBO();
        $query = "SELECT title FROM #__usergroups WHERE id IN 
            (SELECT group_id FROM #__user_usergroup_map WHERE user_id='$user_id')";
        $db->setQuery($query);
        if(!$db->query()) {
            JError::raiseError($db->getErrorMsg());
        }
        return $db->loadResult();
    }
    
    function status_html($item_id, $status) {
        switch($status) {
            case '1' :
                $class = 'status_inprogress';
                $text = 'IN PROGRESS';
                break;
            case '2' :
                $class = 'status_pass';
                $text = 'PASS';
                break;
            case '3' :
                $class = 'status_fail';
                $text = 'FAIL';
                break;
            case '4' :
                $class = 'status_distinction';
                $text = 'DISTINCTION';
                break;
            case '5' :
                $class = 'status_submitted';
                $text = 'SUBMITTED';
                break;
            default :
                $class = 'status_inprogress';
                $text = 'IN PROGRESS';
                break;
        }

        $html = '<a href="javascript:void(0)" data-item_id="' . $item_id . '" data-status_id="' . $status . '" class="status_button ' . $class . '">' . $text . '</a>';

        return $html;
    }
    

    

}
