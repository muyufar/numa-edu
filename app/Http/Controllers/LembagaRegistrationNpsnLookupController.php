<?php

namespace App\Http\Controllers;

use App\Http\Requests\LookupLembagaRegistrationByNpsnRequest;
use App\Models\LembagaRegistration;
use Illuminate\View\View;

class LembagaRegistrationNpsnLookupController extends Controller
{
    public function create(): View
    {
        return view('public.lembaga-check-npsn', [
            'registration' => null,
            'submittedNpsn' => null,
        ]);
    }

    public function store(LookupLembagaRegistrationByNpsnRequest $request): View
    {
        $npsn = $request->validated()['npsn'];

        $registration = LembagaRegistration::query()
            ->where('npsn', $npsn)
            ->orderByDesc('id')
            ->first();

        return view('public.lembaga-check-npsn', [
            'registration' => $registration,
            'submittedNpsn' => $npsn,
        ]);
    }
}
