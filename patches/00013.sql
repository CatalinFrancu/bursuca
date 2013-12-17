create table tourney (
  id int not null auto_increment,
  userId int not null,
  gameSize int not null,
  numRounds int not null,
  created int,
  modified int,

  primary key(id)
);

create table participant (
  id int not null auto_increment,
  tourneyId int not null,
  agentId int not null,
  created int,
  modified int,

  primary key(id)
);

alter table game
      add winnerId int not null after status,
      add tourneyId int not null after winnerId,
      add round int not null after tourneyId,
      add key(tourneyId);
