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

drop procedure if exists patch_8|

create procedure patch_8 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 8 and 8 - @version = 1) then
        update acl set `change` = 1 where `group` = 1 and board is null and thread is null and post is null;

        update db_version set version = 8 limit 1;
        select 'Patch 8 was applied.';
    else
        select 'Patch 8 cannot be applied.';
    end if;
end|

call patch_8()|
