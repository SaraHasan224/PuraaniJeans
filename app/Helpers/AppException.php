<?php
namespace App\Helpers;

use Sentry\State\Scope;

class AppException
{
    public static function log( \Exception $exception )
    {
//        if (app()->bound('sentry')) {
//            app('sentry')->configureScope(function (Scope $scope): void {
//                $user = auth()->user();
//
//                if ($user) {
//                    $scope->setUser([
//                      'id' => $user->id,
//                      'email' => $user->email
//                    ]);
//                }
//            });
//            app('sentry')->captureException($exception);
//        }
    }
}

