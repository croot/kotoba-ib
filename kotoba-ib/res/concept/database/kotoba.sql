use kotoba2;


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
drop procedure sp_delete_post|
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
	-- thread id
	threadid int,
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
	declare imagevalue varchar(64);
	declare done int default 0;
	declare cur1 cursor for select id, post_number, image from posts where thread_id = threadid and deleted <> 1 order by post_number asc, date_time asc;
	declare continue handler for not found set done = 1;

	set count = 0;
	set postskip = 0;
	set imageskip = 0;

	select count(id) into postscount from posts
		where thread_id = threadid
			and deleted <> 1;


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
			if imagevalue is not null then
				set imageskip = imageskip + 1;
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
-- Create date: 05.05.2009
-- Description:	create new board
-- =============================================
delimiter |
drop procedure if exists  sp_create_board|
CREATE PROCEDURE sp_create_board(
	-- board name ie b, a
	boardname varchar(16),
	-- board description like random, anime
	boarddescription varchar(50),
	-- default bump limit for board
	bumplimit int,
	-- maximum threads on board
	maxthreads int
)
BEGIN
	insert into boards (board_name, board_description, bump_limit, max_threads) 
	values (boardname, boarddescription, bumplimit, maxthreads);
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

	insert into threads (board_id, open_post_num, bump_limit, sage)
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
	-- id of thread
	declare threadid int;
	declare done int default 0;
	declare thread_view_cur cursor for select id from threads
		where board_id = boardid
			and deleted <> 1 and archive <> 1
		order by last_post desc;
	declare continue handler for not found set done = 1;

	-- store results here
	drop temporary table if exists Tthreads_on_page;
	create temporary table Tthreads_on_page (id int not null);

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
	board_id int,
	-- thread id
	thread_id int,
	-- post test
	text text,
	-- date and time of post
	date datetime,
	-- image for post
	image varchar(64),
	-- sage
	sage bit
)
BEGIN
	-- posts in thread
	declare count_posts int;
	-- posts in thread with images
	declare count_images int;
	-- number on post on thread
	declare post_number int;
	-- number of bump posts (posts which brings thread to up)
	declare bumplimit int;
	-- whole thread sage
	declare threadsage bit;

	-- if date is not supplied use internal sql date time
	if date is null then 
		select now() into date;
	end if;

	set count_images = 0;

	-- get next post number on board
	set post_number = get_next_post_on_board(board_id);
	if thread_id = 0 then
		-- create new thread
		call sp_create_thread(board_id, post_number);
		set thread_id = LAST_INSERT_ID();
		set count_posts = 1;
		-- sage never happens on new thread
		set sage = 0;
	else
		-- existing thread
		set count_posts = get_posts_count(thread_id);
		set count_posts = count_posts + 1;
	end if;
	
	-- count images in thread and...	
	set count_images = get_images_count(thread_id);
	-- ... update images counter if post has image
	if image is not null then
		set count_images = count_images + 1;
	end if;
	
	-- each thread may have individual bump limit
	select bump_limit into bumplimit from threads
	where id = thread_id;
	
	-- thread may forcibly sage''d
	select sage into threadsage from threads where id = thread_id;
	if threadsage = 1 then
		set sage = 1;
	end if;

	-- thread reached bumplimit
	if count_posts > bumplimit then
		set sage = 1;
	end if;

	-- update thread counters
	update threads set messages = count_posts, with_images = count_images 
	where id = thread_id and board_id = board_id;

	-- thread age''d
	if sage = 0 then
		update threads set last_post = date
		where id = thread_id and board_id = board_id;
	end if;

	-- insert data of post in table
	insert into posts(board_id, thread_id, post_number, text, image, sage, date_time)
	values
	(board_id, thread_id, post_number, text, image, sage, date);
END|
