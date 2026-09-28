<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Message;
use App\Models\PortfolioItem;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $settings = Setting::current();
        $clientCount = Client::query()->published()->count();
        $projectCount = Category::query()->count();
        $itemCount = PortfolioItem::query()->count();

        return view('site.about', compact('settings', 'clientCount', 'projectCount', 'itemCount'));
    }

    public function contact(): View
    {
        $settings = Setting::current();

        return view('site.contact', compact('settings'));
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'نام و نام‌خانوادگی را وارد کنید.',
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
            'message.required' => 'متن پیام را وارد کنید.',
        ]);

        Message::query()->create($data);

        return back()->with('contact_success', true);
    }
}
