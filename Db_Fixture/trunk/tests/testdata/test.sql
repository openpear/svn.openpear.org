DROP TABLE IF EXISTS `test1`;
DROP TABLE IF EXISTS `test2`;

CREATE TABLE `test1` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `test_id` int(11) collate utf8_unicode_ci NOT NULL,
  `test1` varchar(100) collate utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `test2` (
  `test_id` int(11) unsigned NOT NULL,
  `test2` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`test_id`,`test2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
