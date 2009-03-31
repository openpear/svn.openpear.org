DROP TABLE IF EXISTS `test1`;

CREATE TABLE `test1` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `test_id` int(11) collate utf8_unicode_ci NOT NULL,
  `test1` varchar(100) collate utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `test1` (`test_id`, `test1`, `created_at`) VALUE(1, 'test1', '2009-03-29 18:00:00');
INSERT INTO `test1` (`test_id`, `test1`, `created_at`) VALUE(2, 'test2', '2009-03-29 18:00:00');

