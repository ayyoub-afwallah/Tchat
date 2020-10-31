create database tchat;
-- use tchat 

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