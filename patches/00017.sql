alter table player add rated int after eloEnd;
update player set rated = 1 where eloStart != eloEnd;
