<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PortfolioItem;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortfolioItemController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'media_type' => ['required', 'in:image,video'],
            'media' => [
                'required',
                $request->input('media_type') === 'video' ? 'mimetypes:video/mp4,video/webm' : 'image',
                'max:20480',
            ],
            'title' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:2000'],
            'featured_home' => ['nullable', 'boolean'],
        ]);

        $category = Category::query()->findOrFail($data['category_id']);
        $maxOrder = (int) ($category->items()->max('order_index') ?? -1);

        $category->items()->create([
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'media_url' => Uploads::store($request->file('media'), 'images'),
            'media_type' => $data['media_type'],
            'featured_home' => $request->boolean('featured_home'),
            'order_index' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.clients.edit', ['client' => $category->client_id, 'tab' => 'portfolio'])
            ->with('status', 'نمونه‌کار اضافه شد.');
    }

    public function toggleFeatured(PortfolioItem $portfolioItem): RedirectResponse
    {
        $portfolioItem->update(['featured_home' => ! $portfolioItem->featured_home]);

        return back();
    }

    public function destroy(PortfolioItem $portfolioItem): RedirectResponse
    {
        $clientId = $portfolioItem->category->client_id;
        Uploads::deletePublicPath($portfolioItem->media_url);
        $portfolioItem->delete();

        return redirect()->route('admin.clients.edit', ['client' => $clientId, 'tab' => 'portfolio'])
            ->with('status', 'نمونه‌کار حذف شد.');
    }
}
