<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Deploy Runner — Sementara</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .wrap { max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px rgb(0 0 0 / 6%); }
        h1 { font-size: 1.25rem; margin: 0 0 .5rem; }
        p { color: #475569; line-height: 1.5; }
        .warn { background: #fff7ed; border: 1px solid #fdba74; color: #9a3412; border-radius: 12px; padding: .875rem 1rem; margin: 1rem 0; font-size: .9rem; }
        .actions { display: grid; gap: .75rem; margin-top: 1.25rem; }
        button { width: 100%; text-align: left; border: 1px solid #cbd5e1; background: #fff; border-radius: 12px; padding: .9rem 1rem; font-size: .95rem; cursor: pointer; }
        button:hover { border-color: #0f766e; background: #f0fdfa; }
        button.primary { background: #0f766e; border-color: #0f766e; color: #fff; font-weight: 600; }
        button.primary:hover { background: #115e59; }
        pre { background: #0f172a; color: #e2e8f0; border-radius: 12px; padding: 1rem; overflow: auto; font-size: .8rem; white-space: pre-wrap; }
        .ok { color: #15803d; font-weight: 600; }
        .fail { color: #b91c1c; font-weight: 600; }
        .result { margin-top: 1rem; }
        .result h2 { font-size: 1rem; margin-bottom: .5rem; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Deploy Runner (sementara)</h1>
            <p>Jalankan perintah Artisan deploy tanpa SSH. Nonaktifkan setelah selesai.</p>

            <div class="warn">
                <strong>Penting:</strong> Set <code>DEPLOY_RUNNER_ENABLED=false</code> di <code>.env</code> production setelah deploy, lalu hapus route &amp; file ini.
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('deploy.runner.run', $token) }}">
                    @csrf
                    <input type="hidden" name="action" value="deploy-all">
                    <button type="submit" class="primary">Jalankan semua (migrate + storage:link + optimize)</button>
                </form>

                @foreach ($actions as $action)
                    @if ($action === 'deploy-all')
                        @continue
                    @endif
                    <form method="POST" action="{{ route('deploy.runner.run', $token) }}">
                        @csrf
                        <input type="hidden" name="action" value="{{ $action }}">
                        <button type="submit">php artisan {{ $action }}{{ $action === 'migrate' ? ' --force' : '' }}</button>
                    </form>
                @endforeach
            </div>

            @isset($results)
                <div class="result">
                    <h2>Hasil: {{ $lastAction }}</h2>
                    @foreach ($results as $result)
                        <p class="{{ $result['exit_code'] === 0 ? 'ok' : 'fail' }}">
                            {{ $result['command'] }} — exit {{ $result['exit_code'] }}
                        </p>
                        <pre>{{ $result['output'] !== '' ? $result['output'] : '(tanpa output)' }}</pre>
                    @endforeach
                </div>
            @endisset
        </div>
    </div>
</body>
</html>
