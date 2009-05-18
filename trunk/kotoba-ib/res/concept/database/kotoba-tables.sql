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
-- CREATE UNIQUE INDEX IX_boards ON boards (board_name);
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
	extension varchar(10) not null,
	store_extension varchar(10) null,
	handler varchar(64) not null default 'internal',
	thumbnail_image varchar(256) not null default 'unknown.png',
	primary key(id)
) engine=innodb;

/*
upload_types: supported binary files for upload and how to handle them
id - upload type identifier
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
	post_id int not null,
	hash varchar(32) not null,
	file_name varchar(256) not null,
	file_x int,
	file_y, int,
	thumbnail varchar(256) not null,
	thumbnail_x int,
	thumbnail_y int
) engine=innodb;


drop table if exists threads;
CREATE TABLE threads (
	id int auto_increment NOT NULL,
	board_id int NOT NULL,
	open_post_num int NULL,
	messages int NULL,
	with_images int NULL,
	last_post datetime NULL,
	deleted tinyint NULL DEFAULT 0,
	bump_limit int NOT NULL DEFAULT 10,
	sage tinyint NOT NULL,
	archive tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	INDEX IX_boards (board_id),
	FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE RESTRICT
) engine=innodb;

CREATE INDEX IX_threads using btree ON threads
(
	last_post DESC
);

drop table if exists posts;
CREATE TABLE posts(
	id int auto_increment NOT NULL,
	board_id int NOT NULL,
	thread_id int NULL,
	post_number int NOT NULL,
	text text NULL,
	date_time datetime NULL,
	image varchar(64) NULL,
	sage tinyint NULL,
	deleted tinyint NULL DEFAULT 0,
	INDEX IX_thread_id (thread_id),
	INDEX IX_board_id (board_id),
	PRIMARY KEY using btree (id ASC),
	FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE RESTRICT,
	FOREIGN KEY (thread_id) REFERENCES threads(id) ON DELETE RESTRICT
) engine=innodb;

CREATE UNIQUE INDEX IX_posts using btree ON posts 
(
	post_number ASC,
	board_id ASC
);

CREATE INDEX IX_posts_1 using btree ON posts 
(
	date_time ASC
);

/*
ALTER TABLE threads ADD  CONSTRAINT FK_threads_threads FOREIGN KEY(board_id)
REFERENCES boards (id);
ALTER TABLE posts  ADD  CONSTRAINT FK_posts_boards FOREIGN KEY(board_id)
REFERENCES boards (id);
ALTER TABLE posts ADD  CONSTRAINT FK_posts_threads FOREIGN KEY(thread_id)
REFERENCES threads (id);
*/
