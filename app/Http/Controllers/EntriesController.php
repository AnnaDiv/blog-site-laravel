<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\EntriesRepository;
use Illuminate\Http\RedirectResponse;

use App\Models\Post;
use App\Models\User;

class EntriesController extends Controller
{
    public function __construct(protected EntriesRepository $entriesRepository) {}

    public function browse(Request $request): View
    {
        $perPage = 15;
        if ($request->user()) {
            $user = $request->user();
            $excludedUsers = $this->entriesRepository->excludedUsers($user->nickname);
        } else {
            $excludedUsers = [];
        }

        $entries = $this->entriesRepository->browse($perPage, $excludedUsers);

        //$art_images = $this->entriesRepository->artBanner();

        return view('home.index', ['entries' => $entries]);
    }

    public function search(Request $request)
    {
        $perPage = 15;
        $quote = strtolower($request->input('search_q'));

        if ($request->user()) {
            $user = $request->user();
            $excludedUsers = $this->entriesRepository->excludedUsers($user->nickname);
        } else {
            $excludedUsers = [];
        }
        $entries = $this->entriesRepository->search($perPage, $quote, $excludedUsers);

        return view('home.index')->with('entries', $entries);
    }

    public function create(): RedirectResponse
    {

        return redirect()->route('post.view');
    }

    public function view(Post $post, Request $request)
    {
        if ($request->user()) {
            // user trying to access the post
            $user = $request->user();

            //owner of post
            $owner = User::where('nickname', $post->user_nickname)->first();

            //if user is blocked by owner
            if ($owner->hasBlocked($user)) {
                return redirect()->route('home'); //not gonna show him this
            }
        }
        return view('post.view-post')->with('post', $post);
    }
}
