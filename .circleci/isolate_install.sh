#!/usr/bin/env bash
sh -c "cd /tmp/isolate; make; sudo make install 2>&1" >> /dev/null 2>&1 &
exit 0