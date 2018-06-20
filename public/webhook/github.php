<?php

shell_exec("nohup sh -c 'cd ../.. && git reset --hard && git pull &' >> /dev/null 2>&1 &");
