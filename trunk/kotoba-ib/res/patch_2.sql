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

drop procedure if exists patch_2|

create procedure patch_2 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 2 and 2 - @version = 1) then
        create table reports (post int not null, constraint foreign key (post) references posts (id) on delete restrict on update restrict) engine=InnoDB;

        update db_version set version = 2 limit 1;
        select 'Patch 2 was applied.';
    else
        select 'Patch 2 cannot be applied.';
    end if;
end|

call patch_2()|
