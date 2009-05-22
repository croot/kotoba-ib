-- vim: set ft=mysql:
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
begin
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
-- Create date: 21.05.2009
-- Description:	add new supported file-type
-- =============================================
delimiter |
drop procedure if exists sp_add_filetype|
create PROCEDURE sp_add_filetype(
	-- is this file type image?
	isimage tinyint
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
-- Create date: 05.05.2009
-- Description:	get posts count in thread
-- =============================================
delimiter |
drop function if exists  get_posts_count|
CREATE FUNCTION get_posts_count
(
	-- voard id
	boardid int,
	-- open post number
	postnum int
)
RETURNS int
not deterministic
BEGIN
	DECLARE countpost int;
	declare threadid int;
	select get_thread_id(boardid, postnum) into threadid;

	SELECT count(id) into countpost from posts where thread_id = threadid
	and deleted <> 1;


	RETURN countpost;
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

	-- find post id
	select id into postid from posts
	where post_number = postnum and board_id = boardid;

	-- find thread id
	select thread_id into threadid from posts
	where id = postid;

	call sp_delete_uploads(boardid, postnum);

	-- find if post was opening for thread...
	select id into wholethreadid from threads
	where open_post_num = postnum;
	-- ... if so delete thread
	if wholethreadid is not null then
		call sp_delete_thread(boardid, threadid);
	end if;
	-- mark post as deleted
	update posts set deleted = 1
	where id = postid and board_id = boardid;

	call sp_update_postscount(boardid, postnum);
END|

-- =============================================
-- Author:		innomines
-- Create date: 22.05.2009
-- Description:	delete uploads for post
-- =============================================
delimiter |
drop procedure if exists sp_delete_uploads|
create procedure sp_delete_uploads (
	boardid int,
	postnum int
)
begin
	delete from posts_uploads where post_id = postnum;
	call sp_update_imagescount(boardid, postnum);
end|

-- =============================================
-- Author:		innomines
-- Create date: 22.05.2009
-- Description:	update posts count
-- =============================================
delimiter |
drop procedure if exists sp_update_postscount|
create procedure sp_update_postscount (
	boardid int,
	postnum int
)
begin
	update threads set messages = get_posts_count(boardid, postnum) 
	where id = get_thread_id(boardid, postnum);
end|
-- =============================================
-- Author:		innomines
-- Create date: 22.05.2009
-- Description:	update images (uploads) count
-- =============================================
delimiter |
drop procedure if exists sp_update_imagescount|
create procedure sp_update_imagescount (
	boardid int,
	postnum int
)
begin
	update threads set messages = get_images_count(boardid, postnum)
	where id = get_thread_id(boardid, postnum);
end|
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
	boardid int,
	postnum int
)
RETURNS int
not deterministic
BEGIN
	DECLARE posts_with_images int;

	SELECT count(id) into posts_with_images from posts_uploads
		where thread_id = get_thread_id(boardid, postnum);
	
--	if posts_with_images is null then
--		set posts_with_images = 0;
--	end if;

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
	-- open post number
	postnum int,
	-- skip messages except last N
	showlast int
)
BEGIN
	declare post int;
	declare postn int;
	declare postscount int;
	declare count int;
	declare postskip int;
	declare imageskip int;
	declare hidedcount int;
	declare imagevalue int;
	declare done int default 0;
--	declare threadid int;
	declare cur1 cursor for select id, post_number, image from posts where thread_id = get_thread_id(boardid, postnum) and deleted <> 1 order by post_number asc, date_time asc;
	declare continue handler for not found set done = 1;

	set count = 0;
	set postskip = 0;
	set imageskip = 0;

	set postscount = get_posts_count(boardid, postnum);
--	select count(id) into postscount from posts
--		where thread_id = threadid
--			and deleted <> 1;


	set hidedcount = postscount - showlast;

	drop temporary table if exists Tthread_preview;
	create temporary table Tthread_preview (post_id int, post_number int,
		posts_skip int, with_images_skip int);

	open cur1;
	repeat
	fetch cur1 into post, postn, imagevalue;
	if not done then
		-- fuck you mysql for this extra if. fuck you
		if count = 0 or count >= hidedcount then
			insert into Tthread_preview (post_id, post_number) values (post, postn);
		else
			set postskip = postskip + 1;
			select count(id) from posts_uploads where post_id = post into imagevalue;
			if imagevalue is not null then
				set imageskip = imageskip + imagevalue;
			end if;
		end if;

		set count = count + 1;
	end if;
	until done end repeat;

	close cur1;
	update Tthread_preview set posts_skip = postskip, with_images_skip = imageskip;

select post_id, post_number, posts_skip, with_images_skip from Tthread_preview;
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
-- Create date: 05.05.2009
-- Description:	mark thread as deleted
-- =============================================
delimiter |
drop procedure if exists  sp_delete_thread|
CREATE PROCEDURE sp_delete_thread (
	-- board id
	boardid int,
	-- (open) post number
	postnum int
)
BEGIN
	-- mark thread as deleted
	update threads set deleted = 1
	where id = get_thread_id(boardid, postnum);

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
	-- thread id
	declare threadid int;
	declare cur1 cursor for select id from threads
		where deleted <> 1 and archive <> 1 and board_id = boardid
		order by last_post desc;
	declare continue handler for not found set done = 1;

	select  `max_threads` into threadlimit from boards 
	where id = boardid;


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
	-- is board is rubber
	decalre rubberboard int default 0;

	select bump_limit into bumplimit from boards where id = boardid;
	select rubber_board into rubberboard from boards where id = boardid;
	select threads into threadscount from boards where id = boardid;
	if threadscount is null then
		set threadscount = 0;
	end if;

	-- mark threads which should drown
	if rubberboard = 0 then
		call sp_mark_archived(boardid);
	end if;

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
-- Description:	get open posts numbers in board on page N
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
	-- id of thread
	declare threadid int;
	declare done int default 0;
	declare thread_view_cur cursor for select original_post_num from threads
		where board_id = boardid
			and deleted <> 1 and archive <> 1
		order by last_post desc;
	declare continue handler for not found set done = 1;

	-- store results here
	drop temporary table if exists Tthreads_on_page;
	create temporary table Tthreads_on_page (post_number int not null);

	set counter = 0;
	set current = quantity * page;
	set quantity = quantity + current;

	open thread_view_cur;

	repeat
		fetch next from thread_view_cur into threadid;
		if not done then
		if counter >= current and counter < quantity then
			insert into Tthreads_on_page values (threadid);
		end if;
		set counter = counter + 1;
		end if;
	until done end repeat;

	close thread_view_cur;
	
	select post_number from Tthreads_on_page;
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
	declare tempresult decimal(2,1);
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
		and deleted <> 1;

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
	-- thread id
	openpostnum int,
	name varchar(128),
	email varchar(128),
	subject varchar(128),
	password varchar(128),
	sessionid varchar(128),
	ip int,
	-- post test
	text text,
	-- date and time of post
	date datetime,
	-- sage
	sage bit
)
BEGIN
	declare threadid int;
	-- posts in thread
	declare countposts int;
	-- posts in thread with images
	declare countimages int;
	-- number on post on thread
	declare postnumber int;
	-- number of bump posts (posts which brings thread to up)
	declare bumplimit int;
	-- whole thread sage
	declare threadsage bit;

	-- if date is not supplied use internal sql date time
	if date is null then 
		select now() into date;
	end if;

--	set countimages = 0;

	-- get next post number on board
	set postnumber = get_next_post_on_board(boardid);
	if openpostnum = 0 then
		-- create new thread
		call sp_create_thread(boardid, postnumber);
		set threadid = LAST_INSERT_ID();
		-- sage never happens on new thread
		set sage = 0;
	else
		-- existing thread
		set threadid = get_thread_id(boardid, postnumber);
	end if;
	
	-- each thread may have individual bump limit
	select bump_limit into bumplimit from threads
	where id = threadid;
	
	-- thread may forcibly sage''d or unsaged
	select sage into threadsage from threads where id = threadid;
	if threadsage is not null then
		set sage = threadsage;
	end if;

	set countposts = get_posts_count(boardid, postnumber);

	-- thread reached bumplimit
	if countposts > bumplimit then
		set sage = 1;
	end if;

	-- thread age''d
	if sage = 0 then
		update threads set last_post = date
		where id = threadid and board_id = boardid;
	end if;

	-- insert data of post in table
	insert into posts(board_id, thread_id, post_number, name, email, 
		subject, password, sessionid, ip, text, sage, date_time)
	values
	(boardid, threadid, postnumber, name, email,
		subject, password, sessionid, ip, text, sage, date);
	call sp_update_postscount(boardid, postnumber);
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

