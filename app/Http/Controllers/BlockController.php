<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\BlocksHelper;

use App\Models\User;

class BlockController extends Controller
{

    public function getBlock(Request $request) {
        
        $profileUser = $request->query('profileUser');
        $blockingUser = $request->query('blockingUser');

        if (!$profileUser || !$blockingUser) {
            return response()->json(['error' => 'Missing profile user or blockingUser nickname']);
        }

        $profileUser = User::where('nickname', $profileUser)->firstOrFail();
        $blockingUser = User::where('nickname', $blockingUser)->firstOrFail();

        $isBlocked = $profileUser->isBlockedBy($blockingUser);

        return response()->json([$isBlocked]);
    }

    public function toggleBlock(Request $request, BlocksHelper $blocksHelper) {

        $profileUser = $request->input('profileUser');
        $blockingUser = $request->input('blockingUser');

        if (!$profileUser || !$blockingUser) {
            return response()->json(['success' => false, 'error' => 'You are not logged in.']);
        }

        return response()->json($blocksHelper->toggleBlock($profileUser, $blockingUser));
    }

}
