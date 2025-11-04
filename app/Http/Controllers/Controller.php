<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View as ViewFacade;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Helper to respond with either JSON data or a Blade view.
     *
     * @return View|JsonResponse|Application|Response
     */
    protected function respond(Request $request, string $view, array $data = [], int $status = 200): View|JsonResponse|Application|Response
    {
        if ($request->wantsJson() || ! ViewFacade::exists($view)) {
            return response()->json($data, $status);
        }

        if ($status !== 200) {
            return response()->view($view, $data, $status);
        }

        return view($view, $data);
    }
}
