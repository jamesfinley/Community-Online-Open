CREATE TABLE `activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `big_idea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `series_title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `short_description` tinytext NOT NULL,
  `long_description` tinytext NOT NULL,
  `description_x` int(11) DEFAULT NULL,
  `description_y` int(11) DEFAULT NULL,
  `videos_x` int(11) DEFAULT NULL,
  `videos_y` int(11) DEFAULT NULL,
  `background_color` varchar(20) NOT NULL,
  `border_color` varchar(20) DEFAULT NULL,
  `text_color` varchar(20) DEFAULT NULL,
  `background_image_path` varchar(127) NOT NULL,
  `begin_at` int(11) DEFAULT NULL,
  `end_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `reply_to` int(11) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`service_id`),
  KEY `reply_to` (`reply_to`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `foreign_id` int(11) NOT NULL,
  `foreign_type` varchar(24) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `giving` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `campus_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `frequency` varchar(30) DEFAULT NULL,
  `first_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `tags` text,
  `comments` text,
  `payment_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `threecms_id` int(11) NOT NULL,
  `campus_id` int(11) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `name` varchar(140) DEFAULT NULL,
  `description` text NOT NULL,
  `service_times` text NOT NULL,
  `slug` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `country` varchar(140) DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `has_childcare` int(1) NOT NULL DEFAULT '0',
  `is_public` int(1) NOT NULL DEFAULT '0',
  `allows_membership` int(1) DEFAULT '1',
  `requires_member_approval` int(1) NOT NULL DEFAULT '0',
  `ministry_logo` varchar(20) DEFAULT NULL,
  `hide_news` int(1) NOT NULL DEFAULT '0',
  `hide_events` int(1) NOT NULL DEFAULT '0',
  `hide_contributions` int(1) DEFAULT '0',
  `hide_discussion` int(1) NOT NULL DEFAULT '0',
  `hide_prayers` int(1) NOT NULL DEFAULT '0',
  `hide_qna` int(1) NOT NULL DEFAULT '0',
  `display_in_sidebar` int(11) NOT NULL DEFAULT '1',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `groups_users` (
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `approved` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `group_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `meetings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(140) NOT NULL,
  `date` int(11) NOT NULL,
  `location` text NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `description` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `meeting_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `short_title` varchar(140) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `subject` text,
  `message` text,
  `short_message` text,
  `digest_at` int(11) DEFAULT NULL,
  `email_sent_at` int(11) DEFAULT NULL,
  `is_unread` int(1) DEFAULT NULL,
  `delete_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `notification_type` varchar(20) DEFAULT NULL,
  `receive_emails` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `type` enum('page','link','giving') NOT NULL DEFAULT 'page',
  `url` varchar(120) DEFAULT NULL,
  `title` varchar(40) DEFAULT NULL,
  `slug` varchar(20) NOT NULL,
  `content` blob,
  `show_in_sidebar` int(1) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day_of_week` int(1) NOT NULL,
  `time` int(4) NOT NULL,
  `deactivated` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `start_at` int(11) NOT NULL,
  `end_at` int(11) NOT NULL,
  `big_idea` varchar(140) NOT NULL,
  `series_title` varchar(140) NOT NULL,
  `twitter_hash` varchar(140) NOT NULL,
  `videos` text NOT NULL,
  `content` text NOT NULL,
  `status` enum('published','draft') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` blob NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `shared_stream_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stream_post_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_post_id` (`stream_post_id`),
  KEY `group_id` (`group_id`),
  KEY `created_at` (`created_at`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `short_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(60) NOT NULL,
  `url` text NOT NULL,
  `tag` varchar(20) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `stored_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL,
  `file` varchar(140) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `chunk` varchar(20) DEFAULT NULL,
  `cache` varchar(100) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `stream_events_attending` (
  `stream_post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `stream_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_sticky` int(1) DEFAULT '0',
  `type` varchar(20) CHARACTER SET latin1 NOT NULL,
  `subject` varchar(140) CHARACTER SET latin1 DEFAULT NULL,
  `slug` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `bitly` varchar(120) COLLATE latin1_general_ci DEFAULT NULL,
  `content` blob NOT NULL,
  `event_date` int(11) NOT NULL,
  `image` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `stream_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stream_post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` blob NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stream_post_id` (`stream_post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `twitter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `access_token_secret` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `uid` varchar(12) NOT NULL,
  `email` varchar(140) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `reset_hash` varchar(255) NOT NULL,
  `avatar` text,
  `from_joomla` int(1) NOT NULL DEFAULT '0',
  `last_on` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `length` int(11) NOT NULL,
  `type` enum('music','teaching','communion','greeting','misc','gbtg','dismissal') NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
