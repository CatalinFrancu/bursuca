alter table game add status int after id;
rename table participant to player;
alter table player add gameId int not null after id;
