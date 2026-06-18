<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortUrl\StoreShortUrlRequest;
use App\Http\Requests\ShortUrl\UpdateShortUrlRequest;
use App\Services\ShortUrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortUrlController extends Controller
{
    public function __construct(
        private ShortUrlService $shortUrlService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = (int) auth('api')->id();
        $perPage = $request->integer('per_page', 15)

        $shortUrls = $this->shortUrlService->paginateByUserId($userId, $perPage);

        return response()->json([
            'message' => 'Short URLs fetched successfully.',
            'data' => $shortUrls,
        ]);
    }

    public function store(StoreShortUrlRequest $request): JsonResponse
    {
        $userId = (int) auth('api')->id();
        $shortUrl = $this->shortUrlService->create($userId, $request->validated());

        return response()->json([
            'message' => 'Short URL created successfully.',
            'data' => $shortUrl,
        ], 201);
    }

    public function update(UpdateShortUrlRequest $request, int $id): JsonResponse
    {
        $userId = (int) auth('api')->id();
        $shortUrl = $this->shortUrlService->update($id, $userId, $request->validated());

        return response()->json([
            'message' => 'Short URL updated successfully.',
            'data' => $shortUrl,
        ]);
    }

    public function redirect(string $shortCode): RedirectResponse
    {
        $shortUrl = $this->shortUrlService->clickCount($shortCode);
        return redirect()->away($shortUrl->original_url);
    }
}
