<?php

require __DIR__.'/../vendor/autoload.php';

echo "boot:autoload\n";

$app = require_once __DIR__.'/../bootstrap/app.php';

echo "boot:app\n";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "boot:kernel\n";

$path = $argv[1] ?? '/';
$method = $argv[2] ?? 'GET';

$req = Illuminate\Http\Request::create($path, $method);
$t0 = microtime(true);

try {
    echo "handle:start\n";
    $resp = $kernel->handle($req);
    echo "handle:done\n";
    $elapsed = microtime(true) - $t0;

    echo "status={$resp->getStatusCode()} time_s=".round($elapsed, 3)."\n";
    // Print a tiny snippet to prove it rendered:
    $content = (string) $resp->getContent();
    echo "bytes=".strlen($content)."\n";
} finally {
    echo "terminate:start\n";
    $kernel->terminate($req, $resp ?? null);
    echo "terminate:done\n";
}

