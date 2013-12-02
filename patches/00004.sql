create table agent (
  id int not null auto_increment,
  userId int not null,
  version int not null,
  name varchar(255),
  language varchar(10),
  sourceCode mediumblob,
  created int,
  modified int,

  primary key(id),
  key(userId),
  key(created)
);
