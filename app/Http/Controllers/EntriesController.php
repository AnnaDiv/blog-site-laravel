<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\EntriesRepository;

class EntriesController extends Controller
{
    public function __construct(protected EntriesRepository $entriesRepository) {}

    public function browse(Request $request): View
    {
        $perPage = 15;
        if ($request->user()) {
            $user = $request->user();
            $nickname = $user->nickname;
            $excludedUsers = $this->entriesRepository->excludedUsers($nickname);
        } else {
            $excludedUsers = [];
        }

        $entries = $this->entriesRepository->browse($perPage, $excludedUsers);

        //$art_images = $this->entriesRepository->artBanner();

        return view('home.index', ['entries' => $entries]);
    }
}
