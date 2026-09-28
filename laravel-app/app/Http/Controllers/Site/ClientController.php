<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()->published()->ordered()->orderByDesc('id')->get();

        return view('site.clients-index', compact('clients'));
    }

    public function show(Client $client): View
    {
        if (! $client->published) {
            throw new NotFoundHttpException();
        }

        $categories = $client->categories()->with(['items' => fn ($q) => $q->limit(1)])->get()
            ->map(function (Category $cat) {
                if (! $cat->cover_image_url) {
                    $first = $cat->items->first();
                    $cat->cover_image_url = $first?->media_url ?? '';
                }

                return $cat;
            });

        return view('site.client-show', compact('client', 'categories'));
    }

    public function category(Client $client, Category $category): View
    {
        if (! $client->published || $category->client_id !== $client->id) {
            throw new NotFoundHttpException();
        }

        $items = $category->items()->get();
        $otherCategories = $client->categories()->where('id', '!=', $category->id)->get();

        return view('site.category-show', compact('client', 'category', 'items', 'otherCategories'));
    }
}
