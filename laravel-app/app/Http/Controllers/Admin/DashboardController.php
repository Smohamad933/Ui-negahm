<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Message;
use App\Models\PortfolioItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $clientCount = Client::query()->count();
        $categoryCount = Category::query()->count();
        $itemCount = PortfolioItem::query()->count();
        $unread = Message::query()->where('is_read', false)->count();

        return view('admin.dashboard', compact('clientCount', 'categoryCount', 'itemCount', 'unread'));
    }
}
