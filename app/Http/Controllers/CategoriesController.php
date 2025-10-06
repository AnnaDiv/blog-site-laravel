<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\CategoriesRepository;
use Illuminate\Http\RedirectResponse;

use App\Models\Category;

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

    public function adminSearch(Request $request): View
    {
        $perPage = 30;
        $quote = strtolower($request->input('search_q'));
        $categories = $this->categoriesRepository->search($perPage, $quote);

        return view('admin.admin-categories')->with('categories', $categories);
    }

    public function view(Category $category) : View {
        return view('categories.category-edit')->with('category', $category);
    }

    public function delete(Category $category) : RedirectResponse {
        $category->delete();
        return back()->with('success', 'deleted category');
    }

    public function update(Category $category, Request $request) : RedirectResponse {

        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string'
        ]);
        $category->update($validatedData);

        return redirect()->route('admin.categories');
    }
}
