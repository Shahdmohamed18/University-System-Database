-- Constraints
alter table student
with check add constraint chk_student_age 
check (datediff(year, date_of_birth, getdate()) >= 18);

alter table student
with check add constraint chk_student_gpa 
check (gpa >= 0.0 and gpa <= 4.0);

alter table student
with check add constraint chk_student_sex 
check (sex in ('m', 'f'));

alter table student
with check add constraint chk_student_level 
check (level in ('freshman', 'sophomore', 'junior', 'senior'));

alter table student
with check add constraint chk_student_status 
check (status in ('active', 'probation', 'warning', 'suspended', 'honor'));

alter table instructor
with check add constraint chk_instructor_sex 
check (sex in ('m', 'f'));

alter table instructor
with check add constraint chk_min_salary 
check (salary >= 2000);

alter table room
with check add constraint chk_room_capacity 
check (capacity > 0);

alter table course
with check add constraint chk_course_credits 
check (credits > 0 and credits <= 6);
