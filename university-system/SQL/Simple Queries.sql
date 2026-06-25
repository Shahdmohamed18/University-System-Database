--1 select a specific student by id to see their courses and who teaches them
select distinct s.id, s.fname, s.lname, c.name as course_name, i.fname as instructor_name
from student s
inner join enrollment e on s.id = e.stdID
inner join course c on e.courseCode = c.code
inner join schedule sch on c.code = sch.courseCode
inner join instructor i on sch.instID = i.id
where s.id = 2007

--2 list all courses along with the names of instructors teaching them
select c.name as course_title, i.fname, i.lname as instructor_name
from course c
inner join schedule sch on c.code = sch.courseCode
inner join instructor i on sch.instID = i.id

--3 show student details along with their enrolled courses and lecture timings
select s.id, s.fname, s.lname, c.name as course, sch.day, sch.time
from student s
left join enrollment e on s.id = e.stdID
left join course c on e.courseCode = c.code
left join schedule sch on c.code = sch.courseCode

--4 display the full schedule showing course, professor, day, and time
select c.name as subject, i.fname as professor, sch.day, sch.time
from schedule sch
right join course c on sch.courseCode = c.code
left join instructor i on sch.instID = i.id

--5 list every major and the name of the dean responsible for it
select m.name as major_name, i.fname + ' ' + i.lname as dean_name
from major m
inner join instructor i on m.supervisorID = i.id

--6 check which course is assigned to which room number
select distinct r.num as room_number, r.bulding, r.floor, c.name as course_name
from room r
right join schedule sch on r.num = sch.roomNum
left join course c on sch.courseCode = c.code

--7 find students who are under warning or probation and show their major
select s.fname, s.lname, s.gpa, s.status, m.name as major
from student s
inner join major m on s.majorID = m.id
where s.status in ('warning', 'probation')

--8 retrieve a list of all students and their phone numbers
select s.id, s.fname, s.lname, sc.contact
from student s
inner join std_contact sc on s.id = sc.id