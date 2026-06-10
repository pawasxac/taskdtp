<?php

use App\Http\Controllers\Admin\CoffeeShopController;
use App\Http\Controllers\AuthController;
use App\Models\CoffeeShop;
use App\Models\Kecamatan;
use App\Models\Komunitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Build a stable avatar URL for any user, falling back to ui-avatars.
 * Supports http(s) URLs, relative filenames, and the empty case.
 */
$avatarUrl = function (?User $user, ?string $fallback = null): string {
    $name = urlencode($fallback ?: ($user?->name ?: $user?->username ?: 'Ngopi User'));

    if ($user?->profile_picture) {
        if (str_starts_with($user->profile_picture, 'http')) {
            return $user->profile_picture;
        }

        return url('/uploads/profile_pictures/' . $user->profile_picture);
    }

    return "https://ui-avatars.com/api/?name={$name}&background=1A0F0A&color=FAF6F0&bold=true";
};

/**
 * Build a fallback cover image URL for communities and cafes.
 */
$fallbackCover = function (string $name): string {
    $label = urlencode($name);
    return "https://ui-avatars.com/api/?name={$label}&background=C19A6B&color=1A0F0A&size=1200&font-size=0.24&bold=true";
};

/**
 * Resolves a human-readable district name from any of the shapes
 * the database may legitimately return: object, array, raw string.
 */
$districtName = function ($relationValue, ?string $fallback = null): ?string {
    if (is_object($relationValue)) {
        return $relationValue->name ?? $relationValue->nama ?? $fallback;
    }

    if (is_array($relationValue)) {
        return $relationValue['name'] ?? $relationValue['nama'] ?? $fallback;
    }

    if (is_string($relationValue) && trim($relationValue) !== '') {
        return $relationValue;
    }

    return $fallback;
};

/**
 * CRITICAL: The `role` column is a primitive string column.
 * Always use strict string comparison to avoid Error 500 from
 * `$user->role->name` or similar object-property access.
 */
$isAdmin = fn (?User $user): bool => (bool) $user && $user->role === 'admin';
$isUser  = fn (?User $user): bool => (bool) $user && $user->role === 'user';

Route::middleware(['web'])->group(function () use ($avatarUrl, $fallbackCover, $districtName, $isAdmin, $isUser) {
    /**
     * PUBLIC LANDING PAGE
     * --------------------------------------------------------------
     * Authenticated users and admins have full liberty to visit `/`
     * without being trapped or redirected back to the dashboard.
     * The hero, filters, forum feed and reviews are always rendered
     * for both guests and members.
     */
    Route::get('/', function (Request $request) use ($districtName) {
        $query = CoffeeShop::with([
            'kecamatan',
            'reviews' => fn($q) => $q->latest()->limit(3)->with('user:id,name,username,profile_picture'),
        ])->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($kecamatan = $request->input('kecamatan')) {
            $query->where(function ($q) use ($kecamatan) {
                $q->whereHas('kecamatan', fn ($k) => $k->where('name', $kecamatan))
                  ->orWhere('daerah', $kecamatan);
            });
        }

        if ($price = $request->input('price')) {
            $query->where('harga_min', '<=', $price);
        }

        if ($rating = $request->input('rating')) {
            $query->where('rating', '>=', $rating);
        }

        $paginated = $query->orderByDesc('rating')->paginate(12)->withQueryString();
        
        $paginated->getCollection()->transform(function ($shop) use ($districtName) {
            $shop->district_name = $districtName(
                $shop->getRelation('kecamatan'),
                $shop->kecamatan ?: $shop->daerah
            );
            return $shop;
        });

        // Cache static data that doesn't change often
        $kecamatans = cache()->remember('kecamatans', 3600, fn () => Kecamatan::query()
            ->orderBy('name')
            ->get(['id', 'name']));
            
        $communities = cache()->remember('communities', 1800, fn () => Komunitas::with([
            'posts' => fn ($q) => $q
                ->latest()
                ->with([
                    'user:id,name,username,profile_picture,kecamatan_id', 'user.kecamatan:id,name',
                    'replyTo.user:id,name,username,profile_picture,kecamatan_id', 'replyTo.user.kecamatan:id,name',
                ]),
            'members.user:id,name,username,profile_picture,kecamatan_id', 'members.user.kecamatan:id,name',
        ])
            ->where('status', 'aktif')
            ->orderBy('nama_komunitas')
            ->get());

        return inertia('Welcome', [
            'coffeeShops' => $paginated,
            'filters' => $request->only(['search', 'kecamatan', 'price', 'rating']),
            'kecamatans' => $kecamatans,
            'communities' => $communities,
        ]);
    })->name('home');

    /**
     * GUEST AUTH ROUTES (login + register).
     * Block entry for users who are already logged in to avoid the
     * login-page trap that previously caused silent route freezes.
     */
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    });

    /**
     * GLOBAL LOGOUT — available to anyone so the Navbar can destroy
     * the session cleanly without routing freezes.
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * AUTHENTICATED DASHBOARD
     * --------------------------------------------------------------
     * No role-trap on `/`. Both 'admin' and 'user' accounts can enter.
     * Role-aware props are passed down to the React layer, which uses
     * primitive string comparison (`auth.user.role === 'admin'`).
     */
    Route::middleware(['auth'])->group(function () use ($avatarUrl, $fallbackCover, $districtName, $isAdmin, $isUser) {
        Route::get('/dashboard', function () use ($avatarUrl, $fallbackCover, $districtName, $isAdmin, $isUser) {
            $authUser     = auth()->user();
            $authUser->avatar_url = $avatarUrl($authUser);
            $adminAccess  = $isAdmin($authUser);
            $memberAccess = $isUser($authUser);

            if (! $adminAccess && ! $memberAccess) {
                abort(403, 'Role akun tidak dikenali.');
            }

            $tags = collect(['kopi', 'nugas', 'nongkrong', 'skena', 'roastery', 'manual brew', 'v60', 'espresso', 'latte', 'cold brew']);

            return inertia('Dashboard', [
                'user'            => $authUser->load('kecamatan'),
                'tags'            => $tags,
                
                'coffeeShops'     => fn () => cache()->remember('dashboard_coffee_shops', 3600, fn () => CoffeeShop::with([
                    'kecamatan',
                    'reviews' => fn($q) => $q->latest()->limit(3)->with(['user:id,name,username,profile_picture,kecamatan_id', 'user.kecamatan:id,name']),
                ])
                    ->where('is_active', true)
                    ->orderByDesc('rating')
                    ->get()
                    ->map(function ($shop) use ($districtName) {
                        $shop->district_name = $districtName(
                            $shop->getRelation('kecamatan'),
                            $shop->kecamatan ?: $shop->daerah
                        );
                        return $shop;
                    })),

                'forums'          => function () use ($avatarUrl, $fallbackCover, $authUser) {
                    $communities = Komunitas::with([
                        'members.user:id,name,username,profile_picture,kecamatan_id,last_seen_at', 'members.user.kecamatan:id,name',
                        'posts' => fn ($q) => $q
                            ->latest()
                            ->with([
                                'user:id,name,username,profile_picture,kecamatan_id,last_seen_at', 'user.kecamatan:id,name',
                                'replyTo.user:id,name,username,profile_picture,kecamatan_id,last_seen_at', 'replyTo.user.kecamatan:id,name',
                            ]),
                    ])
                        ->where('status', 'aktif')
                        ->orderBy('nama_komunitas')
                        ->get();

                    $forums = $communities->map(function ($community) use ($avatarUrl, $fallbackCover, $authUser) {
                        $leaderMember = $community->members->where('role', 'leader')->first();
                        $creator = $leaderMember ? $leaderMember->user : optional($community->posts->first())->user;
                        $fallbackUser = (object) [
                            'id'       => null,
                            'name'     => $community->ketua ?: 'Penjaga Tongkrongan',
                            'username' => strtolower(str_replace(' ', '', $community->ketua ?: 'penjaga')),
                            'profile_picture' => null,
                        ];

                        $forumOwner = $creator ?: $fallbackUser;

                        return [
                            'id'              => $community->id,
                            'community_slug'  => str()->slug($community->nama_komunitas),
                            'title'           => $community->nama_komunitas,
                            'description'     => $community->deskripsi ?: 'Tempat curhat soal seduhan, colokan, dan playlist sore.',
                            'member_count'    => $community->members->count() ?: ($community->jumlah_anggota ?? 0),
                            'is_member'       => $community->members->where('user_id', $authUser?->id)->isNotEmpty(),
                            'is_leader'       => $authUser && (
                                ($leaderMember && $leaderMember->user_id === $authUser->id) ||
                                ($creator && $creator->id === $authUser->id) ||
                                ($authUser->role === 'admin')
                            ),
                            'members'         => $community->members->map(fn ($m) => [
                                'id'         => $m->user?->id,
                                'name'       => $m->user?->name ?: 'Anggota Skena',
                                'username'   => $m->user?->username ?: 'anggotaskena',
                                'avatar_url' => $avatarUrl($m->user, $m->user?->name ?: 'Anggota Skena'),
                                'is_online'  => $m->user && $m->user->last_seen_at && (now()->timestamp - $m->user->last_seen_at->timestamp <= 120),
                                'role'       => $m->role ?: 'member',
                            ])->values(),
                            'reply_count'     => $community->posts->count(),
                            'cover_image'     => $community->photo_url ?: $fallbackCover($community->nama_komunitas),
                            'domisili'        => $community->domisili,
                            'creator'         => [
                                'id'              => $forumOwner->id ?? null,
                                'name'            => $forumOwner->name,
                                'username'        => $forumOwner->username,
                                'profile_picture' => $forumOwner->profile_picture ?? null,
                                'avatar_url'      => $avatarUrl($creator, $forumOwner->name),
                                'is_online'       => $creator && $creator->last_seen_at && (now()->timestamp - $creator->last_seen_at->timestamp <= 120),
                            ],
                            'tags' => collect([
                                $community->domisili,
                                'forum',
                                'ngopi',
                            ])->filter()->unique()->values(),
                            'replies' => $community->posts
                                ->sortByDesc('created_at')
                                ->filter(function ($post) use ($authUser) {
                                    $deletedBy = $post->deleted_by_users ?: [];
                                    return !in_array($authUser->id, $deletedBy);
                                })
                                ->take(50)
                                ->values()
                                ->map(fn ($post) => [
                                    'id'      => $post->id,
                                    'comment' => $post->deleted_for_everyone ? 'Pesan ini telah dihapus' : $post->content,
                                    'is_deleted_for_everyone' => (bool)$post->deleted_for_everyone,
                                    'read_by_users' => $post->read_by_users ?: [],
                                    'user'    => [
                                        'id'              => $post->user?->id,
                                        'name'            => $post->user?->name ?: 'Anak Nongki',
                                        'username'        => $post->user?->username ?: 'anaknongki',
                                        'profile_picture' => $post->user?->profile_picture,
                                        'avatar_url'      => $avatarUrl($post->user, $post->user?->name ?: 'Anak Nongki'),
                                        'is_online'       => $post->user && $post->user->last_seen_at && (now()->timestamp - $post->user->last_seen_at->timestamp <= 120),
                                    ],
                                    'reply_to' => $post->replyTo ? [
                                        'id'      => $post->replyTo->id,
                                        'comment' => $post->replyTo->deleted_for_everyone ? 'Pesan ini telah dihapus' : $post->replyTo->content,
                                        'is_deleted_for_everyone' => (bool)$post->replyTo->deleted_for_everyone,
                                        'user'    => [
                                            'name'            => $post->replyTo->user?->name ?: 'Anak Nongki',
                                            'username'        => $post->replyTo->user?->username ?: 'anaknongki',
                                            'profile_picture' => $post->replyTo->user?->profile_picture,
                                            'avatar_url'      => $avatarUrl($post->replyTo->user, $post->replyTo->user?->name ?: 'Anak Nongki'),
                                        ],
                                    ] : null,
                                ]),
                        ];
                    })->values();

                    if ($forums->isEmpty()) {
                        $coffeeShops = CoffeeShop::where('is_active', true)->orderByDesc('rating')->take(3)->get();
                        $forums = collect($coffeeShops->values()->map(function ($shop, $index) use ($authUser, $avatarUrl, $fallbackCover) {
                            return [
                                'id'              => 'fallback-' . $shop->id,
                                'community_slug'  => 'circle-' . $shop->id,
                                'title'           => 'Circle ' . $shop->nama,
                                'description'     => 'Tempat ngobrol santai soal beans favorit, kursi pojok, dan jam paling aman buat datang.',
                                'member_count'    => 14 + $index,
                                'reply_count'     => 4 + $index,
                                'cover_image'     => $shop->photo_url ?: $fallbackCover($shop->nama),
                                'domisili'        => $shop->daerah,
                                'creator'         => [
                                    'name'            => $authUser->name,
                                    'username'        => $authUser->username,
                                    'profile_picture' => $authUser->profile_picture,
                                    'avatar_url'      => $avatarUrl($authUser),
                                ],
                                'tags' => collect([$shop->daerah, 'kopi', 'skena'])->filter()->values(),
                                'replies' => [
                                    [
                                        'id'      => 'reply-' . $shop->id . '-1',
                                        'comment' => 'Spot ini enak buat ngilang bentar dari deadline yang kelewat cerewet.',
                                        'user'    => [
                                            'name'            => $authUser->name,
                                            'username'        => $authUser->username,
                                            'profile_picture' => $authUser->profile_picture,
                                            'avatar_url'      => $avatarUrl($authUser),
                                        ],
                                    ],
                                ],
                            ];
                        }));
                    }

                    return $forums;
                },

                'globalChat'      => fn () => \App\Models\GlobalChat::with(['user.kecamatan', 'replyTo.user.kecamatan'])
                    ->orderByDesc('created_at')
                    ->take(50)
                    ->get()
                    ->reverse()
                    ->map(function ($chat) use ($avatarUrl) {
                        return [
                            'id'         => 'chat-' . $chat->id,
                            'user'       => [
                                'id'              => $chat->user?->id,
                                'name'            => $chat->user?->name ?: 'Anonim',
                                'username'        => $chat->user?->username ?: 'anonim',
                                'profile_picture' => $chat->user?->profile_picture,
                                'avatar_url'      => $avatarUrl($chat->user, $chat->user?->name ?: 'Anonim'),
                                'instagram'       => $chat->user?->instagram,
                                'whatsapp'        => $chat->user?->whatsapp,
                                'discord'         => $chat->user?->discord,
                                'kecamatan'       => $chat->user?->kecamatan,
                            ],
                            'area'       => 'Global Lounge',
                            'text'       => $chat->message,
                            'time'       => $chat->created_at->format('H:i'),
                            'tags'       => ['lounge'],
                            'reply_to'   => $chat->replyTo ? [
                                'id'      => $chat->replyTo->id,
                                'text'    => $chat->replyTo->message,
                                'user'    => [
                                    'name'            => $chat->replyTo->user?->name ?: 'Anonim',
                                    'username'        => $chat->replyTo->user?->username ?: 'anonim',
                                    'profile_picture' => $chat->replyTo->user?->profile_picture,
                                    'avatar_url'      => $avatarUrl($chat->replyTo->user, $chat->replyTo->user?->name ?: 'Anonim'),
                                ],
                            ] : null,
                        ];
                    })->values(),

                'directMessages'  => function () use ($authUser, $avatarUrl) {
                    $realDms = \App\Models\DirectMessage::where(function($q) use ($authUser) {
                            $q->where('sender_id', $authUser->id)
                              ->where('deleted_for_sender', false);
                        })->orWhere(function($q) use ($authUser) {
                            $q->where('receiver_id', $authUser->id)
                              ->where('deleted_for_receiver', false);
                        })
                        ->with(['sender', 'receiver'])
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    $allCounts = \App\Models\DirectMessage::where('sender_id', $authUser->id)
                        ->orWhere('receiver_id', $authUser->id)
                        ->selectRaw('sender_id, receiver_id, COUNT(*) as total')
                        ->groupBy('sender_id', 'receiver_id')
                        ->get();

                    $countsMap = [];
                    foreach ($allCounts as $c) {
                        $otherId = $c->sender_id === $authUser->id ? $c->receiver_id : $c->sender_id;
                        $countsMap[$otherId] = ($countsMap[$otherId] ?? 0) + $c->total;
                    }

                    $dmGroups = [];
                    foreach ($realDms as $msg) {
                        $contact = $msg->sender_id === $authUser->id ? $msg->receiver : $msg->sender;
                        if (!$contact) continue;

                        if (!isset($dmGroups[$contact->id])) {
                            $dmGroups[$contact->id] = [
                                'id' => $contact->id,
                                'user' => [
                                    'id' => $contact->id,
                                    'name' => $contact->name,
                                    'username' => $contact->username,
                                    'profile_picture' => $contact->profile_picture,
                                    'avatar_url' => $avatarUrl($contact),
                                    'instagram' => $contact->instagram,
                                    'whatsapp' => $contact->whatsapp,
                                    'discord' => $contact->discord,
                                    'kecamatan' => $contact->kecamatan,
                                    'is_online' => $contact->last_seen_at && (now()->timestamp - $contact->last_seen_at->timestamp <= 120),
                                ],
                                'messages' => [],
                                'total_messages_count' => $countsMap[$contact->id] ?? 0,
                            ];
                        }

                        $msgSender = $msg->sender;
                        if (!$msgSender) continue;

                        $dmGroups[$contact->id]['messages'][] = [
                            'id' => 'dm-' . $msg->id,
                            'text' => $msg->deleted_for_everyone ? 'Pesan ini telah dihapus' : $msg->message,
                            'is_deleted_for_everyone' => (bool)$msg->deleted_for_everyone,
                            'read_at' => $msg->read_at ? $msg->read_at->toIso8601String() : null,
                            'user' => [
                                'id' => $msgSender->id,
                                'name' => $msgSender->name,
                                'username' => $msgSender->username,
                                'profile_picture' => $msgSender->profile_picture,
                                'avatar_url' => $avatarUrl($msgSender),
                            ],
                        ];
                        $dmGroups[$contact->id]['time'] = $msg->created_at ? $msg->created_at->format('H:i') : now()->format('H:i');
                        $dmGroups[$contact->id]['last_message'] = $msg->deleted_for_everyone ? 'Pesan ini telah dihapus' : $msg->message;
                    }
                    return collect(array_values($dmGroups))->sortByDesc('time')->values();
                },

                'notifications'   => function () use ($authUser, $avatarUrl) {
                    $notifications = collect();

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

                    // Dynamic Community Request Notifications
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
                        // Regular user pending requests
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

                        // Regular user approved (active) requests created in the last 3 days
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
                        $notifications = collect([
                            [
                                'id'    => 'notif-3',
                                'type'  => 'Radar',
                                'title' => 'Spot rating tinggi lagi rame dicari.',
                                'body'  => 'Beberapa kedai dengan rating manis lagi naik daun. Cocok buat hunting sore ini.',
                                'route' => '/',
                                'cta'   => 'Lihat Spotlight',
                            ],
                        ]);
                    }
                    return $notifications->values();
                },

                'kecamatans'      => fn () => \App\Models\Kecamatan::query()
                    ->orderBy('name')
                    ->get(['id', 'name']),
            ]);
        })->name('dashboard');

        /**
         * Forum thread index + detail (compact stubs that always
         * return non-empty payloads to keep the UI deterministic).
         */
        Route::get('/forum', function () {
            return redirect()->route('dashboard');
        })->name('forum.index');

        Route::get('/forum/{slug}', function (string $slug) {
            return redirect()->route('dashboard', ['tab' => 'forum', 'focus' => $slug]);
        })->name('forum.show');

        /**
         * Notification deep-link target — closes the popover loop and
         * bounces the user back to the dashboard inbox / forum.
         */
        Route::get('/notifications/{id}', function (string $id) {
            return redirect()->route('dashboard', ['notif' => $id]);
        })->name('notifications.show');

        /**
         * Global Chat submit endpoint.
         */
        Route::post('/chat/send', function (Request $request) {
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'reply_to_id' => 'nullable|exists:global_chats,id',
            ]);

            \App\Models\GlobalChat::create([
                'user_id' => auth()->id(),
                'message' => $validated['message'],
                'reply_to_id' => $validated['reply_to_id'] ?? null,
            ]);

            return back()->with('success', 'Pesan terkirim ke Global Lounge.');
        })->name('chat.send');

        /**
         * Profile update endpoint — accepts a multipart payload with
         * a file upload and updates the authenticated user's bio and
         * profile picture. The Navbar/Dashboard reads the new state
         * via the next Inertia visit.
         */
        Route::post('/profile/update', function (Request $request) {
            $user = $request->user();
            $data = $request->validate([
                'name'            => ['nullable', 'string', 'max:120'],
                'bio'             => ['nullable', 'string', 'max:500'],
                'instagram'       => ['nullable', 'string', 'max:60'],
                'whatsapp'        => ['nullable', 'string', 'max:20'],
                'discord'         => ['nullable', 'string', 'max:60'],
                'profile_picture' => ['nullable', 'image', 'max:2048'],
                'kecamatan_id'    => ['nullable', 'exists:kecamatans,id'],
            ]);

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile_pictures'), $filename);
                $user->profile_picture = $filename;
            }

            if (isset($data['name']))      $user->name      = $data['name'];
            if (isset($data['bio']))       $user->bio       = $data['bio'];
            if (array_key_exists('instagram', $data)) $user->instagram = $data['instagram'];
            if (array_key_exists('whatsapp',  $data)) $user->whatsapp  = $data['whatsapp'];
            if (array_key_exists('discord',   $data)) $user->discord   = $data['discord'];
            if (array_key_exists('kecamatan_id', $data)) $user->kecamatan_id = $data['kecamatan_id'];
            $user->save();

            return back()->with('success', 'Profil berhasil diperbarui. Anak skena makin pede.');
        })->name('profile.update');

        /**
         * Community join stub — returns a flash message so the React
         * side can keep moving without an actual network roundtrip.
         */
        Route::post('/komunitas/{id}/join', function (Request $request, $id) {
            $komunitas = Komunitas::findOrFail($id);
            try {
                \DB::table('community_members')->insertOrIgnore([
                    'community_id' => $komunitas->id,
                    'user_id' => auth()->id(),
                    'role' => 'member',
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                cache()->forget('communities');
            } catch (\Throwable $e) {}
            return redirect()->back()->with('success', 'Kamu sudah tergabung di komunitas ini!');
        })->name('komunitas.join');

        Route::post('/komunitas/store', function (Request $request) {
            $validated = $request->validate([
                'nama_komunitas' => 'required|string|max:255',
                'deskripsi'      => 'nullable|string',
                'domisili'       => 'nullable|string|max:255',
            ]);

            $isAdmin = auth()->user()->role === 'admin';

            $komunitas = Komunitas::create([
                'nama_komunitas' => $validated['nama_komunitas'],
                'deskripsi'      => $validated['deskripsi'] ?: '-',
                'domisili'       => $validated['domisili'] ?: '-',
                'ketua'          => auth()->user()->name,
                'tanggal_dibentuk' => now(),
                'status'         => $isAdmin ? 'aktif' : 'pending',
            ]);

            // Auto-join creator as leader member
            try {
                $komunitas->members()->create([
                    'user_id' => auth()->id(),
                    'role'    => 'leader',
                ]);
            } catch (\Throwable $e) {
                // members() pivot may not exist on all installs — ignore gracefully
                \Log::warning('komunitas.members create failed: ' . $e->getMessage());
            }

            // Clear the communities cache so the new community appears immediately on the dashboard
            cache()->forget('communities');

            $msg = $isAdmin ? 'Komunitas "' . $validated['nama_komunitas'] . '" berhasil dibuat!' : 'Pengajuan pembuatan komunitas "' . $validated['nama_komunitas'] . '" berhasil dikirim dan menunggu persetujuan admin.';
            return back()->with('success', $msg);
        })->name('komunitas.store');

        Route::post('/komunitas/{id}/post', function (Request $request, $id) {
            $validated = $request->validate([
                'content' => 'required|string|max:2000',
                'reply_to_id' => 'nullable|exists:community_posts,id',
            ]);

            $komunitas = Komunitas::findOrFail($id);

            // Try posts() relation first, fallback to direct table insert
            try {
                $komunitas->posts()->create([
                    'user_id' => auth()->id(),
                    'content' => $validated['content'],
                    'reply_to_id' => $validated['reply_to_id'] ?? null,
                ]);
            } catch (\Throwable $e) {
                // If posts() relation doesn't exist, use direct DB
                \DB::table('community_posts')->insert([
                    'community_id' => $komunitas->id,
                    'user_id'      => auth()->id(),
                    'content'      => $validated['content'],
                    'reply_to_id'  => $validated['reply_to_id'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            cache()->forget('communities');

            return back()->with('success', 'Pesan terkirim ke komunitas!');
        })->name('komunitas.post');

        Route::post('/komunitas/{id}/update', function (Request $request, $id) {
            $komunitas = Komunitas::findOrFail($id);
            
            $isLeader = $komunitas->members()->where('user_id', auth()->id())->where('role', 'leader')->exists() || auth()->user()->role === 'admin';
            abort_unless($isLeader, 403, 'Hanya leader komunitas yang bisa mengedit profil.');

            $data = $request->validate([
                'nama_komunitas' => 'required|string|max:150',
                'deskripsi'      => 'nullable|string|max:1000',
                'domisili'       => 'required|string|max:100',
                'photo'          => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'community_' . $komunitas->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                if (!file_exists(public_path('uploads/community_covers'))) {
                    mkdir(public_path('uploads/community_covers'), 0777, true);
                }
                $file->move(public_path('uploads/community_covers'), $filename);
                $komunitas->photo_url = url('/uploads/community_covers/' . $filename);
            }

            $komunitas->nama_komunitas = $data['nama_komunitas'];
            $komunitas->deskripsi      = $data['deskripsi'];
            $komunitas->domisili       = $data['domisili'];
            $komunitas->save();

            cache()->forget('communities');

            return back()->with('success', 'Profil komunitas berhasil diperbarui!');
        })->name('komunitas.update');

        Route::post('/komunitas/{id}/kick/{userId}', function (Request $request, $id, $userId) {
            $komunitas = Komunitas::findOrFail($id);
            
            $isLeader = $komunitas->members()->where('user_id', auth()->id())->where('role', 'leader')->exists() || auth()->user()->role === 'admin';
            abort_unless($isLeader, 403, 'Hanya leader komunitas yang bisa me-kick anggota.');
            
            abort_if(auth()->id() == $userId, 400, 'Anda tidak bisa me-kick diri sendiri.');

            $komunitas->members()->where('user_id', $userId)->delete();

            cache()->forget('communities');

            return back()->with('success', 'Anggota berhasil dikeluarkan dari komunitas.');
        })->name('komunitas.kick');

        Route::post('/komunitas/{id}/add-member', function (Request $request, $id) {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $komunitas = Komunitas::findOrFail($id);
            
            $isLeader = $komunitas->members()->where('user_id', auth()->id())->where('role', 'leader')->exists() || auth()->user()->role === 'admin';
            abort_unless($isLeader, 403, 'Hanya leader komunitas yang bisa menambahkan anggota.');

            $komunitas->members()->firstOrCreate(
                ['user_id' => $request->user_id],
                ['role' => 'member', 'joined_at' => now()]
            );

            cache()->forget('communities');

            return back()->with('success', 'Anggota berhasil ditambahkan ke komunitas.');
        })->name('komunitas.add-member');

        Route::get('/komunitas/{id}/search-users', function (Request $request, $id) use ($avatarUrl) {
            $komunitas = Komunitas::findOrFail($id);
            $query = $request->query('q');
            
            $existingUserIds = $komunitas->members()->pluck('user_id');

            $users = \App\Models\User::whereNotIn('id', $existingUserIds)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('username', 'like', "%{$query}%");
                })
                ->take(10)
                ->get(['id', 'name', 'username', 'profile_picture']);

            $resolvedUsers = $users->map(function($u) use ($avatarUrl) {
                $u->avatar_url = $avatarUrl($u);
                return $u;
            });

            return response()->json($resolvedUsers);
        })->name('komunitas.search-users');

        Route::post('/dm/send', function (Request $request) {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000',
            ]);

            $senderId = auth()->id();
            $receiverId = $request->receiver_id;

            $existingCount = \App\Models\DirectMessage::where(function($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
            })->orWhere(function($q) use ($senderId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
            })->count();

            abort_if($existingCount >= 10, 403, 'Udah limit brok, lanjut sosmed aja!');

            \App\Models\DirectMessage::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => $request->message,
            ]);

            return back()->with('success', 'Pesan terkirim.');
        })->name('dm.send');

        Route::post('/dm/read', function (Request $request) {
            $request->validate([
                'contact_id' => 'required|exists:users,id',
            ]);
            \App\Models\DirectMessage::where('sender_id', $request->contact_id)
                ->where('receiver_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            return back();
        })->name('dm.read');

        Route::post('/dm/{id}/delete', function (Request $request, $id) {
            $request->validate([
                'type' => 'required|in:me,everyone',
            ]);
            $msg = \App\Models\DirectMessage::findOrFail($id);
            if ($request->type === 'everyone') {
                if ($msg->sender_id !== auth()->id()) {
                    abort(403);
                }
                $msg->update(['deleted_for_everyone' => true]);
            } else {
                if ($msg->sender_id === auth()->id()) {
                    $msg->update(['deleted_for_sender' => true]);
                } else if ($msg->receiver_id === auth()->id()) {
                    $msg->update(['deleted_for_receiver' => true]);
                }
            }
            return back()->with('success', 'Pesan berhasil dihapus.');
        })->name('dm.delete');

        Route::post('/komunitas/{id}/read', function (Request $request, $id) {
            $posts = \App\Models\CommunityPost::where('community_id', $id)->get();
            $userId = auth()->id();
            foreach ($posts as $post) {
                $reads = $post->read_by_users ?: [];
                if (!in_array($userId, $reads)) {
                    $reads[] = $userId;
                    $post->update(['read_by_users' => $reads]);
                }
            }
            cache()->forget('communities');
            return back();
        })->name('komunitas.read');

        Route::post('/komunitas/post/{id}/delete', function (Request $request, $id) {
            $request->validate([
                'type' => 'required|in:me,everyone',
            ]);
            $post = \App\Models\CommunityPost::findOrFail($id);
            if ($request->type === 'everyone') {
                if ($post->user_id !== auth()->id()) {
                    abort(403);
                }
                $post->update(['deleted_for_everyone' => true]);
            } else {
                $deletedBy = $post->deleted_by_users ?: [];
                if (!in_array(auth()->id(), $deletedBy)) {
                    $deletedBy[] = auth()->id();
                    $post->update(['deleted_by_users' => $deletedBy]);
                }
            }
            cache()->forget('communities');
            return back()->with('success', 'Postingan berhasil dihapus.');
        })->name('komunitas.post.delete');

        Route::post('/coffee-shops/{id}/review', function (Request $request, $id) {
            $validated = $request->validate([
                'review' => 'required|string|max:1000',
            ]);

            \DB::table('coffee_shop_reviews')->insert([
                'coffee_shop_id' => $id,
                'user_id' => auth()->id(),
                'rating' => 5, // Default rating
                'review' => $validated['review'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            cache()->forget('dashboard_coffee_shops');

            return back()->with('success', 'Review berhasil ditambahkan.');
        })->name('coffee-shops.review');
    });

    /**
     * ADMIN ROUTES
     * --------------------------------------------------------------
     * The `role` column is a primitive string. Always use the strict
     * string comparison `auth()->user()->role === 'admin'`. Never
     * call `$user->role->name`.
     */
    Route::middleware(['auth'])->prefix('admin')->group(function () use ($districtName) {
        Route::get('/dashboard-lama', function () {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('dashboard');
            }

            $totalUsers = \App\Models\User::count();
            $communityCount = \App\Models\Komunitas::count();

            return inertia('Admin/OriginalDashboard', [
                'stats' => [
                    'coffee_shops_count' => \App\Models\CoffeeShop::count(),
                    'users_count' => $totalUsers,
                    'komunitas_count' => $communityCount,
                    'avg_rating' => number_format(\App\Models\CoffeeShop::avg('rating') ?? 0, 1),
                    'reviews_count' => \DB::table('coffee_shop_reviews')->count(),
                    'community_members_count' => \DB::table('community_members')->count(),
                    'community_posts_count' => \DB::table('community_posts')->count(),
                    'global_chats_count' => \DB::table('global_chats')->count(),
                    'engagement_rate' => $totalUsers > 0 ? round((\DB::table('community_posts')->count() / $totalUsers) * 100, 1) : 0,
                    'avg_per_community' => $communityCount > 0 ? round(\DB::table('community_members')->count() / $communityCount) : 0,
                ]
            ]);
        })->name('admin.dashboard-lama');

        Route::get('/gateway', function () {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('dashboard');
            }

            return inertia('Admin/Gateway');
        })->name('admin.gateway');

        Route::get('/management', function () use ($districtName) {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('dashboard');
            }

            $coffeeShops = CoffeeShop::with('kecamatan')->latest()->paginate(15);
            $coffeeShops->getCollection()->transform(function ($shop) use ($districtName) {
                $shop->district_name = $districtName(
                    $shop->getRelation('kecamatan'),
                    $shop->kecamatan ?: $shop->daerah
                );
                return $shop;
            });

            return inertia('Admin', [
                'user'         => auth()->user(),
                'coffeeShops'  => $coffeeShops,
                'communities'  => Komunitas::latest()->paginate(15),
                'users'        => User::latest()->paginate(15),
            ]);
        })->name('admin.management');

        Route::get('/coffee-shops', [CoffeeShopController::class, 'index'])->name('coffee.index');
        Route::get('/coffee-shops/create', [CoffeeShopController::class, 'create'])->name('coffee.create');
        Route::post('/coffee-shops', [CoffeeShopController::class, 'store'])->name('coffee.store');
        Route::get('/coffee-shops/{id}/edit', [CoffeeShopController::class, 'edit'])->name('coffee.edit');
        Route::put('/coffee-shops/{id}', [CoffeeShopController::class, 'update'])->name('coffee.update');
        Route::delete('/coffee-shops/{id}', [CoffeeShopController::class, 'destroy'])->name('coffee.destroy');

        // Restore missing CRUD routes for Blade views
        Route::get('/dashboard', function () { return redirect()->route('admin.dashboard-lama'); })->name('admin.dashboard');
        Route::resource('kecamatan', \App\Http\Controllers\Admin\KecamatanController::class);
        Route::resource('komunitas', \App\Http\Controllers\Admin\KomunitasController::class);
        Route::resource('community-posts', \App\Http\Controllers\Admin\CommunityPostController::class);
        Route::resource('coffee-shop-reviews', \App\Http\Controllers\Admin\CoffeeShopReviewController::class);
        Route::resource('community-members', \App\Http\Controllers\Admin\CommunityMemberController::class);
        Route::resource('global-chats', \App\Http\Controllers\Admin\GlobalChatController::class)->only(['index', 'destroy']);
        Route::resource('direct-messages', \App\Http\Controllers\Admin\DirectMessageController::class)->only(['index', 'destroy']);
        Route::resource('login-logs', \App\Http\Controllers\Admin\LoginLogController::class)->only(['index', 'destroy']);
        Route::resource('conversations', \App\Http\Controllers\Admin\ConversationController::class)->only(['index', 'destroy']);
        Route::resource('notifications', \App\Http\Controllers\Admin\NotificationController::class)->only(['index', 'destroy']);
        
        // User management routes
        Route::get('/users/create', [CoffeeShopController::class, 'createUser'])->name('admin.user.create');
        Route::post('/users', [CoffeeShopController::class, 'storeUser'])->name('admin.user.store');
        Route::get('/users/{id}/edit', [CoffeeShopController::class, 'editUser'])->name('admin.user.edit');
        Route::put('/users/{id}', [CoffeeShopController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/users/{id}', [CoffeeShopController::class, 'destroyUser'])->name('admin.user.destroy');

        Route::get('/login-monitor', [CoffeeShopController::class, 'loginMonitor'])->name('admin.login.monitor');

        Route::post('/komunitas/{id}/approve', function ($id) {
            $komunitas = Komunitas::findOrFail($id);
            $komunitas->status = 'aktif';
            $komunitas->save();

            cache()->forget('communities');

            return back()->with('success', 'Komunitas "' . $komunitas->nama_komunitas . '" berhasil disetujui!');
        })->name('admin.komunitas.approve');

        Route::post('/komunitas/{id}/reject', function ($id) {
            $komunitas = Komunitas::findOrFail($id);
            $komunitas->delete();

            cache()->forget('communities');

            return back()->with('success', 'Pengajuan komunitas berhasil ditolak.');
        })->name('admin.komunitas.reject');
    });
});

