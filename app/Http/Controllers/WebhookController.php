<?php

namespace App\Http\Controllers;

use App\Jobs\SendWebhookJob;
use App\Models\WebhookEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\WebhookServer\WebhookCall;

class WebhookController extends Controller
{
    // //not correct
    // public function receiveViaToken(Request $request)
    // {

    //     $token = $request->bearerToken();

    //     $user = User::where('webhook_token', $token)->firstOrFail();

    //     $payload = $request->all();

    //     $event = WebhookEvent::create([
    //         'user_id'      => $user->id,
    //         'event'        => $payload['event'] ?? 'generic',
    //         'source'       => $request->ip(),
    //         'callback_url' => null,
    //         'payload'      => $payload,
    //         'status'       => WebhookEvent::STATUS_PENDING,
    //     ]);
    //     $event->markAsProcessed();
    //     return response()->json(['success' => true, 'id' => $event->id]);
    // }

    // public function verifyViaToken(Request $request)
    // {
    //     $token = $request->bearerToken();

    //     $user = User::where('webhook_token', $token)->firstOrFail();

    //     $payload = $request->all();

    //     $event = WebhookEvent::where('id', $payload['event_id'] ?? null)
    //         ->where('user_id', $user->id)
    //         ->firstOrFail();
    //     if ($event->status != WebhookEvent::isProcessed() || $event != WebhookEvent::isFailed()) {
    //         $event->markAsProcessed();
    //     }
    //     return response()->json(['success' => true, 'event' => $event]);
    // }
    // //not correct

    public function send(Request $request)
    {
        $request->validate([
            'callback_url' => 'required|url',
            'payload'      => 'required|array',
        ]);

        $event = WebhookEvent::create([
            'user_id'      => $request->user()->id,
            'event'        => $request->input('payload.event', 'generic'),
            'source'       => $request->ip(),
            'callback_url' => $request->input('callback_url'),
            'payload'      => $request->input('payload'),
            'status'       => WebhookEvent::STATUS_PENDING,
        ]);

        try {
            SendWebhookJob::dispatch(
                $event->id,
                $request->input('callback_url'),
                $request->input('payload'),
                config('app.webhook_secret')
            );

            $event->markAsProcessing();

            return response()->json([
                'success' => true,
                'message' => 'Webhook successfully queued for delivery',
                'id'      => $event->id,
            ]);
        } catch (\Throwable $th) {
            $event->markAsFailed($th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to queue webhook',
            ], 500);
        }
    }

    public function echo(Request $request)
    {
        $signature = $request->header('Signature');
        $payload   = $request->getContent();
        $secret    = config('app.webhook_secret');

        if (str_starts_with($secret, 'base64:')) {
            $secret = base64_decode(substr($secret, 7));
        }

        $computed = hash_hmac('sha256', $payload, $secret);

        return response()->json([
            'success'         => true,
            'echo'            => $request->all(),
            'signature_valid' => $signature ? hash_equals($computed, (string) $signature) : false,
            'received_at'     => now()->toDateTimeString(),
        ]);
    }

    public function logs(Request $request)
    {
        $user = $request->user();

        if (!$user->webhook_token) {
            $user->webhook_token = \Illuminate\Support\Str::random(64);
            $user->save();
        }

        $events = WebhookEvent::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('webhook.logs', compact('events'));
    }

    public function show(WebhookEvent $event)
    {
        $this->authorize('view', $event);
        return response()->json($event);
    }

    public function destroy(WebhookEvent $event)
    {
        $this->authorize('delete', $event);
        $event->delete();
        return back()->with('deleted', 'Log entry deleted.');
    }
    public function receive(Request $request, string $token)
    {
        $payload = $request->all();

        $user = User::where('webhook_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid webhook token'
            ], 401);
        }

        Cache::put('latest_webhook_data', [
            'user_id' => $user->id,
            'payload' => $payload,
            'token' => $token,
        ], now()->addHours(1));

        return response()->json([
            'message' => 'received'
        ]);
    }
    public function latest()
    {
        $cachedData = Cache::get('latest_webhook_data');

        if (!$cachedData) {
            return response()->json(['data' => null]);
        }
        $payload = $cachedData['payload'];
        return response()->json([
            'data' => [
                'payload' => $payload
            ]
        ]);
    }

    public function verify(Request $request, $id)
    {
        if (empty($id)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Webhook id is requied'
            ]);
        }

        $webhook = WebhookEvent::where('id', $id)->first();
        if (empty($webhook)) {
            return response()->json([
                'status' =>  'failed'
            ]);
        }
        $webhook->markAsProcessed();

        return response()->json([
            'status' => 'processed'
        ]);
    }
}
