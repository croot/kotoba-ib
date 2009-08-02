drop procedure if exists sp_get_boards_list;
drop procedure if exists sp_ban;
drop procedure if exists sp_check_ban;
drop procedure if exists sp_refresh_banlist;

create procedure sp_refresh_banlist ()
begin
delete from bans where untill <= now();
end;

create procedure sp_ban
(
	_range_beg int,
	_range_end int,
	_reason text,
	_untill datetime
)
begin
call sp_refresh_banlist();

if _reason = '' or _reason is null
then
	insert into bans (range_beg, range_end, reason, untill) values (_range_beg, _range_end, null, _untill);
else
	insert into bans (range_beg, range_end, reason, untill) values (_range_beg, _range_end, _reason, _untill);
end if;
end;

create procedure sp_check_ban
(
	ip int
)
begin
call sp_refresh_banlist();
select range_beg, range_end, untill, reason from bans where range_beg >= ip and range_end <= ip order by range_end desc limit 1;
end;

create procedure sp_get_boards_list
(
	user int
)
begin
	select b.name, c.name as category
	from boards b
	join user_groups ug on ug.user = user
	left join categories c on b.category = c.id
	left join acl a1 on ug.`group` = a1.`group` and a1.board is null -- global premissions for specifed groups
	left join acl a2 on b.id = a2.board and a2.`group` is null and a2.thread is null and a2.post is null -- group independent premissions for specifed board
	left join acl a3 on ug.`group` = a3.`group` and b.id = a3.board and a2.thread is null and a2.post is null -- premissions for specifed groups and boards
	group by b.id
	having max(coalesce(a3.view, a2.view, a1.view)) = 1
	order by c.name, b.name;
end;