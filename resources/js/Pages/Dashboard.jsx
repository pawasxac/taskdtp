import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
  Bell, Camera, Coffee, MapPin, MessageCircleMore, MessageSquareText,
  Send, Sparkles, Tags, Users, X, AtSign, BookUser, Hash, ShieldCheck,
} from 'lucide-react';
import Navbar from '../Components/Navbar';
import CustomCursor from '../Components/CustomCursor';

const getAvatarUrl = (user, fallbackLabel = 'Anak Skena') => {
  if (user?.profile_picture?.startsWith('http')) return user.profile_picture;
  if (user?.profile_picture) return `/uploads/profile_pictures/${user.profile_picture}`;
  const label = encodeURIComponent(user?.name || user?.username || fallbackLabel);
  return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
};

const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));
const districtLabel = (cafe) => cafe?.district_name || cafe?.kecamatan?.name || cafe?.kecamatan || cafe?.daerah || '';

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

  const handleSendChat = (e) => {
    e.preventDefault();
    if (!chatInput.trim()) return;
    setChatMessages((m) => [
      ...m,
      {
        id: `chat-${Date.now()}`,
        user: { name: profileForm.name, username: profileForm.username, profile_picture: profileForm.profile_picture },
        area: 'Global Lounge',
        text: chatInput,
        time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        tags: ['baru'],
      },
    ]);
    setChatInput('');
  };

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

  const handleProfileSave = (event) => {
    event.preventDefault();
    setProfileStatus('Profil kebaru. Senyumnya anak skena makin pede.');
    window.setTimeout(() => setProfileStatus(null), 2800);
  };

  const handleNotificationClick = (n) => {
    setNotifOpen(false);
    if (n?.route) router.visit(n.route);
  };

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Dashboard Roastery Skena" />
      <Navbar current="dashboard" />
      <CustomCursor />

      {profileStatus && (
        <div className="fixed right-4 top-24 z-[80] w-[calc(100%-2rem)] max-w-sm animate-in slide-in-from-right-4 duration-300">
          <div className="border-2 border-[#1A0F0A] bg-white p-3 shadow-[4px_4px_0px_0px_#1A0F0A]">
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Update Profil</p>
            <p className="mt-1 text-sm font-semibold">{profileStatus}</p>
          </div>
        </div>
      )}

      <main className="pt-24 pb-12 px-4 md:px-8 max-w-[1440px] mx-auto">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="font-clash text-3xl md:text-5xl font-black uppercase">Dashboard Lounge</h1>
            <p className="font-mono text-xs font-black uppercase tracking-[0.1em] mt-1 text-[#1A0F0A]/60">Sruput, ngobrol, atur ritme.</p>
          </div>
          <div className="relative" ref={notifRef}>
            <button
              onClick={() => setNotifOpen(!notifOpen)}
              className="magnetic inline-flex h-12 w-12 items-center justify-center border-2 border-[#1A0F0A] bg-white shadow-[4px_4px_0px_0px_#1A0F0A] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#1A0F0A] transition-all"
            >
              <Bell size={20} />
              {notifications.length > 0 && (
                <span className="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center border-2 border-[#1A0F0A] bg-[#C19A6B] font-mono text-[10px] font-black">
                  {notifications.length}
                </span>
              )}
            </button>
            {notifOpen && (
              <div className="absolute right-0 top-14 z-50 w-80 border-2 border-[#1A0F0A] bg-white p-3 shadow-[6px_6px_0px_0px_#1A0F0A] animate-in slide-in-from-top-2">
                <div className="flex items-center justify-between border-b-2 border-[#1A0F0A] pb-2 mb-2">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Notifikasi</p>
                  <button onClick={() => setNotifOpen(false)} className="magnetic hover:text-[#C19A6B]"><X size={16} /></button>
                </div>
                <div className="grid gap-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                  {notifications.map(n => (
                    <button key={n.id} onClick={() => handleNotificationClick(n)} className="magnetic text-left border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2 hover:bg-white flex gap-2">
                      <div className="shrink-0 mt-1"><Bell size={14} className="text-[#C19A6B]" /></div>
                      <div>
                        <p className="font-clash text-sm font-black uppercase">{n.title}</p>
                        <p className="text-xs text-[#1A0F0A]/70 line-clamp-2 mt-1">{n.body}</p>
                      </div>
                    </button>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>

        {/* 12-Column Bento Box Grid */}
        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-min">
          
          {/* PROFILE CARD - spans 8 cols */}
          <div className="md:col-span-12 lg:col-span-8 border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-5 shadow-[6px_6px_0px_0px_#C19A6B] flex flex-col justify-between">
            <div className="flex flex-col md:flex-row gap-6 items-start md:items-center">
              <div className="relative group">
                <img
                  src={getAvatarUrl({ ...profileForm, profile_picture: profilePreview || profileForm.profile_picture }, profileForm.name)}
                  alt="Avatar"
                  className="h-24 w-24 border-2 border-[#FAF6F0] object-cover"
                />
                <label className="magnetic absolute inset-0 bg-[#1A0F0A]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                  <Camera size={20} className="text-[#FAF6F0]" />
                  <input type="file" className="hidden" accept="image/*" onChange={handlePfpChange} />
                </label>
              </div>
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                  <h2 className="font-clash text-3xl font-black uppercase tracking-wide">{profileForm.name}</h2>
                  {isAdmin && <ShieldCheck size={20} className="text-[#C19A6B]" />}
                </div>
                <p className="font-mono text-xs uppercase tracking-widest text-[#FAF6F0]/60 mb-2">@{profileForm.username}</p>
                <form onSubmit={handleProfileSave} className="flex flex-col sm:flex-row gap-2 w-full mt-3">
                  <input
                    type="text"
                    value={profileForm.bio}
                    onChange={(e) => setProfileForm(s => ({ ...s, bio: e.target.value }))}
                    placeholder="Bio singkat..."
                    className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-sm py-1 text-[#FAF6F0] placeholder:text-[#FAF6F0]/30"
                  />
                  <button type="submit" className="magnetic bg-[#C19A6B] text-[#1A0F0A] px-4 py-1 font-mono text-[10px] font-black uppercase shrink-0">Simpan Bio</button>
                </form>
              </div>
            </div>
            {isAdmin && (
              <div className="mt-6 border-t border-[#FAF6F0]/20 pt-4">
                <Link href="/admin/gateway" className="magnetic inline-flex items-center gap-2 border-2 border-[#C19A6B] bg-[#C19A6B] text-[#1A0F0A] px-4 py-2 font-mono text-xs font-black uppercase tracking-wide">
                  <ShieldCheck size={16} /> Akses Control Panel Admin
                </Link>
              </div>
            )}
          </div>

          {/* STATS BENTO - spans 4 cols */}
          <div className="md:col-span-12 lg:col-span-4 grid grid-cols-2 gap-4">
            <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[4px_4px_0px_0px_#1A0F0A] flex flex-col justify-between">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Chat Aktif</p>
              <p className="font-clash text-4xl font-black mt-2">{chatMessages.length}</p>
            </div>
            <div className="border-2 border-[#1A0F0A] bg-[#C19A6B] p-4 shadow-[4px_4px_0px_0px_#1A0F0A] flex flex-col justify-between">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#1A0F0A]">Forum</p>
              <p className="font-clash text-4xl font-black mt-2 text-[#1A0F0A]">{forums.length}</p>
            </div>
            <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-4 shadow-[4px_4px_0px_0px_#C19A6B] col-span-2 flex flex-col justify-between">
              <div className="flex justify-between items-center mb-2">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">DM Masuk</p>
                <MessageSquareText size={16} className="text-[#C19A6B]" />
              </div>
              <p className="font-clash text-3xl font-black">{directMessages.length} Pesan Baru</p>
            </div>
          </div>

          {/* GLOBAL CHAT - spans 4 cols */}
          <div className="md:col-span-6 lg:col-span-4 border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col h-[400px]">
            <div className="border-b-2 border-[#1A0F0A] p-3 flex justify-between items-center bg-[#FAF6F0]">
              <h3 className="font-clash text-lg font-black uppercase">Global Lounge</h3>
              <MessageCircleMore size={18} className="text-[#C19A6B]" />
            </div>
            <div className="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar" ref={chatListRef}>
              {chatMessages.map(msg => (
                <div key={msg.id} className="border border-[#1A0F0A]/20 bg-[#FAF6F0] p-2">
                  <div className="flex justify-between items-center mb-1">
                    <span className="font-mono text-[10px] font-bold uppercase">{msg.user?.name || msg.user?.username}</span>
                    <span className="font-mono text-[9px] text-[#1A0F0A]/50">{msg.time}</span>
                  </div>
                  <p className="text-sm leading-snug">{msg.text}</p>
                </div>
              ))}
            </div>
            <form onSubmit={handleSendChat} className="border-t-2 border-[#1A0F0A] flex">
              <input 
                type="text" 
                value={chatInput} 
                onChange={(e) => setChatInput(e.target.value)} 
                placeholder="Ketik santai..." 
                className="flex-1 bg-transparent px-3 py-2 text-sm outline-none font-sans"
              />
              <button type="submit" className="magnetic bg-[#C19A6B] border-l-2 border-[#1A0F0A] px-4 flex items-center justify-center">
                <Send size={14} />
              </button>
            </form>
          </div>

          {/* DIRECT MESSAGES - spans 4 cols */}
          <div className="md:col-span-6 lg:col-span-4 border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col h-[400px]">
            <div className="border-b-2 border-[#1A0F0A] p-3 flex justify-between items-center bg-[#FAF6F0]">
              <h3 className="font-clash text-lg font-black uppercase">Inbox DM</h3>
            </div>
            <div className="flex flex-col flex-1 min-h-0">
              <div className="flex overflow-x-auto border-b-2 border-[#1A0F0A] custom-scrollbar p-2 gap-2">
                {directMessages.map(dm => (
                  <button 
                    key={dm.id} 
                    onClick={() => setSelectedDmId(dm.id)}
                    className={`magnetic shrink-0 h-10 w-10 border-2 rounded-full overflow-hidden ${selectedDmId === dm.id ? 'border-[#C19A6B]' : 'border-[#1A0F0A]'}`}
                  >
                    <img src={getAvatarUrl(dm.user)} alt="User" className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
              <div className="flex-1 overflow-y-auto p-3 custom-scrollbar bg-[#FAF6F0]">
                {selectedDm?.messages?.map(m => (
                  <div key={m.id} className="mb-3">
                    <p className="font-mono text-[9px] font-bold uppercase text-[#1A0F0A]/50 mb-1">{m.user?.name}</p>
                    <div className="bg-white border-2 border-[#1A0F0A] p-2 text-sm inline-block max-w-[90%]">
                      {m.text}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* RECOMMENDED SPOTS / MOOD BOARD - spans 4 cols */}
          <div className="md:col-span-12 lg:col-span-4 grid gap-6 grid-rows-[auto_1fr]">
            <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A]">
              <div className="flex justify-between items-center mb-3">
                <h3 className="font-clash text-lg font-black uppercase">Spot Nyaman</h3>
                <Coffee size={16} />
              </div>
              <div className="space-y-3">
                {coffeeShops.slice(0, 2).map(spot => (
                  <div key={spot.id} className="border-2 border-[#1A0F0A] p-2 bg-[#FAF6F0] flex justify-between items-center">
                    <div>
                      <p className="font-clash font-black uppercase text-sm truncate max-w-[150px]">{spot.nama}</p>
                      <p className="font-mono text-[9px] text-[#C19A6B]">{districtLabel(spot)}</p>
                    </div>
                    <Link href="/" className="magnetic text-[10px] font-mono border-2 border-[#1A0F0A] px-2 py-1 bg-white hover:bg-[#1A0F0A] hover:text-white transition-colors">Lihat</Link>
                  </div>
                ))}
              </div>
            </div>

            <div className="border-2 border-[#1A0F0A] bg-white p-4 shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col">
              <h3 className="font-clash text-lg font-black uppercase mb-3">Vibe Tags</h3>
              <div className="flex flex-wrap gap-2 mt-auto">
                {tags.slice(0, 8).map(t => (
                  <span key={t} className="magnetic border border-[#1A0F0A] bg-[#FAF6F0] px-2 py-1 font-mono text-[10px] font-black uppercase tracking-wider">
                    #{t}
                  </span>
                ))}
              </div>
            </div>
          </div>

          {/* FORUM THREADS - spans 12 cols */}
          <div className="md:col-span-12 border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A] p-4 md:p-6 mb-12">
            <div className="flex justify-between items-end mb-6">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B]">Komunitas</p>
                <h2 className="font-clash text-2xl md:text-3xl font-black uppercase">Top Threads Skena</h2>
              </div>
              <Users size={24} className="text-[#C19A6B]" />
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {forums.map(f => (
                <article key={f.id} className="border-2 border-[#1A0F0A] bg-[#FAF6F0] flex flex-col hover:-translate-y-1 transition-transform duration-300">
                  <div className="h-24 bg-[#1A0F0A] border-b-2 border-[#1A0F0A] relative overflow-hidden">
                    <img src={f.cover_image} className="w-full h-full object-cover opacity-80" alt="Cover" />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#1A0F0A]/80 to-transparent" />
                    <div className="absolute bottom-2 left-2 right-2 flex justify-between items-center">
                      <span className="font-mono text-[10px] text-white uppercase bg-[#1A0F0A] px-2 py-0.5 border border-white/20">{f.member_count} Member</span>
                      <span className="font-mono text-[10px] text-[#C19A6B] uppercase bg-[#1A0F0A] px-2 py-0.5 border border-[#C19A6B]/50">{f.reply_count} Reply</span>
                    </div>
                  </div>
                  <div className="p-4 flex flex-col flex-1">
                    <h4 className="font-clash text-lg font-black uppercase mb-1 line-clamp-1">{f.title}</h4>
                    <p className="text-sm text-[#1A0F0A]/70 line-clamp-2 mb-4 flex-1">{f.description}</p>
                    <button className="magnetic border-2 border-[#1A0F0A] bg-white w-full py-2 font-mono text-[10px] font-black uppercase hover:bg-[#C19A6B] transition-colors">
                      Masuk Thread
                    </button>
                  </div>
                </article>
              ))}
            </div>
          </div>

        </div>
      </main>
    </div>
  );
}
