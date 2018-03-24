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

/**
 * Multicalendar Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldNMonths extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'NMonths';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected $forceMultiple = true; 
	protected function getInput()
	{
		if ((string) @$this->value[0]!='1')
		    @array_splice ($this->value, 0, 0, "0");
		if ((string) @$this->value[2]!='1')
		    @array_splice ($this->value, 2, 0, "0");

		$checked1	= ((string) @$this->value[0]=='1') ? ' checked' : '';
		$checked2	= ((string) @$this->value[2]=='1') ? ' checked' : '';
		$selected1	= ((string) @$this->value[1]=='mouseover') ? ' selected="true"' : '';
		$selected2	= ((string) @$this->value[1]=='click') ? ' selected="true"' : '';
		$selected3	= ((string) @$this->value[4]=='new_window') ? ' selected="true"' : '';
		$selected4	= ((string) @$this->value[4]=='same_window') ? ' selected="true"' : '';
		
		
        $html = array();
        $html[] = '<script>function showhide(id){'.
                  'var obj1 = document.getElementById(id+"0");'.
                  'var obj2 = document.getElementById(id+"1");'. 
                  'var obj3 = document.getElementById(id+"div");'.
                  'if ((obj1.checked) && (obj2.selectedIndex==1))'.
                  '    obj3.style.display = "none";'.
                  'else    '.
                  '    obj3.style.display = "";'.
                  '}</script>';
		$html[] = '<div><input type="checkbox" name="'.$this->name.'0" id="'.$this->id.'0"' .
				' value="1" '.$checked1.' onclick="javascript:showhide(\''.$this->id.'\')"/><span style="float:left;display:inline;padding:5px 5px 0px 0px">'.JText::_( 'SHOW TOOLTIP ON' ).'</span>' .
				'<select name="'.$this->name.'1" id="'.$this->id.'1" onchange="javascript:showhide(\''.$this->id.'\')"><option value="mouseover" '.$selected1.' >'.JText::_( 'MOUSE OVER' ).'</option><option value="click" '.$selected2.'>'.JText::_( 'CLICK' ).'</option></select>' .
				'</div><label id="jform_params_sample-lbl" class="hasTip">&nbsp;</label><div id="'.$this->id.'div"><input type="checkbox" name="'.$this->name.'2" id="'.$this->id.'2"' .
				' value="1" '.$checked2.'/><span style="float:left;display:inline;padding:5px 5px 0px 0px">'.JText::_( 'GO TO THE URL' ).'</span><input type="text" name="'.$this->name.'3" id="'.$this->id.'3"' .
				' value="'.htmlspecialchars(@$this->value[3], ENT_COMPAT, 'UTF-8').'"/><label id="jform_params_sample-lbl" class="hasTip">&nbsp;</label><span style="float:left;display:inline;padding:5px 5px 0px 20px">'.JText::_( 'IN' ).'</span>'.
				'<select name="'.$this->name.'1" id="'.$this->id.'1"><option value="new_window" '.$selected3.' >'.JText::_( 'NEW WINDOW' ).'</option><option value="same_window" '.$selected4.'>'.JText::_( 'SAME WINDOW' ).'</option></select>' .
				'</div>';
		$html[] = '<script>showhide(\''.$this->id.'\')</script>';
		$html[] = '';

		return implode($html);		
	}	
	
}
