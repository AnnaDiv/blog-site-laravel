<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\EntriesRepository;
use Illuminate\Http\RedirectResponse;
use App\Support\ImageCreator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class EntriesController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected EntriesRepository $entriesRepository) {}

    public function home() : View {

        $art_images = $this->entriesRepository->myart();

        return view('home.home')->with('art_images', $art_images);
    }
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

        $art_images = $this->entriesRepository->myart();

        return view('home.index', compact('posts', 'art_images'));
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
        $art_images = $this->entriesRepository->myart();

        return view('home.index', compact('posts', 'art_images'));
    }

    public function createPostView(): View
    {
        $categories = Category::query()
            ->pluck('title')
            ->toArray();
        
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
            if ($user->admin) {
                return view('post.view-post')->with('post', $post);
            }
            elseif (($user == $owner) && ($post->deleted == 0)){
                return view('post.view-post')->with('post', $post);
            }
            elseif ($post->status == 'public' && !$owner->hasBlocked($user) && ($post->deleted == 0)) {
                return view('post.view-post')->with('post', $post);
            }
            else {
                return redirect()->route('page404'); //not gonna show him this
            }
        }
        else {
            if (($post->status == 'public') && ($post->deleted == 0)) {
                return view('post.view-post')->with('post', $post);
            }
            else {
                return redirect()->route('page404'); //not gonna show him this
            }
        }
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

        return back()->with('error', $errors);
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

        $art_images = $this->entriesRepository->myart();

        return view('home.index', compact('posts', 'art_images'));
    }

    public function editPostView(Request $request, $post_id) : View {

        $user = $request->user();
        if(!$user){
            return view('login.login');
        }

        $post = Post::where('id', $post_id)->first();

        $allcategories = Category::query()
            ->pluck('title')
            ->toArray();

        return view('post.edit-post', ['post' => $post])->with('allcategories', $allcategories);
    }

    public function edit(Post $post, Request $request, ImageCreator $creator) : RedirectResponse {

        $this->authorize('update', $post);

        $user = $request->user();
        //all inputs
        $title = ($request->input('title') ?? '');
        $description = ($request->input('description') ?? '');
        $categories = json_decode($request->input('categories'), true);
        $post_status = $request->input('post_status');

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

        if ($post_status != 'public' && $post_status != 'private') {
            $post_status = 'private';
        }

        if (!empty($title) && !empty($description)) {

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageSubmit = $creator->createImage($image, $user->id);
            }

            if ($request->hasFile('image') && !empty($imageSubmit)) {
                
                $post = $this->entriesRepository->update($post, $imageSubmit, $title, $description, $final_categories, $post_status);

                if ($post !== false) {
                    return redirect()->route('post.view', ['post' => $post]);
                } else {
                    $errors[] = "entry couldnt be created";
                    return back()->with('error', $errors);
                }
            }
            elseif ($request->hasFile('image') && empty($imageSubmit)) {
                $errors[] = "something is wrong with your image, submit a different one";
                return back()->with('error', $errors);
            }
            else {

                $new_post = $this->entriesRepository->update($post, false, $title, $description, $final_categories, $post_status);

                if ($new_post !== false) {
                    return redirect()->route('post.view', ['post' => $new_post]);
                } else {
                    $errors[] = "entry couldnt be created";
                    return back()->with('error', $errors);
                }
            }
        }
        else {
            $errors[] = "not everything is filled";
        }

        return back()->with('error', $errors);
    }

    public function remove(Post $post) : RedirectResponse { //pseudo delete
        
        $this->authorize('update', $post);

        if(!$post){
            return back()->with('error', "couldnt delete post");
        }
        else{
            $post->update(['deleted' => 1]);
            return redirect()->route('browse');
        }
    }

    public function adminDelete(Post $post) : RedirectResponse {

        $this->authorize('delete', $post);

        Storage::disk('public')->delete($post->image_folder);
        $post->delete();

        return redirect()->route('browse');
    }

    public function reinstatePost(Post $post) : View {
        $this->authorize('delete', $post);

        $post->update(['deleted' => 0]);

        return view('post.view-post')->with('post', $post);
    }

    public function myLikedPosts(Request $request) : View | RedirectResponse{
        $user = $request->user();

        if (!$user) {
            return back()->with('error', 'error loading liked posts');
        }

        $posts = $this->entriesRepository->likedPosts($user);
        $title = 'My Liked Posts';

        return  view('search.only-posts', compact('posts', 'title'));       
    }

    public function myart() : View {
        $posts = $this->entriesRepository->myart();

        return view('search.only-posts')->with('posts', $posts);
    }

    public function myfeed(Request $request) : View {

        $user = $request->user();

        $posts = $this->entriesRepository->myfeed($user);
        $title = 'Posts from my followed accounts';

        return view('search.only-posts', compact('posts', 'title'));
    }
}
