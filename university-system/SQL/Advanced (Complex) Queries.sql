--1 Find students who achieved a grade of 'a' in ALL courses they have ever enrolled in
select s.fname, s.lname
from student s
where not exists (
    select e1.courseCode
    from enrollment e1
    where e1.stdID = s.id
    except
    select e2.courseCode
    from enrollment e2
    where e2.stdID = s.id and e2.grade = 'a'
)

--2 Identify courses scheduled in a room whose capacity is greater than or equal to ALL other rooms used for that same course
select c.name
from course c
where exists (
    select r1.capacity
    from room r1
    inner join schedule s1 on r1.num = s1.roomNum
    where s1.courseCode = c.code
    and r1.capacity >= all (
        select r2.capacity
        from room r2
        inner join schedule s2 on r2.num = s2.roomNum
        where s2.courseCode = c.code
    )
)

--3 Retrieve the names of students who are currently enrolled in more than two course
select s.fname, s.lname
from student s
inner join enrollment e on s.id = e.stdID
group by s.id, s.fname, s.lname
having count(e.courseCode) > 2

--4 List instructors whose total teaching credit load exceeds the university average
select i.fname, i.lname
from instructor i
inner join schedule s on i.id = s.instID
inner join course c on s.courseCode = c.code
group by i.id, i.fname, i.lname
having sum(c.credits) > (
    select avg(total_credits)
    from (
        select sum(c2.credits) as total_credits
        from instructor i2
        inner join schedule s2 on i2.id = s2.instID
        inner join course c2 on s2.courseCode = c2.code
        group by i2.id
    ) as instructor_loads
)

--5 Extract students whose contact information is guaranteed unique (no duplicate numbers)
select s.fname, s.lname
from student s
inner join std_contact sc on s.id = sc.id
group by s.id, s.fname, s.lname
having count(sc.contact) = count(distinct sc.contact)
and count(sc.contact) > 0

--6 Identify majors in which ALL registered students have a GPA greater than 3.00
select m.name
from major m
where not exists (
    select *
    from student s
    where s.majorID = m.id
    and s.gpa <= 3.00
)

--7 Find students who are enrolled in ALL courses taught by instructor 'Zewail'
--7 Find students who are enrolled in ALL courses taught by instructor 'Zewail'
select s.fname, s.lname
from student s
where not exists (
    select sch.courseCode
    from schedule sch
    inner join instructor i on sch.instID = i.id
    where i.lname = 'Zewail'
    except
    select e.courseCode
    from enrollment e
    where e.stdID = s.id
)

--8 Combines Student academic warnings and Instructor workload analysis
select'academic warning' as report_category, s.fname + ' ' + s.lname as person_name,
    c.name as details, cast(s.gpa as varchar(10)) as metric_value
from student s join enrollment e on s.id = e.stdid join course c on e.coursecode = c.code
where s.gpa < 3.0 and c.credits = 4
union all
select 'high workload instructor' as report_category, i.fname + ' ' + i.lname as person_name,
    'teaches ' + cast(count(s.coursecode) as varchar(5)) as details, cast(sum(c.credits) as varchar(10)) as metric_value
from instructor i join schedule s on i.id = s.instid join course c on s.coursecode = c.code
where not exists (
        select 1 
        from major m 
        where m.supervisorID = i.id
    )
group by i.id, i.fname, i.lname
having sum(c.credits) > 6
order by report_category, metric_value desc