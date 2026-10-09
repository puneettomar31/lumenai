<?php

use App\Mcp\Servers\LumenAiServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', LumenAiServer::class)
    ->middleware(['auth:sanctum']);

Mcp::local('lumenai', LumenAiServer::class);
