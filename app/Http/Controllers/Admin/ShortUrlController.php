<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShortUrl\UpdateShortUrlRequest;
use App\Services\ShortUrl\ShortUrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    public function __construct(
        private ShortUrlService $shortUrlService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);

        $shortUrls = $this->shortUrlService->paginate($perPage, $page);

        return $this->paginated($shortUrls, 'Short URLs fetched successfully.');
    }

    public function show(int $id): JsonResponse
    {
        $shortUrl = $this->shortUrlService->findById($id);

        return $this->success($shortUrl, 'Short URL fetched successfully.');
    }

    public function update(UpdateShortUrlRequest $request, int $id): JsonResponse
    {
        $shortUrl = $this->shortUrlService->updateById($id, $request->validated());

        return $this->success($shortUrl, 'Short URL updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->shortUrlService->deleteById($id);

        return $this->success(null, 'Short URL deleted successfully.');
    }
}
