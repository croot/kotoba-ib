drop procedure if exists sp_save_user_settings;
drop procedure if exists sp_get_languages;
drop procedure if exists sp_get_stylesheets;
drop procedure if exists sp_get_user_settings;
drop procedure if exists sp_get_boards_list;
drop procedure if exists sp_ban;
drop procedure if exists sp_check_ban;
drop procedure if exists sp_refresh_banlist;

create procedure sp_refresh_banlist ()
begin
delete from bans where untill <= now();
end;

create procedure sp_ban
(
	_range_beg int,
	_range_end int,
	_reason text,
	_untill datetime
)
begin
call sp_refresh_banlist();

if _reason = '' or _reason is null
then
	insert into bans (range_beg, range_end, reason, untill) values (_range_beg, _range_end, null, _untill);
else
	insert into bans (range_beg, range_end, reason, untill) values (_range_beg, _range_end, _reason, _untill);
end if;
end;

create procedure sp_check_ban
(
	ip int
)
begin
call sp_refresh_banlist();
select range_beg, range_end, untill, reason from bans where range_beg >= ip and range_end <= ip order by range_end desc limit 1;
end;

create procedure sp_get_boards_list
(
	user int
)
begin
	select b.name, c.name as category
	from boards b
	join user_groups ug on ug.user = user
	left join categories c on b.category = c.id
	left join acl a1 on ug.`group` = a1.`group` and a1.board is null -- global premissions for specifed groups
	left join acl a2 on b.id = a2.board and a2.`group` is null and a2.thread is null and a2.post is null -- group independent premissions for specifed board
	left join acl a3 on ug.`group` = a3.`group` and b.id = a3.board and a2.thread is null and a2.post is null -- premissions for specifed groups and boards
	group by b.id
	having max(coalesce(a3.view, a2.view, a1.view)) = 1
	order by c.name, b.name;
end;

create procedure sp_get_user_settings
(
	_keyword varchar(32)
)
begin
	declare user_id int;
	select id into user_id from users where keyword = _keyword;

	select u.id, u.posts_per_thread, u.threads_per_page, u.lines_per_post, l.name as language, s.name as stylesheet from users u
	join stylesheets s on u.stylesheet = s.id
	join languages l on u.language = l.id
	where u.keyword = _keyword;

	select g.name from user_groups ug
	join users u on ug.`user` = u.id and u.id = user_id
	join groups g on ug.`group` = g.id;
end;

create procedure sp_get_stylesheets ()
begin
	select name from stylesheets;
end;

create procedure sp_get_languages ()
begin
	select name from languages;
end;

create procedure sp_save_user_settings
(
	_keyword varchar(32),
	_threads_per_page int,
	_posts_per_thread int,
	_lines_per_post int,
	_stylesheet varchar(50),
	_language varchar(50)
)
begin
	declare user_id int;
	declare stylesheet_id int;
	declare language_id int;
	set @user_id = null;
	select id into user_id from users where keyword = _keyword;
	select id into stylesheet_id from stylesheets where name = _stylesheet;
	select id into language_id from languages where name = _language;

	if(user_id is null)
	then
		-- Создаём ногового пользователя
		start transaction;
		insert into users (keyword, threads_per_page, posts_per_thread, lines_per_post, stylesheet, language)
		values (_keyword, _threads_per_page, _posts_per_thread, _lines_per_post, stylesheet_id, language_id);
		select last_insert_id() into user_id;
		insert into user_groups (`user`, `group`) select user_id, id from groups where name = 'Users';
		commit;
	else
		-- Редактируем настройки существующего
		update users set threads_per_page = _threads_per_page, posts_per_thread = _posts_per_thread, lines_per_post = _lines_per_post, stylesheet = stylesheet_id, language = language_id where id = user_id;
	end if;
end;