use kotoba2;
-- =============================================
-- Author:		innomines
-- Create date: 21.05.2009
-- Description:	create new user
-- =============================================
delimiter |
drop procedure if exists sp_add_user|
create PROCEDURE sp_add_user(
	-- auth key
	authkey varchar(32),
	-- user defined qty of posts
	posts int,
	-- user defined qty of threads
	threads int,
	-- user defined qty of pages
	pages int,
	-- default qty of posts
	defaultposts int,
	-- default qty of threads
	defaultthreads int,
	-- default of pages
	defaultpages int
)
BEGIN
	-- local calculated posts, threads and pages
	-- declare postsqty threadsqty, pagesqty int;
	insert into users (auth_key, preview_posts, preview_threads, preview_pages)
	values (authkey, ifnull(posts, defaultposts), ifnull(threads, defaultthreads),
		ifnull(pages, defaultpages));

	select last_insert_id() as userid;
END|

-- =============================================
-- Author:		innomines
-- Create date: 21.05.2009
-- Description:	change user settings
-- =============================================
delimiter |
drop procedure if exists sp_change_user|
create PROCEDURE sp_change_user(
	userid int,
	-- user defined qty of posts
	userposts int,
	-- user defined qty of threads
	userthreads int,
	-- user defined qty of pages
	userpages int
)
begin
	update users set
		preview_posts = ifnull(userposts, preview_posts),
		preview_threads = ifnull(userthreads, preview_threads),
		preview_pages = ifnull(userpages, preview_pages)
	where id = userid;
end|

-- =============================================
-- Author:		innomines
-- Create date: 21.05.2009
-- Description:	set role for user
-- =============================================
delimiter |
drop procedure if exists sp_set_user_role|
create PROCEDURE sp_set_user_role(
	-- user identifier
	userid int,
	-- role integer
	userrole int
)
begin
	update users set role = userrole where id = userid;
end|

-- =============================================
-- Author:		innomines
-- Create date: 28.05.2009
-- Description:	get supported file-types with settings
-- =============================================
delimiter |
drop procedure if exists sp_get_filetypes_ex|
create PROCEDURE sp_get_filetypes_ex()
begin
	select id, image, extension, store_extension, handler, thumbnail_image
	from upload_types
	order by extension;
end|
-- =============================================
-- Author:		innomines
-- Create date: 28.05.2009
-- Description:	get supported file-types without settings
-- =============================================
delimiter |
drop procedure if exists sp_get_filetypes|
create PROCEDURE sp_get_filetypes()
begin
	select id, extension, handler, thumbnail_image
	from upload_types
	order by extension;
end|
-- =============================================
-- Author:		innomines
-- Create date: 03.06.2009
-- Description:	get file-type settings if supported
-- =============================================
delimiter |
drop procedure if exists sp_get_filetype|
create PROCEDURE sp_get_filetype(
	typeextension varchar(10)
)
begin
	select image, extension, store_extension, handler, thumbnail_image
	from upload_types
	where extension = typeextension;
end|
-- =============================================
-- Author:		innomines
-- Create date: 28.05.2009
-- Description:	change supported file-type
-- =============================================
delimiter |
drop procedure if exists sp_change_filetype|
create PROCEDURE sp_change_filetype(
	-- file type identifier
	filetypeid int,
	-- is this file type image?
	isimage tinyint,
	-- file extension
	fileext varchar(10),
	-- store file extension
	storefileext varchar(10),
	-- handler method
	exthandler tinyint,
	-- default thumbnail
	extthumbnail varchar(256)
)
begin
	declare localhandler varchar(64);
	select ifnull(storefileext, fileext) into storefileext;
	select case exthandler 
		when 1 then 'store'
		when 2 then 'internal'
		when 3 then 'internal_png' end
	into localhandler;

	update upload_types set image = isimage, extension = fileext, store_extension = storefileext,
	handler = localhandler, thumbnail_image = extthumbnail
	where id = filetypeid;
end|
-- =============================================
-- Author:		innomines
-- Create date: 21.05.2009
-- Description:	add new supported file-type
-- =============================================
delimiter |
drop procedure if exists sp_add_filetype|
create PROCEDURE sp_add_filetype(
	-- is this file type image?
	isimage tinyint,
	-- file extension
	fileext varchar(10),
	-- store file extension
	storefileext varchar(10),
	-- handler method
	exthandler tinyint,
	-- default thumbnail
	extthumbnail varchar(256)
)
begin
	declare localhandler varchar(64);
	select ifnull(storefileext, fileext) into storefileext;
	select case exthandler 
		when 1 then 'store'
		when 2 then 'internal'
		when 3 then 'internal_png' end
	into localhandler;

	insert into upload_types (image, extension, store_extension, handler, thumbnail_image)
	values (isimage, fileext, storefileext, localhandler, extthumbnail);
end|

-- add default filetypes
call sp_add_filetype(1, 'jpg', 'jpg', 2, null)|
call sp_add_filetype(1, 'jpeg', 'jpg', 2, null)|
call sp_add_filetype(1, 'gif', 'gif', 2, null)|
call sp_add_filetype(1, 'png', 'png', 2, null)|
call sp_add_filetype(1, 'svg', 'png', 3, null)|
-- =============================================
-- Author:		innomines
-- Create date: 29.05.2009
-- Description:	get supported file-types (extension field) of board
-- =============================================
delimiter |
drop procedure if exists sp_get_board_filetypes|
create PROCEDURE sp_get_board_filetypes(
	boardid int
)
begin
	select t.extension from board_upload_types bt join upload_types t
	on (bt.upload_id = t.id)
	where bt.board_id = boardid
	group by t.extension
	order by t.extension;
end|
-- =============================================
-- Author:		innomines
-- Create date: 21.05.2009
-- Description:	add supported file-type to board
-- =============================================
delimiter |
drop procedure if exists sp_add_board_filetype|
create PROCEDURE sp_add_board_filetype(

	boardid int,
	filetypeid int
)
begin
	insert into board_upload_types (board_id, upload_id)
	values (boardid, filetypeid);
end|

-- =============================================
-- Author:		innomines
-- Create date: 29.05.2009
-- Description:	remove all supported filetypes from board
-- =============================================
delimiter |
drop procedure if exists sp_delete_board_filetypes|
create PROCEDURE sp_delete_board_filetypes(
	boardid int
)
begin
	delete from board_upload_types
	where board_id = boardid;
end|
-- =============================================
-- Author:		innomines
-- Create date: 29.05.2009
-- Description:	delete supported file-type from board
-- =============================================
delimiter |
drop procedure if exists sp_delete_board_filetype|
create PROCEDURE sp_delete_board_filetype(
	boardid int,
	filetypeid int
)
begin
	delete from board_upload_types 
	where board_id = boardid and filetype_id = filetypeid;
end|
-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	get posts count in thread
-- =============================================
delimiter |
drop function if exists  get_posts_count|
CREATE FUNCTION get_posts_count
(
	-- thread id
	threadid int
)
RETURNS int
not deterministic
BEGIN
	DECLARE count_post int;

	SELECT count(id) into count_post from posts where thread_id = threadid
	and deleted <> 1;

	RETURN count_post;
END|
-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	delete post. if post opened new thread, delete whole thread
-- =============================================
delimiter |
drop procedure if exists sp_delete_post|
create PROCEDURE sp_delete_post(
	-- post number field
	postnum int,
	-- board id
	boardid int
)
BEGIN
	-- post id	
	declare postid int;
	-- post in thread
	declare threadid int;
	-- thread id if we're deleting opening post
	declare wholethreadid int;
	-- posts count in thread
	declare postcount int;
	-- image name
	declare image varchar(64);
	-- images count in thread
	declare imagescount int;

	-- find post id
	select id into postid from posts
	where post_number = postnum and board_id = boardid;

	-- find thread id
	select thread_id into threadid from posts
	where id = postid;

	-- get image name
	select image into image from posts
	where id = postid;


	-- find if post was opening for thread...
	select id into wholethreadid from threads
	where open_post_num = postnum;
	-- ... if so delete thread
	if wholethreadid is not null then
		call sp_delete_thread(threadid, boardid);
	end if;
	-- mark post as deleted
	update posts set deleted = 1
	where id = postid and board_id = boardid;

	-- get posts and images count
	select  get_posts_count(threadid) into postcount;
	select  get_images_count(threadid) into imagescount;
	-- update counters in thread
	update threads set messages = postcount, with_images = imagescount
	where id = threadid;
END|

/*
-- =============================================
-- Author:		innomines
-- Create date: <Create Date, ,>
-- Description:	TODO unused function??
-- =============================================
delimiter |
drop function if exists get_next_post_on_thread|
CREATE FUNCTION get_next_post_on_thread
(
	-- Add the parameters for the function here
	thread_id int,
	board_id int
)
RETURNS int
BEGIN
	-- Declare the return variable here
	DECLARE post_num int;

	-- Add the T-SQL statements to compute the return value here
	SELECT  p.post_number into post_num from posts p, threads t, boards b
	where p.thread_id = @thread_id and t.board_id = @board_id;
	if post_num is null then
		return -1;
	end;

	set post_num = post_num + 1;

	-- Return the result of the function
	RETURN post_num;

END|
*/

-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	get next post number on board (not id!)
-- =============================================
delimiter |
drop function if exists  get_next_post_on_board|
CREATE FUNCTION get_next_post_on_board
(
	-- board id
	boardid int
)
RETURNS int
not deterministic
BEGIN
	DECLARE postnumber int;

	SELECT max(p.post_number) into postnumber from posts p
		where board_id = boardid;
	if postnumber is null then
		set postnumber = 0;
	end if;
	
	set postnumber = postnumber + 1;

	RETURN postnumber;
END|


-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	count quantity of posts with images in thread
-- =============================================
delimiter |
drop function if exists get_images_count|
CREATE FUNCTION get_images_count
(
	-- thread id
	threadid int
)
RETURNS int
not deterministic
BEGIN
	DECLARE posts_with_images int;

	SELECT count(id) into posts_with_images from posts 
		where thread_id = threadid and image is not null and deleted <> 1;
	
	if posts_with_images is null then
		set posts_with_images = 0;
	end if;

	RETURN posts_with_images;
END|


-- =============================================
-- Author:		innomines
-- Create date: 06.05.2009
-- Description:	preview thread on page view (hide middle of thread)
-- =============================================
delimiter |
drop procedure if exists  sp_thread_preview|
CREATE PROCEDURE sp_thread_preview(
	-- thread on board id
	boardid int,
	-- open post id
	postid int,
	-- skip messages except last N
	showlast int
)
BEGIN
	declare post int;
	declare postn int;
	declare threadid int;
	declare postscount int;
	declare count int;
	declare postskip int;
	declare imagescount int;
	declare imageskip int;
	declare hidedcount int;
	declare done int default 0;
	declare cur1 cursor for select id, post_number from posts where thread_id = threadid and board_id = boardid and deleted <> 1 order by post_number asc, date_time asc;
	declare continue handler for not found set done = 1;

	set count = 0;
	set postskip = 0;
	set imageskip = 0;

	select get_thread_id(boardid, postid) into threadid;

	select count(id) into postscount from posts
		where thread_id = threadid
			and deleted <> 1;


	set hidedcount = postscount - showlast;

	drop temporary table if exists Tthread_preview;
	create temporary table Tthread_preview (
		postnumber int,
		skipped int,
		uploads int
	);

	open cur1;
	repeat
	fetch cur1 into post, postn;
	if not done then
		-- fuck you mysql for this extra if. fuck you
		if count = 0 or count >= hidedcount then
			insert into Tthread_preview (postnumber) values (postn);
		else
			set postskip = postskip + 1;
			select count(upload_id) into imagescount from posts_uploads
			where post_id = post;
			set imageskip = imageskip + imagescount;
		end if;

		set count = count + 1;
	end if;
	until done end repeat;

	close cur1;
	update Tthread_preview set skipped = postskip, uploads = imageskip;

select postnumber, skipped, uploads from Tthread_preview;
drop temporary table if exists Tthread_preview;
END|


-- =============================================
-- Author:		innomines
-- Create date: 21.05.2009
-- Description:	create new board
-- =============================================
delimiter |
drop procedure if exists  sp_create_board|
CREATE PROCEDURE sp_create_board(
	-- board name ie b, a
	boardname varchar(16),
	-- board description like random, anime
	boarddescription varchar(50),
	boardtitle varchar(50),
	-- default bump limit for board
	bumplimit int,
	rubberboard tinyint,
	-- maximum visible threads on board
	visiblethreads int,
	sameupload tinyint
)
BEGIN
	declare localsameupload varchar(32);

	select case sameupload
		when 0 then 'yes'
		when 1 then 'once'
		when 2 then 'no' end
	into localsameupload;

	insert into boards (board_name, board_description, board_title,
		bump_limit, rubber_board, visible_threads, same_upload) 
	values (boardname, boarddescription, boardtitle,
		bumplimit, rubberboard, visiblethreads, localsameupload);
END|


-- =============================================
-- Author:		innomines
-- Create date: 28.05.2009
-- Description:	update board
-- =============================================
delimiter |
drop procedure if exists  sp_save_board|
CREATE PROCEDURE sp_save_board(
	-- board identifier
	boardid int,
	-- board name ie b, a
	boardname varchar(16),
	-- board description like random, anime
	boarddescription varchar(50),
	boardtitle varchar(50),
	-- default bump limit for board
	bumplimit int,
	rubberboard tinyint,
	-- maximum visible threads on board
	visiblethreads int,
	sameupload tinyint
)
BEGIN
	declare localsameupload varchar(32);

	select case sameupload
		when 0 then 'yes'
		when 1 then 'once'
		when 2 then 'no' end
	into localsameupload;

	update boards set board_name = boardname, board_description = boarddescription,
		board_title = boardtitle, bump_limit = bumplimit, rubber_board = rubberboard,
		visible_threads = visiblethreads, same_upload = localsameupload 
	where id = boardid;
END|
-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	mark thread as deleted
-- =============================================
delimiter |
drop procedure if exists  sp_delete_thread|
CREATE PROCEDURE sp_delete_thread (
	-- thread id
	threadid int,
	-- board id
	boardid int
)
BEGIN
	-- mark thread as deleted
	update threads set deleted = 1
	where id = threadid;

	-- update board threads count
	update boards set threads = get_threads_count(boardid)
	where id = boardid;
END|


-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	mark too old threads for drowning
-- =============================================
delimiter |
drop procedure if exists  sp_mark_archived|
CREATE PROCEDURE sp_mark_archived (
	-- board id
	boardid int
)
BEGIN
	-- cursor end flag
	declare done int default 0;
	-- thread counter
	declare threadcount int;
	-- thread limit from boards
	declare threadlimit int default 0;
	declare rubber int default 0;
	-- thread id
	declare threadid int;
	declare cur1 cursor for select id from threads
		where deleted <> 1 and archive <> 1 and board_id = boardid
		order by last_post desc;
	declare continue handler for not found set done = 1;

	select rubber_board into rubber from boards
	where id = boardid;

	-- todo: maximum integer from documentation
	if rubber = 1 then
		set threadlimit = 2147483647;
	else
		select visible_threads into threadlimit from boards 
		where id = boardid;
	end if;

	set threadcount = 0;

	open cur1;

	repeat
		fetch next from cur1 into threadid;
		-- just in case. cause mysql is bloody sucker
		if not done then
		if threadcount >= threadlimit then
			update threads set archive = 1 where id = threadid;
		else
			update threads set archive = 0 where id = threadid;
		end if;
		
		set threadcount = threadcount + 1;
		end if;
	until done END repeat;

	close cur1;
END|



-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	create new thread on board
-- =============================================
delimiter |
drop procedure if exists  sp_create_thread|
CREATE PROCEDURE sp_create_thread (
	-- board id
	boardid int,
	-- post number contained opening message
	postnumber int
)
BEGIN
	-- thread number on board
	declare threadscount int;
	-- default bump limit for board
	declare bumplimit int;

	select bump_limit into bumplimit from boards where id = boardid;
	select threads into threadscount from boards where id = boardid;
	if threadscount is null then
		set threadscount = 0;
	end if;

	-- mark threads which should drown
	call sp_mark_archived(boardid);

	insert into threads (board_id, original_post_num, bump_limit, sage)
	values (boardid, postnumber, bumplimit, 0);

	-- update number of threads on board
	set threadscount = get_threads_count(boardid);

	update boards set threads = threadscount
	where id = boardid;
END|

/*
-- =============================================
-- Author:		innomines
-- Create date: <Create Date, ,>
-- Description:	TODO unused function
-- =============================================
CREATE FUNCTION [dbo].[get_next_thread_id] 
(@board_id int)
RETURNS int
AS
BEGIN
	-- Declare the return variable here
	DECLARE @thread_id int

	-- Add the T-SQL statements to compute the return value here
	SELECT @thread_id = max(id) from threads
		where board_id = @board_id
	if @thread_id is null
	begin
		select @thread_id = 0
	end

	select @thread_id = @thread_id + 1;
	-- Return the result of the function
	RETURN @thread_id

END
*/
-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	get thread ids in board on page N
-- =============================================
delimiter |
drop procedure if exists  sp_threads_on_page|
CREATE PROCEDURE sp_threads_on_page (
	-- board id
	boardid int,
	-- quantity of threads on page
	quantity int,
	-- page N
	page int
)
BEGIN
	-- first thread to be displayed
	declare current int;
	-- counter of threads
	declare counter int;
	-- open post num of thread
	declare postid int;
	-- sorting filed
	declare threaddatetime datetime;
	declare done int default 0;
	declare thread_view_cur cursor for select original_post_num,last_post from threads
		where board_id = boardid
			and deleted <> 1 and archive <> 1
		order by last_post desc;
	declare continue handler for not found set done = 1;

	-- store results here
	drop temporary table if exists Tthreads_on_page;
	create temporary table Tthreads_on_page (num int not null, ordertime datetime);

	set counter = 0;
	set current = quantity * page;
	set quantity = quantity + current;

	open thread_view_cur;

	repeat
		fetch next from thread_view_cur into postid, threaddatetime;
		if not done then
		if counter >= current and counter < quantity then
			insert into Tthreads_on_page values (postid, threaddatetime);
		end if;
		set counter = counter + 1;
		end if;
	until done end repeat;

	close thread_view_cur;
	select num from Tthreads_on_page order by ordertime desc;

	drop temporary table if exists Tthreads_on_page;
END|

-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	count pages on board if threads on page is N
-- =============================================
delimiter |
drop function if exists  get_pages|
CREATE FUNCTION get_pages
(
	-- board id
	boardid int,
	-- threads on page is N
	threadsonpage int
)
RETURNS int
not deterministic
BEGIN
	-- threads on page
	declare quantity decimal(5,2);
	-- threads count on board
	declare threadscount int;
	-- threads on page
	declare tempresult decimal(5,2);
	declare result int;

	select cast(threadsonpage as decimal(5,2)) into quantity;

	select count(id) into threadscount from threads
		where board_id = boardid
		and archive <> 1 and deleted <> 1;

	set tempresult = threadscount / quantity;

	select ceiling(tempresult) into result;
	return result;
END|

-- =============================================
-- Author:		innomines
-- Create date: 05.05.2009
-- Description:	get threads count on board
-- =============================================
delimiter |
drop function if exists get_threads_count|
CREATE FUNCTION get_threads_count
(
	-- board id
	boardid int
)
RETURNS int
not deterministic
BEGIN
	DECLARE threadscount int;

	SELECT count(id) into threadscount from threads
	where board_id = boardid 
		and deleted <> 1 and archive <> 1;

	if threadscount is null then
		set threadscount = 0;
	end if;

	-- Return the result of the function
	RETURN threadscount;

END|


-- =============================================
-- Author:		innomines
-- Create date: 27.04.2009 - ...
-- Description:	create post
-- =============================================
delimiter |
drop procedure if exists  sp_post|
CREATE PROCEDURE sp_post (
	-- board id
	boardid int,
	-- open post number
	openpost int,
	-- name field
	postname varchar(128),
	-- email field
	postemail varchar(128),
	-- subject field
	postsubject varchar(128),
	-- pasword for deleteion
	postpassword varchar(128),
	-- session id of poster
	postersessionid varchar(128),
	-- ip of poster
	posterip int,
	-- message text
	posttext text,
	-- date time of post
	datetime datetime,
	-- post with sage
	sage tinyint
)
BEGIN
	-- thread id (real)
	declare threadid int;
	-- posts in thread
	declare count_posts int;
	-- number on post on thread
	declare post_number int;
	-- number of bump posts (posts which brings thread to up)
	declare bumplimit int;
	-- whole thread sage
	declare threadsage bit;

	-- if date is not supplied use internal sql date time
	if datetime is null then 
		select now() into datetime;
	end if;

	-- get next post number on board
	set post_number = get_next_post_on_board(boardid);
	if openpost = 0 then
		-- create new thread
		call sp_create_thread(boardid, post_number);
		set threadid = LAST_INSERT_ID();
		-- sage never happens on new thread
		set sage = 0;
	else
		set threadid = get_thread_id(boardid, openpost);
	end if;
	-- count posts in thread
	select get_posts_count(threadid) into count_posts;
	-- each thread may have individual bump limit
	select bump_limit into bumplimit from threads
	where id = threadid;
	
	-- thread may forcibly sage''d or unsaged
	select sage into threadsage from threads where id = threadid;
	if threadsage is not null then
		set sage = threadsage;
	end if;

	-- thread reached bumplimit
	if count_posts > bumplimit then
		set sage = 1;
	end if;


	-- thread age''d
	if sage = 0 then
		update threads set last_post = datetime
		where id = threadid and board_id = boardid;
	end if;

	-- insert data of post in table
	insert into posts(board_id, thread_id, post_number, name, email, subject,
	password, session_id, ip, text, date_time)
	values
	(boardid, threadid, post_number, postname, postemail, postsubject,
	postpassword, postersessionid, posterip, posttext, datetime);
	select last_insert_id();

	set count_posts = count_posts + 1;
	-- update thread counters
	update threads set messages = count_posts
	where id = threadid and board_id = boardid;
END|

-- =============================================
-- Author:		innomines
-- Create date: 12.05.2009 - ...
-- Description:	get thread id for post number
-- =============================================
delimiter |
drop function if exists get_thread_id|
create function get_thread_id
(
	boardid int,
	postnumber int
)
RETURNS int
deterministic
begin
	declare threadid int default 0;
	select thread_id into threadid from posts where post_number = postnumber and
	board_id = boardid;
	if threadid is null then
		set threadid = 0;
	end if;

	return threadid;
end|

-- =============================================
-- Author:		innomines
-- Create date: 27.04.2009 - ...
-- Description:	create new login
-- =============================================
delimiter |
drop procedure if exists  sp_add_user|
create procedure sp_add_user
(

)
begin
end|
-- =============================================
-- Author:		innomines
-- Create date: 31.05.2009
-- Description:	get boards with filetypes enabled
-- =============================================
delimiter |
drop procedure if exists sp_get_boards|
create PROCEDURE sp_get_boards(
)
begin
	select b.id, b.board_name, b.board_description 
	from boards b join board_upload_types u 
	on (u.board_id = b.id)
	group by b.id
	order by b.board_name;

end|
-- =============================================
-- Author:		innomines
-- Create date: 27.05.2009
-- Description:	get all boards with settings
-- =============================================
delimiter |
drop procedure if exists sp_get_boards_ex|
create PROCEDURE sp_get_boards_ex(
)
begin
	select id, board_name, board_description, board_title, threads, 
	bump_limit, rubber_board, visible_threads, same_upload
	from boards
	order by board_name;
end|
-- =============================================
-- Author:		innomines
-- Create date: 31.05.2009
-- Description:	get board id
-- =============================================
delimiter |
drop procedure if exists sp_get_board_id|
create PROCEDURE sp_get_board_id (
	boardname varchar(16)
)
begin
	select id from boards
	where board_name = boardname;
end|

-- =============================================
-- Author:		innomines
-- Create date: 03.06.2009
-- Description:	get board id and other settings
-- =============================================
delimiter |
drop procedure if exists sp_get_board|
create PROCEDURE sp_get_board (
	boardname varchar(16)
)
begin
	select id, board_description, board_title, bump_limit, rubber_board, visible_threads, same_upload
	from boards
	where board_name = boardname;
end|
-- =============================================
-- Author:		innomines
-- Create date: 31.05.2009
-- Description:	get board posts count
-- TODO: more reliable count (deleted, archived...)
-- =============================================
delimiter |
drop procedure if exists sp_get_board_post_count|
create PROCEDURE sp_get_board_post_count (
	boardid int
)
begin
	select count(id) from posts
	where board_id = boardid;
end|
-- =============================================
-- Author:		innomines
-- Create date: 31.05.2009
-- Description:	get board bump limit
-- =============================================
delimiter |
drop procedure if exists sp_get_board_bumplimit|
create PROCEDURE sp_get_board_bumplimit (
	boardid int
)
begin
	select bump_limit from boards
	where id = boardid;
end|
-- =============================================
-- Author:		innomines
-- Create date: 04.06.2009
-- Description: store information about upload in database
-- =============================================
delimiter |
drop procedure if exists sp_upload|
create PROCEDURE sp_upload(
	boardid int,
	name varchar(256),
	filesize int,
	uploadhash varchar(32),
	isimage tinyint,
	filename varchar(256),
	width int,
	height int,
	thumbnailfile varchar(256),
	thuwidth int,
	thuheight int
)
begin
	insert into uploads (board_id, file, size, hash, is_image, file_name, file_w, file_h,
	thumbnail, thumbnail_w, thumbnail_h)
	values
	(boardid, name, filesize, uploadhash, isimage, filename, width, height, thumbnailfile, thuwidth, thuheight);
	select last_insert_id();
end|
-- =============================================
-- Author:		innomines
-- Create date: 06.06.2009
-- Description: link post and upload
-- =============================================
delimiter |
drop procedure if exists sp_post_upload|
create PROCEDURE sp_post_upload(
	boardid int,
	postid int,
	uploadid int
)
begin
	declare threadid int;
	select get_thread_id(boardid, postid) into threadid;
	insert into posts_uploads (thread_id, post_id, upload_id)
	values (threadid, postid, uploadid);
end|
-- =============================================
-- Author:		innomines
-- Create date: 06.06.2009
-- Description: get post
-- =============================================
delimiter |
drop procedure if exists sp_get_post|
create PROCEDURE sp_get_post(
	boardid int,
	postnum int
)
begin
	select post_number, name, email, subject, text, unix_timestamp(date_time) as date_time, sage
	from posts
	where post_number = postnum and board_id = boardid;

	select u.is_image, u.file, u.size, u.file_name, u.file_w, u.file_h,
	u.thumbnail, u.thumbnail_w, u.thumbnail_h
	from uploads u 
	join posts_uploads p on (p.upload_id = u.id)
	where p.post_id = get_postid(boardid, postnum);
end|

-- =============================================
-- Author:		innomines
-- Create date: 04.06.2009 - ...
-- Description:	get post id for post number
-- =============================================
delimiter |
drop function if exists get_postid|
create function get_postid
(
	boardid int,
	postnumber int
)
RETURNS int
deterministic
begin
	declare postid int default 0;
	select id into postid from posts where post_number = postnumber and
	board_id = boardid;
	if postid is null then
		set postid = 0;
	end if;

	return postid;
end|
-- =============================================
-- Author:		innomines
-- Create date: 06.06.2009
-- Description: get thread posts (not deleted)
-- =============================================
delimiter |
drop procedure if exists sp_get_thread|
create PROCEDURE sp_get_thread(
	boardid int,
	openpostnum int
)
begin
	declare threadid int;
	select get_thread_id(boardid, openpostnum) into threadid;

	select post_number
	from posts
	where thread_id = threadid and board_id = boardid
	and deleted <> 1
	order by date_time asc;
end|
-- =============================================
-- Author:		innomines
-- Create date: 09.06.2009
-- Description: get post thread number and board name
---- for links etc
-- =============================================
delimiter |
drop procedure if exists sp_get_post_link|
create PROCEDURE sp_get_post_link(
	boardname varchar(16),
	postnum int
)
begin
	select b.board_name, t.original_post_num, p.post_number 
	from boards b 
	join threads t on  (t.archive <> 1 and t.deleted <> 1)
	join posts p on (p.deleted <> 1 and p.thread_id = t.id and p.board_id = b.id)
	where b.board_name = boardname and p.post_number = postnum;
end|
-- =============================================
-- Author:		innomines
-- Create date: 09.06.2009
-- Description: get thread info (useful for thread exsisting check)
-- =============================================
delimiter |
drop procedure if exists sp_get_thread_info|
create PROCEDURE sp_get_thread_info(
	boardid int,
	openpostnum int
)
begin
	select original_post_num, messages, bump_limit, sage from threads
	where board_id = boardid and original_post_num = openpostnum;
end|
-- =============================================
-- Author:		innomines
-- Create date: 10.06.2009
-- Description: get same uploads
-- =============================================
delimiter |
drop procedure if exists sp_same_uploads|
create PROCEDURE sp_same_uploads(
	boardid int,
	uploadhash varchar(32)
)
begin
	select u.id from uploads u 
	join threads t on (t.archive <> 1 and t.deleted <> 1) 
	join posts p on (p.deleted <> 1 and p.thread_id = t.id)
	join posts_uploads pu on (pu.post_id = p.id and pu.upload_id = u.id) 
	where u.board_id = boardid and u.hash=uploadhash;
end|

-- =============================================
-- Author:		innomines
-- Create date: 10.06.2009
-- Description: get post(s) with this upload
-- =============================================
delimiter |
drop procedure if exists sp_upload_post|
create PROCEDURE sp_upload_post(
	boardid int,
	uploadid int
)
begin
	select b.board_name, t.original_post_num, p.post_number from boards b
	join threads t on (t.archive <> 1 and t.deleted <> 1)     
	join posts p on (p.thread_id = t.id and p.board_id = b.id and p.deleted <> 1)
	join posts_uploads pu on (p.id = pu.post_id)
	where b.id = boardid and pu.upload_id = uploadid;
end|

-- =============================================
-- Author:		innomines
-- Create date: 11.06.2009
-- Description: add group
-- =============================================
delimiter |
drop procedure if exists sp_addgroup|
create procedure sp_addgroup (
	groupname varchar(32)
)
begin
	insert into groups (group_name)
	values (groupname);
end|
-- =============================================
-- Author:		innomines
-- Create date: 11.06.2009
-- Description: add user to group
-- =============================================
delimiter |
drop procedure if exists sp_add_membership|
create procedure sp_addmembership(
	userid int,
	groupid int
)
begin
	insert into membership (user_id, group_id)
	values (userid, groupid);
end|

-- =============================================
-- Author:		innomines
-- Create date: 11.06.2009
-- Description: add per-board group access settings
-- =============================================
delimiter |
drop procedure if exists sp_addaccess|
create procedure sp_addaccess(
	boardid int,
	groupid int,
	mayread int,
	maythread tinyint,
	maypost int,
	maypostimage int,
	maydeletepost int,
	maydeleteimage int,
	maybanuser int,
	maycreateboard tinyint
)
begin
	insert into access_lists (board_id, group_id,
		readboard, thread, post, postimage, delpost, delimage, banuser, createboard)
	values
	(boardid, groupid, 
		mayread,
		maythread,
		maypost,
		maypostimage,
		maydeletepost,
		maydeleteimage,
		maybanuser,
		maycreateboard);
end|

-- =============================================
-- Author:		innomines
-- Create date: 11.06.2009
-- Description: get user per-board access settings
-- =============================================
delimiter |
drop procedure if exists sp_getaccess|
create procedure sp_getaccess(
	boardid int,
	userid int
)
begin
	select a.readboard, a.thread, a.post, a.postimage,
		a.delpost, a.delimage, a.banuser, a.createboard
	from access_lists a
	join boards b on (b.id = boardid and a.board_id = boardid)
	join membership m on (m.user_id = userid)
	join groups g on (g.id = m.group_id and a.group_id = m.group_id);
end|
