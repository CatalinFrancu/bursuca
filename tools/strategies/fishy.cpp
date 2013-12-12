#include <stdio.h>
#include <unistd.h>

int main(void) {
  printf("%d\n", unlink("/tmp/a.txt"));
}
