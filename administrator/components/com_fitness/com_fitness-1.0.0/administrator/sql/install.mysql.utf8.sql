-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 07 2014 г., 14:33
-- Версия сервера: 5.5.38
-- Версия PHP: 5.3.10-1ubuntu3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `fitness`
--

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_business_profiles`
--

CREATE TABLE IF NOT EXISTS `#__fitness_business_profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL,
  `primary_administrator` int(11) NOT NULL,
  `secondary_administrator` int(11) NOT NULL,
  `terms_conditions` text NOT NULL,
  `header_image` varchar(255) NOT NULL,
  `facebook_url` varchar(255) NOT NULL,
  `twitter_url` varchar(255) NOT NULL,
  `youtube_url` varchar(255) NOT NULL,
  `instagram_url` varchar(255) NOT NULL,
  `google_plus_url` varchar(255) NOT NULL,
  `linkedin_url` varchar(255) NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_clients`
--

CREATE TABLE IF NOT EXISTS `#__fitness_clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_profile_id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `primary_trainer` int(11) NOT NULL,
  `other_trainers` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`user_id`),
  FOREIGN KEY (user_id) REFERENCES #__users(id) ON DELETE CASCADE,
  FOREIGN KEY (business_profile_id) REFERENCES #__fitness_business_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_categories`
--

CREATE TABLE IF NOT EXISTS `#__fitness_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__dc_mv_events`
--

CREATE TABLE IF NOT EXISTS `#__dc_mv_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `calid` int(10) unsigned DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `title` int(11) unsigned NOT NULL,
  `location` int(11) unsigned DEFAULT NULL,
  `description` text,
  `comments` text,
  `session_type` int(11) unsigned NOT NULL,
  `session_focus` int(11) unsigned NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `frontend_published` int(1) NOT NULL DEFAULT '0',
  `isalldayevent` tinyint(3) unsigned DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `published` tinyint(1) DEFAULT NULL,
  `rrule` varchar(255) DEFAULT NULL,
  `uid` int(10) DEFAULT NULL,
  `exdate` text,
  `business_profile_id` int(11) unsigned NOT NULL,
  `auto_publish_workout` varchar(10) NOT NULL,
  `auto_publish_event` varchar(10) NOT NULL,
  `age` varchar(10) NOT NULL,
  `height` varchar(10) NOT NULL,
  `weight` varchar(10) NOT NULL,
  `body_fat` varchar(10) NOT NULL,
  `lean_mass` varchar(10) NOT NULL,
  `sum_10` varchar(10) NOT NULL,
  `quads_ham` varchar(10) NOT NULL,
  `priority_1` varchar(10) NOT NULL,
  `priority_2` varchar(10) NOT NULL,
  `priority_3` varchar(10) NOT NULL,
  `chin` varchar(10) NOT NULL,
  `cheek` varchar(10) NOT NULL,
  `pectorial` varchar(10) NOT NULL,
  `triceps` varchar(10) NOT NULL,
  `sub_scapularis` varchar(10) NOT NULL,
  `midaxillary` varchar(10) NOT NULL,
  `supraspinatus` varchar(10) NOT NULL,
  `umbilical` varchar(10) NOT NULL,
  `knee` varchar(10) NOT NULL,
  `calf` varchar(10) NOT NULL,
  `quadricep` varchar(10) NOT NULL,
  `hamstring` varchar(10) NOT NULL,
  `chest` varchar(10) NOT NULL,
  `left_thigh` varchar(10) NOT NULL,
  `waist` varchar(10) NOT NULL,
  `right_thigh` varchar(10) NOT NULL,
  `hips` varchar(10) NOT NULL,
  `left_calf` varchar(10) NOT NULL,
  `left_bicep` varchar(10) NOT NULL,
  `right_calf` varchar(10) NOT NULL,
  `right_bicep` varchar(10) NOT NULL,
  `blood_pressure` varchar(10) NOT NULL,
  `body_composition` varchar(10) NOT NULL,
  `lung_function` varchar(10) NOT NULL,
  `v02_max` varchar(10) NOT NULL,
  `video` varchar(255) NOT NULL,
  `video_name` varchar(255) NOT NULL,
  `video_description` text NOT NULL,
  `video_client_comments` text NOT NULL,
  `video_trainer_comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_profile_id` (`business_profile_id`),
  KEY `location` (`location`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=430 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_user_groups`
--

CREATE TABLE IF NOT EXISTS `#__fitness_user_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_profile_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `primary_trainer` int(11) NOT NULL,
  `other_trainers` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gid` (`group_id`),
  KEY `business_profile_id` (`business_profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------





--
-- Структура таблицы `#__fitness_settings_force_type`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_force_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_settings_exercise_type`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_exercise_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_appointments_favourites`
--

CREATE TABLE IF NOT EXISTS `#__fitness_appointments_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_training_period`
--

CREATE TABLE IF NOT EXISTS `#__fitness_training_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_settings_target_muscles`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_target_muscles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_settings_mechanics_type`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_mechanics_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_settings_equipment`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_equipment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_settings_difficulty`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_difficulty` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_settings_body_part`
--

CREATE TABLE IF NOT EXISTS `#__fitness_settings_body_part` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_session_type`
--

CREATE TABLE IF NOT EXISTS `#__fitness_session_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_session_focus`
--

CREATE TABLE IF NOT EXISTS `#__fitness_session_focus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `session_type_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `session_type_id` (`session_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=535 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_recipe_variations`
--

CREATE TABLE IF NOT EXISTS `#__fitness_recipe_variations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_recipe_types`
--

CREATE TABLE IF NOT EXISTS `#__fitness_recipe_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_programs_templates`
--

CREATE TABLE IF NOT EXISTS `#__fitness_programs_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `appointment_id` int(11) unsigned NOT NULL,
  `session_type` int(11) unsigned NOT NULL,
  `session_focus` int(11) unsigned NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `state` int(1) DEFAULT '1',
  `business_profile_id` int(11) unsigned NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `created` date NOT NULL,
  `access` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_profile_id` (`business_profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_pr_temp_exercises`
--

CREATE TABLE IF NOT EXISTS `#__fitness_pr_temp_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `order` int(11) NOT NULL,
  `sequence` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `speed` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL,
  `reps` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `sets` varchar(255) NOT NULL,
  `rest` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `video_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_pr_temp_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_pr_temp_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_pr_temp_clients`
--

CREATE TABLE IF NOT EXISTS `#__fitness_pr_temp_clients` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `client_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_program_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_program_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_recipes`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_recipes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_name` varchar(255) NOT NULL,
  `recipe_type` varchar(255) NOT NULL,
  `recipe_variation` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `video` varchar(255) NOT NULL,
  `number_serves` int(3) NOT NULL,
  `instructions` text NOT NULL,
  `status` int(1) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `assessed_by` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `business_profile_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=149 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_recipes_meals`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_recipes_meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(11) unsigned NOT NULL,
  `ingredient_id` int(11) unsigned NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `measurement` varchar(20) NOT NULL,
  `protein` float NOT NULL,
  `fats` float NOT NULL,
  `carbs` float NOT NULL,
  `calories` float NOT NULL,
  `energy` float NOT NULL,
  `saturated_fat` float NOT NULL,
  `total_sugars` float NOT NULL,
  `sodium` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_id` (`recipe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_recipes_favourites`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_recipes_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_recipes_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_recipes_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=156 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_focus`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_focus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(10) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `business_profile_id` (`business_profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_database`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_database` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ingredient_name` varchar(255) NOT NULL,
  `calories` float NOT NULL,
  `energy` float NOT NULL,
  `protein` float NOT NULL,
  `fats` float NOT NULL,
  `saturated_fat` float NOT NULL,
  `carbs` float NOT NULL,
  `total_sugars` float NOT NULL,
  `sodium` float NOT NULL,
  `specific_gravity` float NOT NULL,
  `description` text NOT NULL,
  `category` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2549 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_notifications`
--

CREATE TABLE IF NOT EXISTS `#__fitness_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_by` int(255) NOT NULL,
  `object` varchar(2) NOT NULL,
  `template_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `readed` varchar(255) NOT NULL,
  `hidden` varchar(255) NOT NULL,
  `url_id_1` int(11) NOT NULL,
  `url_id_2` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_locations`
--

CREATE TABLE IF NOT EXISTS `#__fitness_locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_goals`
--

CREATE TABLE IF NOT EXISTS `#__fitness_goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `goal_category_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `details` text NOT NULL,
  `comments` text NOT NULL,
  `status` int(1) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `minigoals_status` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=137 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_goal_focus`
--

CREATE TABLE IF NOT EXISTS `#__fitness_goal_focus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `goal_caregory_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_goal_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_goal_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_goal_categories`
--

CREATE TABLE IF NOT EXISTS `#__fitness_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_mini_goals`
--

CREATE TABLE IF NOT EXISTS `#__fitness_mini_goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `primary_goal_id` int(11) unsigned NOT NULL,
  `mini_goal_category_id` int(11) NOT NULL,
  `training_period_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `details` text NOT NULL,
  `comments` text NOT NULL,
  `status` int(1) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `primary_goal_id` (`primary_goal_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=164 ;

-- --------------------------------------------------------
--
-- Структура таблицы `#__fitness_mini_goal_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_mini_goal_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_mini_goal_categories`
--

CREATE TABLE IF NOT EXISTS `#__fitness_mini_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_exercise_library`
--

CREATE TABLE IF NOT EXISTS `#__fitness_exercise_library` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_name` varchar(255) NOT NULL,
  `exercise_type` varchar(25) NOT NULL,
  `force_type` varchar(255) NOT NULL,
  `mechanics_type` varchar(255) NOT NULL,
  `body_part` varchar(255) NOT NULL,
  `target_muscles` varchar(255) NOT NULL,
  `equipment_type` varchar(255) NOT NULL,
  `difficulty` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `global_business_permissions` tinyint(1) NOT NULL,
  `user_view_permission` text NOT NULL,
  `show_my_exercise` text NOT NULL,
  `my_exercise_clients` text NOT NULL,
  `business_profiles` text NOT NULL,
  `video` varchar(255) NOT NULL,
  `assessed_by` int(11) NOT NULL,
  `viewed` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=86 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_exercise_library_favourites`
--

CREATE TABLE IF NOT EXISTS `#__fitness_exercise_library_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_exercise_library_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_exercise_library_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(255) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `active_start` date NOT NULL,
  `active_finish` date NOT NULL,
  `force_active` tinyint(1) NOT NULL,
  `override_dates` tinyint(1) NOT NULL DEFAULT '0',
  `primary_goal` int(11) NOT NULL,
  `mini_goal` int(11) unsigned NOT NULL,
  `nutrition_focus` int(11) NOT NULL,
  `trainer_comments` text NOT NULL,
  `information` text NOT NULL,
  `activity_level` int(1) NOT NULL DEFAULT '0',
  `allowed_proteins` text NOT NULL,
  `allowed_fats` text NOT NULL,
  `allowed_carbs` text NOT NULL,
  `allowed_liquids` text NOT NULL,
  `other_recommendations` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `trainer_id` (`trainer_id`),
  KEY `mini_goal` (`mini_goal`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_targets_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_targets_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `business_profile_id` int(11) unsigned NOT NULL,
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_targets`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_targets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `calories` float NOT NULL,
  `water` float NOT NULL,
  `protein` float NOT NULL,
  `fats` float NOT NULL,
  `carbs` float NOT NULL,
  `gender` varchar(10) NOT NULL,
  `age` int(2) NOT NULL,
  `height` int(10) NOT NULL,
  `weight` int(10) NOT NULL,
  `formula` varchar(10) NOT NULL,
  `step2_fat_loss` varchar(10) NOT NULL,
  `step2_maintain` varchar(10) NOT NULL,
  `step2_bulking` varchar(10) NOT NULL,
  `step2_custom` varchar(10) NOT NULL,
  `exercise_level` varchar(20) NOT NULL,
  `exercise_level_water` varchar(10) NOT NULL,
  `body_fat` varchar(10) NOT NULL,
  `climate` varchar(15) NOT NULL,
  `BMR` varchar(10) NOT NULL,
  `TDEE` varchar(10) NOT NULL,
  `intensity` varchar(10) NOT NULL,
  `common_profiles` varchar(20) NOT NULL,
  `step3_protein` varchar(10) NOT NULL,
  `step3_protein_custom` varchar(10) NOT NULL,
  `step3_fats` varchar(10) NOT NULL,
  `step3_carbs` varchar(10) NOT NULL,
  `step3_fats_custom` varchar(10) NOT NULL,
  `step4_calories` varchar(10) NOT NULL,
  `step4_protein_percent` float NOT NULL,
  `step4_fat_percent` float NOT NULL,
  `step4_carbs_percent` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_supplement_protocols`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_supplement_protocols` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_supplements_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_supplements_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `business_profile_id` int(11) unsigned NOT NULL,
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `sub_item_id` (`sub_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_supplements`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_supplements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `protocol_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `comments` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `protocol_id` (`protocol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------



--
-- Структура таблицы `#__fitness_nutrition_plan_shopping_list_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_shopping_list_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_nutrition_plan_menus`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `submit_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `assessed_by` int(11) NOT NULL,
  `protein` float NOT NULL,
  `fats` float NOT NULL,
  `carbs` float NOT NULL,
  `calories` float NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_nutrition_plan_macronutrients_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_macronutrients_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_example_day_recipes`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_example_day_recipes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `menu_id` int(11) unsigned NOT NULL,
  `example_day_id` int(1) NOT NULL,
  `original_recipe_id` int(255) unsigned NOT NULL,
  `number_serves` int(3) NOT NULL,
  `description` varchar(255) NOT NULL,
  `time` varchar(10) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `original_recipe_id` (`original_recipe_id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_plan_example_day_ingredients`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_example_day_ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `menu_id` int(11) unsigned NOT NULL,
  `recipe_id` int(11) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `ingredient_id` int(11) unsigned NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `measurement` varchar(20) NOT NULL,
  `protein` float NOT NULL,
  `fats` float NOT NULL,
  `carbs` float NOT NULL,
  `calories` float NOT NULL,
  `energy` float NOT NULL,
  `saturated_fat` float NOT NULL,
  `total_sugars` float NOT NULL,
  `sodium` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `recipe_id` (`recipe_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Структура таблицы `#__fitness_nutrition_plan_example_day_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_example_day_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `business_profile_id` int(11) unsigned NOT NULL,
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Структура таблицы `#__fitness_nutrition_plan_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_diary`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `entry_date` date NOT NULL,
  `submit_date` datetime NOT NULL,
  `client_id` int(255) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `assessed_by` int(11) NOT NULL,
  `goal_category_id` int(11) NOT NULL,
  `nutrition_focus` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `score` float NOT NULL,
  `trainer_comments` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `target_calories` float NOT NULL,
  `target_water` float NOT NULL,
  `target_protein` float NOT NULL,
  `target_fats` float NOT NULL,
  `target_carbs` float NOT NULL,
  `target_protein_percent` float NOT NULL,
  `target_fats_percent` float NOT NULL,
  `target_carbs_percent` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_diary_meal_entries`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_meal_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `diary_id` int(11) unsigned NOT NULL,
  `meal_time` varchar(10) NOT NULL,
  `water` float NOT NULL,
  `previous_water` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `diary_id` (`diary_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_diary_meals`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_meals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `diary_id` int(11) unsigned NOT NULL,
  `meal_entry_id` int(11) unsigned NOT NULL,
  `description` int(11) NOT NULL,
  `trainer_comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `meal_entry_id` (`meal_entry_id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `diary_id` (`diary_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_diary_ingredients`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `diary_id` int(11) unsigned NOT NULL,
  `meal_entry_id` int(11) unsigned NOT NULL,
  `meal_id` int(11) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `ingredient_id` int(11) unsigned NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `measurement` varchar(20) NOT NULL,
  `protein` float NOT NULL,
  `fats` float NOT NULL,
  `carbs` float NOT NULL,
  `calories` float NOT NULL,
  `energy` float NOT NULL,
  `saturated_fat` float NOT NULL,
  `total_sugars` float NOT NULL,
  `sodium` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `meal_id` (`meal_id`),
  KEY `diary_id` (`diary_id`),
  KEY `meal_entry_id` (`meal_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_nutrition_diary_comments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conversation_permissions` varchar(255) NOT NULL,
  `allowed_users` varchar(255) NOT NULL,
  `allowed_business` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_training_periodalization`
--

CREATE TABLE IF NOT EXISTS `#__fitness_training_periodalization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mini_goal_id` int(11) unsigned NOT NULL,
  `period_focus` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mini_goal_id` (`mini_goal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_training_sessions`
--

CREATE TABLE IF NOT EXISTS `#__fitness_training_sessions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `period_id` int(11) unsigned NOT NULL,
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  `appointment_type_id` int(11) NOT NULL,
  `session_type` int(11) NOT NULL,
  `session_focus` int(11) NOT NULL,
  `location` int(11) NOT NULL,
  `pr_temp_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `period_id` (`period_id`),
  KEY `session_type` (`session_type`),
  KEY `session_focus` (`session_focus`),
  KEY `location` (`location`),
  KEY `pr_temp_id` (`pr_temp_id`),
  KEY `appointment_type_id` (`appointment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_events_exercises`
--

CREATE TABLE IF NOT EXISTS `#__fitness_events_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `order` int(11) NOT NULL,
  `sequence` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `speed` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL,
  `reps` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `sets` varchar(255) NOT NULL,
  `rest` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `video_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_email_reminder`
--

CREATE TABLE IF NOT EXISTS `#__fitness_email_reminder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  `sent` int(1) NOT NULL DEFAULT '0',
  `confirmed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__fitness_database_categories`
--

CREATE TABLE IF NOT EXISTS `#__fitness_database_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_assessments_photos`
--

CREATE TABLE IF NOT EXISTS `#__fitness_assessments_photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `client_comments` text NOT NULL,
  `trainer_comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------
--
-- Структура таблицы `#__fitness_assessments`
--

CREATE TABLE IF NOT EXISTS `#__fitness_assessments` (
  `assessment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `as_height` float NOT NULL,
  `as_weight` float NOT NULL,
  `as_age` int(2) NOT NULL,
  `as_body_fat` float NOT NULL,
  `as_lean_mass` float NOT NULL,
  `as_comments` text,
  `ha_blood_pressure` varchar(10) NOT NULL,
  `ha_body_mass_index` float DEFAULT '0',
  `ha_sit_reach` float DEFAULT '0',
  `ha_lung_function` float DEFAULT '0',
  `ha_aerobic_fitness` varchar(15) DEFAULT '0',
  `ha_comments` text,
  `am_height` float DEFAULT '0',
  `am_weight` float DEFAULT '0',
  `am_waist` float DEFAULT '0',
  `am_hips` float DEFAULT '0',
  `am_chest` float DEFAULT '0',
  `am_bicep_r` float DEFAULT '0',
  `am_bicep_l` float DEFAULT '0',
  `am_thigh_r` float DEFAULT '0',
  `am_thigh_l` float DEFAULT '0',
  `am_calf_r` float DEFAULT '0',
  `am_calf_l` float DEFAULT '0',
  `am_comments` text,
  `bia_body_fat` float DEFAULT '0',
  `bia_body_water` float DEFAULT '0',
  `bia_muscle_mass` float DEFAULT '0',
  `bia_bone_mass` float DEFAULT '0',
  `bia_visceral_fat` int(2) DEFAULT '0',
  `bio_comments` text,
  `bsm_height` float DEFAULT '0',
  `bsm_weight` float DEFAULT '0',
  `bsm_chin` float DEFAULT '0',
  `bsm_check` float DEFAULT '0',
  `bsm_pec` float DEFAULT '0',
  `bsm_tricep` float DEFAULT '0',
  `bsm_subscapularis` float DEFAULT '0',
  `bsm_sum10` float DEFAULT '0',
  `bsm_sum12` float DEFAULT '0',
  `bsm_midaxillary` float DEFAULT '0',
  `bsm_supraillac` float DEFAULT '0',
  `bsm_umbilical` float DEFAULT '0',
  `bsm_knee` float DEFAULT '0',
  `bsm_calf` float DEFAULT '0',
  `bsm_quadricep` float DEFAULT '0',
  `bsm_hamstring` float DEFAULT '0',
  `bsm_body_fat` float DEFAULT '0',
  `bsm_lean_mass` float DEFAULT '0',
  `bsm_comments` text,
  `nutrition_protocols` text,
  `supplementation_protocols` text,
  `training_protocols` text,
  PRIMARY KEY (`assessment_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Структура таблицы `#__fitness_appointment_clients`
--

CREATE TABLE IF NOT EXISTS `#__fitness_appointment_clients` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `client_id` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `client_id` (`client_id`),
  KEY `client_id_2` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=413 ;

-- --------------------------------------------------------


-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `#__fitness_appointments_favourites`
--
ALTER TABLE `#__fitness_appointments_favourites`
  ADD CONSTRAINT `#__fitness_appointments_favourites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_appointments_favourites_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `#__fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_appointment_clients`
--
ALTER TABLE `#__fitness_appointment_clients`
  ADD CONSTRAINT `#__fitness_appointment_clients_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_appointment_clients_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `#__fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_assessments`
--
ALTER TABLE `#__fitness_assessments`
  ADD CONSTRAINT `#__fitness_assessments_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_assessments_photos`
--
ALTER TABLE `#__fitness_assessments_photos`
  ADD CONSTRAINT `#__fitness_assessments_photos_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_email_reminder`
--
ALTER TABLE `#__fitness_email_reminder`
  ADD CONSTRAINT `#__fitness_email_reminder_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_events_exercises`
--
ALTER TABLE `#__fitness_events_exercises`
  ADD CONSTRAINT `#__fitness_events_exercises_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_exercise_library_comments`
--
ALTER TABLE `#__fitness_exercise_library_comments`
  ADD CONSTRAINT `#__fitness_exercise_library_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_exercise_library` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_exercise_library_favourites`
--
ALTER TABLE `#__fitness_exercise_library_favourites`
  ADD CONSTRAINT `#__fitness_exercise_library_favourites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_exercise_library` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_exercise_library_favourites_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `#__fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_goals`
--
ALTER TABLE `#__fitness_goals`
  ADD CONSTRAINT `#__fitness_goals_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `#__fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_goal_comments`
--
ALTER TABLE `#__fitness_goal_comments`
  ADD CONSTRAINT `#__fitness_goal_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_mini_goals`
--
ALTER TABLE `#__fitness_mini_goals`
  ADD CONSTRAINT `#__fitness_mini_goals_ibfk_1` FOREIGN KEY (`primary_goal_id`) REFERENCES `#__fitness_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_mini_goal_comments`
--
ALTER TABLE `#__fitness_mini_goal_comments`
  ADD CONSTRAINT `#__fitness_mini_goal_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_mini_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_diary`
--
ALTER TABLE `#__fitness_nutrition_diary`
  ADD CONSTRAINT `#__fitness_nutrition_diary_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_diary_comments`
--
ALTER TABLE `#__fitness_nutrition_diary_comments`
  ADD CONSTRAINT `#__fitness_nutrition_diary_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_diary` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_diary_ingredients`
--
ALTER TABLE `#__fitness_nutrition_diary_ingredients`
  ADD CONSTRAINT `#__fitness_nutrition_diary_ingredients_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `#__fitness_nutrition_diary_meals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_diary_ingredients_ibfk_3` FOREIGN KEY (`diary_id`) REFERENCES `#__fitness_nutrition_diary` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_diary_ingredients_ibfk_4` FOREIGN KEY (`meal_entry_id`) REFERENCES `#__fitness_nutrition_diary_meal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_diary_ingredients_ibfk_5` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_diary_meals`
--
ALTER TABLE `#__fitness_nutrition_diary_meals`
  ADD CONSTRAINT `#__fitness_nutrition_diary_meals_ibfk_1` FOREIGN KEY (`meal_entry_id`) REFERENCES `#__fitness_nutrition_diary_meal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_diary_meals_ibfk_2` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_diary_meals_ibfk_3` FOREIGN KEY (`diary_id`) REFERENCES `#__fitness_nutrition_diary` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_diary_meal_entries`
--
ALTER TABLE `#__fitness_nutrition_diary_meal_entries`
  ADD CONSTRAINT `#__fitness_nutrition_diary_meal_entries_ibfk_1` FOREIGN KEY (`diary_id`) REFERENCES `#__fitness_nutrition_diary` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_diary_meal_entries_ibfk_2` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan`
--
ALTER TABLE `#__fitness_nutrition_plan`
  ADD CONSTRAINT `#__fitness_nutrition_plan_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `#__fitness_clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `#__users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_ibfk_3` FOREIGN KEY (`mini_goal`) REFERENCES `#__fitness_mini_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_comments`
--
ALTER TABLE `#__fitness_nutrition_plan_comments`
  ADD CONSTRAINT `#__fitness_nutrition_plan_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_example_day_comments`
--
ALTER TABLE `#__fitness_nutrition_plan_example_day_comments`
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_plan_menus` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_example_day_ingredients`
--
ALTER TABLE `#__fitness_nutrition_plan_example_day_ingredients`
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_ingredients_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_ingredients_ibfk_3` FOREIGN KEY (`recipe_id`) REFERENCES `#__fitness_nutrition_plan_example_day_recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_ingredients_ibfk_4` FOREIGN KEY (`menu_id`) REFERENCES `#__fitness_nutrition_plan_menus` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_example_day_recipes`
--
ALTER TABLE `#__fitness_nutrition_plan_example_day_recipes`
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_recipes_ibfk_1` FOREIGN KEY (`original_recipe_id`) REFERENCES `#__fitness_nutrition_recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_recipes_ibfk_2` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_example_day_recipes_ibfk_3` FOREIGN KEY (`menu_id`) REFERENCES `#__fitness_nutrition_plan_menus` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_macronutrients_comments`
--
ALTER TABLE `#__fitness_nutrition_plan_macronutrients_comments`
  ADD CONSTRAINT `#__fitness_nutrition_plan_macronutrients_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_menus`
--
ALTER TABLE `#__fitness_nutrition_plan_menus`
  ADD CONSTRAINT `#__fitness_nutrition_plan_menus_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_supplements`
--
ALTER TABLE `#__fitness_nutrition_plan_supplements`
  ADD CONSTRAINT `#__fitness_nutrition_plan_supplements_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_supplements_ibfk_2` FOREIGN KEY (`protocol_id`) REFERENCES `#__fitness_nutrition_plan_supplement_protocols` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_supplements_comments`
--
ALTER TABLE `#__fitness_nutrition_plan_supplements_comments`
  ADD CONSTRAINT `#__fitness_nutrition_plan_supplements_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_plan_supplements_comments_ibfk_2` FOREIGN KEY (`sub_item_id`) REFERENCES `#__fitness_nutrition_plan_supplement_protocols` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_supplement_protocols`
--
ALTER TABLE `#__fitness_nutrition_plan_supplement_protocols`
  ADD CONSTRAINT `#__fitness_nutrition_plan_supplement_protocols_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_targets`
--
ALTER TABLE `#__fitness_nutrition_plan_targets`
  ADD CONSTRAINT `#__fitness_nutrition_plan_targets_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_plan_targets_comments`
--
ALTER TABLE `#__fitness_nutrition_plan_targets_comments`
  ADD CONSTRAINT `#__fitness_nutrition_plan_targets_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_recipes_comments`
--
ALTER TABLE `#__fitness_nutrition_recipes_comments`
  ADD CONSTRAINT `#__fitness_nutrition_recipes_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_recipes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_recipes_favourites`
--
ALTER TABLE `#__fitness_nutrition_recipes_favourites`
  ADD CONSTRAINT `#__fitness_nutrition_recipes_favourites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_nutrition_recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_nutrition_recipes_favourites_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `#__fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_nutrition_recipes_meals`
--
ALTER TABLE `#__fitness_nutrition_recipes_meals`
  ADD CONSTRAINT `#__fitness_nutrition_recipes_meals_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `#__fitness_nutrition_recipes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_program_comments`
--
ALTER TABLE `#__fitness_program_comments`
  ADD CONSTRAINT `#__fitness_program_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_pr_temp_clients`
--
ALTER TABLE `#__fitness_pr_temp_clients`
  ADD CONSTRAINT `#__fitness_pr_temp_clients_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_programs_templates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_pr_temp_comments`
--
ALTER TABLE `#__fitness_pr_temp_comments`
  ADD CONSTRAINT `#__fitness_pr_temp_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_programs_templates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_pr_temp_exercises`
--
ALTER TABLE `#__fitness_pr_temp_exercises`
  ADD CONSTRAINT `#__fitness_pr_temp_exercises_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `#__fitness_programs_templates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_session_focus`
--
ALTER TABLE `#__fitness_session_focus`
  ADD CONSTRAINT `#__fitness_session_focus_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `#__fitness_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_session_focus_ibfk_2` FOREIGN KEY (`session_type_id`) REFERENCES `#__fitness_session_type` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_session_type`
--
ALTER TABLE `#__fitness_session_type`
  ADD CONSTRAINT `#__fitness_session_type_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `#__fitness_categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_training_periodalization`
--
ALTER TABLE `#__fitness_training_periodalization`
  ADD CONSTRAINT `#__fitness_training_periodalization_ibfk_1` FOREIGN KEY (`mini_goal_id`) REFERENCES `#__fitness_mini_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_training_sessions`
--
ALTER TABLE `#__fitness_training_sessions`
  ADD CONSTRAINT `#__fitness_training_sessions_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `#__fitness_training_periodalization` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_training_sessions_ibfk_3` FOREIGN KEY (`session_type`) REFERENCES `#__fitness_session_type` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_training_sessions_ibfk_4` FOREIGN KEY (`session_focus`) REFERENCES `#__fitness_session_focus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `#__fitness_training_sessions_ibfk_5` FOREIGN KEY (`appointment_type_id`) REFERENCES `#__fitness_categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `#__fitness_user_groups`
--
ALTER TABLE `#__fitness_user_groups`
  ADD CONSTRAINT `#__fitness_user_groups_ibfk_1` FOREIGN KEY (`business_profile_id`) REFERENCES `#__fitness_business_profiles` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;