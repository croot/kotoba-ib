delimiter |
if not exists (select version into @version from db_version) then
    set @version = 1;
end if|
select @version|