<?php

shell_exec("nohup sh -c 'cd ../.. && sudo git reset --hard && git pull &' >> /dev/null 2>&1 &");
