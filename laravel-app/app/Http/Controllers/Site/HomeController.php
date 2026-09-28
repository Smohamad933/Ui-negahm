<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PortfolioItem;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $settings = Setting::current();

        $clients = Client::query()->published()->ordered()->orderByDesc('id')->get();
        $featuredClients = Client::query()->published()->featured()->ordered()->get();

        $featured = PortfolioItem::query()
            ->join('categories', 'categories.id', '=', 'portfolio_items.category_id')
            ->join('clients', 'clients.id', '=', 'categories.client_id')
            ->where('portfolio_items.featured_home', true)
            ->where('clients.published', true)
            ->orderBy('portfolio_items.order_index')
            ->limit(7)
            ->select([
                'portfolio_items.*',
                'clients.name as client_name',
                'clients.slug as client_slug',
                'categories.slug as category_slug',
                'categories.title as category_title',
                'categories.aspect_ratio as category_aspect_ratio',
            ])
            ->get();

        return view('site.home', compact('settings', 'clients', 'featuredClients', 'featured'));
    }
}
