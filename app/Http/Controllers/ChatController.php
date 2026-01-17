<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GetStream\StreamChat\Client as StreamChat;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $streamClient;

    public function __construct()
    {
        $this->streamClient = new StreamChat(
            env('STREAM_API_KEY'),
            env('STREAM_API_SECRET')
        );
    }

    public function index()
    {
        /** @var User */
        $user = Auth::user();

        $this->streamClient->upsertUser($user->getStreamUserData());

        $streamToken = $user->getStreamToken();

        return view('panels.chat', [
            'streamApiKey' => env('STREAM_API_KEY'),
            'streamToken' => $streamToken,
            'userId' => (string)$user->id,
            'userName' => $user->name,
        ]);
    }

    public function createChannel(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'id' => 'required|string',
            'name' => 'required|string',
            'members' => 'required|array',
        ]);

        try {
            $channel = $this->streamClient->channel(
                $request->type,
                $request->id,
                [
                    'name' => $request->name,
                    'created_by' => ['id' => (string)Auth::id()],
                ]
            );

            // Convert member IDs to strings
            $members = array_map(fn($id) => ['user_id' => (string)$id], $request->members);

            $channel->create((string)Auth::id());
            $channel->addMembers($members);

            return response()->json([
                'success' => true,
                'channel' => [
                    'id' => $channel->id,
                    'type' => $request->type,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers()
    {
        $users = User::where('id', '!=', Auth::id())
            ->select(['id', 'first_name', 'last_name', 'photo_url', 'role'])
            ->get()
            ->map(function ($user) {

                $middle = $user->middle_initial ? ' ' . $user->middle_initial . '.' : '';
                $fullName = $user->first_name . $middle . ' ' . $user->last_name;

                return [
                    'id' => $user->id,
                    'name' => $fullName,
                    'photo_url' => $user->photo_url,
                    'role' => $user->role
                ];
            });

        return response()->json($users);
    }

    public function registerUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
        ]);

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            
            $streamUsers = [];
            foreach ($users as $user) {
                $streamUsers[] = $user->getStreamUserData();
            }

            // Upsert users in Stream (server-side)
            $this->streamClient->upsertUsers($streamUsers);

            return response()->json([
                'success' => true,
                'message' => 'Users registered successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
