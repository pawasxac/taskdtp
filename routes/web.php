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
            'reviews.user:id,name,username,profile_picture',
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
            $query->where('harga_max', '<=', $price);
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

        return inertia('Welcome', [
            'coffeeShops' => $paginated,
            'filters' => $request->only(['search', 'kecamatan', 'price', 'rating']),
            'kecamatans' => Kecamatan::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'communities' => Komunitas::with([
                'posts' => fn ($q) => $q
                    ->latest()
                    ->with([
                        'user:id,name,username,profile_picture',
                        'comments.user:id,name,username,profile_picture',
                    ]),
                'members.user:id,name,username,profile_picture',
            ])
                ->orderBy('nama_komunitas')
                ->get(),
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
            $adminAccess  = $isAdmin($authUser);
            $memberAccess = $isUser($authUser);

            if (! $adminAccess && ! $memberAccess) {
                abort(403, 'Role akun tidak dikenali.');
            }

            $coffeeShops = CoffeeShop::with([
                'kecamatan',
                'reviews.user:id,name,username,profile_picture',
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
                });

            $users = User::query()
                ->whereNull('deleted_at')
                ->where('id', '!=', $authUser->id)
                ->get(['id', 'name', 'username', 'email', 'profile_picture']);

            $communities = Komunitas::with([
                'members.user:id,name,username,profile_picture',
                'posts' => fn ($query) => $query
                    ->latest()
                    ->with([
                        'user:id,name,username,profile_picture',
                        'comments.user:id,name,username,profile_picture',
                    ]),
            ])
                ->orderBy('nama_komunitas')
                ->get();

            $forums = $communities->map(function ($community) use ($avatarUrl, $fallbackCover) {
                $creator = optional($community->posts->first())->user;
                $fallbackUser = (object) [
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
                    'reply_count'     => $community->posts->sum(fn ($post) => $post->comments->count()),
                    'cover_image'     => $community->photo_url ?: $fallbackCover($community->nama_komunitas),
                    'domisili'        => $community->domisili,
                    'creator'         => [
                        'name'            => $forumOwner->name,
                        'username'        => $forumOwner->username,
                        'profile_picture' => $creator?->profile_picture ?? null,
                        'avatar_url'      => $avatarUrl($creator, $forumOwner->name),
                    ],
                    'tags' => collect([
                        $community->domisili,
                        'forum',
                        'ngopi',
                    ])->filter()->unique()->values(),
                    'replies' => $community->posts
                        ->flatMap(fn ($post) => $post->comments)
                        ->sortByDesc('created_at')
                        ->take(3)
                        ->values()
                        ->map(fn ($comment) => [
                            'id'      => $comment->id,
                            'comment' => $comment->comment,
                            'user'    => [
                                'name'            => $comment->user?->name ?: 'Anak Nongki',
                                'username'        => $comment->user?->username ?: 'anaknongki',
                                'profile_picture' => $comment->user?->profile_picture,
                                'avatar_url'      => $avatarUrl($comment->user, $comment->user?->name ?: 'Anak Nongki'),
                            ],
                        ]),
                ];
            })->values();

            if ($forums->isEmpty()) {
                $forums = collect($coffeeShops->take(3)->values()->map(function ($shop, $index) use ($authUser, $avatarUrl, $fallbackCover) {
                    return [
                        'id'              => 'fallback-' . $shop->id,
                        'community_slug'  => 'circle-' . $shop->id,
                        'title'           => 'Circle ' . $shop->nama,
                        'description'     => 'Tempat ngobrol santai soal beans favorit, kursi pojok, dan jam paling aman buat datang.',
                        'member_count'    => 14 + $index,
                        'reply_count'     => 4 + $index,
                        'cover_image'     => $shop->photo_url ?: $fallbackCover($shop->nama),
                        'domisili'        => $shop->district_name ?? $shop->daerah,
                        'creator'         => [
                            'name'            => $authUser->name,
                            'username'        => $authUser->username,
                            'profile_picture' => $authUser->profile_picture,
                            'avatar_url'      => $avatarUrl($authUser),
                        ],
                        'tags' => collect([$shop->district_name, 'kopi', 'skena'])->filter()->values(),
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

            $globalChat = collect();

            foreach ($coffeeShops->take(5) as $shop) {
                foreach ($shop->reviews->take(1) as $review) {
                    $globalChat->push([
                        'id'         => 'review-' . $review->id,
                        'user'       => [
                            'name'            => $review->user?->name ?: 'Anak Kopi',
                            'username'        => $review->user?->username ?: 'anakkopi',
                            'profile_picture' => $review->user?->profile_picture,
                            'avatar_url'      => $avatarUrl($review->user, $review->user?->name ?: 'Anak Kopi'),
                        ],
                        'area'       => $shop->district_name ?: $shop->daerah ?: 'Sidoarjo',
                        'text'       => $review->review ?: "Baru nyoba {$shop->nama}, tempatnya adem dan nggak bikin buru-buru pulang.",
                        'time'       => optional($review->created_at)->format('H:i') ?: now()->format('H:i'),
                        'tags'       => collect([$shop->district_name, 'review'])->filter()->values(),
                        'cafe_id'    => $shop->id,
                        'cafe_name'  => $shop->nama,
                    ]);
                }
            }

            if ($globalChat->isEmpty()) {
                $globalChat = collect([
                    [
                        'id'    => 'seed-chat-1',
                        'user'  => [
                            'name'            => $authUser->name,
                            'username'        => $authUser->username,
                            'profile_picture' => $authUser->profile_picture,
                            'avatar_url'      => $avatarUrl($authUser),
                        ],
                        'area'  => 'Global Lounge',
                        'text'  => 'Siapa yang punya rekomendasi spot buat nugas sambil cari cinnamon roll yang beneran niat?',
                        'time'  => now()->subMinutes(12)->format('H:i'),
                        'tags'  => ['lounge', 'nugas'],
                        'cafe_id'    => null,
                        'cafe_name'  => null,
                    ],
                ]);
            }

            $directMessages = $users->take(4)->values()->map(function ($contact, $index) use ($avatarUrl, $authUser) {
                return [
                    'id'           => $contact->id,
                    'time'         => now()->subMinutes(($index + 1) * 9)->format('H:i'),
                    'last_message' => $index % 2 === 0
                        ? 'Nanti sore jadi pindah nongkrong ke spot yang ada colokan panjang?'
                        : 'Aku nemu kopi susu yang nggak lebay manisnya, kamu harus coba.',
                    'user' => [
                        'name'            => $contact->name,
                        'username'        => $contact->username,
                        'profile_picture' => $contact->profile_picture,
                        'avatar_url'      => $avatarUrl($contact),
                    ],
                    'messages' => [
                        [
                            'id'   => 'dm-' . $contact->id . '-1',
                            'text' => 'Besok kalau sempat, kita review tempat baru yang deket kampus yuk.',
                            'user' => [
                                'name'            => $contact->name,
                                'username'        => $contact->username,
                                'profile_picture' => $contact->profile_picture,
                                'avatar_url'      => $avatarUrl($contact),
                            ],
                        ],
                        [
                            'id'   => 'dm-' . $contact->id . '-2',
                            'text' => 'Gas. Yang penting kopinya enak dan kursinya nggak bikin pinggang nyerah.',
                            'user' => [
                                'name'            => $authUser->name,
                                'username'        => $authUser->username,
                                'profile_picture' => $authUser->profile_picture,
                                'avatar_url'      => $avatarUrl($authUser),
                            ],
                        ],
                    ],
                ];
            });

            if ($directMessages->isEmpty()) {
                $directMessages = collect([
                    [
                        'id'           => 'fallback-dm',
                        'time'         => now()->format('H:i'),
                        'last_message' => 'Belum ada DM baru, tapi inbox tetap siap dipakai buat ngajak ngopi.',
                        'user' => [
                            'name'            => 'Teman Nongki',
                            'username'        => 'temannongki',
                            'profile_picture' => null,
                            'avatar_url'      => $avatarUrl(null, 'Teman Nongki'),
                        ],
                        'messages' => [
                            [
                                'id'   => 'fallback-dm-1',
                                'text' => 'Kalau nanti mau ngopi, tinggal lempar lokasi ya.',
                                'user' => [
                                    'name'            => 'Teman Nongki',
                                    'username'        => 'temannongki',
                                    'profile_picture' => null,
                                    'avatar_url'      => $avatarUrl(null, 'Teman Nongki'),
                                ],
                            ],
                        ],
                    ],
                ]);
            }

            /**
             * Notification items with deep-link routes so the React
             * popover can navigate to the right destination on click.
             */
            $notifications = collect([
                [
                    'id'    => 'notif-1',
                    'type'  => 'Forum',
                    'title' => 'Ada balasan baru di thread favoritmu.',
                    'body'  => 'Seseorang baru nimbrung di obrolan soal tempat ngopi yang aman buat laptop dan rapat mendadak.',
                    'route' => '/dashboard',
                    'cta'   => 'Buka Forum',
                ],
                [
                    'id'    => 'notif-2',
                    'type'  => 'DM',
                    'title' => 'Inbox kamu lagi gerak.',
                    'body'  => 'Ada ajakan nongkrong masuk. Tinggal pilih: gas sekarang atau pura-pura sibuk dulu.',
                    'route' => '/dashboard',
                    'cta'   => 'Cek DM',
                ],
                [
                    'id'    => 'notif-3',
                    'type'  => 'Radar',
                    'title' => 'Spot rating tinggi lagi rame dicari.',
                    'body'  => 'Beberapa kedai dengan rating manis lagi naik daun. Cocok buat hunting sore ini.',
                    'route' => '/',
                    'cta'   => 'Lihat Spotlight',
                ],
            ]);

            $tags = collect($coffeeShops)
                ->flatMap(fn ($shop) => [
                    $shop->district_name,
                    $shop->daerah,
                    'kopi',
                    'nugas',
                    'nongkrong',
                ])
                ->merge($communities->pluck('domisili'))
                ->filter()
                ->unique()
                ->take(10)
                ->values();

            // The dashboard props below are returned synchronously.
            return inertia('Dashboard', [
                'user'            => $authUser,
                'coffeeShops'     => $coffeeShops,
                'forums'          => $forums,
                'globalChat'      => $globalChat->values(),
                'directMessages'  => $directMessages->values(),
                'notifications'   => $notifications->values(),
                'tags'            => $tags,
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
            return inertia('Dashboard', [
                'focusThread' => $slug,
            ]);
        })->name('forum.show');

        /**
         * Notification deep-link target — closes the popover loop and
         * bounces the user back to the dashboard inbox / forum.
         */
        Route::get('/notifications/{id}', function (string $id) {
            return redirect()->route('dashboard', ['notif' => $id]);
        })->name('notifications.show');

        /**
         * Chat stream (SSE) — Server-Sent Event channel that streams
         * heartbeats and recent review rows so the global chat pane
         * feels alive even without a real WebSocket server.
         */
        Route::get('/chat/stream', function () {
            return response()->stream(function () {
                @ob_end_flush();
                @ob_implicit_flush(true);

                $i = 0;
                $start = time();
                $samples = [
                    'Barista di sini ngerti banget seleramu, anti-judge.',
                    'Spot baru buka pojok buat laptop, colokan aman.',
                    'Cinnamon roll-nya lembut, kopinya nggak lebay pahit.',
                    'Mau review bareng malem ini? Kursi pojok masih longgar.',
                ];

                while ($i < 6 && connection_aborted() === 0 && (time() - $start) < 25) {
                    echo "event: ping\n";
                    echo 'data: ' . json_encode([
                        'id'   => 'stream-' . $i,
                        'text' => $samples[$i % count($samples)],
                        'time' => now()->format('H:i:s'),
                    ]) . "\n\n";
                    $i++;
                    sleep(4);
                }
            }, 200, [
                'Content-Type'      => 'text/event-stream',
                'Cache-Control'     => 'no-cache, no-store, must-revalidate',
                'X-Accel-Buffering' => 'no',
            ]);
        })->name('chat.stream');

        /**
         * Profile update endpoint — accepts a multipart payload with
         * a file upload and updates the authenticated user's bio and
         * profile picture. The Navbar/Dashboard reads the new state
         * via the next Inertia visit.
         */
        Route::post('/profile/update', function (Request $request) {
            $user = $request->user();
            $data = $request->validate([
                'name'  => ['nullable', 'string', 'max:120'],
                'bio'   => ['nullable', 'string', 'max:500'],
                'profile_picture' => ['nullable', 'image', 'max:2048'],
            ]);

            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile_pictures'), $filename);
                $user->profile_picture = $filename;
            }

            if (isset($data['name'])) $user->name = $data['name'];
            if (isset($data['bio']))  $user->bio  = $data['bio'];
            $user->save();

            return back()->with('success', 'Profil berhasil diperbarui. Anak skena makin pede.');
        })->name('profile.update');

        /**
         * Community join stub — returns a flash message so the React
         * side can keep moving without an actual network roundtrip.
         */
        Route::post('/community/join', function (Request $request) {
            return redirect()->back()->with('success', 'Permintaan join komunitas sudah dikirim. Tinggal tunggu diajak ngopi bareng.');
        })->name('community.join');
    });

    /**
     * ADMIN ROUTES
     * --------------------------------------------------------------
     * The `role` column is a primitive string. Always use the strict
     * string comparison `auth()->user()->role === 'admin'`. Never
     * call `$user->role->name`.
     */
    Route::middleware(['auth'])->prefix('admin')->group(function () use ($districtName) {
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

            return inertia('Admin', [
                'user'         => auth()->user(),
                'coffeeShops'  => CoffeeShop::with('kecamatan')->get()->map(function ($shop) use ($districtName) {
                    $shop->district_name = $districtName(
                        $shop->getRelation('kecamatan'),
                        $shop->kecamatan ?: $shop->daerah
                    );
                    return $shop;
                }),
                'communities'  => Komunitas::all(),
                'users'        => User::all(),
            ]);
        })->name('admin.management');

        Route::get('/coffee-shops', [CoffeeShopController::class, 'index'])->name('coffee.index');
        Route::get('/coffee-shops/create', [CoffeeShopController::class, 'create'])->name('coffee.create');
        Route::post('/coffee-shops', [CoffeeShopController::class, 'store'])->name('coffee.store');
        Route::get('/coffee-shops/{id}/edit', [CoffeeShopController::class, 'edit'])->name('coffee.edit');
        Route::put('/coffee-shops/{id}', [CoffeeShopController::class, 'update'])->name('coffee.update');
        Route::delete('/coffee-shops/{id}', [CoffeeShopController::class, 'destroy'])->name('coffee.destroy');
    });
});

