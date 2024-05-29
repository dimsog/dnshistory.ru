<?php

return [
    'zones' => ['ru', 'su', 'rf', 'shop', 'site', 'moscow', 'online', 'tech', 'pro', 'ws', 'info', 'biz', 'store', 'com', 'by', 'kz'],
    'whois' => [
        'servers' => explode(';', env('APP_WHOIS_SERVERS', ''))
    ]
];
