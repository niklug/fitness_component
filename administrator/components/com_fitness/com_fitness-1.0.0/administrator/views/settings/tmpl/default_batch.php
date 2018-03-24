<?php
require_once  JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fitness' . DS .'helpers' . DS . 'fitness.php';

$helper = new FitnessHelper();

$document = &JFactory::getDocument();
$document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'jquery.js');
$document -> addscript( JUri::root() . 'administrator/components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'jquerynoconflict.js');
$document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'ajax_call_function.js');
$document -> addscript( JUri::base() . 'components' . DS . 'com_fitness' . DS .'assets'. DS .'js'. DS . 'lib' . DS . 'batch_copy_class.js');

?>
<fieldset class="batch">
	<legend>Batch process the selected items</legend>
        <label>Select Bussiness Access</label>
	<?php echo $helper->generateSelect($helper->getBusinessProfileList(), 'batch_business_profile', 'batch_business_profile', '' , 'Original Business Access', false, "inputbox"); ?>
        <div class="clr"></div>
        <br/>
	<button id="batch_copy" type="button" >
		Copy
	</button>
	<button id="batch_clear" type="button">
		Clear
	</button>
</fieldset>

