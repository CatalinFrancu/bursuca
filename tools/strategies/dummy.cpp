#include <stdio.h>

#define MAX_PLAYERS 10
#define MAX_DIE 6
#define START_CASH 10
#define WIN_CASH 100

typedef struct {
  int userId, variant;
  int stock[MAX_DIE + 1];
  int cash;
} player;

int numPlayers;
int me;                         // my index, 1 <= me <= numPlayers
player p[MAX_PLAYERS + 1];
int stockPrice[MAX_DIE + 1];

// Read player info and stock prices
void initGame() {
  scanf("%d %d", &numPlayers, &me);

  for (int i = 1; i <= numPlayers; i++) {
    if (i != me) {
      scanf("%d %d", &p[i].userId, &p[i].variant);
    }
    for (int j = 1; j <= MAX_DIE; j++) {
      p[i].stock[j] = 0;
    }
    p[i].cash = START_CASH;
  }

  for (int i = 1; i <= MAX_DIE; i++) {
    scanf("%d", &stockPrice[i]);
  }
}

void buy(int who, int count, int company) {
  p[who].stock[company] += count;
  p[who].cash -= count * stockPrice[company];
  stockPrice[company] += count / 3;
}

void sell(int who, int count, int company) {
  p[who].stock[company] -= count;
  p[who].cash += count * stockPrice[company];
  stockPrice[company] -= count / 3;
  if (stockPrice[company] < 1) {
    stockPrice[company] = 1;
  }
}

void raise(int count, int company) {
  stockPrice[company] += count;
}

void lower(int count, int company) {
  stockPrice[company] -= count;
}

bool canBuy(int count, int company) {
  return (stockPrice[company] <= 2) && (p[me].cash >= count * stockPrice[company]);
}

bool canSell(int count, int company) {
  return (stockPrice[company] >= 4) && (p[me].stock[company] >= count);
}

bool canLower(int count, int company) {
  int otherStock = 0;
  for (int i = 1; i <= MAX_PLAYERS; i++) {
    if (i != me) {
      otherStock += p[i].stock[company];
    }
  }
  return (stockPrice[company] > count) && (otherStock >= p[me].stock[company]);
}

// Very basic strategy:
// - Sell if the price is >= 4
// - Buy if the price is <= 2
// - Lower price if combined opponents have more stock than I do
// - Otherwise raise price by as little as possible
void makeMove() {
  int die1, die2;
  scanf("%d %d", &die1, &die2);

  if (canSell(die1, die2)) {
    sell(me, die1, die2);
    printf("S %d %d\n", die1, die2);
  } else if (canSell(die2, die1)) {
    sell(me, die2, die1);
    printf("S %d %d\n", die2, die1);
  } else if (canBuy(die1, die2)) {
    buy(me, die1, die2);
    printf("B %d %d\n", die1, die2);
  } else if (canBuy(die2, die1)) {
    buy(me, die2, die1);
    printf("B %d %d\n", die2, die1);
  } else if (canLower(die1, die2)) {
    lower(die1, die2);
    printf("L %d %d\n", die1, die2);
  } else if (canLower(die2, die1)) {
    lower(die2, die1);
    printf("L %d %d\n", die2, die1);
  } else if (die1 < die2) {
    raise(die1, die2);
    printf("R %d %d\n", die1, die2);
  } else {
    raise(die2, die1);
    printf("R %d %d\n", die2, die1);
  }
  fflush(stdout);
}

// Read another player's move. The arbiter will always send us valid moves from the set (B, S, R, L, P)
void readMove(int p) {
  char action;
  int factor, company;
  scanf(" %c %d %d", &action, &factor, &company);
  switch (action) {
    case 'B': buy(p, factor, company); break;
    case 'S': sell(p, factor, company); break;
    case 'R': raise(factor, company); break;
    case 'L': lower(factor, company); break;
  }
}

void printGameState() {
  fprintf(stderr, "----------------------------------------------------------------\n");
  fprintf(stderr, "player:            ");
  for (int i = 1; i <= numPlayers; i++) {
    if (i == me) {
      fprintf(stderr, "    me    ");
    } else {
      fprintf(stderr, "<%4d,%2d> ", p[i].userId, p[i].variant);
    }
  }
  fprintf(stderr, "\n");

  fprintf(stderr, "cash:              ");
  for (int i = 1; i <= numPlayers; i++) {
    fprintf(stderr, "%6d    ", p[i].cash);
  }
  fprintf(stderr, "\n");
  fprintf(stderr, "----------------------------------------------------------------\n");

  for (int company = 1; company <= MAX_DIE; company++) {
    fprintf(stderr, "Company %d: | %4d |", company, stockPrice[company]);
    for (int i = 1; i <= numPlayers; i++) {
      fprintf(stderr, "%6d    ", p[i].stock[company]);
    }
    fprintf(stderr, "\n");
  }
  fprintf(stderr, "----------------------------------------------------------------\n");
}

int main(void) {
  initGame();

  int curPlayer = 0;
  do {
    if (++curPlayer > numPlayers) {
      curPlayer = 1;
    }
    if (curPlayer == me) {
      makeMove();
    } else {
      readMove(curPlayer);
    }
    // printGameState();
  } while (p[curPlayer].cash < WIN_CASH);
}
