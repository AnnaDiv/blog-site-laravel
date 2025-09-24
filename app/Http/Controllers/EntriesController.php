<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\EntriesRepository;
use Illuminate\Http\RedirectResponse;
use App\Support\ImageCreator;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;

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

        $posts = $this->entriesRepository->browse($perPage, $excludedUsers);

        //$art_images = $this->entriesRepository->artBanner();

        return view('home.index', ['posts' => $posts]);
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
        $posts = $this->entriesRepository->search($perPage, $quote, $excludedUsers);

        return view('home.index')->with('posts', $posts);
    }

    public function createPostView(Request $request): View
    {
        $categories = Category::query()
            ->pluck('title')
            ->toArray();
        //dd($categories);
        return view('post.create-post')->with('categories', $categories);
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

    public function create(Request $request, ImageCreator $creator): RedirectResponse
    {
        //user
        $user = $request->user();

        //all inputs
        $title = ($request->input('title') ?? '');
        $description = ($request->input('description') ?? '');
        $categories = json_decode($request->input('categories'), true);
        $post_status = $request->input('post_status');
        $type = $request->input('post_type');
        
        $all_cats = Category::query()
            ->pluck('title')
            ->toArray();
        
        $final_categories = [];

        foreach ($categories as $category) {

            $category = ucfirst(strtolower($category));
            if (in_array($category, $all_cats)) {
                $final_categories[] = ucfirst(strtolower($category));
            } else {
                $category = ucfirst(strtolower($category));
                Category::create([
                    'title' => $category,
                    'description' => ' ',
                ]);

                $final_categories[] = ucfirst(strtolower($category));
            }
        }
        if (empty($final_categories)) {
            $final_categories[] = 'None';
        }

        if ($post_status !== 'public' && $post_status !== 'private') {
            $post_status = 'private';
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
        } else {
            return back()->with('error', 'Image is missing');
        }

        if (!empty($title) && !empty($description) && !empty($image)) {

            $imageSubmit = $creator->createImage($image, $user->id);

            if (!empty($imageSubmit)) {
                $post = $this->entriesRepository->finalizing_posting($imageSubmit, $user->id, $user->nickname, $title, $description, $final_categories, $post_status, $type);

                if ($post !== false) {
                    return redirect()->route('post.view', ['post' => $post->id]);
                } else {
                    $errors[] = "entry couldnt be created";
                }
            } else {
                $errors[] = "something is wrong with your image, submit a different one";
            }
        } else {
            $errors[] = "not everything is filled";
        }

        return back()->with('error', 'errors on posting');
    }

    public function category(Request $request, string $category_id) : View {

        if ($request->user()) {
            $user = $request->user();

            $excludedUsers = $this->entriesRepository->excludedUsers($user->nickname);
        }
        else {
            $excludedUsers = [];
        }

        $posts = $this->entriesRepository->categoryPosts($category_id, $excludedUsers);

        return view('home.index')->with('posts', $posts);
    }
}
