<?php

namespace App\Http\Controllers;

use App\Actions\SubmitAdmissionRequest as SubmitAdmissionRequestAction;
use App\Enums\GradeLevel;
use App\Http\Requests\StoreAdmissionRequest;
use App\Models\Major;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AdmissionController extends Controller
{
    /**
     * Show the admission form.
     */
    public function index(): View
    {
        return view('pages.admission', [
            'majors' => Major::query()
                ->active()
                ->select(['id', 'title', 'slug', 'summary', 'seats'])
                ->orderBy('order')
                ->orderBy('id')
                ->get(),
            'gradeLevels' => GradeLevel::ordered(),
        ]);
    }

    /**
     * Store an admission request.
     */
    public function store(
        StoreAdmissionRequest $request,
        SubmitAdmissionRequestAction $action,
    ): JsonResponse|RedirectResponse {
        $action->handle($request->validated());

        $message = 'درخواست ثبت‌نام شما با موفقیت ثبت شد. برای پیگیری، کادر آموزشی با شمارهٔ تماسی که وارد کرده‌اید تماس می‌گیرد.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
