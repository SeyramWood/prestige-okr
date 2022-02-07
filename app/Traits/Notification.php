<?php

namespace App\Traits;

use Spatie\Activitylog\Models\Activity;

/**
 * Notificatication
 */
trait Notification
{
  private function modelActivity($model, $causedBy, array $properties, $message)
  {
    activity()
      ->performedOn($model)
      ->causedBy($causedBy)
      ->withProperties($properties)
      ->log($message);
    return Activity::all()->last();
  }
}
