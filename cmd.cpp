#include <string>
#include <iostream>
#include <stdlib.h>

using namespace std;

int main(int ac, char** av) {
  string cmd =   "/etc/init.d/minidlna ";
  if(ac == 2) {
    string arg = av[1];
    cmd += arg;
    system(cmd.c_str());
  }
  return 0;
}
