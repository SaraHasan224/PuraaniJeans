<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\AuthCode;
use App\Models\Client;
use App\Models\Token;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /**
         * This method will register the routes necessary to issue access tokens .
         * and revoke access tokens, clients, and personal access tokens
         */

        Passport::loadKeysFrom(storage_path("secrets/oauth"));


//        Passport::routes(function ($router) {
//            $router->forAccessTokens();
//        }, ['prefix' => '/v1/passport',
//            'middleware' => ['log'],
//            'domain' => env('API_DOMAIN')]);

        /**
         * If you would like your client's secrets to be hashed when stored in your database.
         */
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));

        /**
         * Extends the models used internally by Passport.
         */
        Passport::useTokenModel(Token::class);
        Passport::useClientModel(Client::class);
        Passport::useAuthCodeModel(AuthCode::class);


        Passport::tokensCan([
            'customer'=> 'customer',
        ]);
    }
}
