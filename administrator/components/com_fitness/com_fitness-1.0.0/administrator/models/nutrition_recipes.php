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
class FitnessModelnutrition_recipes extends JModelList {

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
                'recipe_name', 'a.recipe_name',
                'recipe_type', 'a.recipe_type',
                'recipe_variation', 'a.recipe_variation',
                'created_by', 'a.created_by',
                'created', 'a.created',
                'status', 'a.status',
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
        
        $status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'string');
        $this->setState('filter.status', $status);

        
        //Filtering created
        $this->setState('filter.created.from', $app->getUserStateFromRequest($this->context.'.filter.created.from', 'filter_from_created', '', 'string'));
        $this->setState('filter.created.to', $app->getUserStateFromRequest($this->context.'.filter.created.to', 'filter_to_created', '', 'string'));
        //Filtering created_by
        $this->setState('filter.created_by', $app->getUserStateFromRequest($this->context.'.filter.created_by', 'filter_created_by', '', 'string'));

        //Filtering recipe type
        $this->setState('filter.recipe_type', $app->getUserStateFromRequest($this->context.'.filter.recipe_type', 'filter_recipe_type', '', 'string'));
        
        //Filtering recipe variation
        $this->setState('filter.recipe_variation', $app->getUserStateFromRequest($this->context.'.filter.recipe_variation', 'filter_recipe_variation', '', 'string'));

                // Filter by business profile
        $business_profile_id = $app->getUserStateFromRequest($this->context . '.filter.business_profile_id', 'filter_business_profile_id', '', 'string');
        $this->setState('filter.business_profile_id', $business_profile_id);
        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.recipe_name', 'asc');
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
                      'list.select',
                        
                       '   a.*,
                           (SELECT ROUND(SUM(protein),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS protein,
                           (SELECT ROUND(SUM(fats),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS fats,
                           (SELECT ROUND(SUM(carbs),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS carbs,
                           (SELECT ROUND(SUM(calories),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS calories,
                           (SELECT ROUND(SUM(energy),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS energy,
                           (SELECT ROUND(SUM(saturated_fat),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS saturated_fat,
                           (SELECT ROUND(SUM(total_sugars),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS total_sugars,
                           (SELECT ROUND(SUM(sodium),2) FROM #__fitness_nutrition_recipes_meals WHERE recipe_id=a.id) AS sodium,
                           bp.name AS business_name
                       '
                )
        );
        $query->from('`#__fitness_nutrition_recipes` AS a');

        
        // Join over the user field 'created_by'
        $query->select('u.name AS created_by');
        $query->join('LEFT', '#__users AS u ON u.id = a.created_by');
        
        
        $query->leftJoin('#__fitness_clients AS c ON c.user_id = a.created_by');
        $query->leftJoin('#__fitness_business_profiles AS bp ON bp.id = c.business_profile_id');
        $query->leftJoin('#__user_usergroup_map AS um ON um.user_id = a.created_by');
        
        
        $user = &JFactory::getUser();
        $user_id = $user->id;
        $current_group_id = FitnessHelper::getCurrentGroupId($user->id);
        $trainers_group_id = FitnessHelper::getTrainersGroupId();
        // if Primary or Secondary administrator of the Business Profile        
        if(FitnessHelper::is_primary_administrator() || FitnessHelper::is_secondary_administrator()) {
            $query->where('bp.group_id = '.(int) $trainers_group_id 
                    . ' OR um.group_id='.(int) $trainers_group_id
                    . ' OR um.group_id='.(int) FitnessHelper::SUPERUSER_GROUP_ID);
        }
        
        // 
        
        
        // if usual Trainer of the Business Profile    
        if(!FitnessHelper::is_primary_administrator() && !FitnessHelper::is_secondary_administrator() && FitnessHelper::is_trainer()) {
            $query->where('(c.primary_trainer = ' . (int) $user->id 
                    . ' OR FIND_IN_SET(' . $user->id . ' , c.other_trainers) )'
                    . ' OR um.group_id='.(int) $trainers_group_id
                    . ' OR um.group_id='.(int) FitnessHelper::SUPERUSER_GROUP_ID
                    );
        }
        
        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }
        
        // Filter by published state
        $status = $this->getState('filter.status');
        if (is_numeric($status)) {
            $query->where('a.status = '.(int) $status);
            if($status == '1') {
                $query->where('a.status = ' . (int) $status . ' OR a.status="0"');
            }
        }


        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.recipe_name LIKE '.$search.'  )');
        }

        

		//Filtering created
		$filter_created_from = $this->state->get("filter.created.from");
		if ($filter_created_from) {
			$query->where("a.created >= '".$db->escape($filter_created_from)."'");
		}
		$filter_created_to = $this->state->get("filter.created.to");
		if ($filter_created_to) {
			$query->where("a.created <= '".$db->escape($filter_created_to)."'");
		}
                
                //Filtering created_by
		$filter_created_by = $this->state->get("filter.created_by");
		if ($filter_created_by) {
			$query->where("a.created_by = '".$db->escape($filter_created_by)."'");
		}
                
                
                //Filtering recipe type
		$filter_recipe_type = $this->state->get("filter.recipe_type");
                
          
                if($filter_recipe_type) {
                    $query->where(" FIND_IN_SET('$filter_recipe_type', a.recipe_type) ");
                }
                
                
                //Filtering recipe variation
		$filter_recipe_variation= $this->state->get("filter.recipe_variation");
                
          
                if($filter_recipe_variation) {
                    $query->where(" FIND_IN_SET('$filter_recipe_variation', a.recipe_variation) ");
                }
                
                // Filter by business profile
                $business_profile_id = $this->getState('filter.business_profile_id');
                if (is_numeric($business_profile_id)) {
                    $query->where('c.business_profile_id = '.(int) $business_profile_id);
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
    
    
    function status_html($item_id, $status, $button_class) {
        switch($status) {
            case '1' :
                $class = 'recipe_status_pending';
                $text = 'PENDING';
                break;
            case '2' :
                $class = 'recipe_status_approved';
                $text = 'APPROVED';
                break;
            case '3' :
                $class = 'recipe_status_notapproved';
                $text = 'NOT APPROVED';
                break;
            default :
                $class = 'recipe_status_pending';
                $text = 'PENDING';
                break;
        }

        $html = '<a href="javascript:void(0)" data-item_id="' . $item_id . '" data-status_id="' . $status . '" class="' . $button_class . ' ' . $class . '">' . $text . '</a>';

        return $html;
    }

}
