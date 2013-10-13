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
class FitnessModelNutrition_diaries extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        
        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);
        

        // List state information.
        parent::populateState($ordering, $direction);
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
                        'list.select', 'a.*'
                )
        );

        $query->from('`#__fitness_nutrition_diary` AS a');

        // Filter by published state
        $published = $this->getState('filter.state');
        if(($published != '0') && ($published != '1') ) $published = '1';
        $query->where('a.state = '.(int) $published);
        
        $user = &JFactory::getUser();

        $query->where('a.client_id = '.(int) $user->id);
        
        $query->order('a.entry_date DESC');

        return $query;
    }

    public function getItems() {
        return parent::getItems();
    }
    
    function status_html($status) {
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

        $html = '<div class="status_button ' . $class . '">' . $text . '</div>';

        return $html;
    }
    
     function status_html_stamp($status) {
        switch($status) {
            case '2' :
                $class = 'status_pass_stamp';
                $text = 'PASS';
                break;
            case '3' :
                $class = 'status_fail_stamp';

                break;
            case '4' :
                $class = 'status_distinction_stamp';
                break;
            case '5' :
                $class = 'status_submitted_stamp';
                break;
            default :
                break;
        }

        $html = '<div class=" status_button_stamp ' . $class . '"></div>';

        return $html;
    }

}
