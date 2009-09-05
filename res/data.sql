delimiter |
insert into languages (`id`, `name`) values (1, 'Russian')|
insert into languages (`name`) values ('English')|
insert into categories (`name`) values ('default')|
-- insert into categories (`name`) values ('rule 34')|
insert into popdown_handlers (`name`) values ('default_handler')|
insert into stylesheets (`id`, `name`) values (1, 'kotoba.css')|
insert into groups (`id`, `name`) values (1, 'Guests')|
insert into groups (`id`, `name`) values (2, 'Users')|
insert into groups (`id`, `name`) values (3, 'Moderators')|
insert into groups (`id`, `name`) values (4, 'Administrators')|
insert into upload_handlers (`name`) values ('default_handler')|
-- insert into boards (`name`, `bump_limit`, `same_upload`, `popdown_handler`, `category`) values ('b', 30, 'no', 1, 1)|
-- insert into boards (`name`, `bump_limit`, `same_upload`, `popdown_handler`, `category`) values ('azu',  30, 'once', 1, 1)|
-- insert into boards (`name`, `bump_limit`, `same_upload`, `popdown_handler`, `category`) values ('azu34',  30, 'yes', 1, 2)|

-- Замечание: Указанные здесь язык и стиль не имеют значения для гостя, они настраиваются в config.php
-- и никогда не берутся из базы данных.
insert into users (`id`, `language`, `stylesheet`) values (1, 1, 1)|

insert into user_groups (`user`, `group`) values (1, 1)|
/*
 * При добавлении новых групп используются права как для гостя. Если вы вносите
 * изменения в права для гостя по умолчанию, то возможно вы захотите изменить и
 * права по умолчанию для вновь создаваемых групп. Для этого отредактируйте код
 * хранимой процедуры sp_group_add.
 */
insert into acl (`group`, `view`, `change`, `moderate`) values (1, 1, 0, 0)|
insert into acl (`group`, `view`, `change`, `moderate`) values (2, 1, 1, 0)|
insert into acl (`group`, `view`, `change`, `moderate`) values (3, 1, 1, 1)|
insert into acl (`group`, `view`, `change`, `moderate`) values (4, 1, 1, 1)|