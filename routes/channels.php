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

Broadcast::channel('event-submissions.{eventId}', function ($user, $eventId) {
    $event = Event::findOrFail($eventId);

    return $event->hostedByUser($user);
});

Broadcast::channel('event-feedback-presence.{eventId}', function ($user, $eventId) {
    $event = Event::findOrFail($eventId);

    if ($event->hostedByUser($user)) {
        return true;
    }
});

Broadcast::channel('attendee-presence.{eventId}', function ($user, $eventId) {
    $event = Event::findOrFail($eventId);

    $userHasSession = $user->sessions()
        ->where('event_id', $eventId)
        ->where('is_submitted', false)
        ->exists();

    // The user must host the event, or have an unsubmitted session
    if ($userHasSession || $event->hostedByUser($user)) {
        return true;
    }
});
