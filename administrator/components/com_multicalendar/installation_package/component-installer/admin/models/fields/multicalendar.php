<?php
/**
 * @version		$Id: multicalendar.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Multicalendar Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldMultiCalendar extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'MultiCalendar';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id As value, a.title As text');
		$query->from('#__dc_mv_calendars AS a');
		$query->order('a.title');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);
        array_unshift($options, JHTML::_('select.option', '-1', 'Logged user\'s calendar (*user plugin required)'));
		array_unshift($options, JHtml::_('select.option', '', JText::_('Select MultiCalendar')));

		return $options;
	}
}
