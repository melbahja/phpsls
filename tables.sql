--
-- Table structure for table `sls_forgot_password`
--

CREATE TABLE IF NOT EXISTS `sls_forgot_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sr_key` varchar(255) NOT NULL,
  `ex_time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `sls_users`
--

CREATE TABLE IF NOT EXISTS `sls_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_verify` enum('0','1') NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `password` varchar(255) NOT NULL,
  `sr_key` varchar(250) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;
