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

drop procedure if exists sp_favorites_add|
drop procedure if exists sp_favorites_delete|
drop procedure if exists sp_favorites_get_by_user|
drop procedure if exists sp_favorites_mark_readed|
drop procedure if exists sp_favorites_mark_readed_all|

drop procedure if exists sp_files_add|
drop procedure if exists sp_files_get_by_post|
drop procedure if exists sp_files_get_by_thread|
drop procedure if exists sp_files_get_dangling|
drop procedure if exists sp_files_get_same|

drop procedure if exists sp_groups_add|
drop procedure if exists sp_groups_delete|
drop procedure if exists sp_groups_get_all|
drop procedure if exists sp_groups_get_by_user|

drop procedure if exists sp_hard_ban_add|
drop procedure if exists sp_hard_ban_execute|

drop procedure if exists sp_hidden_threads_add|
drop procedure if exists sp_hidden_threads_delete|
drop procedure if exists sp_hidden_threads_get_by_board|
drop procedure if exists sp_hidden_threads_get_visible|

drop procedure if exists sp_images_add|
drop procedure if exists sp_images_get_by_board|
drop procedure if exists sp_images_get_by_post|
drop procedure if exists sp_images_get_by_thread|
drop procedure if exists sp_images_get_dangling|
drop procedure if exists sp_images_get_same|

drop procedure if exists sp_languages_add|
drop procedure if exists sp_languages_delete|
drop procedure if exists sp_languages_get_all|

drop procedure if exists sp_links_add|
drop procedure if exists sp_links_get_by_post|
drop procedure if exists sp_links_get_by_thread|
drop procedure if exists sp_links_get_dangling|

drop procedure if exists sp_macrochan_tags_add|
drop procedure if exists sp_macrochan_tags_delete_by_name|
drop procedure if exists sp_macrochan_tags_get_all|

drop procedure if exists sp_macrochan_images_add|
drop procedure if exists sp_macrochan_images_delete_by_name|
drop procedure if exists sp_macrochan_images_get_all|
drop procedure if exists sp_macrochan_images_get_random|

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
drop procedure if exists sp_posts_get_all_numbers|
drop procedure if exists sp_posts_get_by_board|
drop procedure if exists sp_posts_get_by_thread|
drop procedure if exists sp_posts_get_reported_by_board|
drop procedure if exists sp_posts_get_visible_by_id|
drop procedure if exists sp_posts_get_visible_by_number|
drop procedure if exists sp_posts_get_visible_by_thread|
drop procedure if exists sp_posts_search_visible_by_board|

drop procedure if exists sp_posts_files_add|
drop procedure if exists sp_posts_files_delete_by_post|
drop procedure if exists sp_posts_files_delete_marked|
drop procedure if exists sp_posts_files_get_by_post|

drop procedure if exists sp_posts_images_add|
drop procedure if exists sp_posts_images_delete_by_post|
drop procedure if exists sp_posts_images_delete_marked|
drop procedure if exists sp_posts_images_get_by_post|

drop procedure if exists sp_posts_links_add|
drop procedure if exists sp_posts_links_delete_by_post|
drop procedure if exists sp_posts_links_delete_marked|
drop procedure if exists sp_posts_links_get_by_post|

drop procedure if exists sp_posts_videos_add|
drop procedure if exists sp_posts_videos_delete_by_post|
drop procedure if exists sp_posts_videos_delete_marked|
drop procedure if exists sp_posts_videos_get_by_post|

drop procedure if exists sp_reports_add|
drop procedure if exists sp_reports_delete|
drop procedure if exists sp_reports_get_all|

drop procedure if exists sp_spamfilter_add|
drop procedure if exists sp_spamfilter_delete|
drop procedure if exists sp_spamfilter_get_all|

drop procedure if exists sp_stylesheets_add|
drop procedure if exists sp_stylesheets_delete|
drop procedure if exists sp_stylesheets_get_all|

drop procedure if exists sp_threads_add|
drop procedure if exists sp_threads_delete_marked|
drop procedure if exists sp_threads_edit|
drop procedure if exists sp_threads_edit_archived_postlimit|
drop procedure if exists sp_threads_edit_deleted|
drop procedure if exists sp_threads_edit_original_post|
drop procedure if exists sp_threads_get_all|
drop procedure if exists sp_threads_get_archived|
drop procedure if exists sp_threads_get_by_id|
drop procedure if exists sp_threads_get_by_original_post|
drop procedure if exists sp_threads_get_changeable_by_id|
drop procedure if exists sp_threads_get_moderatable|
drop procedure if exists sp_threads_get_moderatable_by_id|
drop procedure if exists sp_threads_get_visible_by_board|
drop procedure if exists sp_threads_get_visible_by_original_post|
drop procedure if exists sp_threads_get_visible_count|
drop procedure if exists sp_threads_move_thread|
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
drop procedure if exists sp_users_get_admins|
drop procedure if exists sp_users_get_by_keyword|
drop procedure if exists sp_users_set_goto|
drop procedure if exists sp_users_set_password|

drop procedure if exists sp_videos_add|
drop procedure if exists sp_videos_get_by_post|
drop procedure if exists sp_videos_get_by_thread|
drop procedure if exists sp_videos_get_dangling|

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

-- --------
--  ACL. --
-- --------

-- Add rule to ACL.
create procedure sp_acl_add
(
    group_id int,   -- Group id.
    board_id int,   -- Board id.
    thread_id int,  -- Thread id.
    post_id int,    -- Post id.
    _view bit,      -- View permission.
    _change bit,    -- Change permission.
    _moderate bit   -- Moderate permission.
)
begin
    insert into acl (`group`, board, thread, post, `view`, `change`, moderate)
    values (group_id, board_id, thread_id, post_id, _view, _change, _moderate);
end|

-- Delete rule.
create procedure sp_acl_delete
(
    group_id int,   -- Group id.
    board_id int,   -- Board id.
    thread_id int,  -- Thread id.
    post_id int     -- Post id.
)
begin
    delete from acl
        where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
            and ((board = board_id) or (coalesce(board, board_id) is null))
            and ((thread = thread_id) or (coalesce(thread, thread_id) is null))
            and ((post = post_id) or (coalesce(post, post_id) is null));
end|

-- Edit ACL rule.
create procedure sp_acl_edit
(
    group_id int,   -- Group id.
    board_id int,   -- Board id.
    thread_id int,  -- Thread id.
    post_id int,    -- Post id.
    _view bit,      -- View permission.
    _change bit,    -- Change permission.
    _moderate bit   -- Moderate permission.
)
begin
    update acl set `view` = _view, `change` = _change, moderate = _moderate
        where ((`group` = group_id) or (coalesce(`group`, group_id) is null))
            and ((board = board_id) or (coalesce(board, board_id) is null))
            and ((thread = thread_id) or (coalesce(thread, thread_id) is null))
            and ((post = post_id) or (coalesce(post, post_id) is null));
end|

-- Select rules.
create procedure sp_acl_get_all ()
begin
    select `group`,
           board,
           thread,
           post,
           `view`,
           `change`,
           moderate
        from acl
        order by `group`, board, thread, post;
end|

-- ---------
--  Bans. --
-- ---------

-- Ban IP-address range.
create procedure sp_bans_add
(
    _range_beg int,     -- Begin of banned IP-address range.
    _range_end int,     -- End of banned IP-address range.
    _reason text,       -- Ban reason.
    _untill datetime    -- Expiration time.
)
begin
    call sp_bans_refresh();
    insert into bans (range_beg, range_end, reason, untill)
        values (_range_beg, _range_end, _reason, _untill);
end|

-- Checks if IP-address banned. If it is then return information about widest
-- banned range of IP-addresses what contains ip.
create procedure sp_bans_check
(
    ip int  -- IP-address.
)
begin
    call sp_bans_refresh();
    select range_beg, range_end, untill, reason
        from bans
        where range_beg <= ip and range_end >= ip
        order by range_end desc limit 1;
end|

-- Delete ban.
create procedure sp_bans_delete_by_id
(
    _id int -- Id.
)
begin
    delete from bans where id = _id;
end|

-- Delete certain ip bans.
create procedure sp_bans_delete_by_ip
(
    ip int  -- IP-address.
)
begin
    delete from bans where range_beg <= ip and range_end >= ip;
end|

-- Select bans.
create procedure sp_bans_get_all ()
begin
    select id, range_beg, range_end, reason, untill from bans;
end|

-- Удаляет все истекшие блокировки.
create procedure sp_bans_refresh ()
begin
    delete from bans where untill <= now();
end|

-- -----------------------
--  Board upload types. --
-- -----------------------

-- Add new board upload type relation.
create procedure sp_board_upload_types_add
(
    board_id int,       -- Board id.
    upload_type_id int  -- Upload type id.
)
begin
    insert into board_upload_types (board, upload_type)
        values (board_id, upload_type_id);
end|

-- Delete board upload type relation.
create procedure sp_board_upload_types_delete
(
    board int,       -- Board id.
    upload_type int  -- Upload type id.
)
begin
    delete from board_upload_types
        where board = _board and upload_type = _upload_type;
end|

-- Select board upload types relations.
create procedure sp_board_upload_types_get_all ()
begin
    select board, upload_type from board_upload_types;
end|

-- ---------------------
--  Работа с досками. --
-- ---------------------

-- Добавляет доску.
create procedure sp_boards_add
(
    _name varchar(16),          -- Name.
    _title varchar(50),         -- Title.
    _annotation text,           -- Annotation.
    _bump_limit int,            -- Board specific bump limit.
    _force_anonymous bit,       -- Hide name flag.
    _default_name varchar(128), -- Default name.
    _with_attachments bit,      -- Attachments flag.
    _enable_macro bit,          -- Macrochan integration flag.
    _enable_youtube bit,        -- Youtube video posting flag.
    _enable_captcha bit,        -- Captcha flag.
    _enable_translation bit,    -- Translation flag.
    _enable_geoip bit,          -- GeoIP flag.
    _enable_shi bit,            -- Painting flag.
    _enable_postid bit,         -- Post identification flag.
    _same_upload varchar(32),   -- Upload policy from same files.
    _popdown_handler int,       -- Popdown handler id.
    _category int               -- Category id.
)
begin
    insert into boards (name,
                        title,
                        annotation,
                        bump_limit,
                        force_anonymous,
                        default_name,
                        with_attachments,
                        enable_macro,
                        enable_youtube,
                        enable_captcha,
                        enable_translation,
                        enable_geoip,
                        enable_shi,
                        enable_postid,
                        same_upload,
                        popdown_handler,
                        category)
                values (_name,
                        _title,
                        _annotation,
                        _bump_limit,
                        _force_anonymous,
                        _default_name,
                        _with_attachments,
                        _enable_macro,
                        _enable_youtube,
                        _enable_captcha,
                        _enable_translation,
                        _enable_geoip,
                        _enable_shi,
                        _enable_postid,
                        _same_upload,
                        _popdown_handler,
                        _category);
end|

-- Delete board.
create procedure sp_boards_delete
(
    _id int -- Board id.
)
begin
    delete from boards where id = _id;
end|

-- Edit board.
create procedure sp_boards_edit
(
    _id int,                    -- Id.
    _title varchar(50),         -- Title.
    _annotation text,           -- Annotation.
    _bump_limit int,            -- Board specific bump limit.
    _force_anonymous bit,       -- Hide name flag.
    _default_name varchar(128), -- Default name.
    _with_attachments bit,      -- Attachments flag.
    _enable_macro bit,          -- Macrochan integration flag.
    _enable_youtube bit,        -- Youtube video posting flag.
    _enable_captcha bit,        -- Captcha flag.
    _enable_translation bit,    -- Translation flag.
    _enable_geoip bit,          -- GeoIP flag.
    _enable_shi bit,            -- Painting flag.
    _enable_postid bit,         -- Post identification flag.
    _same_upload varchar(32),   -- Upload policy from same files.
    _popdown_handler int,       -- Popdown handler id.
    _category int               -- Category id.
)
begin
    update boards set title = _title,
                      annotation = _annotation,
                      bump_limit = _bump_limit,
                      force_anonymous = _force_anonymous,
                      default_name = _default_name,
                      with_attachments = _with_attachments,
                      enable_macro = _enable_macro,
                      enable_youtube = _enable_youtube,
                      enable_captcha = _enable_captcha,
                      enable_translation = _enable_translation,
                      enable_geoip = _enable_geoip,
                      enable_shi = _enable_shi,
                      enable_postid = _enable_postid,
                      same_upload = _same_upload,
                      popdown_handler = _popdown_handler,
                      category = _category
        where id = _id;
end|

-- Select boards.
create procedure sp_boards_get_all ()
begin
    select b.id,
           b.name,
           b.title,
           b.annotation,
           b.bump_limit,
           b.force_anonymous,
           b.default_name,
           b.with_attachments,
           b.enable_macro,
           b.enable_youtube,
           b.enable_captcha,
           b.enable_translation,
           b.enable_geoip,
           b.enable_shi,
           b.enable_postid,
           b.same_upload,
           b.popdown_handler,
           b.category,
           c.name as category_name
        from boards b
        join categories c on c.id = b.category;
end|

-- Select board.
create procedure sp_boards_get_by_id
(
    board_id int    -- Board id.
)
begin
    select id,
           name,
           title,
           annotation,
           bump_limit,
           force_anonymous,
           default_name,
           with_attachments,
           enable_macro,
           enable_youtube,
           enable_captcha,
           same_upload,
           popdown_handler,
           category
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

-- Select changeable board.
create procedure sp_boards_get_changeable_by_id
(
    _board_id int,  -- Board id.
    user_id int     -- User id.
)
begin
    declare board_id int;
    select id into board_id from boards where id = _board_id;
    if (board_id is null) then
        select 'NOT_FOUND' as error;
    else
        select b.id,
               b.name,
               b.title,
               b.annotation,
               b.bump_limit,
               b.force_anonymous,
               b.default_name,
               b.with_attachments,
               b.enable_macro,
               b.enable_youtube,
               b.enable_captcha,
               b.same_upload,
               b.popdown_handler,
               b.category,
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

-- Select boards visible to user.
create procedure sp_boards_get_visible
(
    user_id int -- User id.
)
begin
    select b.id, b.name, b.title, b.annotation, b.bump_limit, b.force_anonymous,
            b.default_name, b.with_attachments, b.enable_macro, b.enable_youtube,
            b.enable_captcha, b.enable_translation, b.enable_geoip, b.enable_shi,
            b.enable_postid, b.same_upload, b.popdown_handler, b.category,
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

-- ----------------
--  Categories.. --
-- ----------------

-- Add category.
create procedure sp_categories_add
(
    _name varchar(50)   -- Name.
)
begin
    insert into categories (name) values (_name);
end|

-- Delete category.
create procedure sp_categories_delete
(
    _id int -- Id.
)
begin
    delete from categories where id = _id;
end|

-- Select categories.
create procedure sp_categories_get_all ()
begin
    select id, name from categories;
end|

-- -------------
-- Favorites. --
-- -------------

-- Adds thread to user's favorites.
create procedure sp_favorites_add
(
    _user int,      -- User id.
    _thread int     -- Thread id.
)
begin
    declare last_num int default 0;

    select max(p.number) into last_num
        from threads t
        join posts p on p.thread = t.id and p.thread = _thread
        where t.deleted = 0 and t.archived = 0 and p.deleted = 0
        order by p.number;

    if (last_num > 0) then
        insert into favorites (user, thread, last_readed)
            values (_user, _thread, last_num);
    end if;
end|

-- Removes thread from user's favorites.
create procedure sp_favorites_delete
(
    _user int,      -- User id.
    _thread int     -- Thread id.
)
begin
    delete from favorites where user = _user and thread = _thread;
end|

-- Select favorite threads.
create procedure sp_favorites_get_by_user
(
    _user int   -- User id.
)
begin
    select  u.id as user_id,
            u.keyword as user_keyword,
            u.posts_per_thread as user_posts_per_thread,
            u.threads_per_page as user_threads_per_page,
            u.lines_per_post as user_lines_per_post,
            u.language as user_language,
            u.stylesheet as user_stylesheet,
            u.password as user_password,
            u.`goto` as user_goto,

            p.id as post_id,
            p.board as post_board,
            p.thread as post_thread,
            p.number as post_number,
            p.user as post_user,
            p.password as post_password,
            p.name as post_name,
            p.tripcode as post_tripcode,
            p.ip as post_ip,
            p.subject as post_subject,
            p.date_time as post_date_time,
            p.text as post_text,
            p.sage as post_sage,

            t.id as thread_id,
            t.board as thread_board,
            t.original_post as thread_original_post,
            t.bump_limit as thread_bump_limit,
            t.deleted as thread_deleted,
            t.archived as thread_archived,
            t.sage as thread_sage,
            t.sticky as thread_sticky,
            t.with_attachments as thread_with_attachments,

            b.id as board_id,
            b.name as board_name,
            b.title as board_title,
            b.annotation as board_annotation,
            b.bump_limit as board_bump_limit,
            b.force_anonymous as board_force_anonymous,
            b.default_name as board_default_name,
            b.with_attachments as board_with_attachments,
            b.enable_macro as board_enable_macro,
            b.enable_youtube as board_enable_youtube,
            b.enable_captcha as board_enable_captcha,
            b.same_upload as board_same_upload,
            b.popdown_handler as board_popdown_handler,
            b.category as board_category,

            f.last_readed
        from favorites f
        join users u on u.id = f.user and u.id = _user
        join threads t on t.id = f.thread
        join boards b on b.id = t.board
        join posts p on p.thread = t.id and p.number = t.original_post;
end|

-- Mark thread as readed in user favorites. If thread is null then marks 
create procedure sp_favorites_mark_readed
(
    _user int,      -- User id.
    _thread int     -- Thread id.
)
begin
    declare last_num int default 0;

    select max(p.number) into last_num
        from threads t
        join posts p on p.thread = t.id and p.thread = _thread
        where t.deleted = 0 and t.archived = 0 and p.deleted = 0
        order by p.number;

    update favorites set last_readed = last_num
        where user = _user and thread = _thread;
end|

-- Mark all threads as readed.
create procedure sp_favorites_mark_readed_all
(
    _user int   -- User id.
)
begin
    declare last_num int default 0;
    declare done int default 0;
    declare _thread int;
    declare `c` cursor for select thread from favorites where user = _user;
    declare continue handler for not found set done = 1;

    open `c`;
    repeat
        fetch `c` into _thread;
        if(not done) then
            select max(p.number) into last_num
                from threads t
                join posts p on p.thread = t.id and p.thread = _thread
                where t.deleted = 0 and t.archived = 0 and p.deleted = 0
                order by p.number;

            update favorites set last_readed = last_num
                where user = _user and thread = _thread;
        end if;
    until done end repeat;
    close `c`;
end|

-- ---------
-- Files. --
-- ---------

-- Add file.
create procedure sp_files_add
(
    _hash varchar(32),          -- Hash.
    _name varchar(256),         -- Name.
    _size int,                  -- Size in bytes.
    _thumbnail varchar(256),    -- Thumbnail.
    _thumbnail_w int,           -- Thumbnail width.
    _thumbnail_h int            -- Thumbnail height.
)
begin
    insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
        values (_hash, _name, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
    select last_insert_id() as id;
end|

-- Select files.
create procedure sp_files_get_by_post
(
    post_id int -- Post id.
)
begin
    select f.id,
           f.hash,
           f.name,
           f.size,
           f.thumbnail,
           f.thumbnail_w,
           f.thumbnail_h
        from posts_files pf
        join files f on f.id = pf.file and pf.post = post_id;
end|

-- Get thread files.
create procedure sp_files_get_by_thread
(
    thread_id int -- Thread id.
)
begin
    select f.id, f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w, f.thumbnail_h
        from files f
        join posts_files pf on pf.file = f.id
        join posts p on p.id = pf.post and p.thread = thread_id;
end|

-- Get dangling files.
create procedure sp_files_get_dangling ()
begin
    select f.id,
           f.hash,
           f.name,
           f.size,
           f.thumbnail,
           f.thumbnail_w,
           f.thumbnail_h
        from files f
        left join posts_files pf on pf.file = f.id
        where pf.post is null;
end|

-- Select same files.
create procedure sp_files_get_same
(
    board_id int,           -- Board id.
    user_id int,            -- User id.
    file_hash varchar(32)   -- File hash.
)
begin
    select f.id as file_id,
           f.hash as file_hash,
           f.name as file_name,
           f.size as file_size,
           f.thumbnail as file_thumbnail,
           f.thumbnail_w as file_thumbnail_w,
           f.thumbnail_h as file_thumbnail_h,

           p.id as post_id,
           p.board as post_board,
           p.thread as post_thread,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.`text` as post_text,
           p.sage as post_sage,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments,

           max(case when a1.`view` = 0 then 0
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

-- -----------
--  Groups. --
-- -----------

-- Add group.
create procedure sp_groups_add
(
    _name varchar(50)   -- Group name.
)
begin
    insert into groups (name) values (_name);
    select id from groups where name = _name;
end|

-- Delete group.
create procedure sp_groups_delete
(
    _id int -- Id.
)
begin
    delete from groups where id = _id;
end|

-- Select groups.
create procedure sp_groups_get_all ()
begin
    select id, name from groups order by id;
end|

-- Выбирает группы, в которые входит пользователь.
create procedure sp_groups_get_by_user
(
    user_id int -- Идентификатор пользователя.
)
begin
    select g.id, g.name
        from users u
        join user_groups ug on ug.user = u.id and u.id = user_id
        join groups g on ug.`group` = g.id;
end|

-- -------------
-- Hard bans. --
-- -------------

-- Ban IP-address range in firewall.
create procedure sp_hard_ban_add
(
    _range_beg varchar(15), -- Begin of banned IP-address range.
    _range_end varchar(15)  -- End of banned IP-address range.
)
begin
    insert into hard_ban (range_beg, range_end) values (_range_beg, _range_end);
end|

-- Выбирает все диапазоны IP-адресов для блокировки и очищает таблицу.
create procedure sp_hard_ban_execute ()
begin
    select range_beg, range_end from hard_ban;
    delete from hard_ban where 1 = 1;
end|

-- -------------------
--  Hidden threads. --
-- -------------------

-- Hide thread.
create procedure sp_hidden_threads_add
(
    thread_id int,  -- Thread id.
    user_id int     -- User id.
)
begin
    insert into hidden_threads (user, thread) values (user_id, thread_id);
end|

-- Unhide thread
create procedure sp_hidden_threads_delete
(
    thread_id int,  -- Thread id.
    user_id int     -- User id.
)
begin
    delete from hidden_threads where user = user_id and thread = thread_id;
end|

-- Select hidden threads.
create procedure sp_hidden_threads_get_by_board
(
    board_id int    -- Board id.
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

-- ----------
-- Images. --
-- ----------

-- Add image.
create procedure sp_images_add
(
    _hash varchar(32),          -- Hash.
    _name varchar(256),         -- Name.
    _widht int,                 -- Width.
    _height int,                -- Height.
    _size int,                  -- Size in bytes.
    _thumbnail varchar(256),    -- Thumbnail.
    _thumbnail_w int,           -- Thumbnail width.
    _thumbnail_h int,           -- Thumbnail height.
    _spoiler bit                -- Spoiler flag.
)
begin
    insert into images (hash, name, widht, height, size, thumbnail, thumbnail_w, thumbnail_h, spoiler)
        values (_hash, _name, _widht, _height, _size, _thumbnail, _thumbnail_w, _thumbnail_h, _spoiler);
    select last_insert_id() as id;
end|

-- Select images.
create procedure sp_images_get_by_board
(
    _board int  -- Board id.
)
begin
    select i.id,
           i.hash,
           i.name,
           i.widht,
           i.height,
           i.size,
           i.thumbnail,
           i.thumbnail_w,
           i.thumbnail_h,
           i.spoiler
        from images i
        join posts_images pi on pi.image = i.id
        join posts p on pi.post = p.id
        where p.board = _board;
end|

-- Select images.
create procedure sp_images_get_by_post
(
    post_id int -- Post id.
)
begin
    select i.id,
           i.hash,
           i.name,
           i.widht,
           i.height,
           i.size,
           i.thumbnail,
           i.thumbnail_w,
           i.thumbnail_h,
           i.spoiler
        from posts_images pi
        join images i on i.id = pi.image and pi.post = post_id;
end|

-- Get thread mages.
create procedure sp_images_get_by_thread
(
    thread_id int -- Thread id.
)
begin
    select i.id, i.hash, i.name, i.widht, i.height, i.size, i.thumbnail, i.thumbnail_w, i.thumbnail_h, i.spoiler
        from images i
        join posts_images pi on i.id = pi.image
        join posts p on p.id = pi.post and p.thread = thread_id;
end|

-- Select dangling images.
create procedure sp_images_get_dangling ()
begin
    select i.id,
           i.hash,
           i.name,
           i.widht,
           i.height,
           i.size,
           i.thumbnail,
           i.thumbnail_w,
           i.thumbnail_h,
           i.spoiler
        from images i
        left join posts_images pi on pi.image = i.id
        where pi.post is null;
end|

-- Select same images.
create procedure sp_images_get_same
(
    board_id int,           -- Board id.
    user_id int,            -- User id.
    image_hash varchar(32)  -- Image file hash.
)
begin
    select i.id as image_id,
           i.hash as image_hash,
           i.name as image_name,
           i.widht as image_widht,
           i.height as image_height,
           i.size as image_size,
           i.thumbnail as image_thumbnail,
           i.thumbnail_w as image_thumbnail_w,
           i.thumbnail_h as image_thumbnail_h,
           i.spoiler as image_spoiler,

           p.id as post_id,
           p.board as post_board,
           p.thread as post_thread,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.`text` as post_text,
           p.sage as post_sage,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments,

           max(case when a1.`view` = 0 then 0
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

-- --------------
--  Languages. --
-- --------------

-- Add languages.
create procedure sp_languages_add
(
    _code char(3)   -- ISO_639-2 code.
)
begin
    insert into languages (code) values (_code);
end|

-- Delete language.
create procedure sp_languages_delete
(
    _id int -- Id.
)
begin
    delete from languages where id = _id;
end|

-- Select languages.
create procedure sp_languages_get_all ()
begin
	select id, code from languages;
end|

-- ---------
-- Links. --
-- ---------

-- Add link.
create procedure sp_links_add
(
    _url varchar(256),          -- URL.
    _widht int,                 -- Width.
    _height int,                -- Height.
    _size int,                  -- Size in bytes.
    _thumbnail varchar(256),    -- Thumbnail URL.
    _thumbnail_w int,           -- Thumbnail width.
    _thumbnail_h int            -- Thumbnail height.
)
begin
    insert into links (url, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
        values (_url, _widht, _height, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
    select last_insert_id() as id;
end|

-- Select links.
create procedure sp_links_get_by_post
(
    post_id int -- Post id.
)
begin
    select l.id,
           l.url,
           l.widht,
           l.height,
           l.size,
           l.thumbnail,
           l.thumbnail_w,
           l.thumbnail_h
        from posts_links pl
        join links l on l.id = pl.link and pl.post = post_id;
end|

-- Get thread links.
create procedure sp_links_get_by_thread
(
    thread_id int -- Thread id.
)
begin
    select l.id, l.url, l.widht, l.height, l.size, l.thumbnail, l.thumbnail_w,
            l.thumbnail_h
        from links l
        join posts_links pl on l.id = pl.link
        join posts p on p.id = pl.post and p.thread = thread_id;
end|

-- Select dangling links.
create procedure sp_links_get_dangling ()
begin
    select l.id,
           l.url,
           l.widht,
           l.height,
           l.size,
           l.thumbnail,
           l.thumbnail_w,
           l.thumbnail_h
        from links l
        left join posts_links pl on pl.link = l.id
        where pl.post is null;
end|

-- ------------------
-- Macrochan tags. --
-- ------------------

-- Add tag.
create procedure sp_macrochan_tags_add
(
    _name varchar(256)  -- Tag name.
)
begin
    insert into macrochan_tags (name) values (_name);
end|

-- Delete tag.
create procedure sp_macrochan_tags_delete_by_name
(
    _name varchar(256)  -- Tag name.
)
begin
    declare _id int default null;

    select id into _id from macrochan_tags where name = _name;
    if (_id is not null) then
        delete from macrochan_tags_images where tag = _id;
        delete from macrochan_tags where id = _id;
    end if;
end|

-- Select macrochan tags.
create procedure sp_macrochan_tags_get_all ()
begin
    select id, name from macrochan_tags;
end|

-- --------------------
-- Macrochan images. --
-- --------------------

-- Add macrochan image.
create procedure sp_macrochan_images_add
(
    _name varchar(256),         -- Name.
    _width int,                 -- Width.
    _height int,                -- Height.
    _size int,                  -- Size in bytes.
    _thumbnail varchar(256),    -- Thumbnail.
    _thumbnail_w int,           -- Thumbnail width.
    _thumbnail_h int            -- Thumbnail height.
)
begin
    insert into macrochan_images (name, width, height, size, thumbnail, thumbnail_w, thumbnail_h)
        values (_name, _width, _height, _size, _thumbnail, _thumbnail_w, _thumbnail_h);
end|

-- Delete macrochan image.
create procedure sp_macrochan_images_delete_by_name
(
    _name varchar(256)  -- Image name.
)
begin
    declare _id int default null;

    select id into _id from macrochan_images where name = _name;
    if (_id is not null) then
        delete from macrochan_tags_images where image = _id;
        delete from macrochan_images where id = _id;
    end if;
end|

-- Select macrochan images.
create procedure sp_macrochan_images_get_all ()
begin
    select id,
           name,
           width,
           height,
           size,
           thumbnail,
           thumbnail_w,
           thumbnail_h
        from macrochan_images;
end|

-- Select random macrochan image.
create procedure sp_macrochan_images_get_random
(
    _name varchar(256)    -- Tag name.
)
begin
    select mi.id,
           mi.name,
           mi.width,
           mi.height,
           mi.size,
           mi.thumbnail,
           mi.thumbnail_w,
           mi.thumbnail_h
        from macrochan_images mi
        join macrochan_tags_images mti on mi.id = mti.image
        join macrochan_tags mt on mti.tag = mt.id and mt.name = _name
        order by rand()
        limit 1;
end|

-- -----------------------------------
-- Macrochan tags images relations. --
-- -----------------------------------

-- Add tag image relation.
create procedure sp_macrochan_tags_images_add
(
    tag_name varchar(256),  -- Macrochan tag name.
    image_name varchar(256) -- Macrochan image name.
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

-- Get tag image relation.
create procedure sp_macrochan_tags_images_get
(
    tag_name varchar(256),  -- Macrochan tag name.
    image_name varchar(256) -- Macrochan image name.
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

-- ---------------------
--  Popdown handlers. --
-- ---------------------

-- Add popdown handler.
create procedure sp_popdown_handlers_add
(
    _name varchar(50)   -- Function name.
)
begin
    insert into popdown_handlers (name) values (_name);
end|

-- Delete popdown handeler.
create procedure sp_popdown_handlers_delete
(
    _id int -- Id.
)
begin
    delete from popdown_handlers where id = _id;
end|

-- Select popdown hanglers.
create procedure sp_popdown_handlers_get_all ()
begin
    select id, name from popdown_handlers;
end|

-- ----------
--  Posts. --
-- ----------

-- Add post.
create procedure sp_posts_add
(
    board_id int,           -- Board id.
    thread_id int,          -- Thread id.
    user_id int,            -- User id.
    _password varchar(128), -- Password.
    _name varchar(128),     -- Name.
    _tripcode varchar(128), -- Tripcode.
    _ip bigint,             -- IP-address.
    _subject varchar(128),  -- Subject.
    _date_time datetime,    -- Date.
    _text text,             -- Text.
    _sage bit               -- Sage flag.
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

-- Remove post.
create procedure sp_posts_delete
(
    _id int -- Post id.
)
begin
    declare thread_id int;
    set thread_id = null;
    -- If post is original remove all thread then.
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

-- Delete last posts.
create procedure sp_posts_delete_last
(
    _id int,            -- Post id.
    _date_time datetime -- Date.
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

-- Delete marked posts.
create procedure sp_posts_delete_marked ()
begin
    update posts p join threads t on t.id = p.thread and t.deleted = 1 set p.deleted = 1;

    delete pf from posts_files pf join posts p on p.id = pf.post where p.deleted = 1;

    delete pi from posts_images pi join posts p on p.id = pi.post where p.deleted = 1;

    delete pl from posts_links pl join posts p on p.id = pl.post where p.deleted = 1;

    delete pv from posts_videos pv join posts p on p.id = pv.post where p.deleted = 1;

    delete a from acl a join posts p on p.id = a.post where p.deleted = 1;

    delete from posts where deleted = 1;

    /*delete ht from hidden_threads ht join threads t on t.id = ht.thread where t.deleted = 1;

    delete a from acl a join threads t on t.id = a.thread where t.deleted = 1;

    delete from threads where deleted = 1;*/
end|

-- Add text to the end of message.
create procedure sp_posts_edit_text_by_id
(
    _id int,    -- Post id.
    _text text  -- Text.
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

-- Выбирает номера всех сообщений с соотвествующими номерами нитей и именами досок.
create procedure sp_posts_get_all_numbers ()
begin
    select p.`number` as post, t.`original_post` as thread, b.`name` as board
        from posts p
        join threads t on t.id = p.thread
        join boards b on b.id = p.board
        where p.deleted = 0 and t.deleted = 0 and t.archived = 0
        order by p.`number`, t.`original_post`, b.`name` asc;
end|

-- Get visible posts.
create procedure sp_posts_get_by_board
(
    board_id int    -- Board id.
)
begin
    select p.id as post_id,
           p.board as post_board,
           p.thread as post_thread,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.`text` as post_text,
           p.sage as post_sage,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments,
           count(pf.file) + count(pi.image) + count(pl.link) + count(pv.video) as attachments_count
        from posts p
        join threads t on t.id = p.thread
        join boards b on b.id = p.board
        left join posts_files pf on pf.post = p.id
        left join posts_images pi on pi.post = p.id
        left join posts_links pl on pl.post = p.id
        left join posts_videos pv on pv.post = p.id
        where p.deleted = 0 and t.deleted = 0 and t.archived = 0
            and p.board = board_id
        group by p.id
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

-- Get reported posts.
create procedure sp_posts_get_reported_by_board
(
    board_id int    -- Board id.
)
begin
    select p.id as post_id,
           p.number as post_number,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.text as post_text,
           p.sage as post_sage,
           p.user as post_user,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments

        from posts p
        join threads t on t.id = p.thread
        join boards b on b.id = p.board
        join reports r on p.id = r.post
        where p.deleted = 0 and t.deleted = 0 and t.archived = 0
            and p.board = board_id
        order by p.date_time desc;
end|

-- Select visible post.
create procedure sp_posts_get_visible_by_id
(
    post_id int,    -- Post id.
    user_id int     -- User id.
)
begin
    select p.id as post_id,
           p.board as post_board,
           p.thread as post_thread,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.`text` as post_text,
           p.sage as post_sage,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments
        from posts p
        left join threads t on t.id = p.thread
        left join boards b on b.id = p.board
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

-- Select visible post.
create procedure sp_posts_get_visible_by_number
(
    board_name varchar(16), -- Board name.
    post_number int,        -- Post number.
    user_id int             -- User id.
)
begin
    select p.id as post_id,
           p.thread as post_thread,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.text as post_text,
           p.sage as post_sage,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category

        from posts p
        join threads t on t.id = p.thread
        join boards b on b.id = p.board
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
        where b.name = board_name
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

-- Select posts visible to user and filter it.
create procedure sp_posts_get_visible_by_thread
(
    thread_id int,  -- Thread id.
    user_id int     -- User id.
)
begin
    select p.id as post_id,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.text as post_text,
           p.sage as post_sage,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category

        from posts p
        join threads t on t.board = p.board and t.id = p.thread
        join boards b on b.id = p.board
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

-- Search posts by keyword.
create procedure sp_posts_search_visible_by_board
(
    board_id int,   -- Board id.
    keyword text,   -- Keyword.
    user_id int     -- User id.
)
begin
    set keyword = CONCAT('%', keyword, '%');
    select p.id as post_id,
           p.board as post_board,
           p.thread as post_thread,
           p.number as post_number,
           p.user as post_user,
           p.password as post_password,
           p.name as post_name,
           p.tripcode as post_tripcode,
           p.ip as post_ip,
           p.subject as post_subject,
           p.date_time as post_date_time,
           p.`text` as post_text,
           p.sage as post_sage,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category,

           t.id as thread_id,
           t.board as thread_board,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.sage as thread_sage,
           t.sticky as thread_sticky,
           t.with_attachments as thread_with_attachments
        from posts p
        join threads t on t.id = p.thread
        join boards b on b.id = p.board and b.id = board_id
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
        where p.deleted = 0 and t.deleted = 0 and t.archived = 0
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
            and p.`text` like keyword
        group by p.id
        order by p.number asc;
end|

-- -------------------------
-- Posts files relations. --
-- -------------------------

-- Add post file relation.
create procedure sp_posts_files_add
(
    _post int,      -- Post id.
    _file int,      -- File id.
    _deleted bit    -- Mark to delete.
)
begin
    insert into posts_files (post, file, deleted)
        values (_post, _file, _deleted);
end|

-- Delete post attachemnts relations.
create procedure sp_posts_files_delete_by_post
(
    post_id int -- Post id.
)
begin
    update posts_files set deleted = 1 where post = post_id;
end|

-- Delete marked posts files relations.
create procedure sp_posts_files_delete_marked ()
begin
    delete from posts_files where deleted = 1;
end|

-- Select posts files relations.
create procedure sp_posts_files_get_by_post
(
    post_id int -- Post id.
)
begin
    select post, file, deleted from posts_files where post = post_id;
end|

-- --------------------------
-- Posts images relations. --
-- --------------------------

-- Add post image relation.
create procedure sp_posts_images_add
(
    _post int,      -- Post id.
    _image int,     -- Image id.
    _deleted bit    -- Mark to delete.
)
begin
    insert into posts_images (post, image, deleted)
        values (_post, _image, _deleted);
end|

-- Delete post images relations.
create procedure sp_posts_images_delete_by_post
(
    post_id int -- Post id.
)
begin
    update posts_images set deleted = 1 where post = post_id;
end|

-- Delete marked posts images relations.
create procedure sp_posts_images_delete_marked ()
begin
    delete from posts_images where deleted = 1;
end|

-- Select posts images relations.
create procedure sp_posts_images_get_by_post
(
    post_id int -- Post id.
)
begin
    select post, image, deleted from posts_images where post = post_id;
end|

-- -------------------------
-- Posts links relations. --
-- -------------------------

-- Add post link relation.
create procedure sp_posts_links_add
(
    _post int,      -- Post id.
    _link int,      -- Link id.
    _deleted bit    -- Mark to delete.
)
begin
    insert into posts_links (post, link, deleted)
        values (_post, _link, _deleted);
end|

-- Delete post links relations.
create procedure sp_posts_links_delete_by_post
(
    post_id int -- Post id.
)
begin
    update posts_links set deleted = 1 where post = post_id;
end|

-- Delete marked posts links relations.
create procedure sp_posts_links_delete_marked ()
begin
    delete from posts_links where deleted = 1;
end|

-- Select posts links relations.
create procedure sp_posts_links_get_by_post
(
    post_id int -- Post id.
)
begin
    select post, link, deleted from posts_links where post = post_id;
end|

-- --------------------------
-- Posts videos relations. --
-- --------------------------

-- Add post video relation.
create procedure sp_posts_videos_add
(
    _post int,      -- Post id.
    _video int,     -- Video id.
    _deleted bit    -- Mark to delete.
)
begin
    insert into posts_videos (post, video, deleted)
        values (_post, _video, _deleted);
end|

-- Delete post videos relations.
create procedure sp_posts_videos_delete_by_post
(
    post_id int -- Post id.
)
begin
    update posts_videos set deleted = 1 where post = post_id;
end|

-- Delete marked posts videos relations.
create procedure sp_posts_videos_delete_marked ()
begin
    delete from posts_videos where deleted = 1;
end|

-- Select posts videos relations.
create procedure sp_posts_videos_get_by_post
(
    post_id int -- Post id.
)
begin
    select post, video, deleted from posts_videos where post = post_id;
end|

-- ------------
--  Reports. --
-- ------------

-- Add report.
create procedure sp_reports_add
(
    _post int   -- Post id.
)
begin
    declare found int default 0;

    select count(post) into found from reports where post = _post;
    if (found = 0) then
        insert into reports (post) values (_post);
    end if;
end|

-- Delete report.
create procedure sp_reports_delete
(
    _post int   -- Post id.
)
begin
    delete from reports where post = _post;
end|

-- Выбирает все жалобы.
create procedure sp_reports_get_all ()
begin
    select post from reports;
end|

-- ---------------
--  Spamfilter. --
-- ---------------

-- Add pattern to spamfilter.
create procedure sp_spamfilter_add
(
    _pattern varchar(256)   -- Pattern.
)
begin
    insert into spamfilter (pattern) values (_pattern);
end|

-- Delete pattern from spamfilter.
create procedure sp_spamfilter_delete
(
    _id int -- Id.
)
begin
    delete from spamfilter where id = _id;
end|

-- Select spamfilter records.
create procedure sp_spamfilter_get_all ()
begin
    select id, pattern from spamfilter;
end|

-- ----------------
--  Stylesheets. --
-- ----------------

-- Add style.
create procedure sp_stylesheets_add
(
    _name varchar(50)   -- Stylesheet name.
)
begin
    insert into stylesheets (name) values (_name);
end|

-- Delete stylesheet.
create procedure sp_stylesheets_delete
(
    _id int -- Id.
)
begin
    delete from stylesheets where id = _id;
end|

-- Select stylesheets.
create procedure sp_stylesheets_get_all ()
begin
    select id, name from stylesheets;
end|

-- --------------------
--  Работа с нитями. --
-- --------------------

-- Добавляет нить. Если номер оригинального сообщения null, то будет создана
-- пустая нить.
create procedure sp_threads_add
(
    board_id int,           -- Идентификатор доски.
    _original_post int,     -- Номер оригинального сообщения.
    _bump_limit int,        -- Специфичный для нити бамплимит.
    _sage bit,              -- Флаг поднятия нити.
    _with_attachments bit   -- Флаг вложений.
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

-- Delete marked threads.
create procedure sp_threads_delete_marked ()
begin
    delete ht from hidden_threads ht join threads t on t.id = ht.thread where t.deleted = 1;

    delete a from acl a join threads t on t.id = a.thread where t.deleted = 1;

    delete from threads where deleted = 1;
end|

-- Edit thread.
create procedure sp_threads_edit
(
    _id int,                -- Thread id.
    _bump_limit int,        -- Thread specific bumplimit.
    _sticky bit,            -- Sage flag.
    _sage bit,              -- Sticky flag.
    _with_attachments bit   -- Attachments flag.
)
begin
    update threads set bump_limit = _bump_limit,
                       sticky = _sticky,
                       sage = _sage,
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

-- Get threads.
create procedure sp_threads_get_all ()
begin
	select id,
               board,
               original_post,
               bump_limit,
               sage,
               sticky,
               with_attachments
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

-- Select thread.
create procedure sp_threads_get_by_id
(
    _id int -- Thread id.
)
begin
    select  t.id as thread_id,
            t.original_post as thread_original_post,
            t.bump_limit as thread_bump_limit,
            t.sage as thread_sage,
            t.sticky as thread_sticky,
            t.with_attachments as thread_with_attachments,

            b.id as board_id,
            b.name as board_name,
            b.title as board_title,
            b.annotation as board_annotation,
            b.bump_limit as board_bump_limit,
            b.force_anonymous as board_force_anonymous,
            b.default_name as board_default_name,
            b.with_attachments as board_with_attachments,
            b.enable_macro as board_enable_macro,
            b.enable_youtube as board_enable_youtube,
            b.enable_captcha as board_enable_captcha,
            b.same_upload as board_same_upload,
            b.popdown_handler as board_popdown_handler,
            b.category as board_category
        from threads t
        join boards b on b.id = t.board
        where t.id = _id and t.deleted = 0 and t.archived = 0;
end|

-- Get thread.
create procedure sp_threads_get_by_original_post
(
    _board int,         -- Board id.
    _original_post int  -- Thread number.
)
begin
    select id, original_post, bump_limit, sticky, archived, sage, with_attachments
        from threads
        where original_post = _original_post
            and board = _board
            and deleted = 0
            and archived = 0;
end|

-- Get changeable thread.
create procedure sp_threads_get_changeable_by_id
(
    thread_id int,  -- Thread id.
    user_id int     -- User id.
)
begin
    select t.id as thread_id,
           t.original_post as thread_original_post,
           t.bump_limit as thread_bump_limit,
           t.archived as thread_archived,
           t.sage as thread_sage,
           t.with_attachments as thread_with_attachments,

           b.id as board_id,
           b.name as board_name,
           b.title as board_title,
           b.annotation as board_annotation,
           b.bump_limit as board_bump_limit,
           b.force_anonymous as board_force_anonymous,
           b.default_name as board_default_name,
           b.with_attachments as board_with_attachments,
           b.enable_macro as board_enable_macro,
           b.enable_youtube as board_enable_youtube,
           b.enable_captcha as board_enable_captcha,
           b.enable_translation as board_enable_translation,
           b.enable_geoip as board_enable_geoip,
           b.enable_shi as board_enable_shi,
           b.enable_postid as board_enable_postid,
           b.same_upload as board_same_upload,
           b.popdown_handler as board_popdown_handler,
           b.category as board_category

        from threads t
        join boards b on b.id = t.board
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

-- Select moderatable threads.
create procedure sp_threads_get_moderatable
(
    user_id int -- User id.
)
begin
    select t.id,
           t.board,
           t.original_post,
           t.bump_limit,
           t.sage,
           t.sticky,
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

-- Select moderatable thread.
create procedure sp_threads_get_moderatable_by_id
(
    thread_id int,  -- Thread id.
    user_id int     -- User id.
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

-- Select threads visible to user on specified board.
create procedure sp_threads_get_visible_by_board
(
    board_id int,   -- Board id.
    user_id int     -- User id.
)
begin
    select q1.thread_id,
           q1.thread_original_post,
           q1.thread_bump_limit,
           q1.thread_sticky,
           q1.thread_sage,
           q1.thread_with_attachments,
           q1.thread_posts_count,
           q1.thread_last_post_num,

           q1.board_id,
           q1.board_name,
           q1.board_title,
           q1.board_annotation,
           q1.board_bump_limit,
           q1.board_force_anonymous,
           q1.board_default_name,
           q1.board_with_attachments,
           q1.board_enable_macro,
           q1.board_enable_youtube,
           q1.board_enable_captcha,
           q1.board_enable_translation,
           q1.board_enable_geoip,
           q1.board_enable_shi,
           q1.board_enable_postid,
           q1.board_same_upload,
           q1.board_popdown_handler,
           q1.board_category

        from (
            -- Find number of last post in the thread except "sage" posts.
            select q.thread_id,
                   q.thread_original_post,
                   q.thread_bump_limit,
                   q.thread_sticky,
                   q.thread_sage,
                   q.thread_with_attachments,
                   q.thread_posts_count,
                   max(p.number) as thread_last_post_num,

                   q.board_id,
                   q.board_name,
                   q.board_title,
                   q.board_annotation,
                   q.board_bump_limit,
                   q.board_force_anonymous,
                   q.board_default_name,
                   q.board_with_attachments,
                   q.board_enable_macro,
                   q.board_enable_youtube,
                   q.board_enable_captcha,
                   q.board_enable_translation,
                   q.board_enable_geoip,
                   q.board_enable_shi,
                   q.board_enable_postid,
                   q.board_same_upload,
                   q.board_popdown_handler,
                   q.board_category

                from posts p
                join (
                    -- Select visible thread and calculate count of visible posts within.
                    select t.id as thread_id,
                           t.original_post as thread_original_post,
                           t.bump_limit as thread_bump_limit,
                           t.sticky as thread_sticky,
                           t.sage as thread_sage,
                           t.with_attachments as thread_with_attachments,
                           count(distinct p.id) as thread_posts_count,

                           b.id as board_id,
                           b.name as board_name,
                           b.title as board_title,
                           b.annotation as board_annotation,
                           b.bump_limit as board_bump_limit,
                           b.force_anonymous as board_force_anonymous,
                           b.default_name as board_default_name,
                           b.with_attachments as board_with_attachments,
                           b.enable_macro as board_enable_macro,
                           b.enable_youtube as board_enable_youtube,
                           b.enable_captcha as board_enable_captcha,
                           b.enable_translation as board_enable_translation,
                           b.enable_geoip as board_enable_geoip,
                           b.enable_shi as board_enable_shi,
                           b.enable_postid as board_enable_postid,
                           b.same_upload as board_same_upload,
                           b.popdown_handler as board_popdown_handler,
                           b.category as board_category

                        from posts p
                        join threads t on t.id = p.thread and t.board = board_id
                        join boards b on b.id = t.board
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
                group by t.id) q on q.thread_id = p.thread
                    and (p.sage = 0 or p.sage is null) and p.deleted = 0
            group by q.thread_id) q1
        order by q1.thread_last_post_num desc;
end|

-- Select visible threads.
create procedure sp_threads_get_visible_by_original_post
(
    _board int,         -- Board id.
    _original_post int, -- Original post number.
    user_id int         -- User id.
)
begin
    declare thread_id int;
    select id into thread_id from threads
        where original_post = _original_post and board = _board;
    if thread_id is null
    then
        select 'NOT_FOUND' as error;
    else
        select t.id as thread_id,
               t.original_post as thread_original_post,
               t.bump_limit as thread_bump_limit,
               t.sticky as thread_sticky,
               t.archived as thread_archived,
               t.sage as thread_sage,
               t.with_attachments as thread_with_attachments,

               b.id as board_id,
               b.name as board_name,
               b.title as board_title,
               b.annotation as board_annotation,
               b.bump_limit as board_bump_limit,
               b.force_anonymous as board_force_anonymous,
               b.default_name as board_default_name,
               b.with_attachments as board_with_attachments,
               b.enable_macro as board_enable_macro,
               b.enable_youtube as board_enable_youtube,
               b.enable_captcha as board_enable_captcha,
               b.enable_translation as board_enable_translation,
               b.enable_geoip as board_enable_geoip,
               b.enable_shi as board_enable_shi,
               b.enable_postid as board_enable_postid,
               b.same_upload as board_same_upload,
               b.popdown_handler as board_popdown_handler,
               b.category as board_category,

               count(p.id) as visible_posts_count
            from posts p
            join threads t on t.id = p.thread
            join boards b on b.id = t.board
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

-- Calculate count of visible threads.
create procedure sp_threads_get_visible_count
(
    user_id int,    -- User id.
    board_id int    -- Board id.
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

-- Move thread.
create procedure sp_threads_move_thread
(
    _id int,    -- Thread id.
    _board int  -- Board id.
)
begin
    declare _bump_limit int;
    declare _sage bit;
    declare _with_attachments bit;
    declare _thread int;
    declare post_id int;
    declare _user int;
    declare _password varchar(128);
    declare _name varchar(128);
    declare _tripcode varchar(128);
    declare _ip bigint;
    declare _subject varchar(128);
    declare _date_time datetime;
    declare _text text;

    -- New original post number.
    declare _number int;

    declare old_original_post_id int;
    declare new_original_post_id int;
    declare new_post_id int;
    declare attachment_id int default null;
    declare attachments_count int;
    declare done int default 0;

    -- Select all posts except original.
    declare `c` cursor for
        select p.id, p.`user`, p.password, p.`name`, p.tripcode,
               p.ip, p.subject, p.date_time, p.text, p.sage
            from posts p
            join threads t on p.thread = t.id and t.id = _id
            where p.number != t.original_post;

    declare continue handler for not found set done = 1;

    select p.id into old_original_post_id
        from posts p
        join threads t on p.thread = t.id and t.id = _id
        where p.number = t.original_post;

    -- Copy thread.
    select bump_limit, sage, with_attachments
            into _bump_limit, _sage, _with_attachments
        from threads where id = _id;
    call sp_threads_add(_board, null, _bump_limit, _sage, _with_attachments);
    -- TODO: А что если ещё какой-то тред будет создан между моментов вызова sp_threads_add() и этим селектом?
    select last_insert_id() into _thread;

    -- Calculate new original post number and copy original post.
    select max(number) into _number from posts where board = _board;
    if(_number is null) then
        set _number = 1;
    else
        set _number = _number + 1;
    end if;
    insert into posts (board, thread, number, user, password, name,
                       tripcode, ip, subject, date_time, text,
                       sage, deleted)
        select _board, _thread, _number, user, password, name,
               tripcode, ip, subject, date_time, text,
               sage, 0
            from posts
            where id = old_original_post_id;
    select last_insert_id() into new_original_post_id;
    call sp_threads_edit_original_post(_thread, _number);

    create temporary table tmp_posts
    (
        old_id int not null,
        new_id int not null
    )
    engine=InnoDB;

    -- Copy another posts of thread.
    open `c`;
        repeat
            fetch `c` into post_id, _user, _password, _name, _tripcode, _ip,
                           _subject, _date_time, _text, _sage;
            if (not done) then
                call sp_posts_add(_board, _thread, _user, _password, _name,
                                  _tripcode, _ip, _subject, _date_time, _text,
                                  _sage);
                select last_insert_id() into new_post_id;
                insert into tmp_posts (old_id, new_id)
                    values (post_id, new_post_id);
            end if;
        until done end repeat;
    close `c`;

    -- Copy attachments of original post.

    set attachments_count = 0;
    select count(f.id) into attachments_count
        from files f
        join posts_files pf on pf.file = f.id
            and pf.post = old_original_post_id;
    if (attachments_count > 0) then
        insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
            select f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w, f.thumbnail_h
                from files f
                join posts_files pf on pf.file = f.id
                    and pf.post = old_original_post_id;
        select last_insert_id() into attachment_id;
        insert into posts_files (post, file, deleted)
            values (new_original_post_id, attachment_id, 0);
    end if;

    set attachments_count = 0;
    select count(i.id) into attachments_count
        from images i
        join posts_images pi on pi.image = i.id
            and pi.post = old_original_post_id;
    if (attachments_count > 0) then
        insert into images (hash, name, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
            select i.hash, i.name, i.widht, i.height, i.size, i.thumbnail, i.thumbnail_w, i.thumbnail_h
                from images i
                join posts_images pi on pi.image = i.id
                    and pi.post = old_original_post_id;
        select last_insert_id() into attachment_id;
        insert into posts_images (post, image, deleted)
            values (new_original_post_id, attachment_id, 0);
    end if;

    set attachments_count = 0;
    select count(l.id) into attachments_count
        from links l
        join posts_links pl on pl.link = l.id
            and pl.post = old_original_post_id;
    if (attachments_count > 0) then
        insert into links (url, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
            select l.url, l.widht, l.height, l.size, l.thumbnail, l.thumbnail_w, l.thumbnail_h
                from links l
                join posts_links pl on pl.link = l.id
                    and pl.post = old_original_post_id;
        select last_insert_id() into attachment_id;
        insert into posts_links (post, link, deleted)
            values (new_original_post_id, attachment_id, 0);
    end if;

    set attachments_count = 0;
    select count(v.id) into attachments_count
        from videos v
        join posts_videos pv on pv.video = v.id
            and pv.post = old_original_post_id;
    if (attachments_count > 0) then
        insert into videos (code, widht, height)
            select v.code, v.widht, v.height
                from videos v
                join posts_videos pv on pv.video = v.id
                    and pv.post = old_original_post_id;
        select last_insert_id() into attachment_id;
        insert into posts_videos (post, video, deleted)
            values (new_original_post_id, attachment_id, 0);
    end if;

    -- Copy another posts attachments.

    set done = 0;
    open `c`;
        repeat
            fetch `c` into post_id, _user, _password, _name, _tripcode, _ip,
                           _subject, _date_time, _text, _sage;
            if (not done) then
                select new_id into new_post_id
                    from tmp_posts
                    where old_id = post_id;

                set attachments_count = 0;
                select count(f.id) into attachments_count
                    from files f
                    join posts_files pf on pf.file = f.id
                        and pf.post = post_id;
                if (attachments_count > 0) then
                    insert into files (hash, name, size, thumbnail, thumbnail_w, thumbnail_h)
                        select f.hash, f.name, f.size, f.thumbnail, f.thumbnail_w, f.thumbnail_h
                            from files f
                            join posts_files pf on pf.file = f.id
                                and pf.post = post_id;
                    select last_insert_id() into attachment_id;
                    insert into posts_files (post, file, deleted)
                        values (new_post_id, attachment_id, 0);
                end if;

                set attachments_count = 0;
                select count(i.id) into attachments_count
                    from images i
                    join posts_images pi on pi.image = i.id
                        and pi.post = post_id;
                if (attachments_count > 0) then
                    insert into images (hash, name, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
                        select i.hash, i.name, i.widht, i.height, i.size, i.thumbnail, i.thumbnail_w, i.thumbnail_h
                            from images i
                            join posts_images pi on pi.image = i.id
                                and pi.post = post_id;
                    select last_insert_id() into attachment_id;
                    insert into posts_images (post, image, deleted)
                        values (new_post_id, attachment_id, 0);
                end if;

                set attachments_count = 0;
                select count(l.id) into attachments_count
                    from links l
                    join posts_links pl on pl.link = l.id
                        and pl.post = post_id;
                if (attachments_count > 0) then
                    insert into links (url, widht, height, size, thumbnail, thumbnail_w, thumbnail_h)
                        select l.url, l.widht, l.height, l.size, l.thumbnail, l.thumbnail_w, l.thumbnail_h
                            from links l
                            join posts_links pl on pl.link = l.id
                                and pl.post = post_id;
                    select last_insert_id() into attachment_id;
                    insert into posts_links (post, link, deleted)
                        values (new_post_id, attachment_id, 0);
                end if;

                set attachments_count = 0;
                select count(v.id) into attachments_count
                    from videos v
                    join posts_videos pv on pv.video = v.id
                        and pv.post = post_id;
                if (attachments_count > 0) then
                    insert into videos (code, widht, height)
                        select v.code, v.widht, v.height
                            from videos v
                            join posts_videos pv on pv.video = v.id
                                and pv.post = post_id;
                    select last_insert_id() into attachment_id;
                    insert into posts_videos (post, video, deleted)
                        values (new_post_id, attachment_id, 0);
                end if;
            end if;
        until done end repeat;
    close `c`;

    drop table tmp_posts;

    call sp_threads_edit_deleted(_id);
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

-- --------------------
--  Upload handlers. --
-- --------------------

-- Add upload handler.
create procedure sp_upload_handlers_add
(
    _name varchar(50)   -- Function name.
)
begin
    insert into upload_handlers (name) values (_name);
end|

-- Delete upload handelr.
create procedure sp_upload_handlers_delete
(
    _id int -- Id.
)
begin
    delete from upload_handlers where id = _id;
end|

-- Select upload handlers.
create procedure sp_upload_handlers_get_all ()
begin
    select id, name from upload_handlers;
end|

-- -----------------
--  Upload types. --
-- -----------------

-- Add upload type.
create procedure sp_upload_types_add
(
    _extension varchar(10),         -- Extension.
    _store_extension varchar(10),   -- Stored extension.
    _is_image bit,                  -- Image flag.
    _upload_handler_id int,         -- Upload handler id.
    _thumbnail_image varchar(256)   -- Thumbnail.
)
begin
    insert into upload_types (extension, store_extension, is_image,
        upload_handler, thumbnail_image)
    values (_extension, _store_extension, _is_image, _upload_handler_id,
        _thumbnail_image);
end|

-- Delete upload type.
create procedure sp_upload_types_delete
(
    _id int -- Id.
)
begin
delete from upload_types where id = _id;
end|

-- Edit upload type.
create procedure sp_upload_types_edit
(
    _id int,                        -- Id.
    _store_extension varchar(10),   -- Stored extension.
    _is_image bit,                  -- Image flag.
    _upload_handler_id int,         -- Upload handler id.
    _thumbnail_image varchar(256)   -- Thumbnail.
)
begin
    update upload_types set store_extension = _store_extension,
           is_image = _is_image,
           upload_handler = _upload_handler_id,
           thumbnail_image = _thumbnail_image
    where id = _id;
end|

-- Select upload types.
create procedure sp_upload_types_get_all ()
begin
    select id,
           extension,
           store_extension,
           is_image,
           upload_handler,
           thumbnail_image
        from upload_types;
end|

-- Select upload types on board.
create procedure sp_upload_types_get_by_board
(
    board_id int    -- Board id.
)
begin
    select ut.id,
           ut.extension,
           ut.store_extension,
           ut.is_image,
           ut.upload_handler,
           uh.name as upload_handler_name,
           ut.thumbnail_image
        from upload_types ut
        join board_upload_types but on ut.id = but.upload_type and but.board = board_id
        join upload_handlers uh on uh.id = ut.upload_handler;
end|

-- --------------------------
--  User groups relations. --
-- --------------------------

-- Add user to group.
create procedure sp_user_groups_add
(
    user_id int,
    group_id int
)
begin
    insert into user_groups (user, `group`) values (user_id, group_id);
end|

-- Delete user from group.
create procedure sp_user_groups_delete
(
    user_id int,    -- User id.
    group_id int    -- Group id.
)
begin
    delete from user_groups where user = user_id and `group` = group_id;
end|

-- Move user to new group.
create procedure sp_user_groups_edit
(
    user_id int,        -- User id.
    old_group_id int,   -- Id of old group.
    new_group_id int    -- Id of new group.
)
begin
    update user_groups set `group` = new_group_id where user = user_id and `group` = old_group_id;
end|

-- Select all user groups relations.
create procedure sp_user_groups_get_all ()
begin
    select user, `group` from user_groups order by `group` desc;
end|

-- ----------
--  Users. --
-- ----------

-- Edit user settings by keyword or create new user if it not exist.
create procedure sp_users_edit_by_keyword
(
    _keyword varchar(32),   -- Keyword hash.
    _posts_per_thread int,  -- Count of posts per thread.
    _threads_per_page int,  -- Count of threads per page.
    _lines_per_post int,    -- Count of lines per post.
    _language int,          -- Language id.
    _stylesheet int,        -- Stylesheet id.
    _password varchar(12),  -- Password.
    _goto varchar(32)       -- Redirection.
)
begin
    declare user_id int default null;

    select id into user_id from users where keyword = _keyword;
    if (user_id is null) then
        -- Create new user.
        insert into users (keyword,
                           threads_per_page,
                           posts_per_thread,
                           lines_per_post,
                           stylesheet,
                           language,
                           password,
                           `goto`)
            values (_keyword,
                    _threads_per_page,
                    _posts_per_thread,
                    _lines_per_post,
                    _stylesheet,
                    _language,
                    _password,
                    _goto);
        select last_insert_id() into user_id;
        insert into user_groups (user, `group`) select user_id, id from groups
            where name = 'Users';
    else
        -- Edit user.
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

-- Select users.
create procedure sp_users_get_all ()
begin
    select id from users;
end|

-- Select admin users.
create procedure sp_users_get_admins ()
begin
    select u.id
        from users u
        join user_groups ug on ug.user = u.id
        join groups g on g.id = ug.`group` and g.name = 'Administrators';
end|

-- Select user.
create procedure sp_users_get_by_keyword
(
    _keyword varchar(32)    -- Keyword hash.
)
begin
    declare user_id int;

    select id into user_id from users where keyword = _keyword;

    select u.id,
           u.posts_per_thread,
           u.threads_per_page,
           u.lines_per_post,
           l.code as language,
           s.name as stylesheet,
           u.password,
           u.`goto`
        from users u
        join stylesheets s on u.stylesheet = s.id
        join languages l on u.language = l.id
        where u.keyword = _keyword;

    select g.name
        from user_groups ug
        join users u on ug.user = u.id and u.id = user_id
        join groups g on ug.`group` = g.id;
end|

-- Set redirection.
create procedure sp_users_set_goto
(
    _id int,            -- User id.
    _goto varchar(32)   -- Redirection.
)
begin
    update users set `goto` = _goto where id = _id;
end|

-- Set password.
create procedure sp_users_set_password
(
    _id int,                -- User id.
    _password varchar(12)   -- New password.
)
begin
    update users set password = _password where id = _id;
end|

-- ----------
-- Videos. --
-- ----------

-- Add video.
create procedure sp_videos_add
(
    _code varchar(256), -- Code.
    _widht int,         -- Width.
    _height int         -- Height.
)
begin
    insert into videos (code, widht, height) values (_code, _widht, _height);
    select last_insert_id() as id;
end|

-- Select videos.
create procedure sp_videos_get_by_post
(
    post_id int -- Post id.
)
begin
    select v.id,
           v.code,
           v.widht,
           v.height
        from posts_videos pv
        join videos v on v.id = pv.video and pv.post = post_id;
end|

-- Get thread vodeos.
create procedure sp_videos_get_by_thread
(
    thread_id int -- Thread id.
)
begin
    select v.id, v.code, v.widht, v.height
        from videos v
        join posts_videos pv on v.id = pv.video
        join posts p on p.id = pv.post and p.thread = thread_id;
end|

-- Select dangling videos.
create procedure sp_videos_get_dangling ()
begin
    select v.id,
           v.code,
           v.widht,
           v.height
        from videos v
        left join posts_videos pv on pv.video = v.id
        where pv.post is null;
end|

-- ---------
-- Words. --
-- ---------

-- Add word.
create procedure sp_words_add
(
    _board_id int,          -- Board id.
    _word varchar(100),     -- Word.
    _replace varchar(100)   -- Replacement.
)
begin
    insert into words (board_id, word, `replace`)
        values (_board_id, _word, _replace);
end|

-- Delete word.
create procedure sp_words_delete
(
    _id int -- Id.
)
begin
    delete from words where id = _id;
end|

-- Edit word.
create procedure sp_words_edit
(
    _id int,                -- Id.
    _word varchar(100),     -- Word.
    _replace varchar(100)   -- Replacement.
)
begin
    update words set word = _word, `replace` = _replace where id = _id;
end|

-- Select words.
create procedure sp_words_get_all ()
begin
    select id, board_id, word, `replace` from words;
end|

-- Select words.
create procedure sp_words_get_all_by_board
(
    _board_id int   -- Board id.
)
begin
    select id, word, `replace` from words where board_id = _board_id;
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
