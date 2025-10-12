<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Repositories\EntriesRepository;
use App\Repositories\UsersRepository;
use Illuminate\View\View;

class SiteMapController extends Controller
{
    public function index() : View {
        return view('xml.xml-index');       
    }
    public function xmlAllPosts(EntriesRepository $entriesRepository) : Response {

        $posts = $entriesRepository->allPublicPosts();

        $xml = view('xml.xml-posts', compact('posts'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function xmlUser(string $user_nickname, UsersRepository $usersRepository, EntriesRepository $entriesRepository) : Response {
        
        $user = $usersRepository->userXml($user_nickname);
        $posts = $entriesRepository->allPublicPostsByUser($user_nickname);
        //dd($user);

        $xml = view('xml.xml-user-posts', compact('user','posts'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

}
