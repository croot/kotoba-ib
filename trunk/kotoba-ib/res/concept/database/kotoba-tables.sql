delimiter ;
use kotoba2

drop table if exists boards;
CREATE TABLE boards (
	id int AUTO_INCREMENT NOT NULL,
	board_name varchar(16) NOT NULL,
	board_description varchar(50) NULL,
	bump_limit int NOT NULL DEFAULT 10,
	threads int NULL DEFAULT 0,
	max_threads int NOT NULL DEFAULT 10,
	PRIMARY KEY (id)
) engine=innodb;
CREATE UNIQUE INDEX IX_boards ON boards (board_name);

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
