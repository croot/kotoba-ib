delimiter |

drop procedure if exists sp_acl_add|
drop procedure if exists sp_acl_delete|
drop procedure if exists sp_acl_edit|
drop procedure if exists sp_acl_get_all|

drop procedure if exists sp_bans_add|
drop procedure if exists sp_bans_check|
drop procedure if exists sp_bans_delete_by_id|
drop procedure if exists sp_bans_delete_by_ip|
drop procedure if exists sp_bans_get_all|
drop procedure if exists sp_bans_refresh|

drop procedure if exists sp_board_upload_types_add|
drop procedure if exists sp_board_upload_types_delete|
drop procedure if exists sp_board_upload_types_get_all|

drop procedure if exists sp_boards_add|
drop procedure if exists sp_boards_delete|
drop procedure if exists sp_boards_edit|
drop procedure if exists sp_boards_get_all|
drop procedure if exists sp_boards_get_by_id|
drop procedure if exists sp_boards_get_by_name|
drop procedure if exists sp_boards_get_changeable|
drop procedure if exists sp_boards_get_changeable_by_id|
drop procedure if exists sp_boards_get_changeable_by_name|
drop procedure if exists sp_boards_get_moderatable|
drop procedure if exists sp_boards_get_visible|

drop procedure if exists sp_categories_add|
drop procedure if exists sp_categories_delete|
drop procedure if exists sp_categories_get_all|

drop procedure if exists sp_files_add|
drop procedure if exists sp_files_get_by_post|
drop procedure if exists sp_files_get_same|

drop procedure if exists sp_groups_add|
drop procedure if exists sp_groups_delete|
drop procedure if exists sp_groups_get_all|

drop procedure if exists sp_hidden_threads_add|
drop procedure if exists sp_hidden_threads_delete|
drop procedure if exists sp_hidden_threads_get_by_board|
drop procedure if exists sp_hidden_threads_get_visible|

drop procedure if exists sp_images_add|
drop procedure if exists sp_images_get_by_post|
drop procedure if exists sp_images_get_same|

drop procedure if exists sp_languages_add|
drop procedure if exists sp_languages_delete|
drop procedure if exists sp_languages_get_all|

drop procedure if exists sp_links_get_by_post|

drop procedure if exists sp_macrochan_tags_add|
drop procedure if exists sp_macrochan_tags_delete_by_name|
drop procedure if exists sp_macrochan_tags_get_all|

drop procedure if exists sp_macrochan_images_add|
drop procedure if exists sp_macrochan_images_delete_by_name|
drop procedure if exists sp_macrochan_images_get_all|

drop procedure if exists sp_macrochan_tags_images_add|
drop procedure if exists sp_macrochan_tags_images_get|
drop procedure if exists sp_macrochan_tags_images_get_all|

drop procedure if exists sp_popdown_handlers_add|
drop procedure if exists sp_popdown_handlers_delete|
drop procedure if exists sp_popdown_handlers_get_all|

drop procedure if exists sp_posts_add|
drop procedure if exists sp_posts_delete|
drop procedure if exists sp_posts_delete_last|
drop procedure if exists sp_posts_delete_marked|
drop procedure if exists sp_posts_edit_text_by_id|
drop procedure if exists sp_posts_get_all|
drop procedure if exists sp_posts_get_by_board|
drop procedure if exists sp_posts_get_by_thread|
drop procedure if exists sp_posts_get_visible_by_id|
drop procedure if exists sp_posts_get_visible_by_number|
drop procedure if exists sp_posts_get_visible_by_thread|

drop procedure if exists sp_posts_files_add|
drop procedure if exists sp_posts_files_get_by_post|

drop procedure if exists sp_posts_images_add|
drop procedure if exists sp_posts_images_get_by_post|

drop procedure if exists sp_posts_links_add|
drop procedure if exists sp_posts_links_get_by_post|

drop procedure if exists sp_posts_videos_add|
drop procedure if exists sp_posts_videos_get_by_post|

drop procedure if exists sp_stylesheets_add|
drop procedure if exists sp_stylesheets_delete|
drop procedure if exists sp_stylesheets_get_all|

drop procedure if exists sp_threads_add|
drop procedure if exists sp_threads_edit|
drop procedure if exists sp_threads_edit_archived_postlimit|
drop procedure if exists sp_threads_edit_deleted|
drop procedure if exists sp_threads_edit_original_post|
drop procedure if exists sp_threads_get_all|
drop procedure if exists sp_threads_get_archived|
drop procedure if exists sp_threads_get_changeable_by_id|
drop procedure if exists sp_threads_get_moderatable|
drop procedure if exists sp_threads_get_moderatable_by_id|
drop procedure if exists sp_threads_get_visible_by_board|
drop procedure if exists sp_threads_get_visible_by_original_post|
drop procedure if exists sp_threads_get_visible_count|
drop procedure if exists sp_threads_search_visible_by_board|

drop procedure if exists sp_upload_handlers_add|
drop procedure if exists sp_upload_handlers_delete|
drop procedure if exists sp_upload_handlers_get_all|

drop procedure if exists sp_upload_types_add|
drop procedure if exists sp_upload_types_delete|
drop procedure if exists sp_upload_types_edit|
drop procedure if exists sp_upload_types_get_all|
drop procedure if exists sp_upload_types_get_by_board|

drop procedure if exists sp_user_groups_add|
drop procedure if exists sp_user_groups_delete|
drop procedure if exists sp_user_groups_edit|
drop procedure if exists sp_user_groups_get_all|

drop procedure if exists sp_users_edit_by_keyword|
drop procedure if exists sp_users_get_all|
drop procedure if exists sp_users_get_by_keyword|
drop procedure if exists sp_users_set_goto|
drop procedure if exists sp_users_set_password|

drop procedure if exists sp_videos_add|
drop procedure if exists sp_videos_get_by_post|

drop procedure if exists sp_words_add|
drop procedure if exists sp_words_delete|
drop procedure if exists sp_words_edit|
drop procedure if exists sp_words_get_all|
drop procedure if exists sp_words_get_all_by_board|

/*drop procedure if exists sp_posts_uploads_get_by_post|
drop procedure if exists sp_posts_uploads_add|
drop procedure if exists sp_posts_uploads_delete_by_post|
drop procedure if exists sp_uploads_get_by_post|
drop procedure if exists sp_uploads_get_same|
drop procedure if exists sp_uploads_add|
drop procedure if exists sp_uploads_get_dangling|
drop procedure if exists sp_uploads_delete_by_id|*/

-- ---------------------------------------
--  Работа со списком контроля доступа. --
-- ---------------------------------------

-- Добавляет правило.
--
-- Аргументы:
-- group_id - Идентификатор группы.
-- board_id - Идентификатор доски.
-- thread_id - Идентификатор нити.
-- post_id - Идентификатор сообщения.
-- _view - Право на просмотр.
-- _change - Право на изменение.
-- _moderate - Право на модерирование.
create procedure sp_acl_add
(
    group_id int,
    board_id int,
    thread_id int,
    post_id int,
    _view bit,
    _change bit,
    _moderate bit
)
begin
    insert into acl (`group`, board, thread, post, `view`, `change`, moderate)
    values (group_id, board_id, thread_id, post_id, _view, _change, _moderate);
end|

-- Удаляет правило.
--
-- Аргументы:
-- group_id - Идентификатор группы.
-- board_id - Идентификатор доски.
-- thread_id - Идентификатор нити.
-- post_id - Идентификатор сообщения.
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
        and ((board = board_id) or (coalesce(board, board_id) is null))
        and ((thread = thread_id) or (coalesce(thread, thread_id) is null))
        and ((post = post_id) or (coalesce(post, post_id) is null));
end|

-- Редактирует правило.
--
-- Аргументы:
-- group_id - Идентификатор группы.
-- board_id - Идентификатор доски.
-- thread_id - Идентификатор нити.
-- post_id - Идентификатор сообщения.
-- _view - Право на просмотр.
-- _change - Право на изменение.
-- _moderate - Право на модерирование.
create procedure sp_acl_edit
(
    group_id int,
    board_id int,
    thread_id int,
    post_id int,
    _view bit,
    _change bit,
    _moderate bit
)
begin
    update acl set `view` = _view, `change` = _change, moderate = _moderate
    where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
        and ((board = board_id) or (coalesce(board, board_id) is null))
        and ((thread = thread_id) or (coalesce(thread, thread_id) is null))
        and ((post = post_id) or (coalesce(post, post_id) is null));
end|

-- Выбирает все правила.
create procedure sp_acl_get_all ()
begin
    select `group`, board, thread, post, `view`, `change`, moderate
    from acl order by `group`, board, thread, post;
end|

-- --------------------------
--  Работа с блокировками. --
-- --------------------------

-- Блокирует диапазон IP-адресов.
--
-- Аргументы:
-- _range_beg - Начало диапазона IP-адресов.
-- _range_end - Конец диапазона IP-адресов.
-- _reason - Причина блокировки.
-- _untill - Время истечения блокировки.
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

-- Проверяет, заблокирован ли IP-адрес. Если да, то возвращает запись с самым
-- широким диапазоном IP-адресов, в который он входит.
create procedure sp_bans_check
(
    ip int  -- IP-адрес.
)
begin
    call sp_bans_refresh();
    select range_beg, range_end, untill, reason
        from bans
        where range_beg <= ip and range_end >= ip
        order by range_end desc limit 1;
end|

-- Удаляет блокировку с заданным идентификатором.
--
-- Аргументы:
-- _id - Идентификатор блокировки.
create procedure sp_bans_delete_by_id
(
    _id int
)
begin
    delete from bans where id = _id;
end|

-- Удаляет блокировки с заданным IP-адресом.
--
-- Аргументы:
-- ip - IP-адрес.
create procedure sp_bans_delete_by_ip
(
    ip int
)
begin
    delete from bans where range_beg <= ip and range_end >= ip;
end|

-- Выбирает все блокировки.
create procedure sp_bans_get_all ()
begin
    select id, range_beg, range_end, reason, untill from bans;
end|

-- Удаляет все истекшие блокировки.
create procedure sp_bans_refresh ()
begin
    delete from bans where untill <= now();
end|

-- --------------------------------------------------------
--  Работа со связями досок с типами загружаемых файлов. --
-- --------------------------------------------------------

-- Добавляет связь доски с типом загружаемых файлов.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- upload_type_id - Идентификатор типа загружаемых файлов.
create procedure sp_board_upload_types_add
(
    board_id int,
    upload_type_id int
)
begin
    insert into board_upload_types (board, upload_type)
        values (board_id, upload_type_id);
end|

-- Удаляет связь доски с типом загружаемых файлов.
--
-- Аргументы:
-- _board - Идентификатор доски.
-- _upload_type - Идентификатор типа загружаемых файлов.
create procedure sp_board_upload_types_delete
(
    _board int,
    _upload_type int
)
begin
    delete from board_upload_types
        where board = _board and upload_type = _upload_type;
end|

-- Выбирает все связи досок с типами загружаемых файлов.
create procedure sp_board_upload_types_get_all ()
begin
    select board, upload_type from board_upload_types;
end|

-- ---------------------
--  Работа с досками. --
-- ---------------------

-- Добавляет доску.
--
-- Аргументы:
-- _name - Имя.
-- _title - Заголовок.
-- _annotation - Аннотация.
-- _bump_limit - Специфичный для доски бамплимит.
-- _force_anonymous - Флаг отображения имени отправителя.
-- _default_name - Имя отправителя по умолчанию.
-- _with_attachments - Флаг вложений.
-- _enable_macro - Включение интеграции с макрочаном.
-- _enable_youtube - Включение вложения видео с ютуба.
-- _enable_captcha - Включение капчи.
-- _same_upload - Политика загрузки одинаковых файлов.
-- _popdown_handler - Идентификатор обработчика автоматического удаления нитей.
-- _category - Идентификатор категории.
create procedure sp_boards_add
(
    _name varchar(16),
    _title varchar(50),
    _annotation text,
    _bump_limit int,
    _force_anonymous bit,
    _default_name varchar(128),
    _with_attachments bit,
    _enable_macro bit,
    _enable_youtube bit,
    _enable_captcha bit,
    _same_upload varchar(32),
    _popdown_handler int,
    _category int
)
begin
    insert into boards (name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category)
        values (_name, _title, _annotation, _bump_limit, _force_anonymous,
            _default_name, _with_attachments, _enable_macro, _enable_youtube,
            _enable_captcha, _same_upload, _popdown_handler, _category);
end|

-- Удаляет доску с заданным идентификатором.
--
-- Аргументы:
-- _id - Идентификатор доски.
create procedure sp_boards_delete
(
	_id int
)
begin
	delete from boards where id = _id;
end|

-- Редактирует доску.
--
-- Аргументы:
-- _id - Идентификатор.
-- _title - Заголовок.
-- _annotation - Аннотация.
-- _bump_limit - Специфичный для доски бамплимит.
-- _force_anonymous - Флаг отображения имени отправителя.
-- _default_name - Имя отправителя по умолчанию.
-- _with_attachments - Флаг вложений.
-- _enable_macro - Включение интеграции с макрочаном.
-- _enable_youtube - Включение вложения видео с ютуба.
-- _enable_captcha - Включение капчи.
-- _same_upload - Политика загрузки одинаковых файлов.
-- _popdown_handler - Идентификатор обработчика автоматического удаления нитей.
-- _category - Идентификатор категории.
create procedure sp_boards_edit
(
    _id int,
    _title varchar(50),
    _annotation text,
    _bump_limit int,
    _force_anonymous bit,
    _default_name varchar(128),
    _with_attachments bit,
    _enable_macro bit,
    _enable_youtube bit,
    _enable_captcha bit,
    _same_upload varchar(32),
    _popdown_handler int,
    _category int
)
begin
    update boards set title = _title, annotation = _annotation,
            bump_limit = _bump_limit, force_anonymous = _force_anonymous,
            default_name = _default_name, with_attachments = _with_attachments,
            enable_macro = _enable_macro, enable_youtube = _enable_youtube,
            enable_captcha = _enable_captcha, same_upload = _same_upload,
            popdown_handler = _popdown_handler, category = _category
        where id = _id;
end|

-- Выбирает все доски.
create procedure sp_boards_get_all ()
begin
    select id, name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category
        from boards;
end|

-- Выбирает доску с заданным идентификатором.
create procedure sp_boards_get_by_id
(
    board_id int    -- Идентификатор доски.
)
begin
    select id, name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category
        from boards where id = board_id;
end|

-- Выбирает доску с заданным именем.
--
-- Аргументы:
-- board_name - Имя доски.
create procedure sp_boards_get_by_name
(
    board_name varchar(16)
)
begin
    select id, name, title, annotation, bump_limit, force_anonymous,
            default_name, with_attachments, enable_macro, enable_youtube,
            enable_captcha, same_upload, popdown_handler, category
        from boards where name = board_name;
end|

-- Выбирает доски, доступные для изменения заданному пользователю.
--
-- Аргументы:
-- user_id - Идентификатор пользователя.
create procedure sp_boards_get_changeable
(
    user_id int
)
begin
    select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
            b.default_name, b.with_attachments, b.enable_macro,
            b.enable_youtube, b.enable_captcha, b.same_upload,
            b.popdown_handler, b.category, ct.name as category_name
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
                -- редактирование доски не запрещено конкретной группе и
                -- разрешено всем группам или
                or (a1.change is null and a2.change = 1)
                -- редактирование доски не запрещено ни конкретной группе ни
                -- всем, и конкретной группе редактирование разрешено.
                or (a1.change is null and a2.change is null and a3.change = 1))
        group by b.id
        order by b.category, b.name;
end|

-- Выбирает заданную доску, доступную для редактирования заданному
-- пользователю.
create procedure sp_boards_get_changeable_by_id
(
    _board_id int,  -- Идентификатор доски.
    user_id int     -- Идентификатор пользователя.
)
begin
    declare board_id int;
    select id into board_id from boards where id = _board_id;
    if (board_id is null) then
        select 'NOT_FOUND' as error;
    else
        select b.id, b.name, b.title, b.annotation, b.bump_limit,
                b.force_anonymous, b.default_name, b.with_attachments,
                b.enable_macro, b.enable_youtube, b.enable_captcha,
                b.same_upload, b.popdown_handler, b.category,
                ct.name as category_name
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
            where b.id = board_id
                -- Доска не запрещена для просмотра группе и
                and ((a1.`view` = 1 or a1.`view` is null)
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
            order by b.category, b.name;
    end if;
end|

-- Выбирает заданную доску, доступную для редактирования заданному
-- пользователю.
--
-- Аргументы:
-- board_name - Имя доски.
-- user_id - Идентификатор пользователя.
create procedure sp_boards_get_changeable_by_name
(
	board_name varchar(16),
	user_id int
)
begin
	declare board_id int;
	select id into board_id from boards where name = board_name;
	if(board_id is null) then
		select 'NOT_FOUND' as error;
	else
		select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
			b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
			b.enable_captcha, b.same_upload, b.popdown_handler, b.category,
			ct.name as category_name
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
		order by b.category, b.name;
	end if;
end|

-- Выбирает доски, доступные для модерирования заданному пользователю.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
create procedure sp_boards_get_moderatable
(
	user_id int
)
begin
	select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
		b.enable_captcha, b.same_upload, b.popdown_handler, b.category
	from boards b
	join user_groups ug on ug.user = user_id
	-- Правила для конкретной группы и доски.
	left join acl a1 on ug.`group` = a1.`group` and b.id = a1.board
	-- Правило для конкретной доски.
	left join acl a2 on a2.`group` is null and b.id = a2.board
	-- Правила для конкретной группы.
	left join acl a3 on ug.`group` = a3.`group` and a3.board is null
		and a3.thread is null and a3.post is null
	where
			-- Доска не запрещена для просмотра группе и
		((a1.`view` = 1 or a1.`view` is null)
			-- доска не запрещена для просмотра всем и
			and (a2.`view` = 1 or a2.`view` is null)
			-- группе разрешен просмотр.
			and a3.`view` = 1)
			-- Модерирование доски разрешено конкретной группе
		and (a1.moderate = 1
			-- или модерирование доски не запрещено конкретной группе и
			-- разрешено всем группам
			or (a1.moderate is null and a2.moderate = 1)
			-- или модерирование доски не запрещено ни конкретной группе ни
			-- всем, и конкретной группе модерирование разрешено.
			or (a1.moderate is null and a2.moderate is null
				and a3.moderate = 1))
	group by b.id
	order by b.name;
end|

-- Выбирает доски, доступные для просмотра заданному пользователю.
--
-- Аргументы:
-- user_id - идентификатор пользователя.
create procedure sp_boards_get_visible
(
	user_id int
)
begin
	select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
		b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
		b.enable_captcha, b.same_upload, b.popdown_handler, b.category,
		ct.name as category_name
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
	order by b.category, b.name;
end|

-- -------------------------
--  Работа с категориями. --
-- -------------------------

-- Добавляет категорию.
--
-- Аргументы:
-- _name - Имя категории.
create procedure sp_categories_add
(
	_name varchar(50)
)
begin
	insert into categories (name) values (_name);
end|

-- Удаляет заданную категорию.
--
-- Аргументы:
-- _id - Идентификатор.
create procedure sp_categories_delete
(
	_id int
)
begin
	delete from categories where id = _id;
end|

-- Возвращает все категории.
create procedure sp_categories_get_all ()
begin
	select id, name from categories;
end|

-- -------------------------------
-- Работа с вложенными файлами. --
-- -------------------------------

-- Добавляет файл.
--
-- Аргументы:
-- _hash - Хеш.
-- _name - Имя.
-- _size - Размер в байтах.
-- _thumbnail - Уменьшенная копия.
-- _thumbnail_w - Ширина уменьшенной копии.
-- _thumbnail_h - Высота уменьшенной копии.
create procedure sp_files_add
(
	_hash varchar(32),
	_name varchar(256),
	_size int,
	_thumbnail varchar(256),
	_thumbnail_w int,
	_thumbnail_h int
)
begin
    insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
        values (_hash, _name, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
    select last_insert_id() as id;
end|

-- Выбирает файлы, вложенные в заданное сообщение.

-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_files_get_by_post
(
	post_id int
)
begin
	select f.id, f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w,
		f.thumbnail_h
	from posts_files pf
	join files f on f.id = pf.file and pf.post = post_id;
end|

-- Выбирает одинаковые файлы, вложенные в сообщения на заданной доске.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
-- hash - Хеш файла.
create procedure sp_files_get_same
(
    board_id int,
    user_id int,
    file_hash varchar(32)
)
begin
	select f.id, f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w,
            f.thumbnail_h, max(case when a1.`view` = 0 then 0
                                    when a2.`view` = 0 then 0
                                    when a3.`view` = 0 then 0
                                    when a4.`view` = 0 then 0
                                    when a5.`view` = 0 then 0
                                    when a6.`view` = 0 then 0
                                    when a7.`view` = 0 then 0
                                    else 1 end) as `view`
        from posts_files pf
        join files f on f.id = pf.file
        join posts p on p.id = pf.post and p.board = board_id
        join threads t on t.id = p.thread
        join user_groups ug on ug.user = user_id
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
        where f.`hash` = file_hash and pf.deleted is null
        group by f.id, p.id;
end|

-- ----------------------
--  Работа с группами. --
-- ----------------------

-- Добавляет группу.
--
-- Аргументы:
-- _name - Имя группы.
create procedure sp_groups_add
(
	_name varchar(50)
)
begin
	insert into groups (name) values (_name);
	select id from groups where name = _name;
	-- TODO:
	-- insert into acl (`group`, `view`, `change`, moderate) values (group_id, 1, 0, 0);
end|

-- Удаляет заданную группу, а так же всех пользователей, которые входят в эту
-- группу и все правила в ACL, распространяющиеся на эту группу.
--
-- Аргументы:
-- _id - Идентификатор группы.
create procedure sp_groups_delete
(
	_id int
)
begin
	delete from groups where id = _id;
end|

-- Выбирает все группы.
create procedure sp_groups_get_all ()
begin
	select id, name from groups order by id;
end|

-- ------------------------------
--  Работа со скрытыми нитями. --
-- ------------------------------

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
	insert into hidden_threads (user, thread) values (user_id, thread_id);
end|

-- Отменяет скрытие нити.
--
-- Аргументы:
-- thread_id - Идентификатор нити.
-- user_id - Идентификатор пользователя.
create procedure sp_hidden_threads_delete
(
	thread_id int,
	user_id int
)
begin
	delete from hidden_threads where user = user_id and thread = thread_id;
end|

-- Выбирает скрыте нити на заданной доске.
--
-- Аргументы:
-- board_id - идентификатор доски.
create procedure sp_hidden_threads_get_by_board
(
	board_id int
)
begin
	select ht.thread, t.original_post, ht.user
	from hidden_threads ht
	join threads t on t.id = ht.thread and t.board = board_id;
end|

-- Выбирает доступную для просмотра скрытую нить и количество сообщений в ней.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- thread_num - Номер нити.
-- user_id - Идентификатор пользователя.
create procedure sp_hidden_threads_get_visible
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
		select t.id, t.original_post, t.bump_limit, t.archived, t.sage,
			t.sticky, t.with_attachments, count(p.id) as posts_count
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

-- -------------------------------------
-- Работа с вложенными изображениями. --
-- -------------------------------------

-- Добавляет вложенное изображение.
--
-- Аргументы:
-- _hash - Хеш.
-- _name - Имя.
-- _widht - Ширина.
-- _height - Высота.
-- _size - Размер в байтах.
-- _thumbnail - Уменьшенная копия.
-- _thumbnail_w - Ширина уменьшенной копии.
-- _thumbnail_h - Высота уменьшенной копии.
create procedure sp_images_add
(
    _hash varchar(32),
    _name varchar(256),
    _widht int,
    _height int,
    _size int,
    _thumbnail varchar(256),
    _thumbnail_w int,
    _thumbnail_h int
)
begin
    insert into images (hash, name, widht, height, size, thumbnail, thumbnail_w,
            thumbnail_h)
        values (_hash, _name, _widht, _height, _size, _thumbnail, _thumbnail_w,
            _thumbnail_h);
    select last_insert_id() as id;
end|

-- Выбирает изображения, вложенные в заданное сообщение.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_images_get_by_post
(
	post_id int
)
begin
	select i.id, i.hash, i.name, i.widht, i.height, i.size, i.thumbnail,
		i.thumbnail_w, i.thumbnail_h
	from posts_images pi
	join images i on i.id = pi.image and pi.post = post_id;
end|

-- Выбирает одинаковые изображения, вложенные в сообщения на заданной доске.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
-- image_hash - Хеш вложенного изображения.
create procedure sp_images_get_same
(
	board_id int,
	user_id int,
	image_hash varchar(32)
)
begin
	select i.id, i.hash, i.name, i.widht, i.height, i.size, i.thumbnail,
		i.thumbnail_w, i.thumbnail_h, p.number, t.original_post,
		max(case
			when a1.`view` = 0 then 0
			when a2.`view` = 0 then 0
			when a3.`view` = 0 then 0
			when a4.`view` = 0 then 0
			when a5.`view` = 0 then 0
			when a6.`view` = 0 then 0
			when a7.`view` = 0 then 0
			else 1 end) as `view`
	from images i
	join posts_images pi on pi.image = i.id
	join posts p on p.id = pi.post and p.board = board_id
	join threads t on t.id = p.thread
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
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null
		and a7.thread is null and a7.post is null
	where i.hash = image_hash and pi.deleted is null
	group by i.id, p.id;
end|

-- ---------------------
--  Работа с языками. --
-- ---------------------

-- Добавляет язык.
--
-- Аргументы:
-- _code - ISO_639-2 код языка.
create procedure sp_languages_add
(
	_code char(3)
)
begin
	insert into languages (code) values (_code);
end|

-- Удаляет язык с заданным идентификатором.
--
-- Аргументы:
-- _id - Идентификатор языка.
create procedure sp_languages_delete
(
	_id int
)
begin
	delete from languages where id = _id;
end|

-- Выбирает все языки.
create procedure sp_languages_get_all ()
begin
	select id, code from languages;
end|

-- -----------------------------------------------
-- Работа с вложенными ссылками на изображения. --
-- -----------------------------------------------

-- Выбирает ссылки на изображения, вложенные в заданное сообщение.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_links_get_by_post
(
	post_id int
)
begin
	select l.id, l.url, l.widht, l.height, l.size, l.thumbnail, l.thumbnail_w,
		l.thumbnail_h
	from posts_links pl
	join links l on l.id = pl.link and pl.post = post_id;
end|

-- -----------------------------
-- Работа с тегами макрочана. --
-- -----------------------------

-- Добавляет тег макрочана.
--
-- Аргументы:
-- _name - Имя.
create procedure sp_macrochan_tags_add
(
    _name varchar(256)
)
begin
    insert into macrochan_tags (name) values (_name);
end|

-- Удаляет тег по заданному имени.
--
-- Аргументы:
-- _name - Имя.
create procedure sp_macrochan_tags_delete_by_name
(
    _name varchar(256)
)
begin
    declare _id int default null;

    select id into _id from macrochan_tags where name = _name;
    if (_id is not null) then
        delete from macrochan_tags_images where tag = _id;
        delete from macrochan_tags where id = _id;
    end if;
end|

-- Выбирает все теги макрочана.
create procedure sp_macrochan_tags_get_all ()
begin
    select id, name from macrochan_tags;
end|

-- -----------------------------
-- Работа с тегами макрочана. --
-- -----------------------------

-- Добавляет изображение макрочана.
create procedure sp_macrochan_images_add
(
    _name varchar(256),         -- Имя.
    _width int,                 -- Ширина.
    _height int,                -- Высота.
    _size int,                  -- Размер в байтах.
    _thumbnail varchar(256),    -- Уменьшенная копия.
    _thumbnail_w int,           -- Ширина уменьшенной копии.
    _thumbnail_h int            -- Высота уменьшенной копии.
)
begin
    insert into macrochan_images (name, width, height, size, thumbnail,
            thumbnail_w, thumbnail_h)
        values (_name, _width, _height, _size, _thumbnail,
            _thumbnail_w, _thumbnail_h);
end|

-- Удаляет изображение по заданному имени.
--
-- Аргументы:
-- _name - Имя.
create procedure sp_macrochan_images_delete_by_name
(
    _name varchar(256)
)
begin
    declare _id int default null;

    select id into _id from macrochan_images where name = _name;
    if (_id is not null) then
        delete from macrochan_tags_images where image = _id;
        delete from macrochan_images where id = _id;
    end if;
end|

-- Выбирает все изображения макрочана.
create procedure sp_macrochan_images_get_all ()
begin
    select id, name, width, height, size, thumbnail, thumbnail_w, thumbnail_h
        from macrochan_images;
end|

-- ---------------------------------------------------
-- Работа со связями тегов и изображений макрочана. --
-- ---------------------------------------------------

-- Добавляет связь тега и изображения макрочана.
create procedure sp_macrochan_tags_images_add
(
    tag_name varchar(256),          -- Имя тега макрочана.
    image_name varchar(256)         -- Имя изображения макрочана.
)
begin
    declare tag_id int default null;
    declare image_id int default null;

    select id into tag_id from macrochan_tags where name = tag_name;
    select id into image_id from macrochan_images where name = image_name;
    if (tag_id is not null and image_id is not null) then
        insert into macrochan_tags_images (tag, image)
        values (tag_id, image_id);
    end if;
end|

-- Выбирает связь тега и изображением макрочана по заданному имени тега
-- и изображения.
create procedure sp_macrochan_tags_images_get
(
    tag_name varchar(256),          -- Имя тега макрочана.
    image_name varchar(256)         -- Имя изображения макрочана.
)
begin
    select ti.tag, ti.image
    from macrochan_tags_images ti
    join macrochan_tags t on ti.tag = t.id and t.name = tag_name
    join macrochan_images i on ti.image = i.id and i.name = image_name;
end|

-- Выбирает все связи тегов и изображениями макрочана.
create procedure sp_macrochan_tags_images_get_all ()
begin
    select tag, image from macrochan_tags_images;
end|

-- ----------------------------------------------------------
--  Работа с обработчиками автоматического удаления нитей. --
-- ----------------------------------------------------------

-- Добавляет обработчик автоматического удаления нитей.
--
-- Аргументы:
-- _name - Имя функции обработчика автоматического удаления нитей.
create procedure sp_popdown_handlers_add
(
	_name varchar(50)
)
begin
	insert into popdown_handlers (name) values (_name);
end|

-- Удаляет обработчик автоматического удаления нитей.
--
-- Аргументы:
-- _id - Идентификатор обработчика автоматического удаления нитей.
create procedure sp_popdown_handlers_delete
(
	_id int
)
begin
	delete from popdown_handlers where id = _id;
end|

-- Выбирает все обработчики автоматического удаления нитей.
create procedure sp_popdown_handlers_get_all ()
begin
    select id, name from popdown_handlers;
end|

-- -------------------------
--  Работа с сообщениями. --
-- -------------------------

-- Добавляет сообщение.
create procedure sp_posts_add
(
    board_id int,           -- Идентификатор доски.
    thread_id int,          -- Идентификатор нити.
    user_id int,            -- Идентификатор пользователя.
    _password varchar(128), -- Пароль на удаление сообщения.
    _name varchar(128),     -- Имя отправителя.
    _tripcode varchar(128), -- Трипкод.
    _ip bigint,             -- IP-адрес отправителя.
    _subject varchar(128),  -- Тема.
    _date_time datetime,    -- Время сохранения.
    _text text,             -- Текст.
    _sage bit               -- Флаг поднятия нити.
)
begin
    declare count_posts int;    -- posts in thread
    declare post_number int;    -- number on post on thread
    declare bumplimit int;      -- number of bump posts (posts which brings thread to up)
    declare threadsage bit;     -- whole thread sage
    declare post_id int;

    select max(number) into post_number from posts where board = board_id;
    if(post_number is null) then
        set post_number = 1;
    else
        set post_number = post_number + 1;
    end if;
    select bump_limit into bumplimit from threads where id = thread_id;
    select count(id) into count_posts from posts where thread = thread_id;
    select sage into threadsage from threads where id = thread_id;
    if(threadsage is not null and threadsage = 1) then
        set _sage = 1;
    end if;
    if(count_posts > bumplimit) then
        set _sage = 1;
    end if;
    if(_date_time is null) then
        set _date_time = now();
    end if;

    insert into posts (board, thread, number, user, password, name,
            tripcode, ip, subject, date_time, text, sage, deleted)
        values (board_id, thread_id, post_number, user_id, _password, _name,
            _tripcode, _ip, _subject, _date_time, _text, _sage, 0);
    select last_insert_id() into post_id;
    select id, board, thread, number, user, password, name, tripcode, ip,
            subject, date_time, `text`, sage
        from posts where id = post_id;
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
		and p.number = t.original_post;
	if(thread_id is null) then
		update posts set deleted = 1 where id = _id;
	else
		update threads set deleted = 1 where id = thread_id;
		update posts set deleted = 1 where thread = thread_id;
	end if;
end|

-- Удаляет сообщение с заданным идентификатором и все сообщения с ip адреса
-- отправителя, оставленные с заданного момента времени.
--
-- Аргументы:
-- _id - Идентификатор сообщения.
-- _date_time - Момент времени.
create procedure sp_posts_delete_last
(
	_id int,
	_date_time datetime
)
begin
	declare _ip bigint;
	declare done int default 0;
	declare thread_id int;
	declare `c` cursor for
		select t.id
		from posts p
		join (select ip from posts where id = _id) q on q.ip = p.ip
		join threads t on t.id = p.thread and p.`date_time` > _date_time
			and p.`number` = t.original_post;
	declare continue handler for not found set done = 1;
	open `c`;
	repeat
	fetch `c` into thread_id;
	if(not done) then
		call sp_threads_edit_deleted(thread_id);
	end if;
	until done end repeat;
	close `c`;
	select ip into _ip from posts where id = _id;
	if(_ip is not null) then
		update posts set deleted = 1 where ip = _ip and `date_time` > _date_time;
	end if;
end|

-- Удаляет сообщения, помеченные на удаление.
create procedure sp_posts_delete_marked ()
begin
	delete pu from posts_uploads pu
	join posts p on p.id = pu.post
	where p.deleted = 1;

	delete a from acl a
	join posts p on p.id = a.post
	where p.deleted = 1;

	delete from posts where deleted = 1;

	delete ht from hidden_threads ht
	join threads t on t.id = ht.thread
	where t.deleted = 1;

	delete from threads where deleted = 1;
end|

-- Редактирует текст заданного сообщения, добавляя в конец его текста заданный
-- текст.
--
-- Аргументы:
-- _id - Идентификатор сообщения.
-- _text - Текст.
create procedure sp_posts_edit_text_by_id
(
	_id int,
	_text text
)
begin
	update posts set text = concat(text, _text) where id = _id;
end|

-- Выбирает все сообщения.
create procedure sp_posts_get_all ()
begin
	select p.id, p.board, b.name as board_name, p.thread,
		t.original_post as thread_number, p.number, p.password, p.name,
		p.tripcode, p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	join threads t on t.id = p.thread
	join boards b on b.id = p.board
	where p.deleted = 0 and t.deleted = 0 and t.archived = 0
	order by p.date_time desc;
end|

-- Выбирает сообщения с заданной доски.
--
-- Аргументы:
-- board_id - Идентификатор доски.
create procedure sp_posts_get_by_board
(
	board_id int
)
begin
	select p.id, p.thread, t.original_post as thread_number, p.board,
		b.name as board_name, p.number, p.password, p.name, p.tripcode,
		p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	join threads t on t.id = p.thread
	join boards b on b.id = p.board
	where p.deleted = 0 and t.deleted = 0 and t.archived = 0
		and p.board = board_id
	order by p.date_time desc;
end|

-- Выбирает сообщения заданной нити.
--
-- Аргументы:
-- thread_id - Идентификатор нити.
create procedure sp_posts_get_by_thread
(
	thread_id int
)
begin
	select id, thread, number, password, name, tripcode, ip, subject,
		date_time, text, sage
	from posts p
	where thread = thread_id;
end|

-- Выбирает заданное сообщение, доступное для просмотра заданному пользователю.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
-- user_id - Идентификатор пользователя.
create procedure sp_posts_get_visible_by_id
(
	post_id int,
	user_id int
)
begin
	select p.id, p.thread, p.board, p.number, p.password, p.name,
		p.tripcode, p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	left join threads t on t.id = p.thread
	join user_groups ug on ug.user = user_id
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
	where p.id = post_id and p.deleted = 0 and t.deleted = 0 and t.archived = 0
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

-- Выбирает заданное сообщение, доступное для просмотра заданному пользователю.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- post_number - Номер сообщения.
-- user_id - Идентификатор пользователя.
create procedure sp_posts_get_visible_by_number
(
	board_id int,
	post_number int,
	user_id int
)
begin
	select p.id, p.thread, p.number, p.password, p.name, p.tripcode, p.ip,
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
		and p.number = post_number
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

-- Для заданной нити выбирает сообщения, доступные для просмотра заданному
-- пользователю.
--
-- Аргументы:
-- thread_id - Идентификатор нити.
-- user_id - Идентификатор пользователя.
create procedure sp_posts_get_visible_by_thread
(
	thread_id int,
	user_id int
)
begin
	select p.id, p.thread, p.number, p.password, p.name, p.tripcode,
			p.ip, p.subject, p.date_time, p.text, p.sage
	from posts p
	join threads t on t.board = p.board and t.id = p.thread
	join user_groups ug on ug.user = user_id
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
	left join acl a7 on a7.`group` = ug.`group` and a7.board is null
		and a7.thread is null and a7.post is null
	where p.thread = thread_id
		and p.deleted = 0 and t.deleted = 0 and t.archived = 0
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
	group by p.id
	order by p.number asc;
end|

-- --------------------------------------------------
-- Работа со связями сообщений и вложенных файлов. --
-- --------------------------------------------------

-- Добавляет связь сообщения с вложенным файлом.
create procedure sp_posts_files_add
(
    _post int,      -- Идентификатор сообщения.
    _file int,      -- Идентификатор вложенного файла.
    _deleted bit    -- Флаг удаления.
)
begin
    insert into posts_files (post, file, deleted)
        values (_post, _file, _deleted);
end|

-- Выбирает связи заданного сообщения с вложенными файлами.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_posts_files_get_by_post
(
    post_id int
)
begin
    select post, file, deleted from posts_files where post = post_id;
end|

-- -------------------------------------------------------
-- Работа со связями сообщений и вложенных изображений. --
-- -------------------------------------------------------

-- Добавляет связь сообщения с вложенным изображением.
create procedure sp_posts_images_add
(
    _post int,      -- Идентификатор сообщения.
    _image int,     -- Идентификатор вложенного изображения.
    _deleted bit    -- Флаг удаления.
)
begin
    insert into posts_images (post, image, deleted)
        values (_post, _image, _deleted);
end|

-- Выбирает связи заданного сообщения с вложенными изображениями.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_posts_images_get_by_post
(
    post_id int
)
begin
    select post, image, deleted from posts_images where post = post_id;
end|

-- -----------------------------------------------------------------
-- Работа со связями сообщений и вложенных ссылок на изображения. --
-- -----------------------------------------------------------------

-- Добавляет связь сообщения с вложенным изображением.
create procedure sp_posts_links_add
(
    _post int,      -- Идентификатор сообщения.
    _link int,      -- Идентификатор вложенной ссылки на изображение.
    _deleted bit    -- Флаг удаления.
)
begin
    insert into posts_links (post, link, deleted)
        values (_post, _link, _deleted);
end|

-- Выбирает связи заданного сообщения с вложенными ссылками на изображения.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_posts_links_get_by_post
(
    post_id int
)
begin
    select post, link, deleted from posts_links where post = post_id;
end|

-- --------------------------------------------------
-- Работа со связями сообщений и вложенного видео. --
-- --------------------------------------------------

-- Добавляет связь сообщения с вложенным видео.
create procedure sp_posts_videos_add
(
    _post int,      -- Идентификатор сообщения.
    _video int,     -- Идентификатор вложенного видео.
    _deleted bit    -- Флаг удаления.
)
begin
    insert into posts_videos (post, video, deleted)
        values (_post, _video, _deleted);
end|

-- Выбирает связи заданного сообщения с вложенным видео.
--
-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_posts_videos_get_by_post
(
    post_id int
)
begin
    select post, video, deleted from posts_videos where post = post_id;
end|

-- ----------------------
--  Работа со стилями. --
-- ----------------------

-- Добавляет стиль.
--
-- Аргументы:
-- _name - Имя файла стиля.
create procedure sp_stylesheets_add
(
    _name varchar(50)
)
begin
    insert into stylesheets (name) values (_name);
end|

-- Удаляет заданный стиль.
--
-- Аргументы:
-- _id - Идентификатор стиля.
create procedure sp_stylesheets_delete
(
	_id int
)
begin
	delete from stylesheets where id = _id;
end|

-- Выбирает все стили.
create procedure sp_stylesheets_get_all ()
begin
	select id, name from stylesheets;
end|

-- --------------------
--  Работа с нитями. --
-- --------------------

-- Добавляет нить. Если номер оригинального сообщения null, то будет создана
-- пустая нить.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- _original_post - Номер оригинального сообщения.
-- _bump_limit - Специфичный для нити бамплимит.
-- _sage - Флаг поднятия нити.
-- _with_attachments - Флаг вложений.
create procedure sp_threads_add
(
	board_id int,
	_original_post int,
	_bump_limit int,
	_sage bit,
	_with_attachments bit
)
begin
    declare thread_id int;
    insert into threads (board, original_post, bump_limit, deleted, archived,
            sage, sticky, with_attachments)
        values (board_id, _original_post, _bump_limit, 0, 0,
            _sage, 0, _with_attachments);
    select last_insert_id() into thread_id;
    select id, board, original_post, bump_limit, sage, sticky, with_attachments
        from threads where id = thread_id;
end|

-- Редактирует заданную нить.
--
-- Аргументы:
-- _id - Идентификатор нити.
-- _bump_limit - Специфичный для нити бамплимит.
-- _sage - Флаг поднятия нити.
-- _sticky - Флаг закрепления.
-- _with_attachments - Флаг вложений.
create procedure sp_threads_edit
(
	_id int,
	_bump_limit int,
	_sticky bit,
	_sage bit,
	_with_attachments bit
)
begin
	update threads set bump_limit = _bump_limit, sticky = _sticky, sage = _sage,
		with_attachments = _with_attachments
	where id = _id;
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

-- Помечает на удаление заданную нить.
--
-- Аргументы:
-- _id - Идентификатор нити.
create procedure sp_threads_edit_deleted
(
	_id int
)
begin
	update threads set deleted = 1 where id = _id;
	update posts set deleted = 1 where thread = _id;
end|

-- Редактирует номер оригинального сообщения нити.
create procedure sp_threads_edit_original_post
(
    _id int,            -- Идентификатор нити.
    _original_post int  -- Номер оригинального сообщения нити.
)
begin
    update threads set original_post = _original_post where id = _id;
end|

-- Выбирает все нити.
create procedure sp_threads_get_all ()
begin
	select id, board, original_post, bump_limit, sage, sticky, with_attachments
	from threads
	where deleted = 0 and archived = 0
	order by id desc;
end|

-- Выбирает нити, помеченные для архивирования.
create procedure sp_threads_get_archived ()
begin
	select id, board, original_post, bump_limit, sage, sticky, with_attachments
	from threads
	where deleted = 0 and archived = 1;
end|

-- Выбирает заданную нить, доступную для редактирования заданному пользователю.
--
-- Аргументы:
-- thread_id - Идентификатор нити.
-- user_id - Идентификатор пользователя.
create procedure sp_threads_get_changeable_by_id
(
	thread_id int,
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.archived, t.sage,
		t.with_attachments
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.user = ht.user
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

-- Выбирает нити, доступные для модерирования заданному пользователю.
--
-- Аргументы:
-- user_id - Идентификатор пользователя.
create procedure sp_threads_get_moderatable
(
	user_id int
)
begin
	select t.id, t.board, t.original_post, t.bump_limit, t.sage, t.sticky,
		t.with_attachments
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on t.id = ht.thread and ug.user = ht.user
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

-- Выбирает заданную нить, доступную для модерирования заданному пользователю.
create procedure sp_threads_get_moderatable_by_id
(
    thread_id int,  -- Идентификатор нити.
    user_id int     -- Идентификатор пользователя.
)
begin
    select t.id
        from threads t
        join user_groups ug on ug.user = user_id
        left join hidden_threads ht on t.id = ht.thread and ug.user = ht.user
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

-- Выбирает с заданной доски доступные для просмотра пользователю нити и
-- количество сообщений в них.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
create procedure sp_threads_get_visible_by_board
(
	board_id int,
	user_id int
)
begin
	select q1.id, q1.original_post, q1.bump_limit, q1.sticky, q1.sage,
		q1.with_attachments, q1.posts_count, q1.last_post_num
	from (
		-- Без учёта сообщений с сажей вычислим последнее сообщение в нити.
		select q.id, q.original_post, q.bump_limit, q.sticky, q.sage,
			q.with_attachments, q.posts_count, max(p.number) as last_post_num
		from posts p
		join (
			-- Выберем видимые нити и подсчитаем количество видимых сообщений.
			select t.id, t.original_post, t.bump_limit, t.sticky, t.sage,
				t.with_attachments, count(distinct p.id) as posts_count
			from posts p
			join threads t on t.id = p.thread and t.board = board_id
			join user_groups ug on ug.`user` = user_id
			left join hidden_threads ht on ht.thread = t.id
				and ht.user = ug.user
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
			where t.deleted = 0 and t.archived = 0 and ht.thread is null
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
			group by t.id) q on q.id = p.thread
				and (p.sage = 0 or p.sage is null) and p.deleted = 0
		group by q.id) q1
	order by q1.last_post_num desc;
end|

-- Выбирает заданную нить, доступную для просмотра заданному пользователю.
create procedure sp_threads_get_visible_by_original_post
(
	_board int,         -- Идентификатор доски.
	_original_post int, -- Номер оригинального сообщения.
	user_id int         -- Идентификатор пользователя.
)
begin
    declare thread_id int;
    select id into thread_id from threads
        where original_post = _original_post and board = _board;
    if thread_id is null
    then
        select 'NOT_FOUND' as error;
    else
        select t.id, t.original_post, t.bump_limit, t.sticky, t.archived,
                t.sage, t.with_attachments, count(p.id) as visible_posts_count
            from posts p
            join threads t on t.id = p.thread
            join user_groups ug on ug.`user` = user_id
            left join hidden_threads ht on t.id = ht.thread
                and ug.user = ht.user
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

-- Вычисляет количество нитей, доступных для просмотра заданному пользователю
-- на заданной доске.
--
-- Аргументы:
-- user_id - Идентификатор пользователя.
-- board_id - Идентификатор доски.
create procedure sp_threads_get_visible_count
(
	user_id int,
	board_id int
)
begin
	select count(q.id) as threads_count
	from (select t.id
	from threads t
	join user_groups ug on ug.user = user_id
	left join hidden_threads ht on ht.thread = t.id and ht.user = ug.user
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

-- Ищет с заданной доски доступные для просмотра пользователю нити и
-- количество сообщений в них.
--
-- Аргументы:
-- board_id - Идентификатор доски.
-- user_id - Идентификатор пользователя.
-- word - Слово для поиска.
create procedure sp_threads_search_visible_by_board
(	
	board_id int,
	user_id int,
	word varchar(60)
)
begin
	select q1.id, q1.original_post, q1.bump_limit, q1.sticky, q1.sage,
		q1.with_attachments, q1.posts_count, q1.last_post_num
	from (
		-- Без учёта сообщений с сажей вычислим последнее сообщение в нити.
		select q.id, q.original_post, q.bump_limit, q.sticky, q.sage,
			q.with_attachments, q.posts_count, max(p.number) as last_post_num
		from posts p
		join (
			-- Выберем видимые нити и подсчитаем количество видимых сообщений.
			select t.id, t.original_post, t.bump_limit, t.sticky, t.sage,
				t.with_attachments, count(distinct p.id) as posts_count
			from posts p
			join threads t on t.id = p.thread and t.board = board_id
			join user_groups ug on ug.`user` = user_id
			left join hidden_threads ht on ht.thread = t.id
				and ht.user = ug.user
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
			where t.deleted = 0 and t.archived = 0 and ht.thread is null
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
				and p.text like concat(concat("%", word), "%")
				-- Ищем нити и сообщения в них
			group by t.id) q on q.id = p.thread
				and (p.sage = 0 or p.sage is null) and p.deleted = 0
		group by q.id) q1
	order by q1.last_post_num desc;
end|

-- ----------------------------------------------
--  Работа с обработчиками загружаемых файлов. --
-- ----------------------------------------------

-- Добавляет обработчик загружаемых файлов.
--
-- Аргументы:
-- _name - Имя фукнции обработчика загружаемых файлов.
create procedure sp_upload_handlers_add
(
	_name varchar(50)
)
begin
	insert into upload_handlers (name) values (_name);
end|

-- Удаляет обработчик загружаемых файлов.
create procedure sp_upload_handlers_delete
(
	_id int
)
begin
	delete from upload_handlers where id = _id;
end|

-- Выбирает все обработчики загружаемых файлов.
create procedure sp_upload_handlers_get_all ()
begin
	select id, name from upload_handlers;
end|

-- ---------------------------------------
--  Работа с типами загружаемых файлов. --
-- ---------------------------------------

-- Добавляет тип загружаемых файлов.
--
-- Аргументы:
-- _extension - Расширение.
-- _store_extension - Сохраняемое расширение.
-- _is_image - Флаг изображения.
-- _upload_handler_id - Идентификатор обработчика загружаемых файлов.
-- _thumbnail_image - Имя файла уменьшенной копии.
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

-- Удаляет заданный тип загружаемых файлов.
create procedure sp_upload_types_delete
(
	_id int
)
begin
	delete from upload_types where id = _id;
end|

-- Редактирует заданный тип загружаемых файлов.
--
-- Аргументы:
-- _id - Идентификатор.
-- _store_extension - Сохраняемое расширение.
-- _is_image - Флаг изображения.
-- _upload_handler_id - Идентификатор обработчика загружаемых файлов.
-- _thumbnail_image - Имя файла уменьшенной копии.
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

-- Выбирает все типы загружаемых файлов.
create procedure sp_upload_types_get_all ()
begin
	select id, extension, store_extension, is_image, upload_handler,
		thumbnail_image
	from upload_types;
end|

-- Выбирает типы загружаемых файлов, доступных для загрузки на заданной доске.
--
-- Аргументы:
-- board_id - Идентификатор доски.
create procedure sp_upload_types_get_by_board
(
	board_id int
)
begin
	select ut.id, ut.extension, ut.store_extension, ut.is_image, ut.upload_handler,
		uh.name as upload_handler_name, ut.thumbnail_image
	from upload_types ut
	join board_upload_types but on ut.id = but.upload_type and but.board = board_id
	join upload_handlers uh on uh.id = ut.upload_handler;
end|

-- -----------------------------------------------
--  Работа со связями пользователей с группами. --
-- -----------------------------------------------

-- Добавляет пользователя в группу.
--
-- Аргументы:
-- user_id - Идентификатор пользователя.
-- group_id - Идентификатор группы.
create procedure sp_user_groups_add
(
	user_id int,
	group_id int
)
begin
	insert into user_groups (user, `group`) values (user_id, group_id);
end|

-- Удаляет заданного пользователя из заданной группы.
--
-- Аргументы:
-- user_id - Идентификатор пользователя.
-- group_id - Идентификатор группы.
create procedure sp_user_groups_delete
(
	user_id int,
	group_id int
)
begin
	delete from user_groups where user = user_id and `group` = group_id;
end|

-- Переносит заданного пользователя из одной группы в другую.
--
-- Аргументы:
-- user_id - Идентификатор пользователя.
-- old_group_id - Идентификатор старой группы.
-- new_group_id - Идентификатор новой группы.
create procedure sp_user_groups_edit
(
	user_id int,
	old_group_id int,
	new_group_id int
)
begin
	update user_groups set `group` = new_group_id
	where user = user_id and `group` = old_group_id;
end|

-- Выбирает все связи пользователей с группами.
create procedure sp_user_groups_get_all ()
begin
	select user, `group` from user_groups order by user, `group`;
end|

-- ----------------------------
--  Работа с пользователями. --
-- ----------------------------

-- Редактирует пользователя с заданным ключевым словом или добавляет нового.
--
-- Аргументы:
-- _keyword - Хеш ключевого слова.
-- _posts_per_thread - Число сообщений в нити на странице просмотра доски.
-- _threads_per_page - Число нитей на странице просмотра доски.
-- _lines_per_post - Количество строк в предпросмотре сообщения.
-- _language - Идентификатор языка.
-- _stylesheet - Идентификатор стиля.
-- _password - Пароль для удаления сообщений.
-- _goto - Перенаправление.
create procedure sp_users_edit_by_keyword
(
	_keyword varchar(32),
	_posts_per_thread int,
	_threads_per_page int,
	_lines_per_post int,
	_language int,
	_stylesheet int,
	_password varchar(12),
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
			lines_per_post, stylesheet, language, password, `goto`)
		values (_keyword, _threads_per_page, _posts_per_thread,
			_lines_per_post, _stylesheet, _language, _password, _goto);
		select last_insert_id() into user_id;
		insert into user_groups (user, `group`) select user_id, id from groups
			where name = 'Users';
	else
		-- Редактируем настройки существующего
		update users set threads_per_page = _threads_per_page,
			posts_per_thread = _posts_per_thread,
			lines_per_post = _lines_per_post,
			stylesheet = _stylesheet,
			language = _language,
			password = _password,
			`goto` = _goto
		where id = user_id;
	end if;
end|

-- Выбирает всех пользователей.
create procedure sp_users_get_all ()
begin
	select id from users;
end|

-- Выбирает ползователя с заданным ключевым словом.
--
-- Аргументы:
-- _keyword - Хеш ключевого слова.
create procedure sp_users_get_by_keyword
(
	_keyword varchar(32)
)
begin
    declare user_id int;

    select id into user_id from users where keyword = _keyword;

    select u.id, u.posts_per_thread, u.threads_per_page, u.lines_per_post,
            l.code as language, s.name as stylesheet, u.password, u.`goto`
        from users u
        join stylesheets s on u.stylesheet = s.id
        join languages l on u.language = l.id
        where u.keyword = _keyword;

    select g.name from user_groups ug
        join users u on ug.user = u.id and u.id = user_id
        join groups g on ug.`group` = g.id;
end|

-- Устанавливает перенаправление заданному пользователю.
create procedure sp_users_set_goto
(
    _id int,            -- Идентификатор пользователя.
    _goto varchar(32)   -- Перенаправление.
)
begin
    update users set `goto` = _goto where id = _id;
end|

-- Устанавливает пароль для удаления сообщений заданному пользователю.
create procedure sp_users_set_password
(
    _id int,                -- Идентификатор пользователя.
    _password varchar(12)   -- Пароль для удаления сообщений.
)
begin
    update users set password = _password where id = _id;
end|

-- ----------------------------
-- Работа с вложенным видео. --
-- ----------------------------

-- Добавляет вложенное видео.

-- Аргументы:
-- _code - HTML-код.
-- _widht - Ширина.
-- _height - Высота.
create procedure sp_videos_add
(
    _code varchar(256),
    _widht int,
    _height int
)
begin
    insert into videos (code, widht, height) values (_code, _widht, _height);
    select last_insert_id() as id;
end|

-- Выбирает видео, вложенные в заданное сообщение.

-- Аргументы:
-- post_id - Идентификатор сообщения.
create procedure sp_videos_get_by_post
(
    post_id int
)
begin
    select v.id, v.code, v.widht, v.height
        from posts_videos pv
        join videos v on v.id = pv.video and pv.post = post_id;
end|

-- ---------------------------------
-- Добавление слова в вордфильтр. --
-- ---------------------------------

-- Добавляет слово и его замену в таблицу вордфильтра.

-- Аргументы:
-- word - Слово для замены.
-- replace - Слово-замена.
create procedure sp_words_add
(
	_board_id int,
    _word varchar(100),
    _replace varchar(100)
)
begin
    insert into words (board_id, word, `replace`)
        values (_board_id, _word, _replace);
end|

-- Удаляет слово и его замену из таблицы вордфильтра.

-- Аргументы:
-- id - Идентификатор.
create procedure sp_words_delete
(
    _id int
)
begin
    delete from words
    where id = _id;
end|

-- Изменяет слово и его замену.

-- Аргументы:
-- id - Идентификатор.
-- word - Слово для замены.
-- replace - Слово-замена.
create procedure sp_words_edit
(
    _id int,
    _word varchar(100),
    _replace varchar(100)
)
begin
    update words set word = _word, `replace` = _replace
        where id = _id;
end|

-- Выбирает все слова, их замени и идентификаторы из таблицы вордфильтра.
create procedure sp_words_get_all ()
begin
    select id, word, `replace`
    from words;
end|

-- Выбирает все слова, их замени и идентификаторы из таблицы вордфильтра.
create procedure sp_words_get_all_by_board
(
	_board_id int
)
begin
    select id, word, `replace`
    from words
    where board_id = _board_id;
end|

/*
-- ----------------------------------------------------------
--  Работа со связями сообщений с информацией о загрузках. --
-- ----------------------------------------------------------

-- Выбирает для сообщений их связи с информацией о загрузках.
create procedure sp_posts_uploads_get_by_post
(
	post_id int
)
begin
	select post, upload from posts_uploads where post = post_id;
end|

-- Связывает сообщение с информацией о загрузке.
--
-- Аргументы:
-- _post_id - идентификатор сообщения.
-- _upload_id - идентификатор записи с информацией о загрузке.
create procedure sp_posts_uploads_add
(
	_post_id int,
	_upload_id int
)
begin
	insert into posts_uploads (post, upload) values (_post_id, _upload_id);
end|

-- Удаляет закрепления загрузок за заданным сообщением.
--
-- Аргументы:
-- _post_id - идентификатор сообщения.
create procedure sp_posts_uploads_delete_by_post
(
	_post_id int
)
begin
	delete from posts_uploads where post = _post_id;
end|

-- ------------------------
--  Работа с загрузками. --
-- ------------------------

-- Добавляет информацию о загрузке.
--
-- Аргументы:
-- _hash - Хеш файла.
-- _is_image - Флаг изображения.
-- _upload_type - Тип загрузки.
-- _file - Имя файла, URL, код видео.
-- _image_w - Ширина изображения.
-- _image_h - Высота изображения.
-- _size - Размер файла в байтах.
-- _thumbnail - Имя уменьшенной копии.
-- _thumbnail_w - Ширина уменьшенной копии.
-- _thumbnail_h - Высота уменьшенной копии.
create procedure sp_uploads_add
(
	_hash varchar(32),
	_is_image bit,
	_upload_type int,
	_file varchar(256),
	_image_w int,
	_image_h int,
	_size int,
	_thumbnail varchar(256),
	_thumbnail_w int,
	_thumbnail_h int
)
begin
	insert into uploads (`hash`, is_image, upload_type, `file`, image_w,
		image_h, `size`, thumbnail, thumbnail_w, thumbnail_h)
	values
	(_hash, _is_image, _upload_type, _file, _image_w,
		_image_h, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
	select last_insert_id() as id;
end|

-- Удаляет заданную загрузку.
--
-- Аргументы:
-- _id - Идентификатор загрузки.
create procedure sp_uploads_delete_by_id
(
	_id int
)
begin
	delete from uploads where id = _id;
end|

-- Выбирает загрузки для заданного сообщения.
--
-- Аргументы:
-- post_id - идентификатор сообщения.
create procedure sp_uploads_get_by_post
(
	post_id int
)
begin
	select id, `hash`, is_image, upload_type, `file`, image_w, image_h, `size`,
		thumbnail, thumbnail_w, thumbnail_h
	from uploads u
	join posts_uploads pu on pu.upload = u.id and pu.post = post_id;
end|

-- Выбирает информацию о висячих загрузках (не связанных с сообщениями).
create procedure sp_uploads_get_dangling ()
begin
	select u.id, u.`hash`, u.is_image, u.link_type, u.`file`, u.file_w,
		u.file_h, u.`size`, u.thumbnail, u.thumbnail_w, u.thumbnail_h
	from uploads u
	left join posts_uploads pu on pu.upload = u.id
	where pu.upload is null;
end|

-- Proc for test mysql bit type support
drop procedure if exists sp_test_mysql_bit|
create procedure sp_test_mysql_bit ()
begin
	insert into uploads (is_image, upload_type, `file`, `size`) values (1, 0, 'test mysql bit', 0);
	insert into uploads (is_image, upload_type, `file`, `size`) values (0, 0, 'test mysql bit', 0);
	select is_image from uploads where `file` = 'test mysql bit';
	delete from uploads where `file` = 'test mysql bit';
end|*/
