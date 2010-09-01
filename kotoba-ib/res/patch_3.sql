delimiter |

drop function if exists versioning_table_exists|

create function versioning_table_exists ()
returns int
deterministic
begin
    declare result int default 0;
    select count(table_name) into result from information_schema.tables where table_schema = 'kotoba2' and table_name = 'db_version';
    return result;
end|

drop procedure if exists patch_3|

create procedure patch_3 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 3 and 3 - @version = 1) then
        create table spamfilter (id int not null auto_increment, pattern varchar(256) not null, primary key (id)) engine=InnoDB;

        update db_version set version = 3 limit 1;
        select 'Patch 3 was appied.';
    else
        select 'Patch 3 cannot be appied.';
    end if;
end|

call patch_3()|
