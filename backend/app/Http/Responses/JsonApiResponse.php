<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

//-------------------------------------------------------------------
// https://medium.com/@a.shatrov.1982/building-laravel-api-http-responses-one-class-to-rule-them-all-feat-laravel-responsable-e311ad75d6ab
//-------------------------------------------------------------------
class JsonApiResponse implements Responsable
{
    protected int $httpCode;
    protected array $data;
    protected string $errorMessage;

	//-----------------------------------------------------
    public function __construct(int $httpCode, array $data = [], string $errorMessage = '')
    {
        if(!(($httpCode >= 200 && $httpCode <= 300) || ($httpCode >= 400 && $httpCode <= 600))) 
		{
        	throw new \RuntimeException($httpCode . ' is not valid');
        }

        $this->httpCode = $httpCode;
        $this->data = $data;
        $this->errorMessage = $errorMessage;
    }
    
	//-----------------------------------------------------
    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        $payload = match (true) {
			$this->httpCode >= 500 => ['error_message' => 'Server error'],
			$this->httpCode >= 400 => ['error_message' => $this->errorMessage, 'data' => $this->data],
			$this->httpCode >= 200 => ['data' => $this->data],
        };

        return response()->json(
			data: $payload,
			status: $this->httpCode,
			options: JSON_UNESCAPED_UNICODE
        );
    }

	//-----------------------------------------------------
    public static function ok(array $data)
    {
        return new static(200, $data);
    }
    
	//-----------------------------------------------------
    public static function created(array $data)
    {
        return new static(201, $data);
    }
    
	//-----------------------------------------------------
    public static function notFound(string $errorMessage = "Item not found")
    {
        return new static(404, errorMessage: $errorMessage);
    }

	//-----------------------------------------------------
    public static function unprocessable(string $errorMessage = "Unprocessable Content", array $data = [])
    {
        return new static(422, $data, $errorMessage);
    }	
}