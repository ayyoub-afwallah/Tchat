create database tchat;
use tchat ;

create table user
(
id INT not null primary key AUTO_INCREMENT,
username varchar(200) 
);
create table message
(
msg varchar(200),
sender varchar(200)
);

insert into message values ('hello , how\'s the test going o far','salah');
insert into message values ('so far so good!','ayoub');
select * from message