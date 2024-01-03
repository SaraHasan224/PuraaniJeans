<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHandler;
use App\Helpers\Constant;
use App\Helpers\Order\BaseOrderHelper;
use App\Http\Traits\LoggingTrait;
use App\Models\Customer;
use App\Models\Order;
use App\Models\AccessToken;
use App\Models\OrderMisc;
use App\Models\RequestResponseLog;
use App\Models\SsoRequest;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\AuthenticationException;

//use Laminas\Diactoros\ResponseFactory;
//use Laminas\Diactoros\ServerRequestFactory;
//use Laminas\Diactoros\StreamFactory;
//use Laminas\Diactoros\UploadedFileFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Http\Middleware\CheckCredentials;
use Laravel\Passport\Token;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use App\Helpers\AppException;

class ValidateUserAccessToken extends CheckCredentials
{
    public function handle($request, Closure $next, ...$scopes)
    {
        $logResponse = true;
        try {
            $psr = (new PsrHttpFactory(
                new Psr17Factory,
                new Psr17Factory,
                new Psr17Factory,
                new Psr17Factory
//                new ServerRequestFactory,
//                new StreamFactory,
//                new UploadedFileFactory,
//                new ResponseFactory
            ))->createRequest($request);

            $psr = $this->server->validateAuthenticatedRequest($psr);

            if ($psr->getAttribute('oauth_client_id') == env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID')) {
                $customerAccessToken = AccessToken::getById($psr->getAttribute('oauth_access_token_id'));

                if ($customerAccessToken->can("customer")) {
                    $customerIdentifier = $customerAccessToken->name;
                    $customer = Customer::findByRef($customerIdentifier);
                    if ($customer) {
                        $request->request->set('customer_id', $customer->id);
                        $request->request->set('customer_ref', $customerIdentifier);

                        if ($customer->status == Constant::CUSTOMER_STATUS['Blocked']) {
                            $errorResponse = ['isExpired' => Constant::Yes];
                            $errMsg = __('messages.order.customer_blocked');
                            $request->request->set('error_msgs', $errorResponse);
                            throw new \Exception($errMsg);
                        }
                    } else {
                        $logResponse = false;
                        throw new \Exception(__('messages.order.invalid_missing_ref'));
                    }
                }
            } else {
                throw new \Exception("unauthenticated");
            }

            $this->validate($psr, $scopes);
        } catch (\Exception $e) {
            if ($logResponse) {
                AppException::log($e);
            }
            $error_response = (object)[];
            RequestResponseLog::addData($request, [
                'oauth_token_id' => $psr->getAttribute('oauth_access_token_id')
            ]);

            if ($request->get('error_msgs')) {
                $error_response = $request->get('error_msgs');
            }
            if ($request->get('result')) {
                $error_response['result'] = $request->get('result');
            }


            return ApiResponseHandler::failure($e->getMessage(), null, $error_response);
            //return ApiResponseHandler::authenticationError();
        }

        return $next($request);
    }

    /**
     * Validate token credentials.
     *
     * @param  Token $token
     * @return void
     *
     * @throws AuthenticationException
     */
    protected function validateCredentials($token)
    {
        if (!$token) {
            throw new AuthenticationException;
        }
    }

    /**
     * Validate token credentials.
     *
     * @param  Token $token
     * @param  array $scopes
     * @return void
     *
     * @throws MissingScopeException
     */
    protected function validateScopes($token, $scopes)
    {
        if (in_array('*', $token->scopes)) {
            return;
        }

        foreach ($scopes as $scope) {
            if ($token->cant($scope)) {
                throw new MissingScopeException($scope);
            }
        }
    }

    protected function byPassOrderExpiry($request, $order)
    {
        $byPassOrderExpiry = $request->header(Constant::REQUEST_HEADERS['bypass_expiry']);
        $byPassOrderExpiryValidation = false;
        if ($byPassOrderExpiry == "enabled") {
            if (
                in_array($request->path(), Constant::AllowedOrderExpiryHeadersOnRoutes) &&
                (
                $order->placement_status = Constant::OrderStatus['expired'] ||
                    strtotime($order->expiry) < time()
                )
            ) {
                if ($order) {
//                    $paymentGateway = $order->getMerchantPaymentGateway();
                    if (in_array($order->payment_gateway_id, Constant::BYPASS_EXPIRY_PAYMENT_INTEGRATIONS)) {
                        $byPassOrderExpiryValidation = true;
                        $expiryTime = (int)env('POST_IFRAME_BASED_ORDER_VALIDATION_TIMEFRAME');
                        $endTime = new Carbon();
                        $endTime->addMinutes($expiryTime);

                        $expiry = Carbon::parse($endTime)->format('Y-m-d H:i:s');

                        LoggingTrait::generalLogWithDescription($order->id, 'expired_order_payment_attempt', 'Order has been expired but we are updating order expiry time and reinitiating it as payment attempt is made: ');

                        $order->placement_status = Constant::OrderStatus['initiated'];
                        $order->expiry = $expiry;
                        $order->save();
                    }
                } else {
                    $byPassOrderExpiryValidation = true;
                }
            }
            return $byPassOrderExpiryValidation;

        }
    }

    protected function getStoreUrl($order)
    {
        return BaseOrderHelper::getStoreRedirectionURL($order);
    }

    protected function triggerPluginFailure($order)
    {
        $result = [];
        $result['reference'] = $order->order_ref;
        $result['status'] = "ABORT";
        $result['payment_failed'] = Constant::Yes;
        $result['limit_reached'] = Constant::No;
        return $result;
    }
}
