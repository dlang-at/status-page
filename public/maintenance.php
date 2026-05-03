<?php

http_response_code(503);
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, no-store');
header('Retry-After: 30');
echo "Maintenance in progress…\nPlease try again later.\n";
die();
