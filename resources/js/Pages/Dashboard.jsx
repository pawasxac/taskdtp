import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
  Bell, Camera, Coffee, MessageCircleMore, MessageSquareText,
  Send, Users, X, ShieldCheck, Plus, Info, LayoutDashboard
} from 'lucide-react';
import Navbar from '../Components/Navbar';
import CustomCursor from '../Components/CustomCursor';
import PublicProfileModal from '../Components/PublicProfileModal';

const getAvatarUrl = (user, fallbackLabel = 'Anak Skena') => {
  if (user?.profile_picture?.startsWith('http')) return user.profile_picture;
  if (user?.profile_picture) return `/uploads/profile_pictures/${user.profile_picture}`;
  const label = encodeURIComponent(user?.name || user?.username || fallbackLabel);
  return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
};

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

  const [localDirectMessages, setLocalDirectMessages] = useState(directMessages);
  const [chatMessages, setChatMessages] = useState(globalChat);
  const [chatInput, setChatInput] = useState('');
  const [selectedDmId, setSelectedDmId] = useState(directMessages[0]?.id || null);
  const [notifOpen, setNotifOpen] = useState(false);
  
  const [profileForm, setProfileForm] = useState({
    name: currentUser?.name || 'Anak Skena',
    username: currentUser?.username || 'anakskena',
    bio: currentUser?.bio || 'Ngopi pelan, mikirnya belakangan.',
    profile_picture: currentUser?.profile_picture || '',
    instagram: currentUser?.instagram || '',
    whatsapp: currentUser?.whatsapp || '',
    discord: currentUser?.discord || '',
  });
  const [profilePreview, setProfilePreview] = useState(currentUser?.profile_picture || '');
  const [profileStatus, setProfileStatus] = useState(null);
  const [dmInput, setDmInput] = useState('');

  // Modals State
  const [publicProfileUser, setPublicProfileUser] = useState(null);
  const [createKomunitasOpen, setCreateKomunitasOpen] = useState(false);
  const [komunitasForm, setKomunitasForm] = useState({ nama_komunitas: '', deskripsi: '', domisili: '' });
  const [selectedKomunitas, setSelectedKomunitas] = useState(null);
  const [komunitasPostInput, setKomunitasPostInput] = useState('');

  const chatListRef = useRef(null);
  const notifRef = useRef(null);
  const forumRef = useRef(null);
  const dmRef = useRef(null);
  const feedRef = useRef(null);

  useEffect(() => { setChatMessages(globalChat); }, [globalChat]);
  useEffect(() => { setLocalDirectMessages(directMessages); }, [directMessages]);
  useEffect(() => { if (chatListRef.current) chatListRef.current.scrollTop = chatListRef.current.scrollHeight; }, [chatMessages.length]);
  useEffect(() => { if (feedRef.current) feedRef.current.scrollTop = feedRef.current.scrollHeight; }, [selectedKomunitas?.replies?.length]);

  // Connect to SSE stream
  useEffect(() => {
    const eventSource = new EventSource('/chat/stream');

    eventSource.addEventListener('ping', (e) => {
      try {
        const data = JSON.parse(e.data);
        setChatMessages((prev) => {
          // Prevent duplicates
          if (prev.some((m) => m.id === data.id)) return prev;
          return [
            ...prev,
            {
              id: data.id,
              user: { name: 'Radar Skena', username: 'radarskena', profile_picture: null },
              area: 'Live Lounge',
              text: data.text,
              time: data.time.substring(0, 5),
              tags: ['live'],
            },
          ];
        });
      } catch (err) {
        console.error('Failed parsing stream message:', err);
      }
    });

    return () => {
      eventSource.close();
    };
  }, []);

  useEffect(() => {
    const tab = new URLSearchParams(window.location.search).get('tab');
    if (tab === 'forum' && forumRef.current) {
      forumRef.current.scrollIntoView({ behavior: 'smooth' });
    } else if (tab === 'dm' && dmRef.current) {
      dmRef.current.scrollIntoView({ behavior: 'smooth' });
    }
  }, []);

  const selectedDm = useMemo(
    () => localDirectMessages.find((d) => d.id === selectedDmId) || localDirectMessages[0] || null,
    [localDirectMessages, selectedDmId]
  );

  const handleSendDm = (e) => {
    e.preventDefault();
    if (!dmInput.trim() || !selectedDm) return;
    const sentText = dmInput;
    setDmInput('');

    // Optimistically update the UI messages list
    setLocalDirectMessages(prev => {
        return prev.map(dm => {
            if (dm.id === selectedDm.id) {
                // If it's a dynamic temporary ID (like dm-new-xx), we keep it until page reload or replace it
                const newMsg = {
                    id: `dm-temp-${Date.now()}`,
                    text: sentText,
                    user: {
                        id: currentUser.id,
                        name: currentUser.name,
                        username: currentUser.username,
                        profile_picture: currentUser.profile_picture,
                    }
                };
                return {
                    ...dm,
                    last_message: sentText,
                    messages: [...(dm.messages || []), newMsg]
                };
            }
            return dm;
        });
    });

    router.post('/dm/send', {
      receiver_id: selectedDm.user.id,
      message: sentText,
    }, {
      preserveScroll: true,
      onSuccess: (page) => {
        // Inertia will reload the page props, syncing localDirectMessages back to server state
      },
    });
  };

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

  const handleCreateKomunitas = (e) => {
    e.preventDefault();
    router.post('/komunitas/store', komunitasForm, {
      preserveScroll: true,
      onSuccess: () => {
        setCreateKomunitasOpen(false);
        setKomunitasForm({ nama_komunitas: '', deskripsi: '', domisili: '' });
      }
    });
  };

  const handleSendKomunitasPost = (e) => {
    e.preventDefault();
    if (!komunitasPostInput.trim() || !selectedKomunitas) return;
    router.post(`/komunitas/${selectedKomunitas.id}/post`, { content: komunitasPostInput }, {
      preserveScroll: true,
      onSuccess: () => setKomunitasPostInput(''),
    });
  };

  const btnPrimary = 'magnetic inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-4 py-2 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors cursor-pointer';
  const btnSecondary = 'magnetic inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-transparent px-4 py-2 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors cursor-pointer';

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Dashboard Roastery Skena" />
      <Navbar current="dashboard" />
      <CustomCursor />

      <PublicProfileModal 
        isOpen={!!publicProfileUser} 
        user={publicProfileUser} 
        onClose={() => setPublicProfileUser(null)} 
        onDmClick={(u) => {
            const existingDm = localDirectMessages.find(dm => dm.user?.id === u.id);
            if (existingDm) {
                setSelectedDmId(existingDm.id);
            } else {
                // Instatiate a mock dynamic thread locally for this user
                const newDmId = `dm-new-${u.id}`;
                const newDmThread = {
                    id: newDmId,
                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                    last_message: 'Mulai percakapan...',
                    user: {
                        id: u.id,
                        name: u.name,
                        username: u.username || u.name.toLowerCase().replace(/\s+/g, ''),
                        profile_picture: u.profile_picture || null,
                        avatar_url: u.profile_picture ? `/uploads/profile_pictures/${u.profile_picture}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=1A0F0A&color=FAF6F0&bold=true`,
                        instagram: u.instagram || null,
                        whatsapp: u.whatsapp || null,
                        discord: u.discord || null,
                    },
                    messages: []
                };
                setLocalDirectMessages(prev => [newDmThread, ...prev]);
                setSelectedDmId(newDmId);
            }
            if (dmRef.current) dmRef.current.scrollIntoView({ behavior: 'smooth' });
        }}
      />

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
                    <button key={n.id} onClick={() => { setNotifOpen(false); if (n?.route) router.visit(n.route); }} className="magnetic text-left border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2 hover:bg-white flex gap-2">
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

        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-min">
          
          {/* PROFILE CARD - spans 8 cols */}
          <div className="md:col-span-12 lg:col-span-8 border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-5 shadow-[6px_6px_0px_0px_#C19A6B] flex flex-col justify-between">
            <div className="flex flex-col md:flex-row gap-6 items-start md:items-center">
              <div className="relative group">
                <img
                  src={getAvatarUrl({ ...profileForm, profile_picture: profilePreview || profileForm.profile_picture }, profileForm.name)}
                  alt="Avatar"
                  className="h-24 w-24 border-2 border-[#FAF6F0] object-cover cursor-pointer"
                  onClick={() => setPublicProfileUser(currentUser)}
                />
              </div>
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                  <h2 className="font-clash text-3xl font-black uppercase tracking-wide cursor-pointer" onClick={() => setPublicProfileUser(currentUser)}>{profileForm.name}</h2>
                  {isAdmin && <ShieldCheck size={20} className="text-[#C19A6B]" />}
                </div>
                <p className="font-mono text-xs uppercase tracking-widest text-[#FAF6F0]/60 mb-2">@{profileForm.username}</p>
                <form className="flex flex-col gap-2 w-full mt-3">
                  <input
                    type="text"
                    value={profileForm.bio}
                    onChange={(e) => setProfileForm(s => ({ ...s, bio: e.target.value }))}
                    className="w-full bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-sm py-1 text-[#FAF6F0]"
                  />
                  <div className="flex gap-2">
                    <input type="text" value={profileForm.instagram} onChange={e => setProfileForm(s => ({...s, instagram: e.target.value}))} placeholder="IG" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                    <input type="text" value={profileForm.whatsapp} onChange={e => setProfileForm(s => ({...s, whatsapp: e.target.value}))} placeholder="WA" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                    <input type="text" value={profileForm.discord} onChange={e => setProfileForm(s => ({...s, discord: e.target.value}))} placeholder="Discord ID" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                  </div>
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
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Komunitas</p>
              <p className="font-clash text-4xl font-black mt-2">{forums.length}</p>
            </div>
            <div className="border-2 border-[#1A0F0A] bg-[#C19A6B] p-4 shadow-[4px_4px_0px_0px_#1A0F0A] flex flex-col justify-between">
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#1A0F0A]">Kedai</p>
              <p className="font-clash text-4xl font-black mt-2 text-[#1A0F0A]">{coffeeShops.length}</p>
            </div>
            <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-4 shadow-[4px_4px_0px_0px_#C19A6B] col-span-2 flex flex-col justify-between">
              <div className="flex justify-between items-center mb-2">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">DM Masuk</p>
                <MessageSquareText size={16} className="text-[#C19A6B]" />
              </div>
              <p className="font-clash text-3xl font-black">{localDirectMessages.length} Pesan Baru</p>
            </div>
          </div>

          {/* GLOBAL CHAT - spans 4 cols */}
          <div className="md:col-span-6 lg:col-span-4 border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col h-[500px]">
            <div className="border-b-2 border-[#1A0F0A] p-3 flex justify-between items-center bg-[#FAF6F0]">
              <h3 className="font-clash text-lg font-black uppercase">Global Lounge</h3>
              <MessageCircleMore size={18} className="text-[#C19A6B]" />
            </div>
            <div className="flex-1 overflow-y-auto p-3 space-y-3 custom-scrollbar" ref={chatListRef}>
              {chatMessages.map(msg => (
                <div key={msg.id} className="border border-[#1A0F0A]/20 bg-[#FAF6F0] p-2">
                  <div className="flex justify-between items-center mb-1">
                    <span 
                        className="font-mono text-[10px] font-bold uppercase cursor-pointer hover:text-[#C19A6B]"
                        onClick={() => setPublicProfileUser(msg.user)}
                    >{msg.user?.name || msg.user?.username}</span>
                    <span className="font-mono text-[9px] text-[#1A0F0A]/50">{msg.time}</span>
                  </div>
                  <p className="text-sm leading-snug">{msg.text}</p>
                </div>
              ))}
            </div>
            <form onSubmit={handleSendChat} className="border-t-2 border-[#1A0F0A] flex shrink-0">
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

          {/* DUAL PANE DIRECT MESSAGES - spans 8 cols */}
          <div ref={dmRef} className="md:col-span-6 lg:col-span-8 border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A] flex flex-col h-[500px] overflow-hidden">
            <div className="border-b-2 border-[#1A0F0A] p-3 flex justify-between items-center bg-[#FAF6F0] shrink-0">
              <h3 className="font-clash text-lg font-black uppercase">Inbox DM</h3>
            </div>
            
            <div className="flex flex-1 min-h-0">
                {/* Left Sidebar (Active Convos) */}
                <div className="w-1/3 border-r-2 border-[#1A0F0A] overflow-y-auto custom-scrollbar bg-white">
                    {localDirectMessages.length === 0 ? (
                        <p className="p-4 text-xs font-mono text-[#1A0F0A]/50">Belum ada DM.</p>
                    ) : localDirectMessages.map(dm => (
                        <button 
                            key={dm.id} 
                            onClick={() => setSelectedDmId(dm.id)}
                            className={`w-full text-left p-3 border-b border-[#1A0F0A]/10 flex items-center gap-3 transition-colors ${selectedDmId === dm.id ? 'bg-[#FAF6F0]' : 'hover:bg-[#FAF6F0]'}`}
                        >
                            <img src={getAvatarUrl(dm.user)} alt="User" className="w-10 h-10 border-2 border-[#1A0F0A] object-cover shrink-0" />
                            <div className="overflow-hidden">
                                <p className="font-mono text-[10px] font-black uppercase truncate">{dm.user?.name}</p>
                                <p className="text-xs text-[#1A0F0A]/60 truncate">{dm.messages?.[dm.messages.length - 1]?.text || 'Mulai chat...'}</p>
                            </div>
                        </button>
                    ))}
                </div>

                {/* Right Pane (Chat Window) */}
                <div className="w-2/3 flex flex-col bg-[#FAF6F0]">
                    <div className="flex-1 overflow-y-auto p-4 custom-scrollbar">
                        {selectedDm ? selectedDm.messages?.map(m => (
                            <div key={m.id} className={`mb-3 flex ${m.user?.id === currentUser?.id ? 'justify-end' : 'justify-start'}`}>
                                <div className={`border-2 border-[#1A0F0A] p-2 text-sm max-w-[80%] ${m.user?.id === currentUser?.id ? 'bg-[#C19A6B]' : 'bg-white'}`}>
                                    <p className="font-mono text-[9px] font-bold uppercase text-[#1A0F0A]/50 mb-1">{m.user?.name}</p>
                                    {m.text}
                                </div>
                            </div>
                        )) : (
                            <div className="h-full flex items-center justify-center font-mono text-xs text-[#1A0F0A]/50">Pilih obrolan di samping.</div>
                        )}

                        {selectedDm?.messages?.length >= 10 && (
                            <div className="mt-6 p-4 border-2 border-[#C19A6B] bg-white text-center">
                                <p className="font-clash font-black uppercase text-[#C19A6B] mb-2">Udah 10 pesan nih!</p>
                                <p className="text-xs mb-4">Lanjut kenalan lebih deket lewat sosmed gih.</p>
                                <div className="flex justify-center gap-2">
                                    {selectedDm.user?.instagram && <a href={`https://instagram.com/${selectedDm.user.instagram.replace('@', '')}`} target="_blank" rel="noreferrer" className="text-[10px] font-mono border-2 border-[#1A0F0A] px-2 py-1 bg-[#1A0F0A] text-white">IG</a>}
                                    {selectedDm.user?.whatsapp && <a href={`https://wa.me/${selectedDm.user.whatsapp}`} target="_blank" rel="noreferrer" className="text-[10px] font-mono border-2 border-[#1A0F0A] px-2 py-1 bg-[#1A0F0A] text-white">WA</a>}
                                    {selectedDm.user?.discord && <span className="text-[10px] font-mono border-2 border-[#1A0F0A] px-2 py-1 bg-white text-black">DC: {selectedDm.user.discord}</span>}
                                </div>
                            </div>
                        )}
                    </div>
                    {selectedDm && (
                        selectedDm.messages?.length < 10 ? (
                            <form onSubmit={handleSendDm} className="border-t-2 border-[#1A0F0A] flex shrink-0 bg-white">
                                <input 
                                    type="text" 
                                    value={dmInput} 
                                    onChange={(e) => setDmInput(e.target.value)} 
                                    placeholder={`Balas ${selectedDm.user?.name}...`} 
                                    className="flex-1 px-4 py-3 text-sm outline-none font-sans bg-transparent"
                                />
                                <button type="submit" className="magnetic bg-[#C19A6B] border-l-2 border-[#1A0F0A] px-6 flex items-center justify-center">
                                    <Send size={16} />
                                </button>
                            </form>
                        ) : (
                            <div className="border-t-2 border-[#1A0F0A] bg-[#1A0F0A] text-white text-center p-3 text-xs font-mono uppercase shrink-0">
                                Chat Dikunci (Limit 10 Pesan)
                            </div>
                        )
                    )}
                </div>
            </div>
          </div>

          {/* KOMUNITAS SECTION - spans 12 cols */}
          <div ref={forumRef} className="md:col-span-12 border-2 border-[#1A0F0A] bg-white shadow-[6px_6px_0px_0px_#1A0F0A] p-4 md:p-6 mb-12">
            <div className="flex justify-between items-end mb-6">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B]">Skena Hub</p>
                <h2 className="font-clash text-2xl md:text-3xl font-black uppercase">Top Komunitas Skena</h2>
              </div>
              <button onClick={() => setCreateKomunitasOpen(true)} className={btnPrimary}>
                <Plus size={16} /> Buat Komunitas
              </button>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {forums.map(f => (
                <article key={f.id} className="border-2 border-[#1A0F0A] bg-[#FAF6F0] flex flex-col hover:-translate-y-1 transition-transform duration-300">
                  <div className="h-32 bg-[#1A0F0A] border-b-2 border-[#1A0F0A] relative overflow-hidden">
                    <img src={f.cover_image} className="w-full h-full object-cover opacity-80" alt="Cover" />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#1A0F0A]/90 to-transparent" />
                    <div className="absolute bottom-3 left-3 right-3 flex justify-between items-end">
                      <div>
                        <h4 className="font-clash text-lg text-white font-black uppercase mb-1 line-clamp-1">{f.title}</h4>
                        <span className="font-mono text-[9px] text-[#C19A6B] uppercase tracking-widest">{f.domisili || 'Global'}</span>
                      </div>
                      <span className="font-mono text-[10px] text-white uppercase bg-[#1A0F0A] px-2 py-0.5 border border-[#C19A6B]/50">{f.member_count} Member</span>
                    </div>
                  </div>
                  <div className="p-4 flex flex-col flex-1">
                    <p className="text-sm text-[#1A0F0A]/80 line-clamp-2 mb-4 flex-1">{f.description}</p>
                    <button onClick={() => setSelectedKomunitas(f)} className="magnetic border-2 border-[#1A0F0A] bg-white w-full py-2 font-mono text-[10px] font-black uppercase hover:bg-[#C19A6B] transition-colors">
                      Masuk Komunitas
                    </button>
                  </div>
                </article>
              ))}
            </div>
          </div>

        </div>
      </main>

      {/* CREATE KOMUNITAS MODAL */}
      {createKomunitasOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-[#1A0F0A]/80 backdrop-blur-sm p-4" onClick={() => setCreateKomunitasOpen(false)}>
          <div className="w-full max-w-md bg-[#FAF6F0] border-2 border-[#1A0F0A] p-6 shadow-[8px_8px_0px_0px_#C19A6B] animate-in zoom-in-95" onClick={e => e.stopPropagation()}>
            <div className="flex justify-between items-center mb-6">
              <h3 className="font-clash text-2xl font-black uppercase">Buat Komunitas</h3>
              <button onClick={() => setCreateKomunitasOpen(false)}><X size={20} /></button>
            </div>
            <form onSubmit={handleCreateKomunitas} className="space-y-4">
              <div>
                <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-1">Nama Komunitas</label>
                <input required type="text" value={komunitasForm.nama_komunitas} onChange={e => setKomunitasForm(s => ({...s, nama_komunitas: e.target.value}))} className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B]" />
              </div>
              <div>
                <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-1">Domisili / Wilayah</label>
                <input required type="text" value={komunitasForm.domisili} onChange={e => setKomunitasForm(s => ({...s, domisili: e.target.value}))} className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B]" />
              </div>
              <div>
                <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-1">Deskripsi Singkat</label>
                <textarea required rows="3" value={komunitasForm.deskripsi} onChange={e => setKomunitasForm(s => ({...s, deskripsi: e.target.value}))} className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B] custom-scrollbar"></textarea>
              </div>
              <button type="submit" className={`${btnPrimary} w-full`}>Dirikan Komunitas</button>
            </form>
          </div>
        </div>
      )}

      {/* DETAIL VIEW KOMUNITAS MODAL */}
      {selectedKomunitas && (
        <div className="fixed inset-0 z-[150] flex items-center justify-center bg-[#1A0F0A]/90 backdrop-blur-md p-4 md:p-8" onClick={() => setSelectedKomunitas(null)}>
          <div className="w-full max-w-5xl h-full max-h-[85vh] bg-[#FAF6F0] border-2 border-[#1A0F0A] overflow-hidden flex flex-col animate-in slide-in-from-bottom-8 shadow-[12px_12px_0px_0px_#C19A6B]" onClick={e => e.stopPropagation()}>
            {/* Header */}
            <div className="h-32 md:h-40 bg-[#1A0F0A] relative flex shrink-0">
              <img src={selectedKomunitas.cover_image} alt="Cover" className="absolute inset-0 w-full h-full object-cover opacity-60" />
              <div className="absolute inset-0 bg-gradient-to-t from-[#1A0F0A] to-transparent" />
              <button onClick={() => setSelectedKomunitas(null)} className="absolute top-4 right-4 bg-black/50 text-white p-2 rounded-full hover:bg-[#C19A6B] transition-colors z-10"><X size={20} /></button>
              
              <div className="relative z-10 mt-auto p-4 md:p-6 flex justify-between items-end w-full">
                <div>
                  <h2 className="font-clash text-3xl md:text-5xl font-black uppercase text-white">{selectedKomunitas.title}</h2>
                  <p className="font-mono text-xs text-[#C19A6B] tracking-[0.2em] uppercase mt-1 flex items-center gap-2">
                    <MapPin size={12} /> {selectedKomunitas.domisili || 'Global Area'}
                  </p>
                </div>
                <div className="hidden md:block bg-white/10 backdrop-blur-sm border border-white/20 px-4 py-2 text-white text-xs font-mono">
                  {selectedKomunitas.member_count} Members
                </div>
              </div>
            </div>

            <div className="flex flex-1 min-h-0 flex-col md:flex-row">
              {/* Left sidebar: Members */}
              <div className="w-full md:w-1/4 border-r-2 border-[#1A0F0A] bg-white overflow-y-auto custom-scrollbar shrink-0">
                <div className="p-4 border-b-2 border-[#1A0F0A] bg-[#FAF6F0]">
                  <h3 className="font-mono text-[10px] font-black uppercase tracking-[0.16em]">Owner & Anggota</h3>
                </div>
                <div className="p-3 space-y-3">
                  {/* Owner */}
                  <div className="flex items-center gap-3 border border-[#C19A6B] bg-[#C19A6B]/10 p-2 cursor-pointer hover:bg-[#C19A6B]/20" onClick={() => setPublicProfileUser(selectedKomunitas.creator)}>
                    <img src={selectedKomunitas.creator?.avatar_url} alt="Owner" className="w-8 h-8 object-cover border border-[#1A0F0A]" />
                    <div className="overflow-hidden">
                      <p className="font-mono text-[10px] font-black uppercase truncate">{selectedKomunitas.creator?.name}</p>
                      <p className="text-[9px] font-mono text-[#C19A6B]">LEADER</p>
                    </div>
                  </div>
                  {/* Fake Member list based on count */}
                  {Array.from({ length: Math.min(selectedKomunitas.member_count - 1, 10) }).map((_, i) => (
                    <div key={i} className="flex items-center gap-3 p-2 hover:bg-[#FAF6F0] cursor-pointer">
                      <img src={`https://ui-avatars.com/api/?name=Member+${i}&background=eee&color=000`} alt="Member" className="w-8 h-8 object-cover border border-[#1A0F0A]/20" />
                      <div>
                        <p className="font-mono text-[10px] font-bold uppercase">Member {i+1}</p>
                        <p className="text-[9px] font-mono text-[#1A0F0A]/50">MEMBER</p>
                      </div>
                    </div>
                  ))}
                  {selectedKomunitas.member_count > 11 && (
                    <p className="text-center text-[10px] font-mono text-[#1A0F0A]/50 pt-2">+ {selectedKomunitas.member_count - 11} lainnya</p>
                  )}
                </div>
              </div>

              {/* Feed area */}
              <div className="w-full md:w-3/4 flex flex-col bg-[#FAF6F0]">
                <div className="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar" ref={feedRef}>
                  {selectedKomunitas.replies?.length === 0 ? (
                    <div className="flex flex-col items-center justify-center h-full opacity-50">
                      <MessageSquareText size={48} className="mb-4" />
                      <p className="font-mono font-bold uppercase text-sm">Belum ada obrolan.</p>
                    </div>
                  ) : (
                    <div className="space-y-6">
                      {[...selectedKomunitas.replies].reverse().map((reply) => (
                        <div key={reply.id} className="flex gap-4">
                          <img src={reply.user?.avatar_url} alt="Avatar" className="w-10 h-10 border-2 border-[#1A0F0A] shrink-0 cursor-pointer" onClick={() => setPublicProfileUser(reply.user)} />
                          <div className="flex-1">
                            <div className="flex items-baseline gap-2 mb-1">
                              <span className="font-mono text-xs font-black uppercase cursor-pointer hover:text-[#C19A6B]" onClick={() => setPublicProfileUser(reply.user)}>
                                {reply.user?.name}
                              </span>
                            </div>
                            <div className="bg-white border-2 border-[#1A0F0A] p-3 text-sm inline-block">
                              {reply.comment}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
                <form onSubmit={handleSendKomunitasPost} className="border-t-2 border-[#1A0F0A] bg-white p-4 shrink-0 flex gap-2">
                  <input 
                    type="text" 
                    value={komunitasPostInput} 
                    onChange={(e) => setKomunitasPostInput(e.target.value)} 
                    placeholder={`Ngobrol di ${selectedKomunitas.title}...`} 
                    className="flex-1 border-2 border-[#1A0F0A] px-4 py-2 outline-none focus:border-[#C19A6B] text-sm"
                  />
                  <button type="submit" className={`${btnPrimary} px-6`}>Kirim</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
