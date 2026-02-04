# emanuel_theme_builder
A prototype for a webpage-building-webbapp, using MySql, JQuery, JQuery UI, Treant, Raphael.

I have come the point where I wonder how CSS should be added.

create_db.sql is done with sudo mysqldump -u root -p -d -h localhost emanoel_theme_builder > create_db.sql

Instructions about how some tables should be prepared:

INSERT INTO `html_element` (`id`, `name`, `is_empty_tag`) VALUES
(21, 'body', 0),
(22, 'div', 0),
(23, 'p', 0),
(24, 'span', 0),
(25, 'img', 1);//for reference in the following:

INSERT INTO `html_element_properties` (`name`, `id`) VALUES
('alt', 2),
('src', 1);

INSERT INTO `html_element_templates` (`id`, `element_name`) VALUES
(1, 'img');



