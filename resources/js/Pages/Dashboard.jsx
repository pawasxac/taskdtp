import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
  Bell, Check, MapPin, MessageCircleMore, MessageSquareText,
  Save, Send, X, ShieldCheck, Plus
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

const formatTime = () => {
  try {
    return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
  } catch (e) {
    const d = new Date();
    return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
  }
};



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

  const [localDirectMessages, setLocalDirectMessages] = useState(Array.isArray(directMessages) ? directMessages : []);
  const [chatMessages, setChatMessages] = useState(Array.isArray(globalChat) ? globalChat : []);
  const [chatInput, setChatInput] = useState('');
  const [selectedDmId, setSelectedDmId] = useState(Array.isArray(directMessages) && directMessages.length > 0 ? directMessages[0]?.id : null);
  const [notifOpen, setNotifOpen] = useState(false);
  const [profileSaving, setProfileSaving] = useState(false);
  const [profileSaved, setProfileSaved] = useState(false);

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
  const [dmInput, setDmInput] = useState('');

  // Modals State
  const [publicProfileUser, setPublicProfileUser] = useState(null);
  const [createKomunitasOpen, setCreateKomunitasOpen] = useState(false);
  const [komunitasForm, setKomunitasForm] = useState({ nama_komunitas: '', deskripsi: '', domisili: '' });
  const [selectedKomunitas, setSelectedKomunitas] = useState(null);
  const [komunitasPostInput, setKomunitasPostInput] = useState('');

  const [globalReplyTo, setGlobalReplyTo] = useState(null);
  const [communityReplyTo, setCommunityReplyTo] = useState(null);

  const renderMessageWithMentions = (text) => {
    if (!text) return null;
    const parts = text.split(/(@\w+)/g);
    return parts.map((part, i) => {
      if (part.startsWith('@')) {
        return <span key={i} className="text-[#C19A6B] font-bold">{part}</span>;
      }
      return <span key={i}>{part}</span>;
    });
  };

  const chatListRef = useRef(null);
  const notifRef = useRef(null);
  const forumRef = useRef(null);
  const dmRef = useRef(null);
  const dmChatRef = useRef(null);
  const feedRef = useRef(null);

  // Close notif popover on outside click
  useEffect(() => {
    if (!notifOpen) return;
    const handleOutside = (e) => {
      if (notifRef.current && !notifRef.current.contains(e.target)) {
        setNotifOpen(false);
      }
    };
    document.addEventListener('mousedown', handleOutside);
    return () => document.removeEventListener('mousedown', handleOutside);
  }, [notifOpen]);

  useEffect(() => { setChatMessages(Array.isArray(globalChat) ? globalChat : []); }, [globalChat]);
  useEffect(() => { 
    const serverDms = Array.isArray(directMessages) ? directMessages : [];
    
    setLocalDirectMessages(prev => {
      // Preserve local temporary threads that aren't synced to server yet
      const tempDms = prev.filter(dm => String(dm.id).startsWith('dm-new-') || String(dm.id).startsWith('dm-temp-'));
      const activeTempDms = tempDms.filter(tempDm => 
        !serverDms.some(serverDm => serverDm.user?.id === tempDm.user?.id)
      );
      
      // Also merge any new messages into existing temporary threads if necessary
      return [...activeTempDms, ...serverDms];
    });

    // When fresh DMs arrive from backend, check if we had a temporary DM selected
    // If so, select the real DM that corresponds to the same user
    if (serverDms.length > 0 && selectedDmId) {
      const isTempId = String(selectedDmId).startsWith('dm-new-');
      if (isTempId) {
        const userIdFromTempId = Number(String(selectedDmId).replace('dm-new-', ''));
        const realDm = serverDms.find(dm => dm.user?.id === userIdFromTempId);
        if (realDm) {
          setSelectedDmId(realDm.id);
        }
      } else {
        // We do NOT reset the selectedDmId if it is a temporary ID that hasn't been saved yet,
        // so we check if the selected DM is either in the server data OR is a temp DM.
        const existsInServer = serverDms.some(dm => dm.id === selectedDmId);
        const isStillTemp = String(selectedDmId).startsWith('dm-new-') || String(selectedDmId).startsWith('dm-temp-');
        
        if (!existsInServer && !isStillTemp) {
          setSelectedDmId(serverDms[0]?.id || null);
        }
      }
    }
  }, [directMessages]);
  useEffect(() => { if (chatListRef.current) chatListRef.current.scrollTop = chatListRef.current.scrollHeight; }, [chatMessages.length]);
  useEffect(() => { if (feedRef.current) feedRef.current.scrollTop = feedRef.current.scrollHeight; }, [selectedKomunitas?.replies?.length]);

  // Keep selectedKomunitas synchronized with updated forums data from backend
  useEffect(() => {
    if (selectedKomunitas && Array.isArray(forums)) {
      const updated = forums.find((f) => f.id === selectedKomunitas.id);
      if (updated) {
        setSelectedKomunitas(updated);
      }
    }
  }, [forums]);

  // Connect to SSE stream with error handling
  // Removed fake SSE stream to use real DB data
  
  // Polling for new Global Chat messages and DMs every 5 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      router.reload({ only: ['globalChat', 'directMessages'], preserveScroll: true, preserveState: true });
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    const focus = params.get('focus');
    
    if (tab === 'forum' && forumRef.current) {
      forumRef.current.scrollIntoView({ behavior: 'smooth' });
    } else if (tab === 'dm' && dmRef.current) {
      dmRef.current.scrollIntoView({ behavior: 'smooth' });
    }

    if (tab === 'forum' && focus && Array.isArray(forums)) {
      const community = forums.find(f => f.community_slug === focus || String(f.id) === focus);
      if (community) {
        setSelectedKomunitas(community);
      }
    } else if (tab === 'dm' && focus) {
      setSelectedDmId(focus);
    }
  }, [forums]);

  const selectedDm = useMemo(
    () => (Array.isArray(localDirectMessages) ? localDirectMessages.find((d) => d.id === selectedDmId) || localDirectMessages[0] : null) || null,
    [localDirectMessages, selectedDmId]
  );

  useEffect(() => { if (dmChatRef.current) dmChatRef.current.scrollTop = dmChatRef.current.scrollHeight; }, [selectedDm?.messages?.length]);

  const handleSendDm = (e) => {
    e.preventDefault();
    if (!dmInput.trim() || !selectedDm) return;
    
    const sentText = dmInput;
    setDmInput('');

    // Optimistically update the UI
    setLocalDirectMessages(prev =>
      prev.map(dm => {
        if (dm.id !== selectedDm.id) return dm;
        const newMsg = {
          id: `dm-temp-${Date.now()}`,
          text: sentText,
          user: {
            id: currentUser.id,
            name: currentUser.name,
            username: currentUser.username,
            profile_picture: currentUser.profile_picture,
          },
        };
        return { 
          ...dm, 
          last_message: sentText, 
          messages: [...(dm.messages || []), newMsg],
          time: formatTime()
        };
      })
    );

    // Only send to backend if it's not a fallback user
    if (!selectedDm.user?.id || selectedDm.user.id === 99999 || String(selectedDm.id).startsWith('fallback-')) {
      return;
    }

    router.post('/dm/send', {
      receiver_id: selectedDm.user.id,
      message: sentText,
    }, { preserveScroll: true });
  };

  const handleSendChat = (e) => {
    e.preventDefault();
    if (!chatInput.trim()) return;

    const text = chatInput;
    setChatInput('');
    const replyId = globalReplyTo?.id?.replace('chat-', '');
    setGlobalReplyTo(null);

    router.post('/chat/send', { message: text, reply_to_id: replyId }, {
      preserveScroll: true,
    });
  };

  const handleSaveProfile = (e) => {
    e.preventDefault();
    if (profileSaving) return;
    setProfileSaving(true);
    const formData = new FormData();
    if (profileForm.name)      formData.append('name', profileForm.name);
    if (profileForm.bio !== undefined && profileForm.bio !== null) formData.append('bio', profileForm.bio);
    if (profileForm.instagram !== undefined && profileForm.instagram !== null) formData.append('instagram', profileForm.instagram);
    if (profileForm.whatsapp !== undefined && profileForm.whatsapp !== null) formData.append('whatsapp', profileForm.whatsapp);
    if (profileForm.discord !== undefined && profileForm.discord !== null) formData.append('discord', profileForm.discord);
    if (profileForm.profile_picture_file) formData.append('profile_picture', profileForm.profile_picture_file);
    router.post('/profile/update', formData, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        setProfileSaving(false);
        setProfileSaved(true);
        setTimeout(() => setProfileSaved(false), 2500);
      },
      onError: () => setProfileSaving(false),
    });
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
    // Guard: fallback entries (id starts with 'fallback-' or 'circle-') have no real DB record
    if (String(selectedKomunitas.id).startsWith('fallback-') || String(selectedKomunitas.id).startsWith('circle-')) {
      setKomunitasPostInput('');
      return;
    }

    const replyId = communityReplyTo?.id;
    setCommunityReplyTo(null);

    router.post(`/komunitas/${selectedKomunitas.id}/post`, { content: komunitasPostInput, reply_to_id: replyId }, {
      preserveScroll: true,
      onSuccess: () => setKomunitasPostInput(''),
    });
  };

  const btnPrimary = 'magnetic inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-4 py-2 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors cursor-pointer';
  const fileInputRef = useRef(null);

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setProfilePreview(URL.createObjectURL(file));
      setProfileForm(s => ({ ...s, profile_picture_file: file }));
    }
  };

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Dashboard Roastery Skena" />
      <Navbar current="dashboard" notifications={notifications} />
      <CustomCursor />

      {publicProfileUser && (
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
                    time: formatTime(),
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
              <div className="relative group cursor-pointer" onClick={() => fileInputRef.current?.click()}>
                <img
                  src={getAvatarUrl({ ...profileForm, profile_picture: profilePreview || profileForm.profile_picture }, profileForm.name)}
                  alt="Avatar"
                  className="h-24 w-24 border-2 border-[#FAF6F0] object-cover group-hover:opacity-50 transition-opacity"
                />
                <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                   <p className="font-mono text-[10px] font-black uppercase bg-[#1A0F0A]/80 px-2 py-1">Ganti</p>
                </div>
                <input type="file" ref={fileInputRef} className="hidden" accept="image/*" onChange={handleFileChange} />
              </div>
              <div className="flex-1 w-full">
                <div className="flex items-center gap-2 mb-1">
                  <input
                    type="text"
                    value={profileForm.name}
                    onChange={(e) => setProfileForm(s => ({ ...s, name: e.target.value }))}
                    className="font-clash text-2xl md:text-3xl font-black uppercase tracking-wide bg-transparent outline-none border-b-2 border-transparent focus:border-[#C19A6B] text-[#FAF6F0] placeholder-[#FAF6F0]/50"
                    placeholder="NAMA KAMU"
                  />
                  {isAdmin && <ShieldCheck size={20} className="text-[#C19A6B]" />}
                </div>
                <p className="font-mono text-xs uppercase tracking-widest text-[#FAF6F0]/60 mb-2">@{profileForm.username}</p>
                <form onSubmit={handleSaveProfile} className="flex flex-col gap-2 w-full mt-3">
                  <input
                    type="text"
                    value={profileForm.bio}
                    onChange={(e) => setProfileForm(s => ({ ...s, bio: e.target.value }))}
                    placeholder="Bio singkat kamu..."
                    className="w-full bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-sm py-1 text-[#FAF6F0]"
                  />
                  <div className="flex gap-2">
                    <input type="text" value={profileForm.instagram} onChange={e => setProfileForm(s => ({...s, instagram: e.target.value}))} placeholder="IG handle" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                    <input type="text" value={profileForm.whatsapp} onChange={e => setProfileForm(s => ({...s, whatsapp: e.target.value}))} placeholder="WA nomor" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                    <input type="text" value={profileForm.discord} onChange={e => setProfileForm(s => ({...s, discord: e.target.value}))} placeholder="Discord ID" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                  </div>
                  <div className="flex items-center gap-3 mt-1">
                    <button
                      type="submit"
                      disabled={profileSaving}
                      className="inline-flex items-center gap-2 border-2 border-[#C19A6B] bg-[#C19A6B] text-[#1A0F0A] px-3 py-1.5 font-mono text-[10px] font-black uppercase tracking-wider hover:bg-[#FAF6F0] transition-colors disabled:opacity-50"
                    >
                      {profileSaving ? <span className="inline-block w-3 h-3 border-2 border-[#1A0F0A] border-t-transparent rounded-full animate-spin" /> : <Save size={12} />}
                      {profileSaving ? 'Nyimpen...' : 'Simpan Profil'}
                    </button>
                    {profileSaved && (
                      <span className="inline-flex items-center gap-1 font-mono text-[10px] text-[#C19A6B] uppercase font-black animate-in fade-in">
                        <Check size={12} /> Tersimpan!
                      </span>
                    )}
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
                <div key={msg.id} className="border border-[#1A0F0A]/20 bg-[#FAF6F0] p-2 relative group">
                  {msg.reply_to && (
                    <div className="border-l-2 border-[#C19A6B] pl-2 mb-2 text-xs text-[#1A0F0A]/60 bg-white p-1 line-clamp-1">
                       <span className="font-bold text-[#1A0F0A]">@{msg.reply_to.user?.username}:</span> {msg.reply_to.text}
                    </div>
                  )}
                  <div className="flex justify-between items-center mb-1">
                    <span 
                        className="font-mono text-[10px] font-bold uppercase cursor-pointer hover:text-[#C19A6B]"
                        onClick={() => setPublicProfileUser(msg.user)}
                    >{msg.user?.name || msg.user?.username}</span>
                    <span className="font-mono text-[9px] text-[#1A0F0A]/50">{msg.time}</span>
                  </div>
                  <p className="text-sm leading-snug">{renderMessageWithMentions(msg.text)}</p>
                  <button onClick={() => setGlobalReplyTo(msg)} className="absolute top-2 right-2 text-[#1A0F0A]/30 hover:text-[#C19A6B] hidden group-hover:block bg-white p-1 border border-[#1A0F0A]/20 rounded"><MessageSquareText size={14}/></button>
                </div>
              ))}
            </div>
            <form onSubmit={handleSendChat} className="border-t-2 border-[#1A0F0A] flex flex-col shrink-0 bg-white">
              {globalReplyTo && (
                <div className="bg-[#FAF6F0] px-3 py-2 flex items-center justify-between border-b border-[#1A0F0A]/20">
                  <div className="text-xs truncate">
                    <span className="font-bold text-[#C19A6B]">Membalas {globalReplyTo.user?.name}:</span> {globalReplyTo.text}
                  </div>
                  <button type="button" onClick={() => setGlobalReplyTo(null)} className="text-[#1A0F0A]/50 hover:text-[#1A0F0A]"><X size={14}/></button>
                </div>
              )}
              <div className="flex">
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
              </div>
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
                    <div ref={dmChatRef} className="flex-1 overflow-y-auto p-4 custom-scrollbar bg-[#e5ddd5]">
                        {selectedDm ? selectedDm.messages?.map(m => (
                            <div key={m.id} className={`mb-3 flex ${m.user?.id === currentUser?.id ? 'justify-end' : 'justify-start'}`}>
                                <div className={`max-w-[80%] rounded-2xl px-4 py-2 ${m.user?.id === currentUser?.id ? 'bg-[#dcf8c6] rounded-tr-none' : 'bg-white rounded-tl-none'}`}>
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
              {(forums || []).map(f => (
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
                  {(selectedKomunitas.replies ?? []).length === 0 ? (
                    <div className="flex flex-col items-center justify-center h-full opacity-50">
                      <MessageSquareText size={48} className="mb-4" />
                      <p className="font-mono font-bold uppercase text-sm">Belum ada obrolan. Jadi yang pertama ngobrol!</p>
                    </div>
                  ) : (
                    <div className="space-y-6">
                      {[...(selectedKomunitas.replies ?? [])].reverse().map((reply) => (
                        <div key={reply.id} className="flex gap-4 group relative">
                          <img src={reply.user?.avatar_url} alt="Avatar" className="w-10 h-10 border-2 border-[#1A0F0A] shrink-0 cursor-pointer" onClick={() => setPublicProfileUser(reply.user)} />
                          <div className="flex-1">
                            <div className="flex items-baseline gap-2 mb-1">
                              <span className="font-mono text-xs font-black uppercase cursor-pointer hover:text-[#C19A6B]" onClick={() => setPublicProfileUser(reply.user)}>
                                {reply.user?.name}
                              </span>
                            </div>
                            <div className="bg-white border-2 border-[#1A0F0A] p-3 text-sm inline-block">
                              {reply.reply_to && (
                                <div className="border-l-2 border-[#C19A6B] pl-2 mb-2 text-xs text-[#1A0F0A]/60 bg-[#FAF6F0] p-1">
                                   <span className="font-bold text-[#1A0F0A]">@{reply.reply_to.user?.username}:</span> {reply.reply_to.comment}
                                </div>
                              )}
                              {renderMessageWithMentions(reply.comment)}
                            </div>
                          </div>
                          <button onClick={() => setCommunityReplyTo(reply)} className="absolute top-0 right-0 mt-2 mr-2 text-[#1A0F0A]/30 hover:text-[#C19A6B] hidden group-hover:block bg-white p-1 border border-[#1A0F0A]/20"><MessageSquareText size={14}/></button>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
                {selectedKomunitas.is_member ? (
                  <form onSubmit={handleSendKomunitasPost} className="border-t-2 border-[#1A0F0A] bg-white p-4 shrink-0 flex flex-col gap-2">
                    {communityReplyTo && (
                      <div className="bg-[#FAF6F0] px-3 py-2 flex items-center justify-between border border-[#1A0F0A]/20 text-xs">
                        <div className="truncate">
                          <span className="font-bold text-[#C19A6B]">Membalas {communityReplyTo.user?.name}:</span> {communityReplyTo.comment}
                        </div>
                        <button type="button" onClick={() => setCommunityReplyTo(null)} className="text-[#1A0F0A]/50 hover:text-[#1A0F0A] ml-2"><X size={14}/></button>
                      </div>
                    )}
                    <div className="flex gap-2">
                      <input 
                        type="text" 
                        value={komunitasPostInput} 
                        onChange={(e) => setKomunitasPostInput(e.target.value)} 
                        placeholder={`Ngobrol di ${selectedKomunitas.title}...`} 
                        className="flex-1 border-2 border-[#1A0F0A] px-4 py-2 outline-none focus:border-[#C19A6B] text-sm"
                      />
                      <button type="submit" className={`${btnPrimary} px-6`}>Kirim</button>
                    </div>
                  </form>
                ) : (
                  <div className="border-t-2 border-[#1A0F0A] bg-[#C19A6B]/10 p-4 shrink-0 text-center">
                    <p className="font-mono text-xs uppercase mb-3 text-[#1A0F0A]/70">Gabung dulu buat ikut ngobrol</p>
                    <button 
                      onClick={() => {
                        router.post(`/komunitas/${selectedKomunitas.id}/join`, {}, {
                          preserveScroll: true,
                          onSuccess: () => {
                            router.reload({ only: ['forums'] });
                            setSelectedKomunitas(prev => ({ ...prev, is_member: true, member_count: prev.member_count + 1 }));
                          }
                        });
                      }}
                      className={btnPrimary}
                    >
                      Gabung Komunitas
                    </button>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
