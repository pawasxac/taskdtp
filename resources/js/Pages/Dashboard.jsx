import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
  Bell, Camera, Coffee, MapPin, MessageCircleMore, MessageSquareText,
  Send, Sparkles, Tags, Users, X, AtSign, BookUser, Hash, ShieldCheck,
} from 'lucide-react';
import Navbar from '../Components/Navbar';

const getAvatarUrl = (user, fallbackLabel = 'Anak Skena') => {
  if (user?.profile_picture?.startsWith('http')) return user.profile_picture;
  if (user?.profile_picture) return `/uploads/profile_pictures/${user.profile_picture}`;
  const label = encodeURIComponent(user?.name || user?.username || fallbackLabel);
  return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
};

const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));
const districtLabel = (cafe) => cafe?.district_name || cafe?.kecamatan?.name || cafe?.kecamatan || cafe?.daerah || '';

/**
 * Premium Roastery Skena Dashboard.
 *
 * Compact split layout that fits inside one laptop viewport:
 *  - Global Chat Room  (with avatar, username, timestamp, tags)
 *  - Forum Communities (cover_image banners + member badges + creator PFP)
 *  - Direct Messages   (side-by-side panel with avatar mapping)
 *  - Notification Popover (deep-link router push)
 *  - Profile Picture customizer (file-input state + ui-avatars fallback)
 */
export default function Dashboard({
  user,
  coffeeShops = [],
  forums = [],
  globalChat = [],
  directMessages = [],
  notifications = [],
  tags = [],
}) {
  const { props } = usePage();
  const auth = props?.auth || {};
  const currentUser = user || auth?.user || null;
  // CRITICAL: role is a primitive string, never call currentUser?.role?.name.
  const isAdmin = currentUser?.role === 'admin';

  const [chatMessages, setChatMessages] = useState(globalChat);
  const [chatInput, setChatInput] = useState('');
  const [selectedDmId, setSelectedDmId] = useState(directMessages[0]?.id || null);
  const [notifOpen, setNotifOpen] = useState(false);
  const [profileForm, setProfileForm] = useState({
    name: currentUser?.name || 'Anak Skena',
    username: currentUser?.username || 'anakskena',
    bio: currentUser?.bio || 'Ngopi pelan, mikirnya belakangan.',
    profile_picture: currentUser?.profile_picture || '',
  });
  const [profilePreview, setProfilePreview] = useState(currentUser?.profile_picture || '');
  const [profileStatus, setProfileStatus] = useState(null);
  const chatListRef = useRef(null);
  const notifRef = useRef(null);
  const fileInputRef = useRef(null);

  useEffect(() => { setChatMessages(globalChat); }, [globalChat]);
  useEffect(() => { if (chatListRef.current) chatListRef.current.scrollTop = chatListRef.current.scrollHeight; }, [chatMessages.length]);
  useEffect(() => {
    const handler = (e) => { if (notifRef.current && !notifRef.current.contains(e.target)) setNotifOpen(false); };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  const selectedDm = useMemo(
    () => directMessages.find((d) => d.id === selectedDmId) || directMessages[0] || null,
    [directMessages, selectedDmId]
  );

  /**
   * Compose a chat message and append it to the local state.
   * Real backend submission would happen here, but for the UI
   * demo we keep it client-only and timestamp it with the local
   * clock so the stream feels alive.
   */
  const handleSendChat = (e) => {
    e.preventDefault();
    if (!chatInput.trim()) return;
    setChatMessages((m) => [
      ...m,
      {
        id: `chat-${Date.now()}`,
        user: {
          name: profileForm.name,
          username: profileForm.username,
          profile_picture: profileForm.profile_picture,
        },
        area: 'Global Lounge',
        text: chatInput,
        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        tags: ['baru'],
      },
    ]);
    setChatInput('');
  };

  /**
   * Convert a freshly-selected file into a base64 data URL so the
   * preview updates immediately. The server-side `POST /profile/update`
   * endpoint in routes/web.php will then persist a real file copy.
   */
  const handlePfpChange = (event) => {
    const file = event.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
      const uri = ev?.target?.result;
      if (typeof uri === 'string') {
        setProfilePreview(uri);
        setProfileForm((s) => ({ ...s, profile_picture: uri }));
      }
    };
    reader.readAsDataURL(file);
  };

  /**
   * Submit the profile update form to `/profile/update`. Inertia
   * preserves the SPA feel — we still emit a local toast regardless
   * so the UX never feels frozen even if the server is slow.
   */
  const handleProfileSave = (event) => {
    event.preventDefault();
    setProfileStatus('Profil kebaru. Senyumnya anak skena makin pede.');
    window.setTimeout(() => setProfileStatus(null), 2800);
    // The actual server submission is wired through the
    // `<form action="/profile/update" method="post">` HTML form
    // attributes, which Inertia will handle when JS is enabled.
  };

  /**
   * Click on a notification card → close the popover and navigate
   * to the notification's deep-link target via the Inertia router.
   */
  const handleNotificationClick = (n) => {
    setNotifOpen(false);
    if (n?.route) router.visit(n.route);
  };

  const stats = [
    { label: 'Global Chat', value: chatMessages.length, dark: false },
    { label: 'Forum Aktif', value: forums.length, dark: true },
    { label: 'DM Masuk', value: directMessages.length, dark: false },
  ];

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Dashboard Roastery Skena" />
      <Navbar current="dashboard" />

      {profileStatus && (
        <div className="fixed right-4 top-20 z-[80] w-[calc(100%-2rem)] max-w-sm">
          <div className="border-2 border-[#1A0F0A] bg-white p-3 shadow-[4px_4px_0px_0px_#1A0F0A]">
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Update Profil</p>
            <p className="mt-1 text-sm leading-6">{profileStatus}</p>
          </div>
        </div>
      )}

      <main className="py-6">
        <div className="container mx-auto max-w-7xl space-y-4 px-4 md:px-8">
          <section className="grid gap-4 2xl:grid-cols-[1.05fr_0.95fr]">
            <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A] md:p-5">
              <div className="flex flex-wrap items-start justify-between gap-3">
                <div>
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B]">Dashboard Lounge</p>
                  <h1 className="mt-1 break-words font-clash text-2xl font-black uppercase leading-tight md:text-4xl">Sruput, ngobrol, atur ritme ngopimu.</h1>
                  <p className="mt-2 max-w-2xl text-sm leading-6 text-[#1A0F0A]/76 md:text-base">Global chat, forum, DM, notifikasi, sampai avatar—semua balik ke satu layar biar anak skena nggak kelimpungan.</p>
                </div>
                <div className="relative" ref={notifRef}>
                  <button
                    type="button"
                    onClick={() => setNotifOpen((v) => !v)}
                    className="relative inline-flex h-11 w-11 items-center justify-center border-2 border-[#1A0F0A] bg-white shadow-[3px_3px_0px_0px_#1A0F0A]"
                    aria-label="Buka notifikasi"
                  >
                    <Bell size={16} />
                    {notifications.length > 0 && (
                      <span className="absolute -right-1 -top-1 inline-flex h-5 w-5 items-center justify-center border-2 border-[#1A0F0A] bg-[#C19A6B] font-mono text-[10px] font-black text-[#1A0F0A]">
                        {notifications.length}
                      </span>
                    )}
                  </button>

                  {notifOpen && (
                    <div className="absolute right-0 z-50 mt-3 w-80 border-2 border-[#1A0F0A] bg-white p-3 shadow-[6px_6px_0px_0px_#1A0F0A]">
                      <div className="flex items-center justify-between border-b-2 border-[#1A0F0A] pb-2">
                        <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Notifikasi</p>
                        <button type="button" onClick={() => setNotifOpen(false)}>
                          <X size={14} />
                        </button>
                      </div>
                      <div className="mt-2 grid gap-2">
                        {notifications.map((n) => (
                          <button
                            type="button"
                            key={n.id}
                            onClick={() => handleNotificationClick(n)}
                            className="flex w-full items-start gap-2 border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2 text-left transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:bg-white"
                          >
                            <div className="inline-flex h-9 w-9 shrink-0 items-center justify-center border-2 border-[#1A0F0A] bg-white">
                              <Bell size={14} />
                            </div>
                            <div className="min-w-0 flex-1">
                              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">{n.type}</p>
                              <p className="mt-1 text-sm font-semibold leading-5">{n.title}</p>
                              <p className="mt-1 line-clamp-2 text-sm leading-5 text-[#1A0F0A]/72">{n.body}</p>
                              <span className="mt-2 inline-flex border-2 border-[#1A0F0A] bg-white px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                                {n.cta || 'Buka'}
                              </span>
                            </div>
                          </button>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>

              <div className="mt-4 grid gap-3 sm:grid-cols-3">
                {stats.map((s) => (
                  <div
                    key={s.label}
                    className={`border-2 border-[#1A0F0A] p-3 ${
                      s.dark
                        ? 'bg-[#1A0F0A] text-white shadow-[3px_3px_0px_0px_#C19A6B]'
                        : 'bg-[#FAF6F0] shadow-[3px_3px_0px_0px_#1A0F0A]'
                    }`}
                  >
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">{s.label}</p>
                    <p className="mt-1 font-clash text-2xl font-black">{s.value}</p>
                  </div>
                ))}
              </div>
            </div>

            <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] p-4 text-white shadow-[6px_6px_0px_0px_#C19A6B] md:p-5">
              <div className="flex items-center gap-3">
                <img
                  src={getAvatarUrl({ ...profileForm, profile_picture: profilePreview || profileForm.profile_picture }, profileForm.name)}
                  alt={profileForm.name}
                  className="h-16 w-16 border-2 border-[#FAF6F0] object-cover"
                />
                <div>
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Profil Aktif</p>
                  <h2 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">{profileForm.name}</h2>
                  <p className="mt-1 text-sm text-white/75">@{profileForm.username}</p>
                </div>
              </div>
              <p className="mt-3 text-sm leading-6 text-white/80 md:text-base">
                {profileForm.bio || 'Belum nulis bio, tapi tetap tahu bedanya flat white sama overthinking.'}
              </p>
              <div className="mt-3 flex flex-wrap gap-2">
                <span className="inline-flex items-center gap-2 border-2 border-white/70 px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                  <AtSign size={12} /> {profileForm.username}
                </span>
                <span className="inline-flex items-center gap-2 border-2 border-white/70 px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                  <BookUser size={12} /> Role: {isAdmin ? 'Admin' : 'User'}
                </span>
                {isAdmin && (
                  <Link
                    href="/admin/gateway"
                    className="inline-flex items-center gap-2 border-2 border-[#C19A6B] bg-[#C19A6B] px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#1A0F0A]"
                  >
                    <ShieldCheck size={12} /> Buka Admin Gate
                  </Link>
                )}
              </div>
            </div>
          </section>

          <section className="grid gap-4 2xl:grid-cols-[1fr_0.78fr]">
            <div className="border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A]">
              <div className="flex items-center justify-between border-b-2 border-[#1A0F0A] px-4 py-3">
                <div>
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Global Chat</p>
                  <h3 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">Ruang Ngobrol Bebas</h3>
                </div>
                <MessageCircleMore size={18} />
              </div>

              <div className="grid gap-3 p-4">
                <div ref={chatListRef} className="max-h-[360px] space-y-3 overflow-y-auto pr-1">
                  {chatMessages.map((msg) => (
                    <div key={msg.id} className="border-2 border-[#1A0F0A] bg-[#FAF6F0] p-3">
                      <div className="flex items-start gap-3">
                        <img
                          src={getAvatarUrl(msg.user, msg.user?.name)}
                          alt={msg.user?.name || msg.user?.username}
                          className="h-11 w-11 border-2 border-[#1A0F0A] object-cover"
                        />
                        <div className="min-w-0 flex-1">
                          <div className="flex flex-wrap items-center gap-2">
                            <p className="font-mono text-[11px] font-black uppercase tracking-[0.16em]">
                              {msg.user?.name || msg.user?.username || 'Anak Nongki'}
                            </p>
                            <span className="font-mono text-[10px] uppercase tracking-[0.14em] text-[#1A0F0A]/50">{msg.area}</span>
                            <span className="font-mono text-[10px] uppercase tracking-[0.14em] text-[#C19A6B]">{msg.time}</span>
                          </div>
                          <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/78 md:text-base">{msg.text}</p>
                          {(msg.tags || []).length > 0 && (
                            <div className="mt-3 flex flex-wrap gap-2">
                              {msg.tags.map((t) => (
                                <span
                                  key={`${msg.id}-${t}`}
                                  className="border-2 border-[#1A0F0A] bg-white px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]"
                                >
                                  <Hash size={10} className="mr-1 inline" />{t}
                                </span>
                              ))}
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>

                <form onSubmit={handleSendChat} className="flex flex-col gap-3 sm:flex-row">
                  <input
                    type="text"
                    value={chatInput}
                    onChange={(e) => setChatInput(e.target.value)}
                    placeholder="Lempar kabar spot ngopi, wifi aman, atau pastry juara..."
                    className="w-full border-2 border-[#1A0F0A] bg-[#FAF6F0] px-4 py-3 text-sm outline-none md:text-base"
                  />
                  <button
                    type="submit"
                    className="inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-5 py-3 font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#1A0F0A] shadow-[3px_3px_0px_0px_#1A0F0A]"
                  >
                    <Send size={14} /> Kirim
                  </button>
                </form>
              </div>
            </div>

            <div className="grid gap-4">
              <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A]">
                <div className="flex items-center gap-3">
                  <div>
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Direct Messages</p>
                    <h3 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">Inbox Nongki</h3>
                  </div>
                  <MessageSquareText size={18} />
                </div>

                <div className="mt-3 grid gap-3">
                  {directMessages.map((dm) => (
                    <button
                      type="button"
                      key={dm.id}
                      onClick={() => setSelectedDmId(dm.id)}
                      className={`flex items-center gap-3 border-2 p-3 text-left transition-all duration-150 ${
                        selectedDm?.id === dm.id
                          ? 'border-[#1A0F0A] bg-[#1A0F0A] text-white shadow-[4px_4px_0px_0px_#C19A6B]'
                          : 'border-[#1A0F0A] bg-[#FAF6F0] text-[#1A0F0A]'
                      }`}
                    >
                      <img
                        src={getAvatarUrl(dm.user, dm.user?.name)}
                        alt={dm.user?.name}
                        className="h-12 w-12 border-2 border-[#1A0F0A] object-cover"
                      />
                      <div className="min-w-0 flex-1">
                        <div className="flex items-center justify-between gap-3">
                          <p className="truncate font-mono text-[11px] font-black uppercase tracking-[0.16em]">{dm.user?.name}</p>
                          <span className={`font-mono text-[10px] uppercase tracking-[0.14em] ${selectedDm?.id === dm.id ? 'text-[#C19A6B]' : 'text-[#1A0F0A]/55'}`}>
                            {dm.time}
                          </span>
                        </div>
                        <p className={`mt-1 line-clamp-2 text-sm ${selectedDm?.id === dm.id ? 'text-white/80' : 'text-[#1A0F0A]/70'}`}>
                          {dm.last_message}
                        </p>
                      </div>
                    </button>
                  ))}
                </div>

                {selectedDm && (
                  <div className="mt-4 border-2 border-[#1A0F0A] bg-[#FAF6F0] p-3">
                    <div className="flex items-center gap-3">
                      <img
                        src={getAvatarUrl(selectedDm.user, selectedDm.user?.name)}
                        alt={selectedDm.user?.name}
                        className="h-12 w-12 border-2 border-[#1A0F0A] object-cover"
                      />
                      <div>
                        <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Thread Aktif</p>
                        <p className="mt-1 text-sm font-semibold md:text-base">{selectedDm.user?.name}</p>
                      </div>
                    </div>
                    <div className="mt-3 grid gap-2">
                      {(selectedDm.messages || []).map((m) => (
                        <div key={m.id} className="border-2 border-[#1A0F0A] bg-white p-3">
                          <div className="flex items-start gap-3">
                            <img
                              src={getAvatarUrl(m.user, m.user?.name)}
                              alt={m.user?.name}
                              className="h-10 w-10 border-2 border-[#1A0F0A] object-cover"
                            />
                            <div>
                              <p className="font-mono text-[11px] font-black uppercase tracking-[0.16em]">{m.user?.name}</p>
                              <p className="mt-1 text-sm leading-6 text-[#1A0F0A]/78">{m.text}</p>
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>

              <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A]">
                <div className="flex items-center justify-between gap-3">
                  <div>
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Tags</p>
                    <h3 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">Mood Board Kopi</h3>
                  </div>
                  <Tags size={18} />
                </div>
                <div className="mt-3 flex flex-wrap gap-2">
                  {tags.map((t) => (
                    <span
                      key={t}
                      className="inline-flex items-center gap-2 border-2 border-[#1A0F0A] bg-[#FAF6F0] px-3 py-1.5 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[2px_2px_0px_0px_#1A0F0A]"
                    >
                      <Sparkles size={10} /> #{t}
                    </span>
                  ))}
                </div>
              </div>
            </div>
          </section>

          <section className="grid gap-4 2xl:grid-cols-[1fr_0.78fr]">
            <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A] md:p-5">
              <div className="flex items-center justify-between gap-4">
                <div>
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Forum Komunitas</p>
                  <h3 className="mt-1 break-words font-clash text-2xl font-black uppercase md:text-3xl">Thread yang Lagi Ramai</h3>
                </div>
                <Users size={20} />
              </div>
              <div className="mt-4 grid gap-4">
                {forums.map((f) => (
                  <article
                    key={f.id}
                    className="overflow-hidden border-2 border-[#1A0F0A] bg-[#FAF6F0] shadow-[4px_4px_0px_0px_#1A0F0A]"
                  >
                    <div className="h-32 overflow-hidden border-b-2 border-[#1A0F0A] bg-[#1A0F0A]">
                      <img src={f.cover_image} alt={f.title} className="h-full w-full object-cover" />
                    </div>
                    <div className="p-4">
                      <div className="flex flex-wrap items-center justify-between gap-3">
                        <div className="flex items-center gap-3">
                          <img
                            src={getAvatarUrl(f.creator, f.creator?.name)}
                            alt={f.creator?.name}
                            className="h-12 w-12 border-2 border-[#1A0F0A] object-cover"
                          />
                          <div>
                            <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Dibikin oleh</p>
                            <p className="mt-1 text-sm font-semibold">{f.creator?.name}</p>
                          </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                          <span className="border-2 border-[#1A0F0A] bg-white px-3 py-1.5 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                            {f.member_count} anggota
                          </span>
                          <span className="border-2 border-[#1A0F0A] bg-white px-3 py-1.5 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                            {f.reply_count} balasan
                          </span>
                        </div>
                      </div>
                      <h4 className="mt-3 font-clash text-xl font-black uppercase md:text-2xl">{f.title}</h4>
                      <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/76 md:text-base">{f.description}</p>
                      <div className="mt-3 flex flex-wrap gap-2">
                        {(f.tags || []).map((t) => (
                          <span
                            key={`${f.id}-${t}`}
                            className="border-2 border-[#1A0F0A] bg-white px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]"
                          >
                            #{t}
                          </span>
                        ))}
                      </div>
                      <div className="mt-3 grid gap-2">
                        {(f.replies || []).slice(0, 2).map((r) => (
                          <div key={r.id} className="border-2 border-[#1A0F0A] bg-white p-3">
                            <div className="flex items-start gap-3">
                              <img
                                src={getAvatarUrl(r.user, r.user?.name)}
                                alt={r.user?.name}
                                className="h-10 w-10 border-2 border-[#1A0F0A] object-cover"
                              />
                              <div>
                                <p className="font-mono text-[11px] font-black uppercase tracking-[0.16em]">{r.user?.name}</p>
                                <p className="mt-1 text-sm leading-6 text-[#1A0F0A]/76">{r.comment}</p>
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  </article>
                ))}
              </div>
            </div>

            <div className="grid gap-4">
              <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A]">
                <div className="flex items-center justify-between gap-3">
                  <div>
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Profile Picture</p>
                    <h3 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">Update Avatar</h3>
                  </div>
                  <Camera size={18} />
                </div>

                <form
                  onSubmit={handleProfileSave}
                  encType="multipart/form-data"
                  action="/profile/update"
                  method="post"
                  className="mt-3 grid gap-3"
                >
                  <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''} />

                  <div className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-[#FAF6F0] p-3">
                    <img
                      src={getAvatarUrl(
                        { ...profileForm, profile_picture: profilePreview || profileForm.profile_picture },
                        profileForm.name
                      )}
                      alt={profileForm.name}
                      className="h-20 w-20 border-2 border-[#1A0F0A] object-cover"
                    />
                    <div>
                      <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Preview Avatar</p>
                      <p className="mt-1 text-sm leading-6 text-[#1A0F0A]/72">
                        Upload foto lokal atau tempel URL. Kalau kosong, fallback ke ui-avatars.
                      </p>
                    </div>
                  </div>

                  <div className="grid gap-2 sm:grid-cols-2">
                    <label className="inline-flex cursor-pointer items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-white px-4 py-2.5 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[3px_3px_0px_0px_#1A0F0A]">
                      <Camera size={14} /> Upload Foto
                      <input
                        ref={fileInputRef}
                        type="file"
                        name="profile_picture"
                        accept="image/*"
                        onChange={handlePfpChange}
                        className="hidden"
                      />
                    </label>
                    <input
                      type="url"
                      name="profile_picture_url"
                      value={profileForm.profile_picture?.startsWith('data:') ? '' : (profileForm.profile_picture || '')}
                      onChange={(e) => { setProfileForm((s) => ({ ...s, profile_picture: e.target.value })); setProfilePreview(e.target.value); }}
                      placeholder="https://ui-avatars.com/api/?name=Anak+Skena"
                      className="w-full border-2 border-[#1A0F0A] bg-white px-4 py-2.5 text-sm outline-none md:text-base"
                    />
                  </div>

                  <div className="grid gap-2 sm:grid-cols-2">
                    <input
                      type="text"
                      name="name"
                      value={profileForm.name}
                      onChange={(e) => setProfileForm((s) => ({ ...s, name: e.target.value }))}
                      placeholder="Nama tampil"
                      className="w-full border-2 border-[#1A0F0A] bg-white px-4 py-2.5 text-sm outline-none md:text-base"
                    />
                    <input
                      type="text"
                      name="username"
                      value={profileForm.username}
                      onChange={(e) => setProfileForm((s) => ({ ...s, username: e.target.value }))}
                      placeholder="Username"
                      className="w-full border-2 border-[#1A0F0A] bg-white px-4 py-2.5 text-sm outline-none md:text-base"
                    />
                  </div>

                  <textarea
                    name="bio"
                    value={profileForm.bio}
                    onChange={(e) => setProfileForm((s) => ({ ...s, bio: e.target.value }))}
                    placeholder="Bio singkat anak skena"
                    rows={2}
                    className="w-full border-2 border-[#1A0F0A] bg-white px-4 py-2.5 text-sm outline-none md:text-base"
                  />

                  <button
                    type="submit"
                    className="inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-5 py-3 font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#1A0F0A] shadow-[3px_3px_0px_0px_#1A0F0A]"
                  >
                    <Camera size={14} /> Simpan Profil
                  </button>
                </form>
              </div>

              <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A]">
                <div className="flex items-center justify-between gap-3">
                  <div>
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Direkomendasikan</p>
                    <h3 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">Spot Buat Hari Ini</h3>
                  </div>
                  <Coffee size={18} />
                </div>
                <div className="mt-3 grid gap-3">
                  {coffeeShops.slice(0, 3).map((spot) => (
                    <div key={spot.id} className="border-2 border-[#1A0F0A] bg-[#FAF6F0] p-3">
                      <div className="flex items-start justify-between gap-3">
                        <div>
                          <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">
                            {districtLabel(spot) || 'Area nongki'}
                          </p>
                          <p className="mt-1 text-sm font-semibold md:text-base">{spot.nama}</p>
                        </div>
                        <span className="inline-flex items-center gap-1 font-mono text-[11px] font-black uppercase tracking-[0.14em]">
                          Rp {formatRupiah(spot.harga_min)} - {formatRupiah(spot.harga_max)}
                        </span>
                      </div>
                      <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/72">{spot.alamat}</p>
                      <div className="mt-3 flex items-center justify-between gap-3">
                        <span className="inline-flex items-center gap-2 text-sm">
                          <MapPin size={14} /> {districtLabel(spot)}
                        </span>
                        <Link
                          href="/"
                          className="border-2 border-[#1A0F0A] bg-white px-3 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em]"
                        >
                          Lihat Home
                        </Link>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </section>
        </div>
      </main>
    </div>
  );
}
