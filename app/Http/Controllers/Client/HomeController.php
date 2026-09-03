<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\HomeService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(protected HomeService $homeService) {}

    public function index(): View
    {
        return view('client.home.index', ['homeData' => $this->getHomeData()]);
    }

    public function about(): View
    {
        return view('client.home.about', ['homeData' => $this->getHomeData()]);
    }

    public function features(): View
    {
        return view('client.home.features', ['homeData' => $this->getHomeData()]);
    }

    public function pricingAndContact(): View
    {
        return view('client.home.pricing-contact', ['homeData' => $this->getHomeData()]);
    }

    private function getHomeData(): array
    {
        return $this->homeService->getHomePageData();
    }
}
