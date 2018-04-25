<?php

namespace App\Exceptions;

use App\Traits\ApiResponder;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class Handler extends ExceptionHandler
{
    use ApiResponder;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof ValidationException){

            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof ModelNotFoundException){

            //obtain model name
            $modelName = class_basename($exception->getModel());

            return $this->errorResponse("Does not exist any {$modelName} with specified Id", 404);
        }

        if($exception instanceof AuthenticationException){

            return $this->errorResponse("Unauthenticated", 401);
        }

        if($exception instanceof AuthorizationException){
            $message = $exception->getMessage();
            $code    = 403;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof \ReflectionException){
            $message = "The Class or Controller Not Found.";//$exception->getMessage();
            $code    = 500;

            return $this->errorResponse($message, $code);
        }

        if($exception instanceof \ErrorException1){
            $message = $exception->getMessage();
            $code    = 404;

            return $this->errorResponse($message, $code);
        }





        //Code for Handling any METHOD NOT FOUND HTTP Exception
        if($exception instanceof MethodNotAllowedHttpException){

            $message = "The Specified Method for the request is invalid";
            $code    = 405;

            return $this->errorResponse($message, $code);

        }
        //Code for Handling any NOT FOUND HTTP Exception
        if($exception instanceof NotFoundHttpException){

            $message = "The Specified URL cannot be found";
            $code    = 404;

            return $this->errorResponse($message, $code);
        }



        //Code for Handling any other General HTTP Exception
        if($exception instanceof HttpException){

            $message = $exception->getMessage();
            $code    = $exception->getCode();

            return $this->errorResponse($message, $code);
        }

        //Code for Handling any other General QUERY Exception
        if($exception instanceof QueryException){

            //dd($exception); - >Debbuger like die() in php

            $errorCode = $exception->errorInfo[1];
            if($errorCode == 1451){
                return $this->errorResponse("Cannot remove this resource permanently. It is related with other resources", 409);
            }
        }



        //Code for Handling any other Token Missmatch Exception
        if($exception instanceof TokenMismatchException){

            return redirect()->back()->withInput($request-input());
        }

        /*
         * Return full error message if in dev mode
         * if(config('app.debug')){
         *  return parent::render($request, $exception);
         * }
         *
         */
        return parent::render($request, $exception);
        //Code for Handling any other Unexpected Exception
        //return $this->errorResponse("Unexpected Server error occurred. Please, Try again Later.", 500);
    }
}
