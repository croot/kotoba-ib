/*
 * How to use.
 *
 * To create a patch number N:
 *
 * 1) Replace X with N in stored procedure name below: patch_X
 * 2) Put actual code instead -- ACTUAL CODE HERE comment below.
 */

delimiter |

drop function if exists vesioning_table_exists|

create function vesioning_table_exists ()
returns int
deterministic
begin
    declare result int default 0;
    select count(table_name) into result from information_schema.tables where table_schema = 'kotoba2' and table_name = 'db_version';
    return result;
end|

drop procedure if exists patch_X|

create procedure patch_X ()
ilovemysql:
begin
    if (not vesioning_table_exists()) then
        -- TODO Create table
        leave ilovemysql;
    end if;

    -- ACTUAL CODE HERE
end|

call patch_X()|
