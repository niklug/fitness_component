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
    .settings_menu {
        padding-left: 0px;
    }
    
    .settings_menu li {
        list-style: none;
        margin-bottom: 10px;
    }
    
    .settings_menu li a {
        font-size: 14px;
    }
</style>

<?php if(FitnessHelper::is_superuser()){ ?>
    <h2>Exercise Library</h2>

    <ul class="settings_menu">
        <li><a class="" href="index.php?option=com_fitness&view=settings_exercise_types ">Exercise Type </a></li>
        <li><a class="" href="index.php?option=com_fitness&view=settings_force_types">Force Type</a></li>
        <li><a class="" href="index.php?option=com_fitness&view=settings_mechanics_types">Mechanics Type</a></li>
        <li><a class="" href="index.php?option=com_fitness&view=settings_body_parts">Body Part</a></li>
        <li><a class="" href="index.php?option=com_fitness&view=settings_target_muscles">Target Muscles </a></li>
        <li><a class="" href="index.php?option=com_fitness&view=settings_equipments">Equipment</a></li>
        <li><a class="" href="index.php?option=com_fitness&view=settings_difficulties">Difficulty</a></li>

    </ul>
<?php } ?>