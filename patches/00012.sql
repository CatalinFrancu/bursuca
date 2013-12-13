alter table agent change elo elo int default 1600;
alter table player add eloStart int after killReason, add eloEnd int after eloStart;
alter table agent add rated int default 1 after elo;
