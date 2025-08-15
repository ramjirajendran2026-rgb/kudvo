<?php

namespace App\Http\Controllers;

use App\Actions\HandleSnsEvent;
use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

class AwsSnsController extends Controller
{
    public static array $routes = [
        'sends' => 'sends',
        'rendering_failures' => 'rendering-failures',
        'rejects' => 'rejects',
        'deliveries' => 'deliveries',
        'bounces' => 'bounces',
        'complaints' => 'complaints',
        'delivery_delays' => 'delivery-delays',
        'subscriptions' => 'subscriptions',
        'opens' => 'opens',
        'clicks' => 'clicks',
    ];

    /**
     * @throws BindingResolutionException
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, Arr::map(array: array_keys(self::$routes), callback: fn ($route) => Str::camel($route)))) {
            $request = app()->make(ServerRequestInterface::class);

            $result = $this->handleRequest($request, Str::studly($method));

            if ($result instanceof JsonResponse) {
                return $result;
            }

            HandleSnsEvent::dispatch($method, $result);

            return response()->json([
                'success' => true,
                'message' => Str::studly($method) . ' processed.',
            ]);
        }

        return parent::__call($method, $parameters);
    }

    protected function handleRequest(ServerRequestInterface $request, string $type): mixed
    {
        $this->validateSns($request);

        $body = request()->getContent();

        $this->logResult($body);

        $body = json_decode($body);

        if ($body === null) {
            Log::error("Failed to parse AWS SES $type request " . json_last_error_msg());

            return response()->json(['success' => false], 422);
        }

        if ($this->isSubscriptionConfirmation($body)) {
            $subscriptionConfirmed = $this->confirmSubscription($body);

            if ($subscriptionConfirmed) {
                return response()->json([
                    'success' => true,
                    'message' => "$type subscription confirmed.",
                ]);
            } else {
                return response()->json(['success' => false], 422);
            }
        }

        if ($this->isNotTopicNotification($body)) {
            Log::info("SES Event notification did not match known type. Type Received: {$body->Type}.");

            return response()->json(['success' => false], 422);
        }

        $message = json_decode($body->Message);

        if (! is_object($message)) {
            Log::error('Result message failed to decode: ' . json_last_error_msg());

            return response()->json(['success' => false], 422);
        }

        if (! isset($message->eventType)) {
            $message->eventType = $message->notificationType;
        }

        return $message;
    }

    protected function validateSns(ServerRequestInterface $request): void
    {
        $message = Message::fromPsrRequest($request);
        $validator = new MessageValidator;

        try {
            $validator->validate($message);
        } catch (InvalidSnsMessageException $e) {
            // Pretend we're not here if the message is invalid
            abort(404, 'Not Found');
        }
    }

    protected function confirmSubscription(object $body): bool
    {
        $response = Http::get($body->SubscribeURL);

        $this->logResult($response->body());

        $xml = simplexml_load_string($response->body());

        if ($response->ok() && $xml !== false && ! empty((string) $xml->ConfirmSubscriptionResult->SubscriptionArn)) {
            $this->logMessage('Subscribed to (' . $body->TopicArn . ') using GET Request ' . $body->SubscribeURL);

            return true;
        } else {
            $this->logMessage('Subscription Attempt Failed for (' . $body->TopicArn . ') using GET Request ' . $body->SubscribeURL);

            return false;
        }
    }

    protected function isSubscriptionConfirmation(object $body): bool
    {
        if (isset($body->Type) && ($body->Type === 'SubscriptionConfirmation')) {
            $this->logMessage('Received subscription confirmation: ' . $body->TopicArn);

            return true;
        }

        return false;
    }

    protected function isTopicNotification(object $body): bool
    {
        if (isset($body->Type) && $body->Type == 'Notification') {
            $this->logMessage('Received topic notification: ' . $body->TopicArn);

            return true;
        }

        return false;
    }

    protected function isNotTopicNotification(object $body): bool
    {
        return ! $this->isTopicNotification($body);
    }

    protected function logMessage(string $message): void
    {
        if ($this->debug()) {
            Log::debug($message);
        }
    }

    protected function logResult(string $content): void
    {
        if ($this->debug()) {
            Log::debug("REQUEST BODY:\n" . $content);
        }
    }

    protected function debug(): bool
    {
        return false;
    }
}
