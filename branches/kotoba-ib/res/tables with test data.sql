drop table if exists posts_uploads;
drop table if exists uploads;
drop table if exists acl;
drop table if exists posts;
drop table if exists board_upload_types;
drop table if exists upload_types;
drop table if exists upload_handlers;
drop table if exists user_groups;
drop table if exists users;
drop table if exists groups;
drop table if exists threads;
drop table if exists boards;
drop table if exists categories;
drop table if exists popdown_handlers;
drop table if exists stylesheets;
drop table if exists languages;

create table languages
(
	id int not null auto_increment,
	name varchar(50) not null,
	primary key (id)
)
engine=InnoDB;
insert into languages (name) values ('Russian');
insert into languages (name) values ('English');

create table categories
(
	id int not null auto_increment,
	name varchar(50) not null,
	primary key (id)
) 
engine=InnoDB;
insert into categories (name) values ('default');
insert into categories (name) values ('rule 34');

create table popdown_handlers
(
	id int not null auto_increment,
	name varchar(50) not null,
	primary key (`id`)
)
engine=InnoDB;
insert into popdown_handlers (name) values ('default_handler');

create table stylesheets
(
	id int not null auto_increment,
	name varchar(50) not null,
	primary key (id)
)
engine=InnoDB;
insert into stylesheets (name) values ('kotoba.css');

create table groups
(
	id int not null auto_increment,
	name varchar(50) not null,
	primary key (id)
)
engine=InnoDB;
insert into groups (name) values ('Guests');
insert into groups (name) values ('Users');
insert into groups (name) values ('Moderators');
insert into groups (name) values ('Administrators');

create table upload_handlers
(
	id int not null auto_increment,
	name varchar(50) not null,
	primary key (id)
)
engine = InnoDB;
insert into  upload_handlers (name) values ('default_handler');

create table boards
(
	id int not null auto_increment,
	name varchar(16) not null,
	title varchar(50) default null,
	bump_limit int not null,
	same_upload varchar(32) not null,
	popdown_handler int not null,
	category int not null,
  	primary key (id),
	unique key (name),
	constraint foreign key (category) references categories (id) on delete restrict on update restrict,
	constraint foreign key (popdown_handler) references popdown_handlers (id) on delete restrict on update restrict
)
engine=InnoDB;
insert into boards (name, bump_limit, same_upload, popdown_handler, category) values ('b', 30, 'no', 1, 1);
insert into boards (name, bump_limit, same_upload, popdown_handler, category) values ('azu',  30, 'once', 1, 1);
insert into boards (name, bump_limit, same_upload, popdown_handler, category) values ('azu34',  30, 'yes', 1, 2);

create table users
(
	id int not null auto_increment,
	`key` varchar(32) default null,
	posts_per_thread int default null,
	threads_per_page int default null,
	lines_per_post int default null,
	language int not null,
	stylesheet int not null,
	primary key (id),
	unique key (`key`),
	constraint foreign key (language) references languages (id) on delete restrict on update restrict,
	constraint foreign key (stylesheet) references stylesheets (id) on delete restrict on update restrict
)
engine=InnoDB;
insert into users (language, stylesheet) values (1, 1);

create table user_groups
(
	user int not null,
	`group` int not null,
	constraint foreign key (`group`) references groups (id),
	constraint foreign key (user) references users (id)
) 
engine=InnoDB;
insert into user_groups (user, `group`) values (1, 1);

create table upload_types
(
	id int not null auto_increment,
	extension varchar(10) not null,
	store_extension varchar(10) default null,
	upload_handler int not null,
	thumbnail_image varchar(256) default null,
	primary key (id),
	constraint foreign key (upload_handler) references upload_handlers (id) on delete restrict on update restrict
)
engine=InnoDB;

create table board_upload_types
(
	board int not null,
	upload_type int not null,
	constraint foreign key (board) references boards (id) on delete restrict on update restrict,
	constraint foreign key (upload_type) references upload_types (id) on delete restrict on update restrict
)
engine=InnoDB;

create table threads
(
	id int not null auto_increment,
	board int not null,
	ump_limit int,
	deleted bit,
	archived bit,
	sage bit,
	with_images bit,
	primary key (id),
	constraint foreign key (board) references boards (id) on delete restrict on update restrict
)
engine=InnoDB;

create table uploads
(
	id int not null auto_increment,
	board int not null,
	hash varchar(32) not null,
	is_image bit not null,
	file_name varchar(256) not null,
	file_w int default null,
	file_h int default null,
	thumbnail_name varchar(256) default null,
	thumbnail_w int default null,
	thumbnail_h int default null,
	primary key (id),
	constraint foreign key (board) references boards (id) on delete restrict on update restrict
)
engine=InnoDB;

create table posts
(
	id int not null auto_increment,
	board int not null,
	number int not null,
	user int not null,
	password varchar(128) default null,
	name varchar(128) default null,
	ip int default null,
	subject varchar(128) default null,
	date_time datetime not null,
	text text default null,
	sage bit default null,
	deleted bit default null,
	primary key (id),
	constraint foreign key (board) references boards (id) on delete restrict on update restrict,
	constraint foreign key (user) references users (id) on delete restrict on update restrict
)
engine=InnoDB;

create table acl
(
	`group` int default null,
	board int default null,
	thread int default null,
	post int default null,
	view bit not null,
	`change` bit not null,
	moderate bit not null,
	unique key (`group`, board, thread, post),
	constraint foreign key (`group`) references groups (id) on delete restrict on update restrict,
	constraint foreign key (board) references boards (id) on delete restrict on update restrict,
	constraint foreign key (thread) references threads (id) on delete restrict on update restrict,
	constraint foreign key (post) references posts (id) on delete restrict on update restrict
)
engine=InnoDB;
insert into acl (`group`, view, `change`, moderate) values (1, 1, 0, 0);
insert into acl (`group`, view, `change`, moderate) values (2, 1, 1, 0);
insert into acl (`group`, view, `change`, moderate) values (3, 1, 1, 1);
insert into acl (`group`, view, `change`, moderate) values (4, 1, 1, 1);

create table posts_uploads
(
	thread int not null,
	post int not null,
	upload int not null,
	constraint foreign key (thread) references threads (id) on delete restrict on update restrict,
	constraint foreign key (post) references posts (id) on delete restrict on update restrict,
	constraint foreign key (upload) references uploads (id) on delete restrict on update restrict
)
engine=InnoDB;