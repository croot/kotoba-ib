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

drop procedure if exists patch_13|

create procedure patch_13 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 13 and 13 - @version = 1) then
        alter table threads add column last_post int not null default 0 after closed;
        alter table posts add unique key (board, number);
        update threads t set last_post =
            (select max(p.number) as last_post
                from posts p
                where p.thread = t.id
                      and (p.sage = 0 or p.sage is null)
                      and p.deleted = 0);

        update db_version set version = 13 limit 1;
        select 'Patch 13 was applied.';
    else
        select 'Patch 13 cannot be applied.';
    end if;
end|

call patch_13()|
