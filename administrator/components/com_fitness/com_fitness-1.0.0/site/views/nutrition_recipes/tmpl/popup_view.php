<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */


// no direct access
defined('_JEXEC') or die;

?>
<style>
    #all {
        width: 100% !important;
        max-width: 100% !important;
    }
    .left {
        float: none !important;
    }
    
    #rt-main, .rt-container {
        width: 100% !important;
    }
    
    body {
        background-color: #F7F7F7 !important;
    }
    
    #filter_search {
        background-color: #ffffff;
    }

</style>
<?php

include JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'nutrition_recipes' . DS . 'tmpl' . DS . 'popup_view.php';

