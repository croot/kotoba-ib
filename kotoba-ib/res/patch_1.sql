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

drop procedure if exists patch_1|

create procedure patch_1 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 1 and 1 - @version = 1) then
        create table hard_ban (range_beg varchar(15) not null, range_end varchar(15) not null) engine=InnoDB;

        update db_version set version = 1 limit 1;
        select 'Patch 1 was appied.';
    else
        select 'Patch 1 cannot be appied.';
    end if;
end|

call patch_1()|

