-- Apply example: mysql -u root -D kotoba < patch_10.sql
delimiter |

drop function if exists versioning_table_exists|

create function versioning_table_exists ()
returns int
deterministic
begin
    declare result int default 0;
    select count(table_name) into result from information_schema.tables where table_schema = database() and table_name = 'db_version';
    return result;
end|

drop procedure if exists patch_9|

create procedure patch_9 ()
begin
    declare n_id int;
    declare misc_id int;
    declare exist int;

    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 9 and 9 - @version = 1) then

        -- News board
        select id into n_id from boards where name = 'n';
        if (n_id > 0) then
            select count(board) into exist from acl where board = n_id;
            if (exist = 0) then
                insert into acl (board, `view`, `change`, moderate) values (n_id, 1, 0, 0);
            end if;
        else
            insert into boards (name, bump_limit, force_anonymous, with_attachments, same_upload, popdown_handler, category)
                        values ('n', 30, 0, 1, 'once', 1, 1);
            select last_insert_id() into n_id;
            insert into acl (board, `view`, `change`, moderate) values (n_id, 1, 0, 0);
        end if;

        -- Banners board
        select id into misc_id from boards where name = 'misc';
        if (misc_id > 0) then
            select count(board) into exist from acl where board = misc_id;
            if (exist = 0) then
                insert into acl (board, `view`, `change`, moderate) values (misc_id, 1, 0, 0);
            end if;
        else
            insert into boards (name, bump_limit, force_anonymous, with_attachments, same_upload, popdown_handler, category)
                        values ('misc',  30, 0, 1, 'once', 1, 1);
            select last_insert_id() into misc_id;
            insert into acl (board, `view`, `change`, moderate) values (misc_id, 1, 0, 0);
        end if;

        update db_version set version = 9 limit 1;
        select 'Patch 9 was applied.';
    else
        select 'Patch 9 cannot be applied.';
    end if;
end|

call patch_9()|
