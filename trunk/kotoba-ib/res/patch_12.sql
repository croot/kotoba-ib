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

drop procedure if exists patch_12|

create procedure patch_12 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 12 and 12 - @version = 1) then
        alter table boards add column last_post_number int not null default 0 after category;
        update boards b
            join (select board, max(number) as last_post_number
                      from posts
                      group by board) q on q.board = b.id
            set b.last_post_number = q.last_post_number;

        update db_version set version = 12 limit 1;
        select 'Patch 12 was applied.';
    else
        select 'Patch 12 cannot be applied.';
    end if;
end|

call patch_12()|
