<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\CategoriesRepository;

class CategoriesController extends Controller
{
    public function __construct(protected CategoriesRepository $categoriesRepository) {}

    public function index(): View
    {
        $perPage = 30;
        $categories = $this->categoriesRepository->browse($perPage);

        return view('categories.categories')->with('categories', $categories);
    }

    public function search(Request $request): View
    {
        $perPage = 30;
        $quote = strtolower($request->input('search_q'));
        $categories = $this->categoriesRepository->search($perPage, $quote);

        return view('categories.categories')->with('categories', $categories);
    }
}
