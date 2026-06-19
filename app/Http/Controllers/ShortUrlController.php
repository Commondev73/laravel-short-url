<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortUrl\StoreShortUrlRequest;
use App\Http\Requests\ShortUrl\UpdateShortUrlRequest;
use App\Jobs\RecordShortUrlClick;
use App\Services\ShortUrl\ShortUrlService;
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
        $perPage = $request->integer('per_page', 15);

        $shortUrls = $this->shortUrlService->paginateByUserId($userId, $perPage);

        return $this->paginated($shortUrls, 'Short URLs fetched successfully.');
    }

    public function store(StoreShortUrlRequest $request): JsonResponse
    {
        $userId = (int) auth('api')->id();
        $shortUrl = $this->shortUrlService->create($userId, $request->validated());

        return $this->created($shortUrl, 'Short URL created successfully.');
    }

    public function update(UpdateShortUrlRequest $request, int $id): JsonResponse
    {
        $userId = (int) auth('api')->id();
        $shortUrl = $this->shortUrlService->updateByIdAndUserId($id, $userId, $request->validated());

        return $this->success($shortUrl, 'Short URL updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $userId = (int) auth('api')->id();
        $this->shortUrlService->deleteByIdAndUserId($id, $userId);

        return $this->success(null, 'Short URL deleted successfully.');
    }

    public function redirect(string $shortCode): RedirectResponse
    {
        $shortUrl = $this->shortUrlService->findByShortCode($shortCode);

        RecordShortUrlClick::dispatch($shortUrl->id);

        return redirect()->away($shortUrl->original_url);
    }
}
