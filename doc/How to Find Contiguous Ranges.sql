/*
https://www.xaprb.com/blog/2005/12/06/find-missing-numbers-in-a-datatable-with-sql/
https://www.xaprb.com/blog/2006/03/22/find-contiguous-ranges-with-sql/
*/
create table datatable (
    id int not null primary key
);

insert into datatable(id) VALUES (1);
insert into datatable(id) VALUES (2);
insert into datatable(id) VALUES (3);
insert into datatable(id) VALUES (4);

insert into datatable(id) VALUES (6);
insert into datatable(id) VALUES (7);
insert into datatable(id) VALUES (8);
insert into datatable(id) VALUES (9);
insert into datatable(id) VALUES (10);

insert into datatable(id) VALUES (15);
insert into datatable(id) VALUES (16);
insert into datatable(id) VALUES (17);
insert into datatable(id) VALUES (18);
insert into datatable(id) VALUES (19);
insert into datatable(id) VALUES (20);

--Finding duplicate and missing numbers

select id, count(*)
from datatable
group by id
having count(*) > 1;

select l.id + 1 s
from datatable l
  left outer join datatable r on l.id + 1 = r.id
where r.id is null;

-- Find ranges of missing values with subqueries

select _start, stop from (
  select m.id + 1 as start,
    (select min(id) - 1 from datatable as x where x.id > m.id) as stop
  from datatable as m
    left outer join datatable as r on m.id = r.id - 1
  where r.id is null
) as x
where stop is not null;



-- How to Find Contiguous Ranges

select l.id
from datatable l
    left outer join datatable r on r.id = l.id - 1
where r.id is null;

select l.id
from datatable l
    left outer join datatable r on r.id = l.id + 1
where r.id is null;

select l.id s,
    (
        select min(a.id) id
        from datatable a
            left outer join datatable b on a.id = b.id + 1
        where b.id is null
            and a.id >= b.id
    ) e
from datatable l
    left outer join datatable r on r.id = l.id - 1
where r.id is null;
