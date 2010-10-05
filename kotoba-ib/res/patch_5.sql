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

drop procedure if exists patch_5|

create procedure patch_5 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 5 and 5 - @version = 1) then
        ALTER TABLE boards ADD COLUMN enable_translation bit default null AFTER enable_captcha;
        ALTER TABLE boards ADD COLUMN enable_geoip bit default null AFTER enable_translation;
        ALTER TABLE boards ADD COLUMN enable_shi bit default null AFTER enable_geoip;

        update db_version set version = 5 limit 1;
        select 'Patch 5 was applied.';
    else
        select 'Patch 5 cannot be applied.';
    end if;
end|

call patch_5()|
