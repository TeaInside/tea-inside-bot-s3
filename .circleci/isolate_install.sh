#!/usr/bin/env bash
git clone https://github.com/ioi/isolate /tmp/isolate
cd /tmp/isolate && make
cd /tmp/isolate && sudo make install
exit 0