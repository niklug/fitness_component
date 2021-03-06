CREATE TABLE IF NOT EXISTS `#__fitness_clients` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`primary_trainer` INT(11)  NOT NULL ,
`other_trainers` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`),
UNIQUE (`user_id`),
FOREIGN KEY (user_id) REFERENCES #__users(id) ON DELETE CASCADE,
FOREIGN KEY (business_profile_id) REFERENCES #__fitness_business_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT COLLATE=utf8_general_ci;




CREATE TABLE IF NOT EXISTS `#__fitness_goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `goal_category_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `details` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `minigoals_status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FOREIGN KEY (user_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLEglobal_business_permission IF NOT EXISTS `#__fitness_categories` (
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
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__fitness_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__fitness_email_reminder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `client_id` int(10)  NOT NULL,
  `sent` int(1) NOT NULL DEFAULT '0',
  `confirmed` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  FOREIGN KEY (event_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fitness_appointment_clients` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL,
  `client_id` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `client_id` (`client_id`),
  KEY `client_id_2` (`client_id`),
  FOREIGN KEY (event_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;





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
  `video_id` int(11) NOT NULL,
  `comments` text,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


















INSERT INTO `#__fitness_session_type` (`id`, `name`, `category_id`, `state`) VALUES
(1, 'Hypertrophy', 1, 1),
(2, 'Strength', 1, 1),
(3, 'Power', 1, 1),
(4, 'CrossFit', 1, 1),
(5, 'Circuit', 1, 1),
(6, 'Endurance', 1, 1),
(7, 'Agility & Speed', 1, 1),
(8, 'Muscle Build & Strength', 2, 1),
(9, 'Fat Loss, Shape & Tone', 2, 1),
(10, 'Strength', 2, 1),
(11, 'CrossFit', 2, 1),
(12, 'Circuit', 2, 1),
(15, 'Hypertrophy', 3, 1),
(16, 'Strength', 3, 1),
(17, 'Power', 3, 1),
(18, 'CrossFit', 3, 1),
(19, 'Circuit', 3, 1),
(20, 'Endurance', 3, 1),
(21, 'Agility & Speed', 3, 1),
(22, 'Freestyle Fitness Class', 4, 1),
(23, 'Les Mills Fitness Class', 4, 1),
(24, 'Cardio Sports', 4, 1),
(25, 'BioSignature', 5, 1),
(26, 'Standard Assessment', 5, 1),
(27, 'Goal Setting & Direction', 6, 1),
(28, 'Elite Progression, Lifestyle & Mindset', 6, 1),
(29, 'Initial Consultation', 6, 1),
(30, 'Training & Nutrition Seminar', 7, 1),
(31, 'Outdoor BootCamp', 7, 1),
(33, 'Personal Training', 8, 1),
(34, 'Semi-Private Training', 8, 1),
(35, 'Assessment', 8, 1),
(36, 'Consultation', 8, 1),
(40, 'Work Commitments', 9, 1),
(41, 'Personal Commitments', 9, 1),
(42, 'Annual Leave/Holdiays', 9, 1),
(43, 'Illness/Unwell', 9, 1),
(44, 'Injury/Injured', 9, 1),
(45, 'Recovery/Training Break', 9, 1);



INSERT INTO `#__fitness_session_focus` (`id`, `name`, `category_id`, `session_type_id`, `state`) VALUES
(1, 'Upper Body Conditioning', 1, 1, 1),
(2, 'Lower Body Conditioning', 1, 1, 1),
(3, 'Whole Body Conditioning', 1, 1, 1),
(4, 'Quads, Hams, Glutes, Calves', 1, 1, 1),
(5, 'Quadriceps & Glutes', 1, 1, 1),
(6, 'Hamstrings & Glutes', 1, 1, 1),
(7, 'Calves', 1, 1, 1),
(8, 'Back & Legs / Posterior Chain', 1, 1, 1),
(9, 'Back & Arms', 1, 1, 1),
(10, 'Back & Chest', 1, 1, 1),
(11, 'Chest & Arms', 1, 1, 1),
(12, 'Chest & Back', 1, 1, 1),
(13, 'Chest & Shoulders', 1, 1, 1),
(14, 'Chest, Shoulders & Arms', 1, 1, 1),
(15, 'Shoulders', 1, 1, 1),
(16, 'Shoulders & Arms', 1, 1, 1),
(17, 'Arms', 1, 1, 1),
(18, 'Abdominals & Core', 1, 1, 1),
(19, 'Conditioning (Push)', 1, 1, 1),
(20, 'Conditioning (Pull)', 1, 1, 1),
(21, 'Upper Body Conditioning (Functional)', 1, 1, 1),
(22, 'Lower Body Conditioning (Functional)', 1, 1, 1),
(23, 'Whole Body Conditioning (Functional)', 1, 1, 1),
(24, 'Conditioning (Functional)', 1, 1, 1),
(32, 'Upper Body Conditioning', 1, 2, 1),
(33, 'Lower Body Conditioning', 1, 2, 1),
(34, 'Whole Body Conditioning', 1, 2, 1),
(35, 'Upper Body Conditioning (Functional)', 1, 2, 1),
(36, 'Lower Body Conditioning (Functional)', 1, 2, 1),
(37, 'Whole Body Conditioning (Functional)', 1, 2, 1),
(38, 'Conditioning (Functional)', 1, 2, 1),
(39, 'Conditioning (Push)', 1, 2, 1),
(40, 'Conditioning (Pull)', 1, 2, 1),
(41, 'Quads, Hams, Glutes, Calves', 1, 2, 1),
(42, 'Quadriceps & Glutes', 1, 2, 1),
(43, 'Hamstrings & Glutes', 1, 2, 1),
(44, 'Calves', 1, 2, 1),
(45, 'Back & Legs / Posterior Chain', 1, 2, 1),
(46, 'Back & Arms', 1, 2, 1),
(47, 'Back & Chest', 1, 2, 1),
(48, 'Chest & Arms', 1, 2, 1),
(49, 'Chest & Back', 1, 2, 1),
(50, 'Chest & Shoulders', 1, 2, 1),
(51, 'Chest, Shoulders & Arms', 1, 2, 1),
(52, 'Shoulders', 1, 2, 1),
(53, 'Shoulders & Arms', 1, 2, 1),
(54, 'Arms', 1, 2, 1),
(55, 'Abdominals & Core', 1, 2, 1),
(63, 'Upper Body Conditioning', 1, 3, 1),
(64, 'Lower Body Conditioning', 1, 3, 1),
(65, 'Whole Body Conditioning', 1, 3, 1),
(66, 'Upper Body Conditioning (Functional)', 1, 3, 1),
(67, 'Lower Body Conditioning (Functional)', 1, 3, 1),
(68, 'Whole Body Conditioning (Functional)', 1, 3, 1),
(69, 'Conditioning (Functional)', 1, 3, 1),
(70, 'Conditioning (Push)', 1, 3, 1),
(71, 'Conditioning (Pull)', 1, 3, 1),
(72, 'Quads, Hams, Glutes, Calves', 1, 3, 1),
(73, 'Quadriceps & Glutes', 1, 3, 1),
(74, 'Hamstrings & Glutes', 1, 3, 1),
(75, 'Calves', 1, 3, 1),
(76, 'Back & Legs / Posterior Chain', 1, 3, 1),
(77, 'Back & Arms', 1, 3, 1),
(78, 'Back & Chest', 1, 3, 1),
(79, 'Chest & Arms', 1, 3, 1),
(80, 'Chest & Back', 1, 3, 1),
(81, 'Chest & Shoulders', 1, 3, 1),
(82, 'Chest, Shoulders & Arms', 1, 3, 1),
(83, 'Shoulders', 1, 3, 1),
(84, 'Shoulders & Arms', 1, 3, 1),
(85, 'Arms', 1, 3, 1),
(86, 'Abdominals & Core', 1, 3, 1),
(94, 'WOD (Workout of the Day)', 1, 4, 1),
(95, 'Upper Body Conditioning', 1, 5, 1),
(96, 'Lower Body Conditioning', 1, 5, 1),
(97, 'Whole Body Conditioning', 1, 5, 1),
(98, 'Upper Body Conditioning (Functional)', 1, 5, 1),
(99, 'Lower Body Conditioning (Functional)', 1, 5, 1),
(100, 'Whole Body Conditioning (Functional)', 1, 5, 1),
(101, 'Conditioning (Functional)', 1, 5, 1),
(102, 'Conditioning (Push)', 1, 5, 1),
(103, 'Conditioning (Pull)', 1, 5, 1),
(104, 'Quads, Hams, Glutes, Calves', 1, 5, 1),
(105, 'Quadriceps & Glutes', 1, 5, 1),
(106, 'Hamstrings & Glutes', 1, 5, 1),
(107, 'Calves', 1, 5, 1),
(108, 'Back & Legs / Posterior Chain', 1, 5, 1),
(109, 'Back & Arms', 1, 5, 1),
(110, 'Back & Chest', 1, 5, 1),
(111, 'Chest & Arms', 1, 5, 1),
(112, 'Chest & Back', 1, 5, 1),
(113, 'Chest & Shoulders', 1, 5, 1),
(114, 'Chest, Shoulders & Arms', 1, 5, 1),
(115, 'Shoulders', 1, 5, 1),
(116, 'Shoulders & Arms', 1, 5, 1),
(117, 'Arms', 1, 5, 1),
(118, 'Abdominals & Core', 1, 5, 1),
(126, 'Upper Body Conditioning', 1, 6, 1),
(127, 'Lower Body Conditioning', 1, 6, 1),
(128, 'Whole Body Conditioning', 1, 6, 1),
(129, 'Upper Body Conditioning (Functional)', 1, 6, 1),
(130, 'Lower Body Conditioning (Functional)', 1, 6, 1),
(131, 'Whole Body Conditioning (Functional)', 1, 6, 1),
(132, 'Conditioning (Functional)', 1, 6, 1),
(133, 'Conditioning (Push)', 1, 6, 1),
(134, 'Conditioning (Pull)', 1, 6, 1),
(135, 'Quads, Hams, Glutes, Calves', 1, 6, 1),
(136, 'Quadriceps & Glutes', 1, 6, 1),
(137, 'Hamstrings & Glutes', 1, 6, 1),
(138, 'Calves', 1, 6, 1),
(139, 'Back & Legs / Posterior Chain', 1, 6, 1),
(140, 'Back & Arms', 1, 6, 1),
(141, 'Back & Chest', 1, 6, 1),
(142, 'Chest & Arms', 1, 6, 1),
(143, 'Chest & Back', 1, 6, 1),
(144, 'Chest & Shoulders', 1, 6, 1),
(145, 'Chest, Shoulders & Arms', 1, 6, 1),
(146, 'Shoulders', 1, 6, 1),
(147, 'Shoulders & Arms', 1, 6, 1),
(148, 'Arms', 1, 6, 1),
(149, 'Abdominals & Core', 1, 6, 1),
(150, 'Agility & Speed', 1, 7, 1),
(151, 'Upper Body Conditioning', 2, 8, 1),
(152, 'Lower Body Conditioning', 2, 8, 1),
(153, 'Whole Body Conditioning', 2, 8, 1),
(154, 'Upper Body Conditioning (Functional)', 2, 8, 1),
(155, 'Lower Body Conditioning (Functional)', 2, 8, 1),
(156, 'Whole Body Conditioning (Functional)', 2, 8, 1),
(157, 'Conditioning (Functional)', 2, 8, 1),
(158, 'Conditioning (Push)', 2, 8, 1),
(159, 'Conditioning (Pull)', 2, 8, 1),
(160, 'Quads, Hams, Glutes, Calves', 2, 8, 1),
(161, 'Quadriceps & Glutes', 2, 8, 1),
(162, 'Hamstrings & Glutes', 2, 8, 1),
(163, 'Calves', 2, 8, 1),
(164, 'Back & Legs / Posterior Chain', 2, 8, 1),
(165, 'Back & Arms', 2, 8, 1),
(166, 'Back & Chest', 2, 8, 1),
(167, 'Chest & Arms', 2, 8, 1),
(168, 'Chest & Back', 2, 8, 1),
(169, 'Chest & Shoulders', 2, 8, 1),
(170, 'Chest, Shoulders & Arms', 2, 8, 1),
(171, 'Shoulders', 2, 8, 1),
(172, 'Shoulders & Arms', 2, 8, 1),
(173, 'Arms', 2, 8, 1),
(174, 'Abdominals & Core', 2, 8, 1),
(182, 'Upper Body Conditioning', 2, 9, 1),
(183, 'Lower Body Conditioning', 2, 9, 1),
(184, 'Whole Body Conditioning', 2, 9, 1),
(185, 'Upper Body Conditioning (Functional)', 2, 9, 1),
(186, 'Lower Body Conditioning (Functional)', 2, 9, 1),
(187, 'Whole Body Conditioning (Functional)', 2, 9, 1),
(188, 'Conditioning (Functional)', 2, 9, 1),
(189, 'Conditioning (Push)', 2, 9, 1),
(190, 'Conditioning (Pull)', 2, 9, 1),
(191, 'Quads, Hams, Glutes, Calves', 2, 9, 1),
(192, 'Quadriceps & Glutes', 2, 9, 1),
(193, 'Hamstrings & Glutes', 2, 9, 1),
(194, 'Calves', 2, 9, 1),
(195, 'Back & Legs / Posterior Chain', 2, 9, 1),
(196, 'Back & Arms', 2, 9, 1),
(197, 'Back & Chest', 2, 9, 1),
(198, 'Chest & Arms', 2, 9, 1),
(199, 'Chest & Back', 2, 9, 1),
(200, 'Chest & Shoulders', 2, 9, 1),
(201, 'Chest, Shoulders & Arms', 2, 9, 1),
(202, 'Shoulders', 2, 9, 1),
(203, 'Shoulders & Arms', 2, 9, 1),
(204, 'Arms', 2, 9, 1),
(205, 'Abdominals & Core', 2, 9, 1),
(213, 'Upper Body Conditioning', 2, 10, 1),
(214, 'Lower Body Conditioning', 2, 10, 1),
(215, 'Whole Body Conditioning', 2, 10, 1),
(216, 'Upper Body Conditioning (Functional)', 2, 10, 1),
(217, 'Lower Body Conditioning (Functional)', 2, 10, 1),
(218, 'Whole Body Conditioning (Functional)', 2, 10, 1),
(219, 'Conditioning (Functional)', 2, 10, 1),
(220, 'Conditioning (Push)', 2, 10, 1),
(221, 'Conditioning (Pull)', 2, 10, 1),
(222, 'Quads, Hams, Glutes, Calves', 2, 10, 1),
(223, 'Quadriceps & Glutes', 2, 10, 1),
(224, 'Hamstrings & Glutes', 2, 10, 1),
(225, 'Calves', 2, 10, 1),
(226, 'Back & Legs / Posterior Chain', 2, 10, 1),
(227, 'Back & Arms', 2, 10, 1),
(228, 'Back & Chest', 2, 10, 1),
(229, 'Chest & Arms', 2, 10, 1),
(230, 'Chest & Back', 2, 10, 1),
(231, 'Chest & Shoulders', 2, 10, 1),
(232, 'Chest, Shoulders & Arms', 2, 10, 1),
(233, 'Shoulders', 2, 10, 1),
(234, 'Shoulders & Arms', 2, 10, 1),
(235, 'Arms', 2, 10, 1),
(236, 'Abdominals & Core', 2, 10, 1),
(244, 'WOD (Workout of the Day)', 2, 11, 1),
(245, 'Upper Body Conditioning', 2, 12, 1),
(246, 'Lower Body Conditioning', 2, 12, 1),
(247, 'Whole Body Conditioning', 2, 12, 1),
(248, 'Upper Body Conditioning (Functional)', 2, 12, 1),
(249, 'Lower Body Conditioning (Functional)', 2, 12, 1),
(250, 'Whole Body Conditioning (Functional)', 2, 12, 1),
(251, 'Conditioning (Functional)', 2, 12, 1),
(252, 'Conditioning (Push)', 2, 12, 1),
(253, 'Conditioning (Pull)', 2, 12, 1),
(254, 'Quads, Hams, Glutes, Calves', 2, 12, 1),
(255, 'Quadriceps & Glutes', 2, 12, 1),
(256, 'Hamstrings & Glutes', 2, 12, 1),
(257, 'Calves', 2, 12, 1),
(258, 'Back & Legs / Posterior Chain', 2, 12, 1),
(259, 'Back & Arms', 2, 12, 1),
(260, 'Back & Chest', 2, 12, 1),
(261, 'Chest & Arms', 2, 12, 1),
(262, 'Chest & Back', 2, 12, 1),
(263, 'Chest & Shoulders', 2, 12, 1),
(264, 'Chest, Shoulders & Arms', 2, 12, 1),
(265, 'Shoulders', 2, 12, 1),
(266, 'Shoulders & Arms', 2, 12, 1),
(267, 'Arms', 2, 12, 1),
(268, 'Abdominals & Core', 2, 12, 1),
(276, 'Upper Body Conditioning', 3, 15, 1),
(277, 'Lower Body Conditioning', 3, 15, 1),
(278, 'Whole Body Conditioning', 3, 15, 1),
(279, 'Quads, Hams, Glutes, Calves', 3, 15, 1),
(280, 'Quadriceps & Glutes', 3, 15, 1),
(281, 'Hamstrings & Glutes', 3, 15, 1),
(282, 'Calves', 3, 15, 1),
(283, 'Back & Legs / Posterior Chain', 3, 15, 1),
(284, 'Back & Arms', 3, 15, 1),
(285, 'Back & Chest', 3, 15, 1),
(286, 'Chest & Arms', 3, 15, 1),
(287, 'Chest & Back', 3, 15, 1),
(288, 'Chest & Shoulders', 3, 15, 1),
(289, 'Chest, Shoulders & Arms', 3, 15, 1),
(290, 'Shoulders', 3, 15, 1),
(291, 'Shoulders & Arms', 3, 15, 1),
(292, 'Arms', 3, 15, 1),
(293, 'Abdominals & Core', 3, 15, 1),
(294, 'Conditioning (Push)', 3, 15, 1),
(295, 'Conditioning (Pull)', 3, 15, 1),
(296, 'Upper Body Conditioning (Functional)', 3, 15, 1),
(297, 'Lower Body Conditioning (Functional)', 3, 15, 1),
(298, 'Whole Body Conditioning (Functional)', 3, 15, 1),
(299, 'Conditioning (Functional)', 3, 15, 1),
(307, 'Upper Body Conditioning', 3, 16, 1),
(308, 'Lower Body Conditioning', 3, 16, 1),
(309, 'Whole Body Conditioning', 3, 16, 1),
(310, 'Upper Body Conditioning (Functional)', 3, 16, 1),
(311, 'Lower Body Conditioning (Functional)', 3, 16, 1),
(312, 'Whole Body Conditioning (Functional)', 3, 16, 1),
(313, 'Conditioning (Functional)', 3, 16, 1),
(314, 'Conditioning (Push)', 3, 16, 1),
(315, 'Conditioning (Pull)', 3, 16, 1),
(316, 'Quads, Hams, Glutes, Calves', 3, 16, 1),
(317, 'Quadriceps & Glutes', 3, 16, 1),
(318, 'Hamstrings & Glutes', 3, 16, 1),
(319, 'Calves', 3, 16, 1),
(320, 'Back & Legs / Posterior Chain', 3, 16, 1),
(321, 'Back & Arms', 3, 16, 1),
(322, 'Back & Chest', 3, 16, 1),
(323, 'Chest & Arms', 3, 16, 1),
(324, 'Chest & Back', 3, 16, 1),
(325, 'Chest & Shoulders', 3, 16, 1),
(326, 'Chest, Shoulders & Arms', 3, 16, 1),
(327, 'Shoulders', 3, 16, 1),
(328, 'Shoulders & Arms', 3, 16, 1),
(329, 'Arms', 3, 16, 1),
(330, 'Abdominals & Core', 3, 16, 1),
(338, 'Upper Body Conditioning', 3, 17, 1),
(339, 'Lower Body Conditioning', 3, 17, 1),
(340, 'Whole Body Conditioning', 3, 17, 1),
(341, 'Upper Body Conditioning (Functional)', 3, 17, 1),
(342, 'Lower Body Conditioning (Functional)', 3, 17, 1),
(343, 'Whole Body Conditioning (Functional)', 3, 17, 1),
(344, 'Conditioning (Functional)', 3, 17, 1),
(345, 'Conditioning (Push)', 3, 17, 1),
(346, 'Conditioning (Pull)', 3, 17, 1),
(347, 'Quads, Hams, Glutes, Calves', 3, 17, 1),
(348, 'Quadriceps & Glutes', 3, 17, 1),
(349, 'Hamstrings & Glutes', 3, 17, 1),
(350, 'Calves', 3, 17, 1),
(351, 'Back & Legs / Posterior Chain', 3, 17, 1),
(352, 'Back & Arms', 3, 17, 1),
(353, 'Back & Chest', 3, 17, 1),
(354, 'Chest & Arms', 3, 17, 1),
(355, 'Chest & Back', 3, 17, 1),
(356, 'Chest & Shoulders', 3, 17, 1),
(357, 'Chest, Shoulders & Arms', 3, 17, 1),
(358, 'Shoulders', 3, 17, 1),
(359, 'Shoulders & Arms', 3, 17, 1),
(360, 'Arms', 3, 17, 1),
(361, 'Abdominals & Core', 3, 17, 1),
(369, 'WOD (Workout of the Day)', 3, 18, 1),
(370, 'Upper Body Conditioning', 3, 19, 1),
(371, 'Lower Body Conditioning', 3, 19, 1),
(372, 'Whole Body Conditioning', 3, 19, 1),
(373, 'Upper Body Conditioning (Functional)', 3, 19, 1),
(374, 'Lower Body Conditioning (Functional)', 3, 19, 1),
(375, 'Whole Body Conditioning (Functional)', 3, 19, 1),
(376, 'Conditioning (Functional)', 3, 19, 1),
(377, 'Conditioning (Push)', 3, 19, 1),
(378, 'Conditioning (Pull)', 3, 19, 1),
(379, 'Quads, Hams, Glutes, Calves', 3, 19, 1),
(380, 'Quadriceps & Glutes', 3, 19, 1),
(381, 'Hamstrings & Glutes', 3, 19, 1),
(382, 'Calves', 3, 19, 1),
(383, 'Back & Legs / Posterior Chain', 3, 19, 1),
(384, 'Back & Arms', 3, 19, 1),
(385, 'Back & Chest', 3, 19, 1),
(386, 'Chest & Arms', 3, 19, 1),
(387, 'Chest & Back', 3, 19, 1),
(388, 'Chest & Shoulders', 3, 19, 1),
(389, 'Chest, Shoulders & Arms', 3, 19, 1),
(390, 'Shoulders', 3, 19, 1),
(391, 'Shoulders & Arms', 3, 19, 1),
(392, 'Arms', 3, 19, 1),
(393, 'Abdominals & Core', 3, 19, 1),
(401, 'Upper Body Conditioning', 3, 20, 1),
(402, 'Lower Body Conditioning', 3, 20, 1),
(403, 'Whole Body Conditioning', 3, 20, 1),
(404, 'Upper Body Conditioning (Functional)', 3, 20, 1),
(405, 'Lower Body Conditioning (Functional)', 3, 20, 1),
(406, 'Whole Body Conditioning (Functional)', 3, 20, 1),
(407, 'Conditioning (Functional)', 3, 20, 1),
(408, 'Conditioning (Push)', 3, 20, 1),
(409, 'Conditioning (Pull)', 3, 20, 1),
(410, 'Quads, Hams, Glutes, Calves', 3, 20, 1),
(411, 'Quadriceps & Glutes', 3, 20, 1),
(412, 'Hamstrings & Glutes', 3, 20, 1),
(413, 'Calves', 3, 20, 1),
(414, 'Back & Legs / Posterior Chain', 3, 20, 1),
(415, 'Back & Arms', 3, 20, 1),
(416, 'Back & Chest', 3, 20, 1),
(417, 'Chest & Arms', 3, 20, 1),
(418, 'Chest & Back', 3, 20, 1),
(419, 'Chest & Shoulders', 3, 20, 1),
(420, 'Chest, Shoulders & Arms', 3, 20, 1),
(421, 'Shoulders', 3, 20, 1),
(422, 'Shoulders & Arms', 3, 20, 1),
(423, 'Arms', 3, 20, 1),
(424, 'Abdominals & Core', 3, 20, 1),
(432, 'Agility & Speed', 3, 21, 1),
(433, 'SH''BAM', 4, 23, 1),
(434, 'BODYVIVE', 4, 23, 1),
(435, 'BODYATTACK', 4, 23, 1),
(436, 'BODYPUMP', 4, 23, 1),
(437, 'RPM', 4, 23, 1),
(438, 'BODYSTEP', 4, 23, 1),
(439, 'CXWORX', 4, 23, 1),
(440, 'GRIT CARDIO', 4, 23, 1),
(441, 'GRIT STRENGTH', 4, 23, 1),
(442, 'GRIT PLYO', 4, 23, 1),
(443, 'BODYBALANCE', 4, 23, 1),
(444, 'BODYCOMBAT', 4, 23, 1),
(445, 'BODYJAM', 4, 23, 1),
(448, 'Step', 4, 22, 1),
(449, 'Zumba', 4, 22, 1),
(450, 'Pilates', 4, 22, 1),
(451, 'Yoga', 4, 22, 1),
(452, 'Boxing', 4, 22, 1),
(453, 'Cycle', 4, 22, 1),
(454, 'KettleBells (SGT)', 4, 22, 1),
(455, 'TRX (SGT)', 4, 22, 1),
(456, 'ViPR (SGT)', 4, 22, 1),
(457, 'RIP (SGT)', 4, 22, 1),
(458, 'CORE (SGT)', 4, 22, 1),
(459, 'SXT (SGT)', 4, 22, 1),
(463, 'Cycling', 4, 24, 1),
(464, 'Swimming', 4, 24, 1),
(465, 'Rowing', 4, 24, 1),
(466, 'Running', 4, 24, 1),
(467, 'Cross Trainer', 4, 24, 1),
(468, 'Stepper', 4, 24, 1),
(469, 'Skipping', 4, 24, 1),
(470, 'Walking', 4, 24, 1),
(478, 'BioSignature Modulation', 5, 25, 1),
(479, 'Composition Assessment', 5, 26, 1),
(480, 'Health Assessment', 5, 26, 1),
(481, 'Anatomical Measurements', 5, 26, 1),
(482, 'Meet & Greet', 6, 29, 1),
(483, 'Follow-up Discussion', 6, 29, 1),
(485, 'Defining Goals & Training Focus', 6, 27, 1),
(486, 'Redefining Current Goals', 6, 27, 1),
(488, 'Lifestyle & Mindset Coaching', 6, 28, 1),
(489, 'Training Periodisation & Programming', 6, 28, 1),
(490, 'Training & Technique Coaching', 6, 28, 1),
(491, 'Nutrition & Supplementation Coaching', 6, 28, 1),
(495, 'VIP Client 1-on-1 Training', 8, 33, 1),
(496, 'VIP Client Group Training', 8, 33, 1),
(498, 'Muscle Build & Strength', 8, 34, 1),
(499, 'Fat Loss, Shape & Tone', 8, 34, 1),
(500, 'CrossFit', 8, 34, 1),
(501, 'Composition Assessment', 8, 35, 1),
(502, 'Health Assessment', 8, 35, 1),
(503, 'BioSignature Modulation', 8, 35, 1),
(504, 'Movement Pattern Analysis', 8, 35, 1),
(508, 'Initial Meet & Greet', 8, 36, 1),
(509, 'Follow-up Consultation', 8, 36, 1),
(510, 'Nutrition & Supplementation Coaching', 8, 36, 1),
(511, 'Training & Technique Coaching', 8, 36, 1),
(512, 'Lifestyle, Mindset & Elite Progression', 8, 36, 1),
(513, 'Goal Setting & Direction', 8, 36, 1),
(514, 'Training Periodisation & Programming', 8, 36, 1),
(515, 'Work Commitments', 9, 40, 1),
(516, 'Personal Commitments', 9, 41, 1),
(517, 'Annual Leave/Holdiays', 9, 42, 1),
(518, 'Illness/Unwell', 9, 43, 1),
(519, 'Injury/Injured', 9, 44, 1),
(520, 'Recovery/Training Break', 9, 45, 1);






CREATE TABLE IF NOT EXISTS `#__fitness_training_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL,
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `#__fitness_training_period` (`id`, `name`, `color`) VALUES
(1, 'Hypertrophy', '#E4C7EA'),
(2, 'Strength', '#FFC7E1'),
(3, 'Power', '#CCDDFF'),
(4, 'Crossfit', '#FFFDAB'),
(5, 'Circuit', '#E5E2A8'),
(6, 'Endurance', '#CDFCF7'),
(7, 'Agility & Speed', '#DCF2C4'),
(8, 'Rehabilitation', '#F4ABDC');


CREATE TABLE IF NOT EXISTS `#__fitness_mini_goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `primary_goal_id` int(11) unsigned NOT NULL,
  `mini_goal_category_id` int(11) NOT NULL,
  `training_period_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `details` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FOREIGN KEY (primary_goal_id) REFERENCES #__fitness_goals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_mini_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/* 
SET FOREIGN_KEY_CHECKS =0;# MySQL returned an empty result set (i.e. zero rows).
DROP TABLE master_fitness_goals1;# MySQL returned an empty result set (i.e. zero rows).
SET FOREIGN_KEY_CHECKS =1;# MySQL returned an empty result set (i.e. zero rows).
*/

CREATE TABLE IF NOT EXISTS `#__fitness_recipe_types` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__fitness_recipe_variations` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;


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
`assessed_by` INT(11)  NOT NULL ,
  `state` tinyint(1) NOT NULL DEFAULT '1',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


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
  KEY `recipe_id` (`recipe_id`),
  FOREIGN KEY (recipe_id) REFERENCES #__fitness_nutrition_recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_recipes_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_targets_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_macronutrients_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_supplements_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `sub_item_id` (`sub_item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
  FOREIGN KEY (sub_item_id) REFERENCES #__fitness_nutrition_plan_supplement_protocols(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_example_day_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `sub_item_id` (`sub_item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
  FOREIGN KEY (sub_item_id) REFERENCES #__fitness_nutrition_plan_example_day_meals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_exercise_library_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_exercise_library(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_program_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_pr_temp_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__programs_templates(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_focus` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`color` VARCHAR(20)  NOT NULL ,
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

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
  `state` tinyint(1) NOT NULL DEFAULT '1',
PRIMARY KEY (`id`),
KEY `client_id` (`client_id`),
KEY `trainer_id` (`trainer_id`),
KEY `mini_goal` (`mini_goal`),
FOREIGN KEY (client_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE,
FOREIGN KEY (trainer_id) REFERENCES #__users(id) ON DELETE CASCADE,
FOREIGN KEY (mini_goal) REFERENCES #__fitness_mini_goals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


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
  `step3_fats_custom` varchar(10) NOT NULL,
  `step4_calories` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
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
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
  FOREIGN KEY (meal_id) REFERENCES #__fitness_nutrition_plan_meals(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_meals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `meal_time` datetime NOT NULL,
  `water` float NOT NULL,
  `previous_water` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_supplement_protocols` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




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
  KEY `protocol_id` (`protocol_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
  FOREIGN KEY (protocol_id) REFERENCES #__fitness_nutrition_plan_supplement_protocols(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


/* nutrition diary  */

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
  `activity_level` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
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
KEY `nutrition_plan_id` (`nutrition_plan_id`),
FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_meals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `meal_time` datetime NOT NULL,
  `water` float NOT NULL,
  `previous_water` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_diary(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
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
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_diary(id) ON DELETE CASCADE,
  FOREIGN KEY (meal_id) REFERENCES #__fitness_nutrition_diary_meals(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_diary_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_diary(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
/* end nutrition diary  */


CREATE TABLE IF NOT EXISTS `#__fitness_goal_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_goals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_mini_goal_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_mini_goals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_shopping_list_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`parent_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `sub_item_id` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`business_profile_id` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_diary(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_user_groups` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`business_profile_id`  INT(11) unsigned NOT NULL ,
`group_id` INT(11) unsigned NOT NULL ,
`primary_trainer` INT(11)  NOT NULL ,
`other_trainers` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`),
KEY `group_id` (`group_id`),
KEY `business_profile_id` (`business_profile_id`),
FOREIGN KEY (group_id) REFERENCES #__usergroups(id) ON DELETE CASCADE,
FOREIGN KEY (business_profile_id) REFERENCES #__fitness_business_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_business_profiles` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`group_id` INT(11) UNSIGNED NOT NULL ,
`primary_administrator` INT(11)  NOT NULL ,
`secondary_administrator` INT(11)  NOT NULL ,
`terms_conditions` TEXT NOT NULL ,
`header_image` VARCHAR(255)  NOT NULL ,
`facebook_url` VARCHAR(255)  NOT NULL ,
`twitter_url` VARCHAR(255)  NOT NULL ,
`youtube_url` VARCHAR(255)  NOT NULL ,
`instagram_url` VARCHAR(255)  NOT NULL ,
`google_plus_url` VARCHAR(255)  NOT NULL ,
`linkedin_url` VARCHAR(255)  NOT NULL ,
`website_url` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`contact_number` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`),
KEY `group_id` (`group_id`),
UNIQUE(group_id),
FOREIGN KEY (group_id) REFERENCES #__usergroups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_recipes_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_nutrition_recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_appointments_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`),
  FOREIGN KEY (item_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_example_day_meals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(10) unsigned NOT NULL,
  `example_day_id` int(10) unsigned NOT NULL,
  `menu_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `meal_time` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  KEY `menu_id` (`menu_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id) REFERENCES #__fitness_nutrition_plan_menus(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_example_day_recipes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `original_recipe_id` int(255) unsigned NOT NULL,
  `meal_id` int(10) unsigned NOT NULL,
  `number_serves` int(3) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `original_recipe_id` (`original_recipe_id`),
  KEY `meal_id` (`meal_id`),
  FOREIGN KEY (original_recipe_id) REFERENCES #__fitness_nutrition_recipes(id) ON DELETE CASCADE,
  FOREIGN KEY (meal_id) REFERENCES #__fitness_nutrition_plan_example_day_meals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
  KEY `menu_id` (`menu_id`),
  KEY `recipe_id` (`recipe_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id) REFERENCES #__fitness_nutrition_plan_menus(id) ON DELETE CASCADE,
  FOREIGN KEY (recipe_id) REFERENCES #__fitness_nutrition_plan_recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_nutrition_plan_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `submit_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `assessed_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`),
  FOREIGN KEY (nutrition_plan_id) REFERENCES #__fitness_nutrition_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__fitness_database_categories` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



INSERT INTO `#__fitness_database_categories` (`id`, `name`, `state`) VALUES
(1, 'Additives and Food Ingredients', 1),
(2, 'Beverages', 1),
(3, 'Cereals & Cereal Products', 1),
(4, 'Condiments', 1),
(5, 'Dairy', 1),
(6, 'Dairy & Meat Alternatives', 1),
(7, 'Edible Fats & Oils ', 1),
(8, 'Eggs', 1),
(9, 'Fruit', 1),
(10, 'Legumes', 1),
(11, 'Meat & Meat Products', 1),
(12, 'Nuts and Seeds', 1),
(13, 'Seafood & Seafood Products', 1),
(14, 'Sugar, Confectionary and Sweet Spreads', 1),
(15, 'Vegetables', 1);

CREATE TABLE IF NOT EXISTS `#__fitness_settings_exercise_type` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__fitness_settings_force_type` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__fitness_settings_body_part` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__fitness_settings_target_muscles` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__fitness_settings_equipment` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__fitness_settings_difficulty` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__fitness_settings_mechanics_type` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


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
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


CREATE TABLE IF NOT EXISTS `#__fitness_exercise_library_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_exercise_library(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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
  `trainer_id` int(11) unsigned NOT NULL,
  `created` date NOT NULL,
  `access` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_profile_id` (`business_profile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fitness_pr_temp_clients` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `client_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_programs_templates(id) ON DELETE CASCADE,
  FOREIGN KEY (client_id) REFERENCES #__fitness_clients(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `video_id` int(11) NOT NULL,
  `comments` text,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__fitness_programs_templates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fitness_assessments_photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `client_comments` text NOT NULL,
  `trainer_comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  FOREIGN KEY (item_id) REFERENCES #__dc_mv_events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__fitness_training_periodalization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mini_goal_id` int(11) unsigned NOT NULL,
  `period_focus` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `minigoal_id` (`mini_goal_id`),
  FOREIGN KEY (mini_goal_id) REFERENCES #__fitness_mini_goals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



















/*








*/



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
-- Структура таблицы `myptsite_fitness_business_profiles`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_business_profiles` (
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
-- Структура таблицы `myptsite_fitness_clients`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_profile_id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `primary_trainer` int(11) NOT NULL,
  `other_trainers` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`user_id`),
  FOREIGN KEY (user_id) REFERENCES myptsite_users(id) ON DELETE CASCADE,
  FOREIGN KEY (business_profile_id) REFERENCES myptsite_fitness_business_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_categories`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_dc_mv_events`
--

CREATE TABLE IF NOT EXISTS `myptsite_dc_mv_events` (
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
-- Структура таблицы `myptsite_fitness_user_groups`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_user_groups` (
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
-- Структура таблицы `myptsite_fitness_settings_force_type`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_force_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_settings_exercise_type`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_exercise_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_appointments_favourites`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_appointments_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_training_period`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_training_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(20) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_settings_target_muscles`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_target_muscles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_settings_mechanics_type`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_mechanics_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_settings_equipment`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_equipment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_settings_difficulty`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_difficulty` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_settings_body_part`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_settings_body_part` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_session_type`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_session_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_session_focus`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_session_focus` (
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
-- Структура таблицы `myptsite_fitness_recipe_variations`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_recipe_variations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_recipe_types`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_recipe_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_programs_templates`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_programs_templates` (
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
-- Структура таблицы `myptsite_fitness_pr_temp_exercises`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_pr_temp_exercises` (
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
-- Структура таблицы `myptsite_fitness_pr_temp_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_pr_temp_comments` (
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
-- Структура таблицы `myptsite_fitness_pr_temp_clients`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_pr_temp_clients` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `client_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_program_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_program_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_recipes`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_recipes` (
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
-- Структура таблицы `myptsite_fitness_nutrition_recipes_meals`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_recipes_meals` (
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
-- Структура таблицы `myptsite_fitness_nutrition_recipes_favourites`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_recipes_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_nutrition_recipes_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_recipes_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_focus`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_focus` (
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
-- Структура таблицы `myptsite_fitness_nutrition_database`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_database` (
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
-- Структура таблицы `myptsite_fitness_notifications`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_notifications` (
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
-- Структура таблицы `myptsite_fitness_locations`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_goals`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_goals` (
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
-- Структура таблицы `myptsite_fitness_goal_focus`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_goal_focus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `goal_caregory_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_goal_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_goal_comments` (
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
-- Структура таблицы `myptsite_fitness_goal_categories`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_mini_goals`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_mini_goals` (
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
-- Структура таблицы `myptsite_fitness_mini_goal_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_mini_goal_comments` (
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
-- Структура таблицы `myptsite_fitness_mini_goal_categories`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_mini_goal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_profile_id` int(11) unsigned NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_exercise_library`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_exercise_library` (
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
-- Структура таблицы `myptsite_fitness_exercise_library_favourites`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_exercise_library_favourites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_exercise_library_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_exercise_library_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_targets_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_targets_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_targets`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_targets` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_supplement_protocols`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_supplement_protocols` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nutrition_plan_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nutrition_plan_id` (`nutrition_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_nutrition_plan_supplements_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_supplements_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_supplements`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_supplements` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_shopping_list_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_shopping_list_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_menus`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_menus` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_macronutrients_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_macronutrients_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_example_day_recipes`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_example_day_recipes` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_example_day_ingredients`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_example_day_ingredients` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_example_day_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_example_day_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_plan_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_plan_comments` (
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
-- Структура таблицы `myptsite_fitness_nutrition_diary`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_diary` (
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
-- Структура таблицы `myptsite_fitness_nutrition_diary_meal_entries`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_diary_meal_entries` (
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
-- Структура таблицы `myptsite_fitness_nutrition_diary_meals`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_diary_meals` (
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
-- Структура таблицы `myptsite_fitness_nutrition_diary_ingredients`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_diary_ingredients` (
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
-- Структура таблицы `myptsite_fitness_nutrition_diary_comments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_nutrition_diary_comments` (
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
-- Структура таблицы `myptsite_fitness_training_periodalization`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_training_periodalization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mini_goal_id` int(11) unsigned NOT NULL,
  `period_focus` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mini_goal_id` (`mini_goal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `myptsite_fitness_training_sessions`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_training_sessions` (
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
-- Структура таблицы `myptsite_fitness_events_exercises`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_events_exercises` (
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
-- Структура таблицы `myptsite_fitness_email_reminder`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_email_reminder` (
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
-- Структура таблицы `myptsite_fitness_database_categories`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_database_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------


--
-- Структура таблицы `myptsite_fitness_assessments_photos`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_assessments_photos` (
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
-- Структура таблицы `myptsite_fitness_assessments`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_assessments` (
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
-- Структура таблицы `myptsite_fitness_appointment_clients`
--

CREATE TABLE IF NOT EXISTS `myptsite_fitness_appointment_clients` (
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
-- Ограничения внешнего ключа таблицы `myptsite_fitness_appointments_favourites`
--
ALTER TABLE `myptsite_fitness_appointments_favourites`
  ADD CONSTRAINT `myptsite_fitness_appointments_favourites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_appointments_favourites_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `myptsite_fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_appointment_clients`
--
ALTER TABLE `myptsite_fitness_appointment_clients`
  ADD CONSTRAINT `myptsite_fitness_appointment_clients_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_appointment_clients_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `myptsite_fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_assessments`
--
ALTER TABLE `myptsite_fitness_assessments`
  ADD CONSTRAINT `myptsite_fitness_assessments_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_assessments_photos`
--
ALTER TABLE `myptsite_fitness_assessments_photos`
  ADD CONSTRAINT `myptsite_fitness_assessments_photos_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_email_reminder`
--
ALTER TABLE `myptsite_fitness_email_reminder`
  ADD CONSTRAINT `myptsite_fitness_email_reminder_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_events_exercises`
--
ALTER TABLE `myptsite_fitness_events_exercises`
  ADD CONSTRAINT `myptsite_fitness_events_exercises_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_exercise_library_comments`
--
ALTER TABLE `myptsite_fitness_exercise_library_comments`
  ADD CONSTRAINT `myptsite_fitness_exercise_library_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_exercise_library` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_exercise_library_favourites`
--
ALTER TABLE `myptsite_fitness_exercise_library_favourites`
  ADD CONSTRAINT `myptsite_fitness_exercise_library_favourites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_exercise_library` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_exercise_library_favourites_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `myptsite_fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_goals`
--
ALTER TABLE `myptsite_fitness_goals`
  ADD CONSTRAINT `myptsite_fitness_goals_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `myptsite_fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_goal_comments`
--
ALTER TABLE `myptsite_fitness_goal_comments`
  ADD CONSTRAINT `myptsite_fitness_goal_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_mini_goals`
--
ALTER TABLE `myptsite_fitness_mini_goals`
  ADD CONSTRAINT `myptsite_fitness_mini_goals_ibfk_1` FOREIGN KEY (`primary_goal_id`) REFERENCES `myptsite_fitness_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_mini_goal_comments`
--
ALTER TABLE `myptsite_fitness_mini_goal_comments`
  ADD CONSTRAINT `myptsite_fitness_mini_goal_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_mini_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_diary`
--
ALTER TABLE `myptsite_fitness_nutrition_diary`
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_diary_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_diary_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_diary` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_diary_ingredients`
--
ALTER TABLE `myptsite_fitness_nutrition_diary_ingredients`
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_ingredients_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `myptsite_fitness_nutrition_diary_meals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_ingredients_ibfk_3` FOREIGN KEY (`diary_id`) REFERENCES `myptsite_fitness_nutrition_diary` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_ingredients_ibfk_4` FOREIGN KEY (`meal_entry_id`) REFERENCES `myptsite_fitness_nutrition_diary_meal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_ingredients_ibfk_5` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_diary_meals`
--
ALTER TABLE `myptsite_fitness_nutrition_diary_meals`
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_meals_ibfk_1` FOREIGN KEY (`meal_entry_id`) REFERENCES `myptsite_fitness_nutrition_diary_meal_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_meals_ibfk_2` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_meals_ibfk_3` FOREIGN KEY (`diary_id`) REFERENCES `myptsite_fitness_nutrition_diary` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_diary_meal_entries`
--
ALTER TABLE `myptsite_fitness_nutrition_diary_meal_entries`
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_meal_entries_ibfk_1` FOREIGN KEY (`diary_id`) REFERENCES `myptsite_fitness_nutrition_diary` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_diary_meal_entries_ibfk_2` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan`
--
ALTER TABLE `myptsite_fitness_nutrition_plan`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `myptsite_fitness_clients` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `myptsite_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_ibfk_3` FOREIGN KEY (`mini_goal`) REFERENCES `myptsite_fitness_mini_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_example_day_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_example_day_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_plan_menus` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_example_day_ingredients`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_example_day_ingredients`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_ingredients_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_ingredients_ibfk_3` FOREIGN KEY (`recipe_id`) REFERENCES `myptsite_fitness_nutrition_plan_example_day_recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_ingredients_ibfk_4` FOREIGN KEY (`menu_id`) REFERENCES `myptsite_fitness_nutrition_plan_menus` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_example_day_recipes`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_example_day_recipes`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_recipes_ibfk_1` FOREIGN KEY (`original_recipe_id`) REFERENCES `myptsite_fitness_nutrition_recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_recipes_ibfk_2` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_example_day_recipes_ibfk_3` FOREIGN KEY (`menu_id`) REFERENCES `myptsite_fitness_nutrition_plan_menus` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_macronutrients_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_macronutrients_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_macronutrients_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_menus`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_menus`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_menus_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_supplements`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_supplements`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_supplements_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_supplements_ibfk_2` FOREIGN KEY (`protocol_id`) REFERENCES `myptsite_fitness_nutrition_plan_supplement_protocols` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_supplements_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_supplements_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_supplements_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_supplements_comments_ibfk_2` FOREIGN KEY (`sub_item_id`) REFERENCES `myptsite_fitness_nutrition_plan_supplement_protocols` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_supplement_protocols`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_supplement_protocols`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_supplement_protocols_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_targets`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_targets`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_targets_ibfk_1` FOREIGN KEY (`nutrition_plan_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_plan_targets_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_plan_targets_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_plan_targets_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_plan` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_recipes_comments`
--
ALTER TABLE `myptsite_fitness_nutrition_recipes_comments`
  ADD CONSTRAINT `myptsite_fitness_nutrition_recipes_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_recipes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_recipes_favourites`
--
ALTER TABLE `myptsite_fitness_nutrition_recipes_favourites`
  ADD CONSTRAINT `myptsite_fitness_nutrition_recipes_favourites_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_nutrition_recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_nutrition_recipes_favourites_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `myptsite_fitness_clients` (`user_id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_nutrition_recipes_meals`
--
ALTER TABLE `myptsite_fitness_nutrition_recipes_meals`
  ADD CONSTRAINT `myptsite_fitness_nutrition_recipes_meals_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `myptsite_fitness_nutrition_recipes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_program_comments`
--
ALTER TABLE `myptsite_fitness_program_comments`
  ADD CONSTRAINT `myptsite_fitness_program_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_dc_mv_events` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_pr_temp_clients`
--
ALTER TABLE `myptsite_fitness_pr_temp_clients`
  ADD CONSTRAINT `myptsite_fitness_pr_temp_clients_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_programs_templates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_pr_temp_comments`
--
ALTER TABLE `myptsite_fitness_pr_temp_comments`
  ADD CONSTRAINT `myptsite_fitness_pr_temp_comments_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_programs_templates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_pr_temp_exercises`
--
ALTER TABLE `myptsite_fitness_pr_temp_exercises`
  ADD CONSTRAINT `myptsite_fitness_pr_temp_exercises_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `myptsite_fitness_programs_templates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_session_focus`
--
ALTER TABLE `myptsite_fitness_session_focus`
  ADD CONSTRAINT `myptsite_fitness_session_focus_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `myptsite_fitness_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_session_focus_ibfk_2` FOREIGN KEY (`session_type_id`) REFERENCES `myptsite_fitness_session_type` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_session_type`
--
ALTER TABLE `myptsite_fitness_session_type`
  ADD CONSTRAINT `myptsite_fitness_session_type_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `myptsite_fitness_categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_training_periodalization`
--
ALTER TABLE `myptsite_fitness_training_periodalization`
  ADD CONSTRAINT `myptsite_fitness_training_periodalization_ibfk_1` FOREIGN KEY (`mini_goal_id`) REFERENCES `myptsite_fitness_mini_goals` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_training_sessions`
--
ALTER TABLE `myptsite_fitness_training_sessions`
  ADD CONSTRAINT `myptsite_fitness_training_sessions_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `myptsite_fitness_training_periodalization` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_training_sessions_ibfk_3` FOREIGN KEY (`session_type`) REFERENCES `myptsite_fitness_session_type` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_training_sessions_ibfk_4` FOREIGN KEY (`session_focus`) REFERENCES `myptsite_fitness_session_focus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `myptsite_fitness_training_sessions_ibfk_5` FOREIGN KEY (`appointment_type_id`) REFERENCES `myptsite_fitness_categories` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `myptsite_fitness_user_groups`
--
ALTER TABLE `myptsite_fitness_user_groups`
  ADD CONSTRAINT `myptsite_fitness_user_groups_ibfk_1` FOREIGN KEY (`business_profile_id`) REFERENCES `myptsite_fitness_business_profiles` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;