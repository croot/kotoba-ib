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

drop procedure if exists patch_4|

create procedure patch_4 ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < 4 and 4 - @version = 1) then
        CREATE TABLE favorites
        (
            user int not null,
            thread int not null,
            last_readed int not null,
            unique key (user, thread),
            constraint foreign key (user) references users (id) on delete restrict on update restrict,
            constraint foreign key (thread) references threads (id) on delete restrict on update restrict
        )
        engine=InnoDB;

        update db_version set version = 4 limit 1;
        select 'Patch 4 was applied.';
    else
        select 'Patch 4 cannot be applied.';
    end if;
end|

call patch_4()|
