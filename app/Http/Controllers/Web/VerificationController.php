<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function send(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent');
        } catch (\Exception $e) {
            Log::error('Error al enviar verificación: ' . $e->getMessage());

            return back()->with('error', 'Hubo un problema al enviar el correo. Puede que su corro no sea válido.');
        }
    }
}
