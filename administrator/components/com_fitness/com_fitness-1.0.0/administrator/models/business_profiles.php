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

/**
 * Methods supporting a list of Fitness records.
 */
class FitnessModelbusiness_profiles extends JModelList {

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
                'name', 'a.name',
                'group_id', 'a.group_id',
                'usergroup_name', 'usergroup_name',
                'primary_administrator', 'a.primary_administrator',
                'secondary_administrator', 'a.secondary_administrator',
                'terms_conditions', 'a.terms_conditions',
                'header_image', 'a.header_image',
                'facebook_url', 'a.facebook_url',
                'twitter_url', 'a.twitter_url',
                'youtube_url', 'a.youtube_url',
                'instagram_url', 'a.instagram_url',
                'google_plus_url', 'a.google_plus_url',
                'linkedin_url', 'a.linkedin_url',
                'website_url', 'a.website_url',
                'email', 'a.email',
                'contact_number', 'a.contact_number',
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

         // Filter by group
        $group = $app->getUserStateFromRequest($this->context . '.filter.group', 'filter_group', '', 'string');
        $this->setState('filter.group', $group);
        

        // Load the parameters.
        $params = JComponentHelper::getParams('com_fitness');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.group_id', 'asc');
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
                        'list.select', 'a.*, ug.title AS usergroup_name'
                )
        );
        $query->from('`#__fitness_business_profiles` AS a');

        
		// Join over the user field 'primary_administrator'
		$query->select('primary_administrator.name AS primary_administrator');
		$query->join('LEFT', '#__users AS primary_administrator ON primary_administrator.id = a.primary_administrator');
		// Join over the user field 'secondary_administrator'
		$query->select('secondary_administrator.name AS secondary_administrator');
		$query->join('LEFT', '#__users AS secondary_administrator ON secondary_administrator.id = a.secondary_administrator');

                $query->leftJoin('#__usergroups AS ug ON ug.id = a.group_id');
        
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
            $search = $db->Quote('%' . $db->escape($search, true) . '%');
            $query->where('( a.name LIKE '.$search.')');
        }

        
        // Filter by group
        $group = $this->getState('filter.group');
        if (is_numeric($group)) {
            $query->where('a.group_id = '.(int) $group);
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

}
