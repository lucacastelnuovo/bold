<?php

namespace App\Http\Controllers;

use App\Enums\Lock;
use Illuminate\Http\Request;

class ApiLockController extends Controller
{
    public function __invoke(Request $request, string $lock)
    {
        $lock = Lock::from($lock);

        if (!$request->user()->tokenCan($lock->value)) {
            return response(['success' => false], 403);
        }

        $message = $lock->trigger($request->user(), $request->user()->name);

        return response(['message' => $message], 'ok' === $message ? 200 : 500);
    }
}
