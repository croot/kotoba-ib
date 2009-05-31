/* DO NOT USE ME*/
delimiter ;
select "do not use me!";

use kotoba2

drop table if exists boards;
CREATE TABLE boards (
	id int AUTO_INCREMENT NOT NULL,
	board_name varchar(16) NOT NULL,
	board_description varchar(50) NULL,
	board_title varchar(50) NOT NULL,
	threads int NULL DEFAULT 0,
	bump_limit int NOT NULL DEFAULT 10,
	rubber_board tinyint NOT NULL DEFAULT 0,
	visible_threads int NOT NULL DEFAULT 10,
	same_upload varchar(32) not null default 'yes',
	PRIMARY KEY (id),
	unique index IX_boards (board_name)
) engine=innodb;
/*
boards: boards in your kotoba
id - board identifier
board_name - board name ie 'a', 'b'
board_description - board description
board_title - board title in board preview
bump_limit - default bump limit in thread
threads - not deleted, not archived threads (for statistics)
rubber_board - boolean value, if 1, threads doesn't archived automatically
visible_threads - how many threads show in board preview. should be ignored if rubber_board is set
same_upload - allow upload same binary files. values: 'yes' - allow, 'no' - disallow and 'once' - use once uploaded file for all instances
*/

drop table if exists upload_types;
create table upload_types (
	id int auto_increment not null,
	image tinyint not null,
	extension varchar(10) not null,
	store_extension varchar(10) null,
	handler varchar(64) not null default 'internal',
	thumbnail_image varchar(256) null default '/img/unknown.png',
	primary key(id),
	unique index(extension)
) engine=innodb;

/*
upload_types: supported binary files for upload and how to handle them
id - upload type identifier
image - is upload image file?
extension - binary file extension
store_extension - binary file should be stored with this extension ('jpeg' as 'jpg')
handler - how to handle files. values:
	'store' - store file, thumbnail use from thumbnail_image field,
	'internal' - use kotoba internal thumbnail routine (make sure file format supported!),
	'internal_png' use kotoba internal thumbnail routine which makes png (also check availability!).
thumbnail_image - (default) thumbnail image for this binary file
*/

drop table if exists board_upload_types;
create table board_upload_types(
	board_id int not null,
	upload_id int not null,
	unique index IX_but (board_id, upload_id),
	foreign key (board_id) references boards(id) on delete cascade,
	foreign key (upload_id) references upload_types(id) on delete cascade
) engine=innodb;
/*
board_upload_types: reduction table. stores information about board binary upload supported.
board_id - board id
upload_id - upload type id
board | upload_type
+---+---+
| 1 | 1 |
+---+---+
| 1 | 2 |
+---+---+
etc...
*/

drop table if exists uploads;
CREATE TABLE uploads (
	id int auto_increment not null,
	board_id int not null,
--	post_id int not null,
	hash varchar(32) not null,
	is_image tinyint not null,
	file_name varchar(256) not null,
	file_w int,
	file_h int,
	thumbnail varchar(256) not null,
	thumbnail_w int,
	thumbnail_h int,
	primary key(id),
	index(hash)
) engine=innodb;

/* uploads: binary uploads information
id - upload identifier
board_id - board identifier where binary was uploaded
hash - md5 (or sha-1) hash of upload
is_image - is dimensions of original upload applicable?
file_name - name of upload (href)
file_w and file_h - widht and height, if upload is image
thumbnail - thumbnail filename
thumbnail_w and thumbnail_h - thumbnail width and height
*/

drop table if exists threads;
CREATE TABLE threads (
	id int auto_increment NOT NULL,
	board_id int NOT NULL,
	original_post_num int NULL,
	messages int NULL,
	with_images int NULL,
	last_post datetime NULL,
	deleted tinyint NULL DEFAULT 0,
	bump_limit int NOT NULL DEFAULT 10,
	sage tinyint NOT NULL,
	archive tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	unique INDEX IX_boards (board_id, original_post_num),
	index IX_threads (last_post),
	FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE RESTRICT
) engine=innodb;
/*
threads: information about threads
id - thread identifier
board_id - thread belongs to board
original_post_num - number of post (not id), which open thread
messages - count of not deleted messages
with_images - count of messages with images
last_post - date time of last post, used for sorting (sage post should not update this field)
delted - this thread is deleted
bump_limit - max number of posts when last_post should be updated
sage - last_post should be never updated if 1
archive - thread prepared to be archived
*/

drop table if exists posts;
CREATE TABLE posts(
	id int auto_increment NOT NULL,
	board_id int NOT NULL,
	thread_id int NULL,
	post_number int NOT NULL,
	name varchar(128) null,
	email varchar(128) null,
	subject varchar(128) null,
	password varchar(128) null,
	session_id varchar(128) null,
	ip int not null,
	text text NULL,
	date_time datetime NULL,
	sage tinyint NULL,
	deleted tinyint NULL DEFAULT 0,
	INDEX IX_thread_id (thread_id),
	INDEX IX_board_id (board_id),
	index IX_posts_date_time (date_time),
	unique index IX_posts (post_number, board_id),
	PRIMARY KEY using btree (id ASC),
	FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE RESTRICT,
	FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE RESTRICT
) engine=innodb;

/*
posts: information about posts
id - post identifier
board_id - post is on this board
thread_id - post is on this thread
post_number - is post number and it unique in board
name - optional name for post (name-fags not welcome here)
email - optional email
subject - optional subject of post
password - password for deleting post
session_id - session id of user which posted message (may useful for deleting post by same user
	even if password not set)
ip - ip address of poster (integer, not 'a.b.c.d')
text - post text
date_time - date and time of post
sage - sage flag of post
deleted - this post is deleted if 1
*/

drop table if exists posts_uploads;
CREATE TABLE posts_uploads(
	id int not null auto_increment,
	thread_id int not null,
	post_id int not null,
	upload_id int not null,
	primary key(id),
	unique index IX_post_uploads (post_id, upload_id),
	foreign key (post_id) references posts(id) on delete cascade,
	foreign key (upload_id) references uploads(id) on delete cascade
) engine=innodb;

/*
posts_uploads: reduction table for uploads in posts
id - reduction identifier
thread_id - uploads in this thread
post_id - identifier of post
upload_id - identifier of upload
*/

drop table if exists banned_networks;
CREATE TABLE banned_networks(
	id int auto_increment not null,
	network_address int not null,
	network_broadcast int not null,
	reason text,
	till datetime,
	primary key(id),
	unique index(network_address, network_broadcast)
) engine=innodb;
/*
banned_networks: table for current banned ip's and networks
id - banned network identifier
network_address - start of network (in integer, not a.b.c.d)
network_broadcast - end of network (in integer too, see ip2long php manual)
	sample: address: 10.0.0.0, brodcast: 10.0.0.255; means whole network 10.0.0.0/24 banned
reason - why this network banned
till - date time when ban should be gone
*/

drop table if exists users;
CREATE TABLE users(
	id int auto_increment not null,
	auth_key  varchar(32) not null,
	role int not null default 0,
	preview_posts int,
	preview_threads int,
	preview_pages int,
	primary key(id),
	unique index(auth_key)
) engine=innodb;
/*
users: users on kotoba
id - user identifier
auth_key - user authentication key (password)
role - user role. 0 is usual user
preview_posts - how many posts showd in preview mode
preview_threads - how many threads on page in preview mode
preview_pages - how many pages in preview mode
*/
