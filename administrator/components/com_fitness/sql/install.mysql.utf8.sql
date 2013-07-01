CREATE TABLE IF NOT EXISTS `#__fitness_clients` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`user_id` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`primary_trainer` INT(11)  NOT NULL ,
`other_trainers` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (user_id) REFERENCES #__users(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__fitness_goals` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`user_id` INT(11)  NOT NULL ,
`goal_category_id` INT(11)  NOT NULL ,
`goal_focus_id` INT(11)  NOT NULL ,
`deadline` DATE NOT NULL ,
`details` TEXT NOT NULL ,
`comments` TEXT NOT NULL ,
`completed` varchar(20) NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
FOREIGN KEY (user_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;





CREATE TABLE IF NOT EXISTS `#__fitness_categories` (
`id` int(11)  NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`color` VARCHAR(20)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



INSERT INTO `#__fitness_categories` (`id`, `name`, `color`, `state`) VALUES
(1, 'Personal Training', '#00BF32', 1),
(2, 'Semi-Private Training ', '#007F01', 1),
(3, 'Resistance Workout ', '#0070FF', 1),
(4, 'Cardio Workout', '#E94E1B', 1),
(5, 'Assessment', '#E6007E', 1),
(6, 'Consultation', '#FFE500', 1),
(7, 'Special Event', '#E30613', 1),
(8, 'Available', '#7FFF8E', 1),
(9, 'Unavailable', '#D1D1D1', 1);


CREATE TABLE IF NOT EXISTS `#__fitness_session_type` (
`id` int(11)  NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`category_id` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`),
FOREIGN KEY (category_id) REFERENCES #__fitness_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__fitness_session_focus` (
`id` int(11)  NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`category_id` INT(11)  NOT NULL ,
`session_type_id` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`),
FOREIGN KEY (category_id) REFERENCES #__fitness_categories(id) ON DELETE CASCADE,
FOREIGN KEY (session_type_id) REFERENCES #__fitness_session_type(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__fitness_locations` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__fitness_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



INSERT INTO `#__fitness_goal_categories` (`id`, `name`) VALUES
(1, 'Primary Goal'),
(2, 'Secondary Goal');



CREATE TABLE IF NOT EXISTS `#__fitness_goal_focus` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`goal_caregory_id` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;




CREATE TABLE IF NOT EXISTS `#__fitness_email_reminder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `sent` int(1) NOT NULL DEFAULT '0',
  `confirmed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  FOREIGN KEY (event_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

