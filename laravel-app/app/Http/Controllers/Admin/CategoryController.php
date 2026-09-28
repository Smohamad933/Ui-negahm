<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Support\Slugger;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:190'],
            'aspect_ratio' => ['required', 'in:16:9,9:16,1:1'],
        ]);

        $client = Client::query()->findOrFail($data['client_id']);

        $baseSlug = Slugger::make($data['title']);
        $slug = Slugger::unique($baseSlug, fn ($s) => $client->categories()->where('slug', $s)->exists());
        $maxOrder = (int) ($client->categories()->max('order_index') ?? -1);

        $client->categories()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => '',
            'cover_image_url' => '',
            'aspect_ratio' => $data['aspect_ratio'],
            'order_index' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.clients.edit', ['client' => $client, 'tab' => 'portfolio'])
            ->with('status', 'دسته‌بندی جدید اضافه شد.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:2000'],
            'aspect_ratio' => ['required', 'in:16:9,9:16,1:1'],
            'cover' => ['nullable', 'image', 'max:12288'],
            'remove_cover' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'aspect_ratio' => $data['aspect_ratio'],
        ];

        if ($request->hasFile('cover')) {
            Uploads::deletePublicPath($category->cover_image_url);
            $payload['cover_image_url'] = Uploads::store($request->file('cover'), 'images');
        } elseif ($request->boolean('remove_cover')) {
            Uploads::deletePublicPath($category->cover_image_url);
            $payload['cover_image_url'] = '';
        }

        $category->update($payload);

        return redirect()->route('admin.clients.edit', ['client' => $category->client_id, 'tab' => 'portfolio'])
            ->with('status', 'دسته‌بندی ذخیره شد.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $clientId = $category->client_id;

        foreach ($category->items as $item) {
            Uploads::deletePublicPath($item->media_url);
        }
        Uploads::deletePublicPath($category->cover_image_url);
        $category->delete();

        return redirect()->route('admin.clients.edit', ['client' => $clientId, 'tab' => 'portfolio'])
            ->with('status', 'دسته‌بندی حذف شد.');
    }
}
