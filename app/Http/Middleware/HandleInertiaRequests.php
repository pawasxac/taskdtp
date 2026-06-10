<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user()?->only([
                    'id',
                    'name',
                    'username',
                    'email',
                    'role',
                    'profile_picture',
                    'bio',
                    'phone_number',
                ]),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'notifications' => function () use ($request) {
                $authUser = $request->user();
                if (!$authUser) {
                    return [];
                }

                $notifications = collect();

                // 1. Direct Messages
                $recentDMs = \App\Models\DirectMessage::where('receiver_id', $authUser->id)
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();

                foreach ($recentDMs as $dm) {
                    $sender = \App\Models\User::find($dm->sender_id);
                    if (!$sender) continue;
                    $notifications->push([
                        'id'    => 'notif-dm-' . $dm->id,
                        'type'  => 'DM',
                        'title' => 'Pesan baru dari ' . $sender->name,
                        'body'  => str($dm->message)->limit(50),
                        'route' => '/dashboard?tab=dm&focus=' . $dm->sender_id,
                        'cta'   => 'Balas DM',
                    ]);
                }

                // 2. Global Chats Mentions
                $globalChatNotifs = \App\Models\GlobalChat::with('user')
                    ->where('user_id', '!=', $authUser->id)
                    ->where(function($q) use ($authUser) {
                        $q->whereHas('replyTo', function($rq) use ($authUser) {
                            $rq->where('user_id', $authUser->id);
                        })->orWhere('message', 'like', '%@' . $authUser->username . '%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();

                foreach ($globalChatNotifs as $chat) {
                    $notifications->push([
                        'id'    => 'notif-global-' . $chat->id,
                        'type'  => 'Lounge',
                        'title' => 'Seseorang menyebutmu di Lounge',
                        'body'  => $chat->user?->name . ': ' . str($chat->message)->limit(50),
                        'route' => '/dashboard',
                        'cta'   => 'Lihat Lounge',
                    ]);
                }

                // 3. Community Posts
                $joinedCommunityIds = \DB::table('community_members')
                    ->where('user_id', $authUser->id)
                    ->pluck('community_id');

                $recentCommunityPosts = \App\Models\CommunityPost::whereIn('community_id', $joinedCommunityIds)
                    ->where('user_id', '!=', $authUser->id)
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();

                foreach ($recentCommunityPosts as $post) {
                    $community = \App\Models\Komunitas::find($post->community_id);
                    $sender = \App\Models\User::find($post->user_id);
                    if (!$community || !$sender) continue;
                    $notifications->push([
                        'id'    => 'notif-forum-' . $post->id,
                        'type'  => 'Forum',
                        'title' => 'Obrolan di ' . $community->nama_komunitas,
                        'body'  => $sender->name . ': ' . str($post->content)->limit(50),
                        'route' => '/dashboard?tab=forum&focus=' . $post->community_id,
                        'cta'   => 'Ikut Nimbrung',
                    ]);
                }

                // 4. Dynamic Community Request Notifications
                if ($authUser->role === 'admin') {
                    $pendingKomunitas = \App\Models\Komunitas::where('status', 'pending')->latest()->get();
                    foreach ($pendingKomunitas as $kom) {
                        $notifications->push([
                            'id'    => 'notif-pending-komunitas-' . $kom->id,
                            'type'  => 'Admin',
                            'title' => 'Request Komunitas Baru',
                            'body'  => 'User "' . $kom->ketua . '" mengajukan komunitas baru: "' . $kom->nama_komunitas . '".',
                            'route' => '/admin/management?tab=communities',
                            'cta'   => 'Tinjau Pengajuan',
                        ]);
                    }
                } else {
                    $userPending = \App\Models\Komunitas::where('status', 'pending')
                        ->where('ketua', $authUser->name)
                        ->latest()
                        ->get();
                    foreach ($userPending as $kom) {
                        $notifications->push([
                            'id'    => 'notif-user-pending-' . $kom->id,
                            'type'  => 'Komunitas',
                            'title' => 'Pengajuan Tertunda',
                            'body'  => 'Pengajuan "' . $kom->nama_komunitas . '" sedang menunggu persetujuan admin.',
                            'route' => '/dashboard',
                            'cta'   => 'Pantau',
                        ]);
                    }

                    $userApproved = \App\Models\Komunitas::where('status', 'aktif')
                        ->where('created_at', '>=', now()->subDays(3))
                        ->whereHas('members', function ($q) use ($authUser) {
                            $q->where('user_id', $authUser->id)->where('role', 'leader');
                        })
                        ->latest()
                        ->get();
                    foreach ($userApproved as $kom) {
                        $notifications->push([
                            'id'    => 'notif-user-approved-' . $kom->id,
                            'type'  => 'Komunitas',
                            'title' => 'Pengajuan Disetujui!',
                            'body'  => 'Komunitas "' . $kom->nama_komunitas . '" telah disetujui admin dan kini aktif.',
                            'route' => '/dashboard?tab=forum&focus=' . $kom->id,
                            'cta'   => 'Buka Forum',
                        ]);
                    }
                }

                if ($notifications->isEmpty()) {
                    return [
                        [
                            'id'    => 'notif-3',
                            'type'  => 'Radar',
                            'title' => 'Spot rating tinggi lagi rame dicari.',
                            'body'  => 'Beberapa kedai dengan rating manis lagi naik daun. Cocok buat hunting sore ini.',
                            'route' => '/',
                            'cta'   => 'Lihat Spotlight',
                        ]
                    ];
                }

                return $notifications->values()->toArray();
            },
        ]);
    }
}
