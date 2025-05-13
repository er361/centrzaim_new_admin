<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * Главная страница.
     *
     * @return Factory|Application|View
     */
    public function __invoke(): Application|Factory|View
    {
        $settings = setting()->all();
        $redirectMainPage = !Arr::get($settings, 'should_redirect_to_register_page_from_sources', '0');
        $redirectMainPage = $redirectMainPage && Arr::get($settings, 'is_redirect_enabled', '0');

        $showForm = 'false';
        $redirectUrl = Arr::get($settings, 'redirect_url', '');

        return view('pages.index', compact('redirectMainPage', 'showForm', 'redirectUrl'));
    }
}
