delimiter |
drop procedure if exists sp_bans_check|
drop procedure if exists sp_bans_refresh|
drop procedure if exists sp_bans_add|
drop procedure if exists sp_bans_delete_byid|
drop procedure if exists sp_bans_delete_byip|
drop procedure if exists sp_bans_get_all|
drop procedure if exists sp_boards_get_all|
drop procedure if exists sp_boards_get_all_view|
drop procedure if exists sp_boards_get_all_change|
drop procedure if exists sp_boards_get_specifed|
drop procedure if exists sp_boards_get_specifed_byname|
drop procedure if exists sp_boards_get_specifed_change_byname|
drop procedure if exists sp_boards_get_specifed_change|
drop procedure if exists sp_boards_edit|
drop procedure if exists sp_boards_edit_annotation|
drop procedure if exists sp_boards_delete|
drop procedure if exists sp_boards_add|
drop procedure if exists sp_categories_get_all|
drop procedure if exists sp_categories_add|
drop procedure if exists sp_categories_delete|
drop procedure if exists sp_users_edit_bykeyword|
drop procedure if exists sp_users_get_settings|
drop procedure if exists sp_users_get_all|
drop procedure if exists sp_stylesheets_get_all|
drop procedure if exists sp_stylesheets_add|
drop procedure if exists sp_stylesheets_delete|
drop procedure if exists sp_languages_get_all|
drop procedure if exists sp_languages_add|
drop procedure if exists sp_languages_delete|
drop procedure if exists sp_groups_get_all|
drop procedure if exists sp_groups_add|
drop procedure if exists sp_groups_delete|
drop procedure if exists sp_user_groups_get_all|
drop procedure if exists sp_user_groups_add|
drop procedure if exists sp_user_groups_edit|
drop procedure if exists sp_user_groups_delete|
drop procedure if exists sp_acl_get_all|
drop procedure if exists sp_acl_edit|
drop procedure if exists sp_acl_delete|
drop procedure if exists sp_acl_add|
drop procedure if exists sp_upload_handlers_get_all|
drop procedure if exists sp_upload_handlers_add|
drop procedure if exists sp_upload_handlers_delete|
drop procedure if exists sp_popdown_handlers_get_all|
drop procedure if exists sp_popdown_handlers_add|
drop procedure if exists sp_popdown_handlers_delete|
drop procedure if exists sp_upload_types_get_all|
drop procedure if exists sp_upload_types_get_board|
drop procedure if exists sp_upload_types_edit|
drop procedure if exists sp_upload_types_add|
drop procedure if exists sp_upload_types_delete|
drop procedure if exists sp_board_upload_types_get_all|
drop procedure if exists sp_board_upload_types_add|
drop procedure if exists sp_board_upload_types_delete|
drop procedure if exists sp_threads_get_all|
drop procedure if exists sp_threads_get_all_archived|
drop procedure if exists sp_threads_get_all_moderate|
drop procedure if exists sp_threads_edit|
drop procedure if exists sp_threads_edit_originalpost|
drop procedure if exists sp_threads_get_board_view|
drop procedure if exists sp_threads_get_view_threadscount|
drop procedure if exists sp_threads_get_specifed_view|
drop procedure if exists sp_threads_get_specifed_view_hiden|
drop procedure if exists sp_threads_get_specifed_change|
drop procedure if exists sp_threads_check_specifed_moderate|
drop procedure if exists sp_threads_add|
drop procedure if exists sp_threads_edit_archived_postlimit|
drop procedure if exists sp_posts_get_thread_view|
drop procedure if exists sp_posts_get_thread|
drop procedure if exists sp_posts_get_specifed_view_bynumber|
drop procedure if exists sp_posts_add|
drop procedure if exists sp_posts_uploads_get_post|
drop procedure if exists sp_posts_uploads_add|
drop procedure if exists sp_posts_delete|
drop procedure if exists sp_posts_edit_specifed_addtext|
drop procedure if exists sp_posts_get_all_numbers|
drop procedure if exists sp_uploads_get_post|
drop procedure if exists sp_uploads_get_same|
drop procedure if exists sp_uploads_add|
drop procedure if exists sp_hidden_threads_get_board|
drop procedure if exists sp_hidden_threads_add|
drop procedure if exists sp_hidden_threads_delete|

--------------------------------
-- Блокировки адресов (баны). --
--------------------------------

-- Удаляет все истекшие блокировки.
create procedure sp_bans_refresh ()
begin
delete from bans where untill <= now();
end|

-- Проверяет, заблокирован ли адрес ip.
--
-- Аргументы:
-- ip - адрес.
--
-- Если адрес заблокирован, то возвращает запись с самым широким диапазоном, в
-- который он входит.
create procedure sp_bans_check
(
	ip int
)
begin
	call sp_bans_refresh();
	select range_beg, range_end, untill, reason
	from bans
	where range_beg <= ip and range_end >= ip
	order by range_end desc limit 1;
end|

-- Удаляет бан с заданным идентификатором.
--
-- Аргументы:
-- _id - идентификатор бана.
create procedure sp_bans_delete_byid
(
	_id int
)
begin
	delete from bans where id = _id;
end|

-- Удаляет баны с заданным IP.
--
-- Аргументы:
-- ip - IP адрес.
create procedure sp_bans_delete_byip
(
	ip int
)
begin
	delete from bans where range_beg <= ip and range_end >= ip;
end|

-- Блокирует диапазон адресов.
--
-- Аргументы:
-- _range_beg - начало диапазона адресов.
-- _range_end - конец диапазона адресов.
-- _reason - причина.
-- _untill - время истечения бана.
create procedure sp_bans_add
(
	_range_beg int,
	_range_end int,
	_reason text,
	_untill datetime
)
begin
	call sp_refresh_banlist();
	insert into bans (range_beg, range_end, reason, untill)
	values (_range_beg, _range_end, _reason, _untill);
end|

-- Выбирает все баны.
create procedure sp_bans_get_all ()
begin
	select id, range_beg, range_end, reason, untill from bans;
end|

-----------------------
-- Работа с досками. --
-----------------------

-- Выбирает доски, доступные для чтения пользователю с идентификатором user_id.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
--
-- Возвращает пустую выборку, если нет досок, доступных для просмотра.
-- TODO Возвращать имя вместо идентификатора категории - плохая идея. А что если
-- понадобится получить результат с идентификаторами, а не с именами?
create procedure sp_boards_get_all_view
(
	user_id int
)
begin
	select b.id, b.`name`, b.title, b.annotation, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_files, b.same_upload, b.popdown_handler,
		ct.`name` as category
	from boards b
	join categories ct on ct.id = b.category
	join user_groups ug on ug.user = user_id
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
		-- Доска не запрещена для просмотра группе и
		(a1.`view` = 1 or a1.`view` is null)
		-- доска не запрещена для просмотра всем и
		and (a2.`view` = 1 or a2.`view` is null)
		-- группе разрешен просмотр.
		and a3.`view` = 1
	group by b.id
	order by b.category, b.`name`;
end|

-- Выбирает доски, доступные для редактирования пользователю.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
create procedure sp_boards_get_all_change
(
	user_id int
)
begin
	select b.id, b.`name`, b.title, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_files, b.same_upload, b.popdown_handler,
		ct.`name` as category
	from boards b
	join categories ct on ct.id = b.category
	join user_groups ug on ug.user = user_id
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	left join acl a2 on a2.`group` is null and b.id = a2.board
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
			-- Доска не запрещена для просмотра группе и
		((a1.`view` = 1 or a1.`view` is null)
			-- доска не запрещена для просмотра всем и
			and (a2.`view` = 1 or a2.`view` is null)
			-- группе разрешен просмотр.
			and a3.`view` = 1)
			-- Редактирование доски разрешено конкретной группе или
		and (a1.change = 1
			-- редактирование доски не запрещено конкретной группе и разрешено
			-- всем группам или
			or (a1.change is null and a2.change = 1)
			-- редактирование доски не запрещено ни конкретной группе ни всем, и
			-- конкретной группе редактирование разрешено.
			or (a1.change is null and a2.change is null and a3.change = 1))
	group by b.id
	order by b.category, b.`name`;
end|

-- Выбирает доску по заданному имени, доступную для редактирования пользователю.
--
-- Аргументы:
-- board_name - Имя доски.
-- user_id - Идентификатор пользователя.
create procedure sp_boards_get_specifed_change_byname
(
	board_name varchar(16),
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where `name` = board_name;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.`name`, b.title, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_files, b.same_upload, b.popdown_handler,
			ct.`name` as category
		from boards b
		join categories ct on ct.id = b.category
		join user_groups ug on ug.user = user_id
		-- Правила для конкретной группы и доски.
		left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
		-- Правило для конкретной доски.
		left join acl a2 on a2.`group` is null and b.id = a2.board
		-- Правила для конкретной группы.
		left join acl a3 on ug.`group` = a3.`group` and a3.board is null
			and a3.thread is null and a3.post is null
		where
			b.id = board_id
			and
				-- Доска не запрещена для просмотра группе и
			((a1.`view` = 1 or a1.`view` is null)
				-- доска не запрещена для просмотра всем и
				and (a2.`view` = 1 or a2.`view` is null)
				-- группе разрешен просмотр.
				and a3.`view` = 1)
				-- Редактирование доски разрешено конкретной группе или
			and (a1.change = 1
				-- редактирование доски не запрещено конкретной группе и
				-- разрешено всем группам или
				or (a1.change is null and a2.change = 1)
				-- редактирование доски не запрещено ни конкретной группе ни
				-- всем, и конкретной группе редактирование разрешено.
				or (a1.change is null and a2.change is null and a3.change = 1))
		group by b.id
		order by b.category, b.`name`;
	end if;
end|

-- Выбирает доску, доступную для редактирования пользователю.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
create procedure sp_boards_get_specifed_change
(
	_board_id int,
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where id = _board_id;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.`name`, b.title, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_files, b.same_upload, b.popdown_handler,
			ct.`name` as category
		from boards b
		join categories ct on ct.id = b.category
		join user_groups ug on ug.user = user_id
		-- Правила для конкретной группы и доски.
		left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
		-- Правило для конкретной доски.
		left join acl a2 on a2.`group` is null and b.id = a2.board
		-- Правила для конкретной группы.
		left join acl a3 on ug.`group` = a3.`group` and a3.board is null
			and a3.thread is null and a3.post is null
		where
			b.id = board_id
			and
				-- Доска не запрещена для просмотра группе и
			((a1.`view` = 1 or a1.`view` is null)
				-- доска не запрещена для просмотра всем и
				and (a2.`view` = 1 or a2.`view` is null)
				-- группе разрешен просмотр.
				and a3.`view` = 1)
				-- Редактирование доски разрешено конкретной группе или
			and (a1.change = 1
				-- редактирование доски не запрещено конкретной группе и
				-- разрешено всем группам или
				or (a1.change is null and a2.change = 1)
				-- редактирование доски не запрещено ни конкретной группе ни
				-- всем, и конкретной группе редактирование разрешено.
				or (a1.change is null and a2.change is null and a3.change = 1))
		group by b.id
		order by b.category, b.`name`;
	end if;
end|

-- Выбирает все доски.
create procedure sp_boards_get_all ()
begin
	select id, `name`, title, annotation, bump_limit, force_anonymous,
		default_name, with_files, same_upload, popdown_handler, category
	from boards;
end|

-- Получает доску по заданному идентификатору.
--
-- Аргументы:
-- board_id - Идентификатор доски.
create procedure sp_boards_get_specifed
(
	board_id int
)
begin
	select id, `name`, title, bump_limit, force_anonymous, default_name,
		with_files, same_upload, popdown_handler, category
	from boards where id = board_id;
end|

-- Получает доску по заданному имени.
--
-- Аргументы:
-- board_name - Имя доски.
create procedure sp_boards_get_specifed_byname
(
	board_name varchar(16)
)
begin
	select id, `name`, title, bump_limit, force_anonymous, default_name,
		with_files, same_upload, popdown_handler, category
	from boards where `name` = board_name;
end|

-- Добавляет доску.
--
-- Аргументы:
-- _name - Имя доски.
-- _title - Заголовок.
-- _bump_limit - Специфичный для доски бамплимит.
-- _force_anonymous - Флаг отображения имени отправителя.
-- _default_name - Имя отправителя по умолчанию.
-- _with_files - Флаг загрузки файлов.
-- _same_upload - Политика загрузки одинаковых файлов.
-- _popdown_handler - Обработчик удаления нитей.
-- _category - Категория.
create procedure sp_boards_add
(
	_name varchar(16),
	_title varchar(50),
	_bump_limit int,
	_force_anonymous bit,
	_default_name varchar(128),
	_with_files bit,
	_same_upload varchar(32),
	_popdown_handler int,
	_category int
)
begin
	insert into boards (`name`, title, bump_limit, force_anonymous,
		default_name, with_files, same_upload, popdown_handler, category)
	values (_name, _title, _bump_limit, _force_anonymous, _default_name,
		_with_files, _same_upload, _popdown_handler, _category);
end|

-- Редактирует параметры доски.
--
-- Аргументы:
-- _id - Идентификатор.
-- _title - Заголовок.
-- _bump_limit - Специфичный для доски бамплимит.
-- _force_anonymous - Флаг отображения имени отправителя.
-- _default_name - Имя отправителя по умолчанию.
-- _with_files - Флаг загрузки файлов.
-- _same_upload - Политика загрузки одинаковых файлов.
-- _popdown_handler - Обработчик удаления нитей.
-- _category - Категория.
create procedure sp_boards_edit
(
	_id int,
	_title varchar(50),
	_bump_limit int,
	_force_anonymous bit,
	_default_name varchar(128),
	_with_files bit,
	_same_upload varchar(32),
	_popdown_handler int,
	_category int
)
begin
	update boards set title = _title, bump_limit = _bump_limit,
		force_anonymous = _force_anonymous, default_name = _default_name,
		with_files = _with_files, same_upload = _same_upload,
		popdown_handler = _popdown_handler, category = _category
	where id = _id;
end|

-- Редактирует аннотацию доски.
--
-- Аргументы:
-- _id - Идентификатор.
-- _annotation - Аннотация.
create procedure sp_boards_edit_annotation
(
	_id int,
	_annotation text
)
begin
	update boards set annotation = _annotation where id = _id;
end|

-- Удаляет заданную доску.
create procedure sp_boards_delete
(
	_id int
)
begin
	delete from boards where id = _id;
end|

---------------------------
-- Работа с категориями. --
---------------------------

-- Возвращает все категории досок.
create procedure sp_categories_get_all ()
begin
	select id, `name` from categories;
end|

-- Добавляет новую категорию с именем _name.
--
-- Аргументы:
-- _name - имя новой категории.
create procedure sp_categories_add
(
	_name varchar(50)
)
begin
	insert into categories (`name`) values (_name);
end|

-- Удаляет категорию с идентификатором _id.
--
-- Аргументы:
-- _id - идентификатор категории для удаления.
create procedure sp_categories_delete
(
	_id int
)
begin
	delete from categories where id = _id;
end|

------------------------------
-- Работа с пользователями. --
------------------------------

-- Получает настройки ползователя с ключевым заданным ключевым словом.
--
-- Аргументы:
-- _keyword - хеш ключевого слова.
create procedure sp_users_get_settings
(
	_keyword varchar(32)
)
begin
	declare user_id int;
	select id into user_id from users where keyword = _keyword;

	select u.id, u.posts_per_thread, u.threads_per_page, u.lines_per_post,
		l.`name` as `language`, s.`name` as `stylesheet`, u.rempass, u.`goto`
	from users u
	join stylesheets s on u.stylesheet = s.id
	join languages l on u.`language` = l.id
	where u.keyword = _keyword;

	select g.`name` from user_groups ug
	join users u on ug.`user` = u.id and u.id = user_id
	join groups g on ug.`group` = g.id;
end|

-- Редактирует настройки пользователя с ключевым словом _keyword или добавляет
-- нового.
--
-- Аргументы:
-- _keyword - хеш ключевого слова
-- _threads_per_page - количество нитей на странице предпросмотра доски
-- _posts_per_thread - количество сообщений в предпросмотре треда
-- _lines_per_post - максимальное количество строк в предпросмотре сообщения
-- _stylesheet - стиль оформления
-- _language - язык
-- _rempass - пароль для удаления сообщений
-- _goto - перенаправление при постинге
create procedure sp_users_edit_bykeyword
(
	_keyword varchar(32),
	_threads_per_page int,
	_posts_per_thread int,
	_lines_per_post int,
	_stylesheet int,
	_language int,
	_rempass varchar(12),
	_goto varchar(32)
)
begin
	declare user_id int;
	set @user_id = null;
	select id into user_id from users where keyword = _keyword;
	if(user_id is null)
	then
		-- Создаём ногового пользователя
		insert into users (keyword, threads_per_page, posts_per_thread,
			lines_per_post, stylesheet, `language`, rempass, `goto`)
		values (_keyword, _threads_per_page, _posts_per_thread,
			_lines_per_post, _stylesheet, _language, _rempass, _goto);
		select last_insert_id() into user_id;
		insert into user_groups (`user`, `group`) select user_id, id from groups
			where name = 'Users';
	else
		-- Редактируем настройки существующего
		update users set threads_per_page = _threads_per_page,
			posts_per_thread = _posts_per_thread,
			lines_per_post = _lines_per_post,
			stylesheet = _stylesheet,
			`language` = _language,
			rempass = _rempass,
			`goto` = _goto
		where id = user_id;
	end if;
end|

-- Выбирает всех пользователей.
create procedure sp_users_get_all ()
begin
	select id from users;
end|

-----------------------------------
-- Работа со стилями оформления. --
-----------------------------------

-- Возвращает все стили оформления.
create procedure sp_stylesheets_get_all ()
begin
	select id, `name` from stylesheets;
end|

-- Добавляет новый стиль оформления с именем _name.
--
-- Аргументы:
-- _name - имя нового стиля оформления.
create procedure sp_stylesheets_add
(
	_name varchar(50)
)
begin
	insert into stylesheets (`name`) values (_name);
end|

-- Удаляет стиль оформления.
--
-- Аргументы:
-- _id - идентификатор стиля для удаления.
create procedure sp_stylesheets_delete
(
	_id int
)
begin
	delete from stylesheets where id = _id;
end|

-----------------------
-- Работа с языками. --
-----------------------

create procedure sp_languages_get_all ()
begin
	select id, `name` from languages;
end|

-- Добавляет новый язык с именем _name.
--
-- Аргументы:
-- _name - имя нового языка.
create procedure sp_languages_add
(
	_name varchar(50)
)
begin
	insert into languages (`name`) values (_name);
end|

-- Удаляет язык с идентификатором _id.
--
-- Аргументы:
-- _id - идентификатор языка для удаления.
create procedure sp_languages_delete
(
	_id int
)
begin
	delete from languages where id = _id;
end|

------------------------
-- Работа с группами. --
------------------------

-- Выбирает все группы.
create procedure sp_groups_get_all ()
begin
	select id, `name` from groups order by id;
end|

-- Добавляет группу с именем _group_name, а так же стандартные разрешения на
-- чтение.
--
-- Аргументы:
-- _group_name - имя группы.
create procedure sp_groups_add
(
	_name varchar(50)
)
begin
	declare group_id int;
	insert into groups (`name`) values (_name);
	select id into group_id from groups where name = _name;
	-- Стандартные права как для Гостя
	insert into acl (`group`, `view`, `change`, moderate) values (group_id, 1, 0, 0);
end|

-- Удаляет группу с идентификатором _id, а так же всех пользователей, которые
-- входят в эту группу и все права, которые заданы для этой группы.
--
-- Аргументы:
-- _id - идентификатор групы.
create procedure sp_groups_delete
(
	_id int
)
begin
	-- TODO: Сделать просто каскадное удаление
	delete from acl where `group` = _id;
	delete from user_groups where `group` = _id;
	delete from groups where id = _id;
end|

-------------------------------------------------------
-- Работа с закреплениями пользователей за группами. --
-------------------------------------------------------

-- Выбирает закрепления пользователей за группами.
create procedure sp_user_groups_get_all ()
begin
	select `user`, `group` from user_groups order by `user`, `group`;
end|

-- Добавляет пользователя с идентификатором user_id в группу с идентификатором
-- group_id.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
-- group_id - идентификатор группы.
create procedure sp_user_groups_add
(
	user_id int,
	group_id int
)
begin
	insert into user_groups (`user`, `group`) values (user_id, group_id);
end|

-- Переносит пользователя с идентификатором user_id из группы с идентификатором
-- old_group_id в группу с идентификатором new_group_id.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
-- old_group_id - идентификатор старой группы.
-- new_group_id - идентификатор новой группы.
create procedure sp_user_groups_edit
(
	user_id int,
	old_group_id int,
	new_group_id int
)
begin
	update user_groups set `group` = new_group_id
	where `user` = user_id and `group` = old_group_id;
end|

-- Удаляет пользователя с идентификатором user_id из группы с идентификатором
-- group_id.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
-- group_id - идентификатор группы.
create procedure sp_user_groups_delete
(
	user_id int,
	group_id int
)
begin
	delete from user_groups where `user` = user_id and `group` = group_id;
end|

-----------------------------------------
-- Работа со списком контроля доступа. --
-----------------------------------------

-- Выбирает список контроля доступа.
create procedure sp_acl_get_all ()
begin
	select `group`, `board`, `thread`, `post`, `view`, `change`, `moderate`
	from acl order by `group`, `board`, `thread`, `post`;
end|

-- Редактирует запись в списке контроля доступа.
--
-- Аргументы:
-- group_id - идентификатор группы или null для всех групп.
-- board_id - идентификатор доски или null для всех досок.
-- thread_id - идентификатор нити или null для всех нитей.
-- post_id - идентификатор сообщения или null для всех сообщений.
-- _view - право на чтение.
-- _change - право на изменение.
-- _moderate - право на модерирование.
create procedure sp_acl_edit
(
	group_id int,
	board_id int,
	thread_num int,
	post_num int,
	_view bit,
	_change bit,
	_moderate bit
)
begin
	update acl set `view` = _view, `change` = _change, `moderate` = _moderate
	where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
		and ((`board` = board_id) or (coalesce(`board`, board_id) is null))
		and ((`thread` = thread_num) or (coalesce(`thread`, thread_num) is null))
		and ((`post` = post_num) or (coalesce(`post`, post_num) is null));
end|

-- Удаляет запись из списка контроля доступа.
--
-- Аргументы:
-- group_id - идентификатор группы или null для всех групп.
-- board_id - идентификатор доски или null для всех досок.
-- thread_id - идентификатор нити или null для всех нитей.
-- post_id - идентификатор сообщения или null для всех сообщений.
create procedure sp_acl_delete
(
	group_id int,
	board_id int,
	thread_id int,
	post_id int
)
begin
	delete from acl
	where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
		and ((`board` = board_id) or (coalesce(`board`, board_id) is null))
		and ((`thread` = thread_id) or (coalesce(`thread`, thread_id) is null))
		and ((`post` = post_id) or (coalesce(`post`, post_id) is null));
end|

-- Добавляет новую запись в список контроля доступа.
--
-- Аргументы:
-- group_id - идентификатор группы или null для всех групп.
-- board_id - идентификатор доски или null для всех досок.
-- thread_id - идентификатор нити или null для всех нитей.
-- post_id - идентификатор сообщения или null для всех сообщений.
-- _view - право на чтение. 0 или 1.
-- _change - право на изменение. 0 или 1.
-- _moderate - право на модерирование. 0 или 1.
create procedure sp_acl_add
(
	group_id int,
	board_id int,
	thread_num int,
	post_num int,
	_view bit,
	_change bit,
	_moderate bit
)
begin
	insert into acl (`group`, `board`, `thread`, `post`, `view`, `change`,
		`moderate`)
	values (group_id, board_id, thread_num, post_num, _view, _change,
		_moderate);
end|

------------------------------------------------
-- Работа с обработчиками загружаемых файлов. --
------------------------------------------------

-- Выбирает все обработчики загружаемых файлов.
create procedure sp_upload_handlers_get_all ()
begin
	select id, `name` from upload_handlers;
end|

-- Добавляет новый обработчик загружаемых файлов.
--
-- Аргументы:
-- _name - имя нового обработчика загружаемых файлов.
create procedure sp_upload_handlers_add
(
	_name varchar(50)
)
begin
	insert into upload_handlers (`name`) values (_name);
end|

--
create procedure sp_upload_handlers_delete
(
	_id int
)
begin
	delete from upload_handlers where id = _id;
end|

--------------------------------------------
-- Работа с обработчиками удаления нитей. --
--------------------------------------------

-- Выбирает все обработчики удаления нитей.
create procedure sp_popdown_handlers_get_all ()
begin
	select id, `name` from popdown_handlers;
end|

-- Добавляет новый обработчик удаления нитей.
--
-- Аргументы:
-- _name - имя нового обработчика удаления нитей.
create procedure sp_popdown_handlers_add
(
	_name varchar(50)
)
begin
	insert into popdown_handlers (`name`) values (_name);
end|

--
create procedure sp_popdown_handlers_delete
(
	_id int
)
begin
	delete from popdown_handlers where id = _id;
end|

-----------------------------------------
-- Работа с типами загружаемых файлов. --
-----------------------------------------

-- Выбирает все типы загружаемых файлов.
create procedure sp_upload_types_get_all ()
begin
	select id, extension, store_extension, is_image, upload_handler,
		thumbnail_image
	from upload_types;
end|

-- Выбирает типы файлов, доступных для загрузки на доске с идентификатором
-- board_id.
--
-- Аргументы:
-- board_id - идентификатор доски.
create procedure sp_upload_types_get_board
(
	board_id int
)
begin
	select ut.id, ut.extension, ut.store_extension, ut.is_image, ut.upload_handler,
		uh.`name` as upload_handler_name, ut.thumbnail_image
	from upload_types ut
	join board_upload_types but on ut.id = but.upload_type and but.board = board_id
	join upload_handlers uh on uh.id = ut.upload_handler;
end|

-- Редактирует тип загружаемых файлов.
--
-- Аргументы:
-- _id - идентификатор типа.
-- _store_extension - сохраняемое расширение файла.
-- _is_image - файлы этого типа являются изображениями.
-- _upload_handler_id - идентификатор обработчика загружаемых файлов.
-- _thumbnail_image - имя картинки для файлов, не являющихся изображением.
create procedure sp_upload_types_edit
(
	_id int,
	_store_extension varchar(10),
	_is_image bit,
	_upload_handler_id int,
	_thumbnail_image varchar(256)
)
begin
	update upload_types set store_extension = _store_extension,
		is_image = _is_image, upload_handler = _upload_handler_id,
		thumbnail_image = _thumbnail_image
	where id = _id;
end|

-- Добавляет новый тип загружаемых файлов.
--
-- Аргументы:
-- _extension - расширение файла.
-- _store_extension - сохраняемое расширение файла.
-- _is_image - Флаг типа файлов изображений.
-- _upload_handler_id - идентификатор обработчика загружаемых файлов.
-- _thumbnail_image - имя картинки для файлов, не являющихся изображением.
create procedure sp_upload_types_add
(
	_extension varchar(10),
	_store_extension varchar(10),
	_is_image bit,
	_upload_handler_id int,
	_thumbnail_image varchar(256)
)
begin
	insert into upload_types (extension, store_extension, is_image,
		upload_handler, thumbnail_image)
	values (_extension, _store_extension, _is_image, _upload_handler_id,
		_thumbnail_image);
end|

--
create procedure sp_upload_types_delete
(
	_id int
)
begin
	delete from upload_types where id = _id;
end|

-----------------------------------------------
-- Работа со связями типов файлов с досками. --
-----------------------------------------------

-- Выбирает все связи типов файлов с досками.
create procedure sp_board_upload_types_get_all ()
begin
	select board, upload_type from board_upload_types;
end|

-- Добавляет связь типа загружаемого файла с доской.
--
-- Аргументы:
-- _board - идентификатор доски.
-- _upload_type - идтенификатор типа загружаемого файла.
create procedure sp_board_upload_types_add
(
	_board int,
	_upload_type int
)
begin
	insert into board_upload_types (board, upload_type)
	values (_board, _upload_type);
end|

-- Удаляет связь типа загружаемого файла с доской.
--
-- Аргументы:
-- _board - идентификатор доски.
-- _upload_type - идтенификатор типа загружаемого файла.
create procedure sp_board_upload_types_delete
(
	_board int,
	_upload_type int
)
begin
	delete from board_upload_types
	where board = _board and upload_type = _upload_type;
end|

----------------------
-- Работа с нитями. --
----------------------

-- Выбирает все нити.
create procedure sp_threads_get_all ()
begin
	select id, board, original_post, bump_limit, sticky, sage, with_files
	from threads
	where deleted = 0 and archived = 0
	order by id desc;
end|

-- Выбирает все нити, помеченные для архивирования.
create procedure sp_threads_get_all_archived ()
begin
	select id, board, original_post, bump_limit, sticky, sage, with_files
	from threads
	where deleted = 0 and archived = 1;
end|

-- Редактирует настройки нити.
--
-- Аргументы:
-- _id - Идентификатор нити.
-- _bump_limit - Специфичный для нити бамплимит.
-- _sticky - Флаг закрепления.
-- _sage - Флаг поднятия нити при ответе.
-- _with_files - Флаг загрузки файлов.
create procedure sp_threads_edit
(
	_id int,
	_bump_limit int,
	_sticky bit,
	_sage bit,
	_with_files bit
)
begin
	update threads set bump_limit = _bump_limit, sticky = _sticky, sage = _sage,
		with_files = _with_files
	where id = _id;
end|

-- Редактирует оригинальное сообщение нити.
--
-- Аргументы:
--_id - идентификатор нити.
-- _original_post - номер нового оригинального сообщения.
create procedure sp_threads_edit_originalpost
(
	_id int,
	_original_post int
)
begin
	update threads set original_post = _original_post
	where id = _id;
end|

-- Выбирает нити, доступные для модерирования заданному пользователю.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
create procedure sp_threads_get_all_moderate
(
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.sticky, t.sage,
		t.with_files
	from threads t
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	-- Правила для конкретной группы и нити.
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	-- Правило для всех групп и конкретной нити.
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	-- Правила для конкретной группы и доски.
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	-- Правило для всех групп и конкретной доски.
	left join acl a4 on a4.`group` is null and a4.board = t.board
	-- Правило для конкретной групы.
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.deleted = 0
		and t.archived = 0
		and ht.thread is null
		-- Нить должна быть доступна для просмотра.
			-- Просмотр нити не запрещен конкретной группе и
		and ((a1.`view` = 1 or a1.`view` is null)
			-- просмотр нити не запрещен всем группам и
			and (a2.`view` = 1 or a2.`view` is null)
			-- просмотр доски не запрещен конкретной группе и
			and (a3.`view` = 1 or a3.`view` is null)
			-- просмотр доски не запрещен всем группам и
			and (a4.`view` = 1 or a4.`view` is null)
			-- просмотр разрешен конкретной группе.
			and a5.`view` = 1)
		-- Нить должна быть доступна для редактирования.
			-- Редактирование нити разрешено конкретной группе или
		and (a1.change = 1
			-- редактирование нити не запрещено конкретной группе и разрешено
			-- всем группам или
			or (a1.change is null and a2.change = 1)
			-- редактирование нити не запрещено ни конкретной группе ни всем, и
			-- конкретной группе редактирование разрешено.
			or (a1.change is null and a2.change is null and a5.change = 1))
		-- Нить должна быть доступна для модерирования
			-- Модерирование нити разрешено конкретной группе или
		and (a1.moderate = 1
			-- модерирование нити не запрещено конкретной группе и разрешено
			-- всем группам или
			or (a1.moderate is null and a2.moderate = 1)
			-- модерирование нити не запрещено ни конкретной группе ни всем, и
			-- конкретной группе модерирование разрешено.
			or (a1.moderate is null and a2.moderate is null and a5.moderate = 1))
	group by t.id
	order by t.id desc;
end|

-- Выбирает доступные для просмотра пользователю нити и количество сообщений в
-- них, с заданной страницы доски.
--
-- Аргументы:
-- board_id - идентификатор доски.
-- page - номер страницы.
-- user_id - идентификатор пользователя.
-- threads_per_page - количество нитей на странице.
-- sticky - фалг закрепления.
create procedure sp_threads_get_board_view
(
	board_id int,
	page int,
	user_id int,
	threads_per_page int,
	sticky bit
)
begin
	-- Потому что в limit нельзя использовать переменные.
	prepare stmnt from
		'-- Выберем нити, отсортированные по последнему сообщению без сажи.
		select q1.id, q1.original_post, q1.bump_limit, q1.sticky, q1.sage, q1.with_files,
			q1.posts_count, q1.last_post_num
		from (
			-- Без учёта постов с сажей вычислим последнее сообщение в нити.
			select q.id, q.original_post, q.bump_limit, q.sticky, q.sage, q.with_files,
				q.posts_count, max(p.`number`) as last_post_num
			from posts p
			join (
				-- Выберем видимые нити и подсчитаем количество видимых сообщений.
				select t.id, t.original_post, t.bump_limit, t.sticky, t.sage, t.with_files,
					count(distinct p.id) as posts_count
				from posts p
				join threads t on t.id = p.thread and t.board = ?
				join user_groups ug on ug.`user` = ?
				left join hidden_threads ht on ht.thread = t.id and ht.`user` = ug.`user`
				-- Правило для конкретной группы и сообщения.
				left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
				-- Правило для всех групп и конкретного сообщения.
				left join acl a2 on a2.`group` is null and a2.post = p.id
				-- Правила для конкретной группы и нити.
				left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
				-- Правило для всех групп и конкретной нити.
				left join acl a4 on a4.`group` is null and a4.thread = p.thread
				-- Правила для конкретной группы и доски.
				left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
				-- Правило для всех групп и конкретной доски.
				left join acl a6 on a6.`group` is null and a6.board = p.board
				-- Правило для конкретной групы.
				left join acl a7 on a7.`group` = ug.`group` and a7.board is null
					and a7.thread is null and a7.post is null
				where t.deleted = 0
					and t.archived = 0
					and t.sticky = ?
					and ht.thread is null
					and p.deleted = 0
					-- Нить должна быть доступна для просмотра.
						-- Просмотр нити не запрещен конкретной группе и
					and ((a3.`view` = 1 or a3.`view` is null)
						-- просмотр нити не запрещен всем группам и
						and (a4.`view` = 1 or a4.`view` is null)
						-- просмотр доски не запрещен конкретной группе и
						and (a5.`view` = 1 or a5.`view` is null)
						-- просмотр доски не запрещен всем группам и
						and (a6.`view` = 1 or a6.`view` is null)
						-- просмотр разрешен конкретной группе.
						and a7.`view` = 1)
					-- Сообщение должно быть доступно для просмотра, чтобы правильно
					-- подсчитать их количество в нити.
						-- Просмотр сообщения не запрещен конкретной группе и
					and ((a1.`view` = 1 or a1.`view` is null)
						-- просмотр сообщения не запрещен всем группам и
						and (a2.`view` = 1 or a2.`view` is null)
						-- просмотр нити не запрещен конкретной группе и
						and (a3.`view` = 1 or a3.`view` is null)
						-- просмотр нити не запрещен всем группам и
						and (a4.`view` = 1 or a4.`view` is null)
						-- просмотр доски не запрещен конкретной группе и
						and (a5.`view` = 1 or a5.`view` is null)
						-- просмотр доски не запрещен всем группам и
						and (a6.`view` = 1 or a6.`view` is null)
						-- просмотр разрешен конкретной группе.
						and a7.`view` = 1)
				group by t.id) q on q.id = p.thread and (p.sage = 0 or p.sage is null)
			group by q.id) q1
		order by q1.last_post_num desc
		limit ? offset ?';
	-- Потому что в prepare можно использовать только переменные.
	set @board_id = board_id;
	set @user_id = user_id;
	set @limit = threads_per_page;
	set @sticky = sticky;
	if(page = 1) then
		set @offset = 0;
	else
		set @offset = threads_per_page * (page - 1);
	end if;
	execute stmnt using @board_id, @user_id, @sticky, @limit, @offset;
	deallocate prepare stmnt;
end|

-- Вычисляет количество нитей, доступных для просмотра заданному пользователю
-- на заданной доске.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
-- board_id - идентификатор доски.
create procedure sp_threads_get_view_threadscount
(
	user_id int,
	board_id int
)
begin
	select count(q.id) as threads_count
	from (select t.id
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on ht.thread = t.id and ht.`user` = ug.`user`
	-- Правила для конкретной группы и нити.
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	-- Правило для всех групп и конкретной нити.
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	-- Правила для конкретной группы и доски.
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	-- Правило для всех групп и конкретной доски.
	left join acl a4 on a4.`group` is null and a4.board = t.board
	-- Правило для конкретной групы.
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.board = board_id
		and t.deleted = 0
		and t.archived = 0
		and ht.thread is null
		-- Нить должна быть доступна для просмотра.
			-- Просмотр нити не запрещен конкретной группе и
		and ((a1.`view` = 1 or a1.`view` is null)
			-- просмотр нити не запрещен всем группам и
			and (a2.`view` = 1 or a2.`view` is null)
			-- просмотр доски не запрещен конкретной группе и
			and (a3.`view` = 1 or a3.`view` is null)
			-- просмотр доски не запрещен всем группам и
			and (a4.`view` = 1 or a4.`view` is null)
			-- просмотр разрешен конкретной группе.
			and a5.`view` = 1)
	group by t.id) q;
end|

-- Выбирает доступную для просмотра пользователю нить с заданной страницы доски,
-- и количество сообщений в ней.
--
-- Аргументы:
-- board_id - идентификатор доски.
-- thread_num - номер нити.
-- user_id - идентификатор пользователя.
create procedure sp_threads_get_specifed_view
(
	board_id int,
	thread_num int,
	user_id int
)
begin
	declare thread_id int;
	select id into thread_id from threads
	where original_post = thread_num and board = board_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, t.original_post, t.bump_limit, t.sticky, t.archived, t.sage,
			t.with_files, count(p.id) as visible_posts_count
		from posts p
		join threads t on t.id = p.thread
		join user_groups ug on ug.`user` = user_id
		left join hidden_threads ht on t.id = ht.thread
			and ug.`user` = ht.`user`
		-- Правило для конкретной группы и сообщения.
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		-- Правило для всех групп и конкретного сообщения.
		left join acl a2 on a2.`group` is null and a2.post = p.id
		-- Правила для конкретной группы и нити.
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		-- Правило для всех групп и конкретной нити.
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		-- Правила для конкретной группы и доски.
		left join acl a5 on a5.`group` = ug.`group` and a5.board = t.board
		-- Правило для всех групп и конкретной доски.
		left join acl a6 on a6.`group` is null and a6.board = t.board
		-- Правило для конкретной групы.
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null
			and a7.thread is null and a7.post is null
		where t.id = thread_id
			and (t.deleted = 0 or t.deleted is null)
			and ht.thread is null
			and (p.deleted = 0 or p.deleted is null)
			-- Нить должна быть доступна для просмотра.
				-- Просмотр нити не запрещен конкретной группе и
			and ((a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
			-- Сообщение должно быть доступно для просмотра, чтобы правильно
			-- подсчитать количество видимых сообщений в нити.
				-- Просмотр сообщения не запрещен конкретной группе и
			and ((a1.`view` = 1 or a1.`view` is null)
				-- просмотр сообщения не запрещен всем группам и
				and (a2.`view` = 1 or a2.`view` is null)
				-- просмотр нити не запрещен конкретной группе и
				and (a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
		group by t.id;
	end if;
end|

-- Получает доступную для просмотра пользователю скрытую нить с заданной доски
-- и количество сообщений в ней.
--
-- Аргументы:
-- board_id - идентификатор доски.
-- thread_num - номер нити.
-- user_id - идентификатор пользователя.
create procedure sp_threads_get_specifed_view_hiden
(
	board_id int,
	thread_num int,
	user_id int
)
begin
	declare thread_id int;
	select id into thread_id from threads
	where original_post = thread_num and board = board_id;
	if thread_id is null
	then
		select 'NOT_FOUND' as error;
	else
		select t.id, t.original_post, t.bump_limit, t.sticky, t.archived,
			t.sage, t.with_files, count(p.id) as visible_posts_count
		from posts p
		join threads t on t.id = p.thread
		join user_groups ug on ug.`user` = user_id
		left join hidden_threads ht on t.id = ht.thread
			and ug.`user` = ht.`user`
		-- Правило для конкретной группы и сообщения.
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		-- Правило для всех групп и конкретного сообщения.
		left join acl a2 on a2.`group` is null and a2.post = p.id
		-- Правила для конкретной группы и нити.
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		-- Правило для всех групп и конкретной нити.
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		-- Правила для конкретной группы и доски.
		left join acl a5 on a5.`group` = ug.`group` and a5.board = t.board
		-- Правило для всех групп и конкретной доски.
		left join acl a6 on a6.`group` is null and a6.board = t.board
		-- Правило для конкретной групы.
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null
			and a7.thread is null and a7.post is null
		where t.id = thread_id
			and t.deleted = 0
			and ht.thread is not null
			and p.deleted = 0
			-- Нить должна быть доступна для просмотра.
				-- Просмотр нити не запрещен конкретной группе и
			and ((a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
			-- Сообщение должно быть доступно для просмотра, чтобы правильно
			-- подсчитать количество видимых сообщений в нити.
				-- Просмотр сообщения не запрещен конкретной группе и
			and ((a1.`view` = 1 or a1.`view` is null)
				-- просмотр сообщения не запрещен всем группам и
				and (a2.`view` = 1 or a2.`view` is null)
				-- просмотр нити не запрещен конкретной группе и
				and (a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
		group by t.id;
	end if;
end|

-- Выбирает нить доступную для редактирования заданному пользователю.
--
-- Аргументы:
-- thread_id - Идентификатор нити.
-- user_id - Идентификатор пользователя.
create procedure sp_threads_get_specifed_change
(
	thread_id int,
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.archived, t.sage,
		t.with_files
	from threads t
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	-- Правила для конкретной группы и нити.
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	-- Правило для всех групп и конкретной нити.
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	-- Правила для конкретной группы и доски.
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	-- Правило для всех групп и конкретной доски.
	left join acl a4 on a4.`group` is null and a4.board = t.board
	-- Правило для конкретной групы.
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.id = thread_id
		and (t.deleted = 0 or t.deleted is null)
		and ht.thread is null
		-- Нить должна быть доступна для просмотра.
			-- Просмотр нити не запрещен конкретной группе и
		and ((a1.`view` = 1 or a1.`view` is null)
			-- просмотр нити не запрещен всем группам и
			and (a2.`view` = 1 or a2.`view` is null)
			-- просмотр доски не запрещен конкретной группе и
			and (a3.`view` = 1 or a3.`view` is null)
			-- просмотр доски не запрещен всем группам и
			and (a4.`view` = 1 or a4.`view` is null)
			-- просмотр разрешен конкретной группе.
			and a5.`view` = 1)
		-- Нить должна быть доступна для редактирования.
			-- Редактирование нити разрешено конкретной группе или
		and (a1.change = 1
				-- редактирование нити не запрещено конкретной группе и
				-- разрешено всем группам или
				or (a1.change is null and a2.change = 1)
				-- редактирование нити не запрещено ни конкретной группе ни
				-- всем, и конкретной группе редактирование разрешено.
				or (a1.change is null and a2.change is null and a5.change = 1))
	group by t.id;
end|

-- Проверяет, доступна ли нить для модерирования пользователю.
--
-- Аргументы:
-- thread_id - идентификатор нити.
-- user_id - идентификатор пользователя.
create procedure sp_threads_check_specifed_moderate
(
	thread_id int,
	user_id int
)
begin
	select t.id
	from threads t
	join user_groups ug on ug.`user` = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.`user` = ht.`user`
	-- Правила для конкретной группы и нити.
	left join acl a1 on a1.`group` = ug.`group` and a1.thread = t.id
	-- Правило для всех групп и конкретной нити.
	left join acl a2 on a2.`group` is null and a2.thread = t.id
	-- Правила для конкретной группы и доски.
	left join acl a3 on a3.`group` = ug.`group` and a3.board = t.board
	-- Правило для всех групп и конкретной доски.
	left join acl a4 on a4.`group` is null and a4.board = t.board
	-- Правило для конкретной групы.
	left join acl a5 on a5.`group` = ug.`group` and a5.board is null
		and a5.thread is null and a5.post is null
	where t.id = thread_id
		and (t.deleted = 0 or t.deleted is null)
		and	(t.archived = 0 or t.archived is null)
		and ht.thread is null
		-- Нить должна быть доступна для просмотра.
			-- Просмотр нити не запрещен конкретной группе и
		and ((a1.`view` = 1 or a1.`view` is null)
			-- просмотр нити не запрещен всем группам и
			and (a2.`view` = 1 or a2.`view` is null)
			-- просмотр доски не запрещен конкретной группе и
			and (a3.`view` = 1 or a3.`view` is null)
			-- просмотр доски не запрещен всем группам и
			and (a4.`view` = 1 or a4.`view` is null)
			-- просмотр разрешен конкретной группе.
			and a5.`view` = 1)
		-- Нить должна быть доступна для редактирования.
			-- Редактирование нити разрешено конкретной группе или
		and (a1.change = 1
				-- редактирование нити не запрещено конкретной группе и разрешено всем группам или
				or (a1.change is null and a2.change = 1)
				-- редактирование нити не запрещено ни конкретной группе ни всем, и конкретной группе редактирование разрешено.
				or (a1.change is null and a2.change is null and a5.change = 1))
		-- Нить должна быть доступна для модерирования
			-- Модерирование нити разрешено конкретной группе или
		and (a1.moderate = 1
			-- модерирование нити не запрещено конкретной группе и разрешено всем группам или
			or (a1.moderate is null and a2.moderate = 1)
			-- модерирование нити не запрещено ни конкретной группе ни всем, и конкретной группе модерирование разрешено.
			or (a1.moderate is null and a2.moderate is null and a5.moderate = 1))
	group by t.id;
end|

-- Создаёт нить. Если номер оригинального сообщения null, то будет создана
-- пустая нить.
--
-- Аргументы:
-- _board_id - Идентификатор доски.
-- _original_post - Номер оригинального сообщения нити.
-- _bump_limit - Специфичный для нити бамплимит.
-- _sage - Не поднимать нить ответами.
-- _with_files - Флаг прикрепления файлов к ответам в нить.
create procedure sp_threads_add
(
	_board_id int,
	_original_post int,
	_bump_limit int,
	_sage bit,
	_with_files bit
)
begin
	declare thread_id int;
	insert into threads (board, original_post, bump_limit, deleted, archived,
		sage, with_files)
	values (_board_id, _original_post, _bump_limit, 0, 0,
		_sage, _with_files);
	select last_insert_id() into thread_id;
	select * from threads where id = thread_id;
end|

-- Оставляет не помеченными на архивирование нити заданной доски, суммарное
-- количество сообщений в которых не более чем x * бамплимит доски.
--
-- Аргументы:
-- board_id - идентификатор доски.
-- x - множитель.
create procedure sp_threads_edit_archived_postlimit
(
	board_id int,
	x int
)
begin
	declare board_bump_limit int;
	declare done int default 0;
	declare thread_id int;
	declare posts_count int;
	declare total int default 0;
	declare `c` cursor for
		select q2.id, q2.posts_count
		from (
			-- Без учёта постов с сажей вычислим последнее сообщение в нити.
			select q1.id, q1.posts_count, max(p.`number`) as last_post_num
			from posts p
			join(
				-- Выберем все нити и подсчитаем количество сообщений в них.
				select t.id, count(distinct p.id) as posts_count
				from posts p
				join threads t on t.id = p.thread and t.board = board_id
				where t.deleted = 0 and t.archived = 0 and p.deleted = 0
				group by t.id) q1 on q1.id = p.thread
					and (p.sage = 0 or p.sage is null)
			group by q1.id) q2
		order by q2.last_post_num desc;
	declare continue handler for not found set done = 1;
	select bump_limit into board_bump_limit from boards where id = board_id;
	set x = x * board_bump_limit;
	open `c`;
	repeat
	fetch `c` into thread_id, posts_count;
	if(not done) then
		set total = total + posts_count;
		if(total > x) then
			update threads set archived = 1 where id = thread_id;
		end if;
	end if;
	until done end repeat;
	close `c`;
end|

---------------------------
-- Работа с сообщениями. --
---------------------------

-- Выбирает posts_per_thread сообщений и оригинальное сообщение для нити с
-- идентификатором thread_id, доступных для чтения пользователю с
-- идентификатором user_id.
--
-- Аргументы:
-- thread_id - идентификатор нити.
-- user_id - идентификатор пользователя.
-- posts_per_thread - количество сообщений, которое необходимо вернуть.
-- TODO Если ориг. сообщение не доступно для просмотра, то нить так же должна
-- быть недоступна для просмотра.
-- TODO Если ориг. сообщение помечено на удаление, то нить помечается на
-- удаление целиком.
create procedure sp_posts_get_thread_view
(
	thread_id int,
	user_id int,
	posts_per_thread int
)
begin
	prepare stmnt from
		'select q.id, q.thread, q.number, q.password, q.name, q.tripcode, q.ip,
			q.subject, q.date_time, q.text, q.sage
		from (select p.id, p.thread, p.number, p.password, p.name, p.tripcode,
			p.ip, p.subject, p.date_time, p.text, p.sage
		from posts p
		join threads t on t.board = p.board and t.id = p.thread
		join user_groups ug on ug.user = ?
		-- Правило для конкретной группы и сообщения.
		left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
		-- Правило для всех групп и конкретного сообщения.
		left join acl a2 on a2.`group` is null and a2.post = p.id
		-- Правила для конкретной группы и нити.
		left join acl a3 on a3.`group` = ug.`group` and a3.thread = t.id
		-- Правило для всех групп и конкретной нити.
		left join acl a4 on a4.`group` is null and a4.thread = t.id
		-- Правила для конкретной группы и доски.
		left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
		-- Правило для всех групп и конкретной доски.
		left join acl a6 on a6.`group` is null and a6.board = p.board
		-- Правило для конкретной групы.
		left join acl a7 on a7.`group` = ug.`group` and a7.board is null and a7.thread is null and a7.post is null
		where p.thread = ?
			and p.number != t.original_post
			and (p.deleted = 0 or p.deleted is null)
			-- Сообщение должно быть доступно для просмотра, чтобы правильно подсчитать их количество в нити.
				-- Просмотр сообщения не запрещен конкретной группе и
			and ((a1.`view` = 1 or a1.`view` is null)
				-- просмотр сообщения не запрещен всем группам и
				and (a2.`view` = 1 or a2.`view` is null)
				-- просмотр нити не запрещен конкретной группе и
				and (a3.`view` = 1 or a3.`view` is null)
				-- просмотр нити не запрещен всем группам и
				and (a4.`view` = 1 or a4.`view` is null)
				-- просмотр доски не запрещен конкретной группе и
				and (a5.`view` = 1 or a5.`view` is null)
				-- просмотр доски не запрещен всем группам и
				and (a6.`view` = 1 or a6.`view` is null)
				-- просмотр разрешен конкретной группе.
				and a7.`view` = 1)
		group by p.id
		order by number desc
		limit ?) q
		union all
		select p.id, p.thread, p.number, p.password, p.name, p.tripcode, p.ip,
			p.subject, p.date_time, p.text, p.sage
		from posts p
		join threads t on t.board = p.board and t.id = p.thread
		where p.number = t.original_post and p.thread = ?
		order by number asc';
	set @user_id = user_id;
	set @thread_id = thread_id;
	set @limit = posts_per_thread;
	execute stmnt using @user_id, @thread_id, @limit, @thread_id;
	deallocate prepare stmnt;
end|

-- Выбирает все сообщения заданной нити.
--
-- Аргументы:
-- thread_id - идентификатор нити.
create procedure sp_posts_get_thread
(
	thread_id int
)
begin
	select id, thread, `number`, password, `name`, tripcode, ip, subject,
		date_time, text, sage
	from posts p
	where thread = thread_id;
end|

-- Выбирает сообщение по номеру.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- post_num - Номер сообщения.
-- user_id - Идентификатор пользователя.
create procedure sp_posts_get_specifed_view_bynumber
(
	board_id int,
	post_num int,
	user_id int
)
begin
	select p.id, p.thread, p.`number`, p.password, p.`name`, p.tripcode, p.ip,
		p.subject, p.date_time, p.text, p.sage
	from posts p
	join user_groups ug on ug.`user` = user_id
	-- Правило для конкретной группы и сообщения.
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	-- Правило для всех групп и конкретного сообщения.
	left join acl a2 on a2.`group` is null and a2.post = p.id
	-- Правила для конкретной группы и нити.
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	-- Правило для всех групп и конкретной нити.
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	-- Правила для конкретной группы и доски.
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	-- Правило для всех групп и конкретной доски.
	left join acl a6 on a6.`group` is null and a6.board = p.board
	-- Правило для конкретной групы.
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null and
		a7.thread is null and a7.post is null
	where p.board = board_id
		and p.`number` = post_num
		and p.deleted = 0
		-- Сообщение должно быть доступно для просмотра.
			-- Просмотр сообщения не запрещен конкретной группе и
		and ((a1.`view` = 1 or a1.`view` is null)
			-- просмотр сообщения не запрещен всем группам и
			and (a2.`view` = 1 or a2.`view` is null)
			-- просмотр нити не запрещен конкретной группе и
			and (a3.`view` = 1 or a3.`view` is null)
			-- просмотр нити не запрещен всем группам и
			and (a4.`view` = 1 or a4.`view` is null)
			-- просмотр доски не запрещен конкретной группе и
			and (a5.`view` = 1 or a5.`view` is null)
			-- просмотр доски не запрещен всем группам и
			and (a6.`view` = 1 or a6.`view` is null)
			-- просмотр разрешен конкретной группе.
			and a7.`view` = 1)
	group by p.id;
end|

-- Добавляет сообщение в сущестующую нить.
--
-- Аргументы:
-- _board_id - идентификатор доски.
-- _thread_id - идентификатор нити.
-- _user_id - идентификатор автора.
-- _password - пароль на удаление сообщения.
-- _name - имя автора.
-- _ip - IP адрес автора.
-- _subject - тема.
-- _datetime - время получения сообщения.
-- _text - текст.
-- _sage - не поднимать нить этим сообщением.
create procedure sp_posts_add
(
	_board_id int,
	_thread_id int,
	_user_id int,
	_password varchar(128),
	_name varchar(128),
	_tripcode varchar(128),
	_ip bigint,
	_subject varchar(128),
	_datetime datetime,
	_text text,
	_sage bit
)
begin
	declare count_posts int;	-- posts in thread
	declare post_number int;	-- number on post on thread
	declare bumplimit int;		-- number of bump posts (posts which brings thread to up)
	declare threadsage bit;		-- whole thread sage
	declare post_id int;
	select max(`number`) into post_number from posts where board = _board_id;
	if(post_number is null)
	then
		set post_number = 1;
	else
		set post_number = post_number + 1;
	end if;
	select bump_limit into bumplimit from threads where id = _thread_id;
	select count(id) into count_posts from posts where thread = _thread_id;
	select sage into threadsage from threads where id = _thread_id;
	if(threadsage is not null and threadsage = 1)
	then
		set _sage = 1;
	end if;
	if(count_posts > bumplimit)
	then
		set _sage = 1;
	end if;
	if(_datetime is null)
	then
		set _datetime = now();
	end if;
	insert into posts (board, thread, `number`, `user`, password, `name`,
		tripcode, ip, subject, date_time, text, sage, deleted)
	values (_board_id, _thread_id, post_number, _user_id, _password, _name,
		_tripcode, _ip, _subject, _datetime, _text, _sage, 0);
	select last_insert_id() into post_id;
	select * from posts where id = post_id;
end|

-- Удаляет сообщение с заданным идентификатором.
--
-- Аргументы:
-- _id - Идентификатор сообщения.
create procedure sp_posts_delete
(
	_id int
)
begin
	declare thread_id int;
	set thread_id = null;
	-- Проверим, не является ли пост оригинальным. Если да, то удалим всю нить
	-- целиком.
	select p.thread into thread_id
	from posts p
	join threads t on t.id = p.thread and p.id = _id
		and p.`number` = t.original_post;
	if(thread_id is null) then
		update posts set deleted = 1 where id = _id;
	else
		update threads set deleted = 1 where id = thread_id;
		update posts set deleted = 1 where thread = thread_id;
	end if;
end|

-- Добавляет текст в конец текста заданного сообщения.
--
-- Аргументы:
-- _id - Идентификатор сообщения.
-- _text - Текст.
create procedure sp_posts_edit_specifed_addtext
(
	_id int,
	_text text
)
begin
	update posts set text = concat(text, _text) where id = _id;
end|

--------------------------------------------------------------------
-- Работа со связями сообщений и информации о загруженных файлах. --
--------------------------------------------------------------------

-- Выбирает для сообщения с идентификатором post_id его связь с информацией о
-- загруженных файлах.
create procedure sp_posts_uploads_get_post
(
	post_id int
)
begin
	select post, upload from posts_uploads where post = post_id;
end|

-- Связывает сообщение с загруженным файлом.
--
-- Аргументы:
-- _post_id - идентификатор сообщения.
-- _upload_id - идентификатор сообщения.
create procedure sp_posts_uploads_add
(
	_post_id int,
	_upload_id int
)
begin
	insert into posts_uploads (post, upload) values (_post_id, _upload_id);
end|

-- Выбирает все сообщения с номерами нитей и именами досок.
create procedure sp_posts_get_all_numbers ()
begin
	select p.`number` as post, t.`original_post` as thread, b.`name` as board
	from posts p
	join threads t on t.id = p.thread
	join boards b on b.id = p.board
	where p.deleted = 0 and t.deleted = 0 and t.archived = 0
	order by p.`number`, t.`original_post`, b.`name` asc;
end|

---------------------------------------
-- Работа с информацией о загрузках. --
---------------------------------------

-- Выбирает для сообщения информацию о загрузках.
--
-- Аргументы:
-- post_id - идентификатор сообщения.
create procedure sp_uploads_get_post
(
	post_id int
)
begin
	select id, `hash`, is_image, link_type, `file`, file_w, file_h, `size`,
		thumbnail, thumbnail_w, thumbnail_h
	from uploads u
	join posts_uploads pu on pu.upload = u.id and pu.post = post_id;
end|

-- Выбирает одинаковые файлы, загруженные на заданную доску.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- hash - Хеш файла.
-- user_id - Идентификатор пользователя.
create procedure sp_uploads_get_same
(
	_board_id int,
	_hash varchar(32),
	_user_id int
)
begin
	select u.id, u.`hash`, u.is_image, u.link_type, u.`file`, u.file_w,
		u.file_h, u.`size`, u.thumbnail, u.thumbnail_w, u.thumbnail_h,
		p.`number`, t.original_post, max(case
		when a1.`view` = 0 then 0
		when a2.`view` = 0 then 0
		when a3.`view` = 0 then 0
		when a4.`view` = 0 then 0
		when a5.`view` = 0 then 0
		when a6.`view` = 0 then 0
		when a7.`view` = 0 then 0
		else 1 end) as `view`
	from uploads u
	join posts_uploads pu on pu.upload = u.id
	join posts p on p.id = pu.post and p.board = _board_id
	join threads t on t.id = p.thread
	join user_groups ug on ug.`user` = _user_id
	-- Правило для конкретной группы и сообщения.
	left join acl a1 on a1.`group` = ug.`group` and a1.post = p.id
	-- Правило для всех групп и конкретного сообщения.
	left join acl a2 on a2.`group` is null and a2.post = p.id
	-- Правила для конкретной группы и нити.
	left join acl a3 on a3.`group` = ug.`group` and a3.thread = p.thread
	-- Правило для всех групп и конкретной нити.
	left join acl a4 on a4.`group` is null and a4.thread = p.thread
	-- Правила для конкретной группы и доски.
	left join acl a5 on a5.`group` = ug.`group` and a5.board = p.board
	-- Правило для всех групп и конкретной доски.
	left join acl a6 on a6.`group` is null and a6.board = p.board
	-- Правило для конкретной групы.
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null
		and a7.thread is null and a7.post is null
	where u.`hash` = _hash
	group by u.id, p.id;
end|

-- Сохраняет данные о загрузке.
--
-- Аргументы:
-- _hash - хеш файла.
-- _is_image - флаг картинки.
-- _link_type - тип ссылки на файл.
-- _file - файл.
-- _file_w - ширина изображения (для изображений).
-- _file_h - высота изображения (для изображений).
-- _size - размер файла в байтах.
-- _thumbnail - уменьшенная копия.
-- _thumbnail_w - ширина уменьшенной копии.
-- _thumbnail_h - высота уменьшенной копии.
create procedure sp_uploads_add
(
	_hash varchar(32),
	_is_image bit,
	_link_type int,
	_file varchar(256),
	_file_w int,
	_file_h int,
	_size int,
	_thumbnail varchar(256),
	_thumbnail_w int,
	_thumbnail_h int
)
begin
	insert into uploads (`hash`, is_image, link_type, `file`, file_w, file_h,
		`size`, thumbnail, thumbnail_w, thumbnail_h)
	values
	(_hash, _is_image, _link_type, _file, _file_w, _file_h,
		_size, _thumbnail, _thumbnail_w, _thumbnail_h);
	select last_insert_id() as id;
end|

--------------------------------
-- Работа со скрытыми нитями. --
--------------------------------

-- Выбирает нити, скрыте пользователем с идентификатором user_id на
-- доске с идентификатором board_id.
--
-- Аргументы:
-- board_id - идентификатор доски.
-- user_id - идентификатор пользователя.
create procedure sp_hidden_threads_get_board
(
	board_id int,
	user_id int
)
begin
	select t.id, t.original_post
	from hidden_threads ht
	join threads t on ht.thread = t.id and t.board = board_id
	where ht.user = user_id;
end|

-- Скрывает нить.
--
-- Аргументы:
-- thread_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
create procedure sp_hidden_threads_add
(
	thread_id int,
	user_id int
)
begin
	insert into hidden_threads (`user`, thread) values (user_id, thread_id);
end|

-- Отменяет скрытие нити.
--
-- Аргументы:
-- thread_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
create procedure sp_hidden_threads_delete
(
	thread_id int,
	user_id int
)
begin
	delete from hidden_threads where `user` = user_id and thread = thread_id;
end|