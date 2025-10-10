<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Repositories\EntriesRepository;

class SiteMapController extends Controller
{
    public function xmlAllPosts(EntriesRepository $entriesRepository) : Response {

        $posts = $entriesRepository->allPublicPosts();

        $xml = view('xml.xml-posts', compact('posts'))->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

}
