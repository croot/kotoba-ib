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

drop procedure if exists patch_14|

create procedure patch_14 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 14 and 14 - @version = 1) then
        -- TODO в идеале и поле hash нужно добавить сюда
        alter table posts_images add column board int not null default 0 after post;
        update posts_images pi join posts p on pi.post = p.id set pi.board = p.board;
        alter table posts_files add column board int not null default 0 after post;
        update posts_files pf join posts p on pf.post = p.id set pf.board = p.board;

        update posts_images pi left join posts p on pi.post = p.id and p.deleted = 0 set pi.deleted = 1 where p.id is null;
        update posts_files pf left join posts p on pf.post = p.id and p.deleted = 0 set pf.deleted = 1 where p.id is null;

        update db_version set version = 14 limit 1;
        select 'Patch 14 was applied.';
    else
        select 'Patch 14 cannot be applied.';
    end if;
end|

call patch_14()|
