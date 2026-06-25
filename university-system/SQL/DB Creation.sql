create database university

use university

create table student (
	id int not null unique,
	ssn int not null unique,
	fname varchar(10) not null,
	lname varchar(10),
	address varchar(100),
	sex varchar(1),
	date_of_birth date,
	level varchar(10),
	gpa real,
	status varchar(15),
	majorID varchar(20) not null,

	primary key(id)
);

create table std_contact (
	id int default 0,
	contact varchar(11),
	primary key(id , contact),
	foreign key(id) references student(id)
);

create table instructor (
	id int not null unique,
	ssn int not null unique,
	fname varchar(10) not null,
	lname varchar(10),
	address varchar(100),
	sex varchar(1),
	[date of birth] date,
	title varchar(20),
	salary int,

	primary key(id)
);

create table inst_contact (
	id int default 0,
	contact varchar(11),
	primary key(id , contact),
	foreign key(id) references instructor(id)
);

create table course(
	code varchar(20),
	name varchar(50),
	credits int,

	primary key (code)
);

create table room(
	num int not null,
	floor int,
	bulding int,
	capacity int,
	Type varchar(50),
	primary key(num) 
);

create table schedule(
	scheduleID int not null,
	roomNum int not null,
	courseCode varchar(20) not null, 
	instID int not null,
	time time not null,
	day varchar(3) not null,
	
	primary key(scheduleID),
	foreign key(roomNum) references room(num),
	foreign key(courseCode) references course(code),
	foreign key(instID) references instructor(id)
);

create table enrollment (
    id int not null unique,
    stdID int not null,
    courseCode varchar(20) not null,
    semester varchar(10),
    year varchar(4),
    grade varchar(2),
    status varchar(15),

    primary key(id),
    foreign key(stdID) references student(id),
    foreign key(courseCode) references course(code)
);

create table major(
	id int not null,
	name varchar(50),
	supervisorID int not null,

	primary key(id),
	foreign key(deanID) references instructor(id)
);

create table courseMajor(
	courseCode varchar(20),
	majorID int,

	primary key(courseCode, majorID),
	foreign key(courseCode) references course(code),
	foreign key(majorID) references major(id)
);

create table instructorMajor(
	instructorID int,
	majorID int,

	primary key(instructorID, majorID),
	foreign key(instructorID) references instructor(id),
	foreign key(majorID) references major(id)
);


