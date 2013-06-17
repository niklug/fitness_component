<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldMultilist extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'multilist';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
  
            $db = &JFactory::getDbo();
            $query = "SELECT id, username FROM jos_users INNER JOIN jos_user_usergroup_map ON jos_user_usergroup_map.user_id=jos_users.id WHERE jos_user_usergroup_map.group_id='9'";
            $db->setQuery($query);
            $result = $db->loadObjectList();
       
            
            $drawField = '';
            $drawField .= '<select name="' . $this->name . '" id="' . $this->name . '" class="inputbox"  multiple="multiple">';
            $drawField .= '<option value="">none</option>';
            foreach ($result as $item) {
                $drawField .= '<option value="' . $item->id . '">' . $item->username . ' </option>';
        
            };
            $drawField .= '</select>';
            return $drawField;
    }
}