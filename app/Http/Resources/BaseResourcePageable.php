<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

class BaseResourcePageable extends ResourceCollection
{
    protected string $message;
    protected int $statusCode;

    public function __construct($resource, string $message = 'Success', int $statusCode = 200)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    /**
     * Transform the paginated resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->collection,
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'lastPage' => $this->lastPage(),
                'perPage' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }

    /**
     * Set status code in response.
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }

    /**
     * Static helper for paginated response.
     */
    public static function respond(int $status, string $message, $resource)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $resource->items(),
            'meta' => [
                'current_page' => $resource->currentPage(),
                'from' => $resource->firstItem(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'to' => $resource->lastItem(),
                'total' => $resource->total(),
            ],
        ], $status);
    }
}
