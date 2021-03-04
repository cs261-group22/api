<?php

use App\Models\Event;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('event-feed.{eventId}', function ($user, $eventId) {
    $event = Event::findOrFail($eventId);

    return $event->hostedByUser($user);
});
