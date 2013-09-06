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

    fieldset.adminform {
        margin: 10px;
        overflow: hidden;
    }
    fieldset {
        border: 1px solid #CCCCCC;
        margin-bottom: 10px;
        padding: 5px;
        text-align: left;
    }
    legend {
        color: #146295;
        font-size: 1.182em;
        font-weight: bold;
    }


    fieldset input, fieldset textarea, fieldset select, fieldset img, fieldset button {
        float: left;
        margin: 5px 5px 5px 0;
        width: auto;
    }
    input, select {
        background: none repeat scroll 0 0 #FFFFFF !important;
        border: 1px solid silver !important;
        font-size: 0.909em !important;
        padding: 1px !important;
        width: auto !important;
    }
    
    textarea {
        border: medium none;
        height: 50px;
        line-height: 1.8em;
        outline: medium none;
        overflow: auto;
        padding: 7px 10px;
        resize: none;
        width: 482px;
        background-color: #fff;
    }

</style>
<?php

include JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'nutrition_recipe' . DS . 'tmpl' . DS . 'popup_view.php';