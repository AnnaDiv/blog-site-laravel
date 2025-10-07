<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntriesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\View\Components\Categories;

Route::get('/', [EntriesController::class, 'browse'])->name('home');

Route::get('/search/users', [UsersController::class, 'search'])->name('search.users');
Route::get('/search/posts', [EntriesController::class, 'search'])->name('search.posts');
Route::get('/post/view/{post}', [EntriesController::class, 'view'])->name('post.view');

Route::get('/like/{post}', [LikesController::class, 'getLikes'])->name('likes.get');
Route::post('/like/toggle', [LikesController::class, 'toggleLike'])->name('likes.toggle');

Route::get('/comment/{post}', [CommentController::class, 'getComments'])->name('comments.get');
Route::post('/comment/add', [CommentController::class, 'addComment'])->name('comment.add');
Route::post('/comment/remove', [CommentController::class, 'removeComment'])->name('comment.remove');

Route::get('/profile/{user_nickname}', [ProfileController::class, 'public'])->name('profile.public');

Route::get('/account/help', [AccountController::class, 'index'])->name('account.help');

Route::get('/password/help', [AccountController::class, 'passwordHelp'])->name('password.help');
Route::post('/password/reset', [AccountController::class, 'passwordResetProduce'])->name('password.token');
Route::get('/password/reset/{token}', [AccountController::class, 'passwordReset'])->name('password.reset');
Route::post('/password/reset/submit', [AccountController::class, 'passwordResetSubmit'])->name('password.reset.submit');

Route::get('/404', [AdminController::class, 'page404'])->name('page404');

//etc views
Route::get('contact_us', function () {
    return view('etc.contact_us');
})->name('contact_us');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::get('/categories/search', [CategoriesController::class, 'search'])->name('categories.search');

Route::get('/category/{category_id}', [EntriesController::class, 'category'])->name('category');

Route::middleware('guest')->group(function () { // only someone who isnt logged in can visit them
    Route::get('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'loginAuth'])->name('login.auth');

    Route::get('/account/activate/{token}', [AccountController::class, 'activateAccount'])->name('account.activate');
});

Route::middleware('auth')->group(function () { // only someone who is logged in can visit this
    Route::get('/post/create', [EntriesController::class, 'createPostView'])->name('post.createView');
    Route::post('/post/create', [EntriesController::class, 'create'])->name('post.create');
    Route::post('/image/preview', [ImageController::class, 'preview'])->name('image.preview');

    Route::get('profile/private/{user_nickname}', [ProfileController::class, 'private'])->name('profile.private');

    Route::get('/post/edit/{post_id}', [EntriesController::class, 'editPostView'])->name('post.editView');
    Route::put('/post/edit/{post}', [EntriesController::class, 'edit'])->name('post.edit');
    Route::put('post/delete/{post}', [EntriesController::class, 'remove'])->name('post.remove');

    Route::get('/mylikes', [EntriesController::class, 'myLikedPosts'])->name('mylikes');

    Route::get('/follow', [FollowController::class, 'getFollows'])->name('follow.get');
    Route::post('/follow', [FollowController::class, 'toggleFollow'])->name('follow.toggle');

    Route::get('/followers', [FollowController::class, 'showFollowers'])->name('followers');
    Route::get('/following', [FollowController::class, 'showFollowing'])->name('following');

    Route::get('/block', [BlockController::class, 'getBlock'])->name('block.get');
    Route::post('/block', [BlockController::class, 'toggleBlock'])->name('block.toggle');

    Route::get('/notifications', [NotificationsController::class, 'showNotifications'])->name('notifications.get');
    Route::post('/notifications', [NotificationsController::class, 'markRead'])->name('notifications.mark');

    Route::get('/profile/edit/{profile}', [UsersController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit/{user}', [UsersController::class, 'update'])->name('profile.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function () { // only admin can access these routes
    Route::get('/admin/panel', [AdminController::class, 'panel'])->name('admin.panel');

    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/search', [AdminController::class, 'userSearch'])->name('admin.user.search');

    Route::get('profile/all/{user_nickname}', [ProfileController::class, 'all'])->name('profile.all');
    Route::get('profile/deleted/{user_nickname}', [ProfileController::class, 'deleted'])->name('profile.deleted');
    Route::delete('user/delete/{user_nickname}', [ProfileController::class, 'permDelete'])->name('admin.user.permDelete');
    Route::put('user/ban/{user_nickname}', [UsersController::class, 'ban'])->name('admin.user.ban');
    Route::put('user/activate/{user_nickname}', [UsersController::class, 'adminActivate'])->name('admin.user.activate');
    Route::put('/profile/edit/admin/{user_nickname}', [UsersController::class, 'editAdmin'])->name('profile.edit.admin');

    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/deleted/posts', [AdminController::class, 'deletedPosts'])->name('admin.deleted.posts');
    Route::get('/admin/deleted/posts/search', [AdminController::class, 'searchDeletedPosts'])->name('admin.deletedPost.search');

    Route::put('post/reinstate/{post}', [EntriesController::class, 'reinstatePost'])->name('post.reinstate');
    Route::delete('post/permadelete/{post}', [EntriesController::class, 'adminDelete'])->name('post.permaDelete');

    Route::get('/category/view/{category}', [CategoriesController::class, 'view'])->name('category.view');
    Route::put('/category/update/{category}', [CategoriesController::class, 'update'])->name('category.update');
    Route::delete('/category/delete/{category}', [CategoriesController::class, 'delete'])->name('category.delete');

    Route::get('/admin/categories/search', [CategoriesController::class, 'adminSearch'])->name('categories.search.admin');
});
