<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Support\Slugger;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()->ordered()->orderByDesc('id')->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'industry' => ['nullable', 'string', 'max:190'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'max:12288'],
            'cover' => ['nullable', 'image', 'max:12288'],
        ], [
            'name.required' => 'نام کارفرما را وارد کنید.',
        ]);

        $slug = Slugger::unique(Slugger::make($data['name']), fn ($s) => Client::query()->where('slug', $s)->exists());
        $maxOrder = (int) (Client::query()->max('order_index') ?? 0);

        $client = Client::query()->create([
            'slug' => $slug,
            'name' => $data['name'],
            'industry' => $data['industry'] ?? '',
            'short_description' => $data['short_description'] ?? '',
            'website_url' => $data['website_url'] ?? '',
            'year' => $data['year'] ?? '',
            'accent_color' => $data['accent_color'] ?? '',
            'logo_url' => $request->hasFile('logo') ? Uploads::store($request->file('logo'), 'images') : '',
            'cover_image_url' => $request->hasFile('cover') ? Uploads::store($request->file('cover'), 'images') : '',
            'featured' => false,
            'published' => true,
            'order_index' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.clients.edit', $client)->with('status', 'کارفرما با موفقیت ایجاد شد.');
    }

    public function edit(Client $client): View
    {
        $client->load(['categories.items']);

        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190'],
            'industry' => ['nullable', 'string', 'max:190'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:20'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'max:12288'],
            'cover' => ['nullable', 'image', 'max:12288'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_cover' => ['nullable', 'boolean'],
            'published' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
        ]);

        $slug = Slugger::make($data['slug']);
        if ($slug !== $client->slug) {
            $slug = Slugger::unique($slug, fn ($s) => Client::query()->where('slug', $s)->where('id', '!=', $client->id)->exists());
        }

        $payload = [
            'name' => $data['name'],
            'slug' => $slug,
            'industry' => $data['industry'] ?? '',
            'short_description' => $data['short_description'] ?? '',
            'website_url' => $data['website_url'] ?? '',
            'year' => $data['year'] ?? '',
            'accent_color' => $data['accent_color'] ?? '',
            'published' => $request->boolean('published'),
            'featured' => $request->boolean('featured'),
        ];

        if ($request->hasFile('logo')) {
            Uploads::deletePublicPath($client->logo_url);
            $payload['logo_url'] = Uploads::store($request->file('logo'), 'images');
        } elseif ($request->boolean('remove_logo')) {
            Uploads::deletePublicPath($client->logo_url);
            $payload['logo_url'] = '';
        }

        if ($request->hasFile('cover')) {
            Uploads::deletePublicPath($client->cover_image_url);
            $payload['cover_image_url'] = Uploads::store($request->file('cover'), 'images');
        } elseif ($request->boolean('remove_cover')) {
            Uploads::deletePublicPath($client->cover_image_url);
            $payload['cover_image_url'] = '';
        }

        $client->update($payload);

        return redirect()->route('admin.clients.edit', $client)->with('status', 'تغییرات ذخیره شد.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        foreach ($client->categories as $category) {
            foreach ($category->items as $item) {
                Uploads::deletePublicPath($item->media_url);
            }
        }
        Uploads::deletePublicPath($client->logo_url);
        Uploads::deletePublicPath($client->cover_image_url);

        $client->delete();

        return redirect()->route('admin.clients.index')->with('status', 'کارفرما حذف شد.');
    }

    public function togglePublished(Client $client): RedirectResponse
    {
        $client->update(['published' => ! $client->published]);

        return back();
    }

    public function toggleFeatured(Client $client): RedirectResponse
    {
        $client->update(['featured' => ! $client->featured]);

        return back();
    }

    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'direction' => ['required', 'in:up,down'],
            'client_id' => ['required', 'integer'],
        ]);

        $clients = Client::query()->ordered()->orderByDesc('id')->get()->values();
        $index = $clients->search(fn ($c) => $c->id === (int) $data['client_id']);

        if ($index !== false) {
            $target = $data['direction'] === 'up' ? $index - 1 : $index + 1;
            if ($target >= 0 && $target < $clients->count()) {
                $a = $clients[$index];
                $b = $clients[$target];
                DB::transaction(function () use ($a, $b) {
                    $aOrder = $a->order_index;
                    $a->update(['order_index' => $b->order_index]);
                    $b->update(['order_index' => $aOrder]);
                });
            }
        }

        return back();
    }
}
