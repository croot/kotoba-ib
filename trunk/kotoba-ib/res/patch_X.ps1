# > .\patch_X.ps1 10 > patch_10.sql

$VERSION = $args[0]
if (! $VERSION) {
    echo "Error. Patch version not specifed."
    exit 1
}

echo "delimiter |

drop function if exists versioning_table_exists|

create function versioning_table_exists ()
returns int
deterministic
begin
    declare result int default 0;
    select count(table_name) into result from information_schema.tables where table_schema = 'kotoba2' and table_name = 'db_version';
    return result;
end|

drop procedure if exists patch_$VERSION|

create procedure patch_$VERSION ()
begin
    if (not versioning_table_exists()) then
        create table db_version (version int) engine=InnoDB;
        insert into db_version (version) values (0);
    end if;
    select version into @version from db_version;
    if (@version < $VERSION and $VERSION - @version = 1) then
        -- ACTUAL CODE HERE

        update db_version set version = $VERSION limit 1;
        select 'Patch $VERSION was appied.';
    else
        select 'Patch $VERSION cannot be appied.';
    end if;
end|

call patch_$VERSION()|"