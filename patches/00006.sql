create table game (
  id int not null auto_increment,
  price1 int not null,
  price2 int not null,
  price3 int not null,
  price4 int not null,
  price5 int not null,
  price6 int not null,
  created int,
  modified int,

  primary key(id)
);

create table participant (
  id int not null auto_increment,
  agentId int not null,
  position int not null,
  created int,
  modified int,

  primary key(id)
);

create table move (
  id int not null auto_increment,
  gameId int not null,
  number int not null,
  action varchar(1) not null,
  die1 int not null,
  die2 int not null,
  created int,
  modified int,

  primary key(id),
  key(gameId, number)
);
