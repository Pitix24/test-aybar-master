<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GmailWebhookController extends Controller
{
    public function handle(Request $request)
    {
        if (!$request->has('message.data')) {
            return response()->json(['error' => 'Payload inválido'], 400);
        }

        try {
            $messageData = json_decode(base64_decode($request->input('message.data')), true);
            $historyId = $messageData['historyId'] ?? null;
            $emailAddress = $messageData['emailAddress'] ?? null;

            Log::info("Webhook Gmail - Buzón: {$emailAddress} | HistoryID: {$historyId}");

            // Aquí luego despacharemos el Job.
            // Sigue el mismo patrón de ProcessIncomingWhatsapp

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error("Error Webhook Gmail: " . $e->getMessage());
            return response()->json(['error' => 'Internal Error'], 500);
        }
    }
}
