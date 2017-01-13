CREATE TABLE `users` (
   `id` int(11) unsigned not null auto_increment,
   `name` varchar(255) not null,
   `password` varchar(255) not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
   `token` varchar(255) not null,
   `user_id` int(11) unsigned not null,
   `device_id` varchar(255) not null,
   PRIMARY KEY (`token`),
   FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;