<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\User;
use App\Repositories\CategoriesRepository;
use App\Repositories\EntriesRepository;
use App\Repositories\UsersRepository;

class AdminController extends Controller
{
    public function page404() : View {
        return view('components.page404');
    }
    public function panel() : View {
        return view('admin.admin-panel');
    }

    public function users() : View {
        $users = User::query('*')
            ->withCount(['likes', 'comments', 'posts'])
            ->paginate(15);
        return view('admin.admin-users')->with('users', $users);
    }

    public function userSearch(UsersRepository $usersRepository, Request $request) : View {

        $perPage = 15;
        $quote = strtolower($request->input('search_q'));
    
        $users = $usersRepository->usersByQuote($perPage, $quote);
        return view('admin.admin-users')->with('users', $users);
    }

    public function categories(CategoriesRepository $categoriesRepository) : View {
        $categories = $categoriesRepository->browse(30);

        return view('admin.admin-categories')->with('categories', $categories);
    }

    public function deletedPosts(EntriesRepository $entriesRepository) : View {
        $posts = $entriesRepository->deletedPosts();
        
        return view('admin.admin-deleted-posts')->with('posts', $posts);
    }

    public function searchDeletedPosts(EntriesRepository $entriesRepository, Request $request) : View {
        
        $perPage = 15;
        $quote = strtolower($request->input('search_q'));

        $posts = $entriesRepository->deletedPostsbyQuote($perPage, $quote);

        return view('admin.admin-deleted-posts')->with('posts', $posts);
    }
}
