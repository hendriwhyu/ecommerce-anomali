<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\MemberCollection;
use App\Models\Members;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberController extends BaseController
{
    public function showMembers(){
        try {
            $members = Members::with('teams')
                ->get();

            $data = MemberCollection::collection($members);

            return $this->sendResponse($data, 'Members fetched successfully', 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return $this->sendError('Something went wrong', code: 500);
        }

    }

    public function showMemberById(Members $member){
        try {
            if(!$member){
                return $this->sendError('Member not found', code: 404);
            }

            $memberByTeams = $member->with('teams')->first();


            $data = new MemberCollection($memberByTeams);

            return $this->sendResponse($data, 'Member fetched successfully', 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return $this->sendError('Something went wrong', code: 500);
        }
    }
}
