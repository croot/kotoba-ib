delimiter |
/*alter table boards change column with_files with_attachments bit not null|
alter table boards add column enable_macro bit default null after with_attachments|
alter table boards add column enable_youtube bit default null after enable_macro|
alter table boards add column enable_captcha bit default null after enable_youtube|
alter table threads change column with_files with_attachments bit default null|
update languages set name = 'rus' where name = 'Russian'|
update languages set name = 'eng' where name = 'English'|
alter table languages change column name code char(3) not null|*/
/*create table images
(
	id int not null auto_increment,
	`hash` varchar(32) default null,
	`name` varchar(256) not null,
	widht int not null,
	height int not null,
	`size` int not null,
	thumbnail varchar(256) not null,
	thumbnail_w int not null,
	thumbnail_h int not null,
	primary key (id)
)
engine=InnoDB|
create table files
(
	id int not null auto_increment,
	`hash` varchar(32) default null,
	`name` varchar(256) not null,
	`size` int not null,
	thumbnail varchar(256) not null,
	thumbnail_w int not null,
	thumbnail_h int not null,
	primary key (id)
)
engine=InnoDB|
create table links
(
	id int not null auto_increment,
	url varchar(2048) not null,
	widht int not null,
	height int not null,
	`size` int not null,
	thumbnail varchar(2048) not null,
	thumbnail_w int not null,
	thumbnail_h int not null,
	primary key (id)
)
engine=InnoDB|
create table videos
(
	id int not null auto_increment,
	code varchar(256) not null,
	widht int not null,
	height int not null,
	primary key (id)
)
engine=InnoDB|
create table posts_images
(
	post int not null,
	image int not null,
	deleted bit not null,
	unique key (post, image),
	constraint foreign key (post) references posts (id) on delete restrict on update restrict,
	constraint foreign key (image) references images (id) on delete restrict on update restrict
)
engine=InnoDB|
create table posts_files
(
	post int not null,
	`file` int not null,
	deleted bit not null,
	unique key (post, `file`),
	constraint foreign key (post) references posts (id) on delete restrict on update restrict,
	constraint foreign key (`file`) references files (id) on delete restrict on update restrict
)
engine=InnoDB|
create table posts_links
(
	post int not null,
	`link` int not null,
	deleted bit not null,
	unique key (post, `link`),
	constraint foreign key (post) references posts (id) on delete restrict on update restrict,
	constraint foreign key (`link`) references links (id) on delete restrict on update restrict
)
engine=InnoDB|
create table posts_videos
(
	post int not null,
	video int not null,
	deleted bit not null,
	unique key (post, video),
	constraint foreign key (post) references posts (id) on delete restrict on update restrict,
	constraint foreign key (video) references videos (id) on delete restrict on update restrict
)
engine=InnoDB|
-- Преобразование не поддерживает ссылки нескольких сообщений на одно и то же вложение.
drop procedure if exists patch_for_revision_201_part_1|
drop procedure if exists patch_for_revision_201_part_2|
drop procedure if exists patch_for_revision_201_part_3|
drop procedure if exists patch_for_revision_201_part_4|
create procedure patch_for_revision_201_part_1 ()
begin
	declare post_id int;
	declare upload_id int;
	declare file_id int;
	declare done int default 0;
	declare `c` cursor for
		select pu.post, pu.upload
			from posts_uploads pu
			join uploads u on u.id = pu.upload and u.upload_type = 1 and u.is_image = 0;
	declare continue handler for not found set done = 1;
	insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
		select u.hash, u.`file` as name, u.size, u.thumbnail, u.thumbnail_w, u.thumbnail_h
		from posts_uploads pu
		right join uploads u on u.id = pu.upload
		where u.upload_type = 1 and u.is_image = 0 and pu.post is null;
	open `c`;
	repeat
	fetch `c` into post_id, upload_id;
	if(not done) then
		insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
			select hash, `file` as name, size, thumbnail, thumbnail_w, thumbnail_h
			from uploads where id = upload_id;
		set file_id = last_insert_id();
		insert into posts_files (post, `file`, deleted) values (post_id, file_id, 0);
	end if;
	until done end repeat;
	close `c`;
end|
create procedure patch_for_revision_201_part_2 ()
begin
	declare post_id int;
	declare upload_id int;
	declare image_id int;
	declare done int default 0;
	declare `c` cursor for
		select pu.post, pu.upload
			from posts_uploads pu
			join uploads u on u.id = pu.upload and u.upload_type = 1 and u.is_image = 1;
	declare continue handler for not found set done = 1;
	insert into images (hash, name, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
		select u.hash, u.`file` as name, u.image_w as widht, u.image_h as height, u.size, u.thumbnail, u.thumbnail_w, u.thumbnail_h
		from posts_uploads pu
		right join uploads u on u.id = pu.upload
		where u.upload_type = 1 and u.is_image = 1 and pu.post is null;
	open `c`;
	repeat
	fetch `c` into post_id, upload_id;
	if(not done) then
		insert into images (hash, name, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
			select hash, `file` as name, image_w as widht, image_h as height, size, thumbnail, thumbnail_w, thumbnail_h
			from uploads where id = upload_id;
		set image_id = last_insert_id();
		insert into posts_images (post, image, deleted) values (post_id, image_id, 0);
	end if;
	until done end repeat;
	close `c`;
end|
create procedure patch_for_revision_201_part_3 ()
begin
	declare post_id int;
	declare upload_id int;
	declare link_id int;
	declare done int default 0;
	declare `c` cursor for
		select pu.post, pu.upload
			from posts_uploads pu
			join uploads u on u.id = pu.upload and u.upload_type = 2;
	declare continue handler for not found set done = 1;
	insert into links (url, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
		select u.`file` as url, u.image_w as widht, u.image_h as height, u.size, u.thumbnail, u.thumbnail_w, u.thumbnail_h
		from posts_uploads pu
		right join uploads u on u.id = pu.upload
		where u.upload_type = 2 and pu.post is null;
	open `c`;
	repeat
	fetch `c` into post_id, upload_id;
	if(not done) then
		insert into links (url, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
			select `file` as url, image_w as widht, image_h as height, size, thumbnail, thumbnail_w, thumbnail_h
			from uploads where id = upload_id;
		set link_id = last_insert_id();
		insert into posts_links (post, `link`, deleted) values (post_id, link_id, 0);
	end if;
	until done end repeat;
	close `c`;
end|
create procedure patch_for_revision_201_part_4 ()
begin
	declare post_id int;
	declare upload_id int;
	declare video_id int;
	declare done int default 0;
	declare `c` cursor for
		select pu.post, pu.upload
		from posts_uploads pu
		join uploads u on u.id = pu.upload and u.upload_type = 3;
	declare continue handler for not found set done = 1;
	insert into videos (code, widht, height)
		select u.`file` as code, 220 as widht, 182 as height
		from posts_uploads pu
		right join uploads u on u.id = pu.upload
		where u.upload_type = 3 and pu.post is null;
	open `c`;
	repeat
	fetch `c` into post_id, upload_id;
	if(not done) then
		insert into videos (code, widht, height)
			select `file` as code, 220 as widht, 182 as height
			from uploads where id = upload_id;
		set video_id = last_insert_id();
		insert into posts_videos (post, video, deleted) values (post_id, video_id, 0);
	end if;
	until done end repeat;
	close `c`;
end|
call patch_for_revision_201_part_1 ()|
call patch_for_revision_201_part_2 ()|
call patch_for_revision_201_part_3 ()|
call patch_for_revision_201_part_4 ()|
drop procedure if exists patch_for_revision_201_part_1|
drop procedure if exists patch_for_revision_201_part_2|
drop procedure if exists patch_for_revision_201_part_3|
drop procedure if exists patch_for_revision_201_part_4|*/