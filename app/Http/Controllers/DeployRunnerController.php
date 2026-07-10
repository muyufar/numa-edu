<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DeployRunnerController extends Controller
{
    /**
     * @var array<string, list<string>>
     */
    private const ACTIONS = [
        'migrate:status' => ['migrate:status'],
        'migrate' => ['migrate', '--force'],
        'storage:link' => ['storage:link'],
        'optimize' => ['optimize'],
        'clear' => ['optimize:clear'],
        'deploy-all' => ['migrate', '--force', 'storage:link', 'optimize'],
    ];

    public function index(Request $request, string $token): View|Response
    {
        if ($deny = $this->authorizeToken($token)) {
            return $deny;
        }

        return view('deploy.runner', [
            'token' => $token,
            'actions' => array_keys(self::ACTIONS),
        ]);
    }

    public function run(Request $request, string $token): View|Response
    {
        if ($deny = $this->authorizeToken($token)) {
            return $deny;
        }

        $action = (string) $request->input('action', '');
        if (! isset(self::ACTIONS[$action])) {
            abort(422, 'Aksi tidak dikenali.');
        }

        $results = $this->runAction($action);

        return view('deploy.runner', [
            'token' => $token,
            'actions' => array_keys(self::ACTIONS),
            'lastAction' => $action,
            'results' => $results,
        ]);
    }

    private function authorizeToken(string $token): ?Response
    {
        if (! config('deploy-runner.enabled')) {
            abort(404);
        }

        $expected = (string) config('deploy-runner.token');
        if ($expected === '' || ! hash_equals($expected, $token)) {
            abort(403);
        }

        return null;
    }

    /**
     * @return list<array{command: string, exit_code: int, output: string}>
     */
    private function runAction(string $action): array
    {
        $results = [];

        if ($action === 'deploy-all') {
            foreach (['migrate', 'storage:link', 'optimize'] as $step) {
                $results = array_merge($results, $this->runAction($step));
            }

            return $results;
        }

        $signature = self::ACTIONS[$action];
        $command = $signature[0];
        $parameters = array_slice($signature, 1);

        $exitCode = Artisan::call($command, $this->parametersToOptions($parameters));

        $results[] = [
            'command' => 'php artisan '.implode(' ', $signature),
            'exit_code' => $exitCode,
            'output' => trim(Artisan::output()),
        ];

        return $results;
    }

    /**
     * @param  list<string>  $parameters
     * @return array<string, mixed>
     */
    private function parametersToOptions(array $parameters): array
    {
        $options = [];

        foreach ($parameters as $parameter) {
            if (str_starts_with($parameter, '--')) {
                $name = ltrim($parameter, '-');
                $options[$name] = true;
            }
        }

        return $options;
    }
}
