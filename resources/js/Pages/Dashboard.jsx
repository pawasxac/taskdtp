import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
  Bell, Check, MapPin, MessageCircleMore, MessageSquareText,
  Save, Send, X, ShieldCheck, Plus, Settings, Trash2, Search, Upload
} from 'lucide-react';
import Navbar from '../Components/Navbar';
import CustomCursor from '../Components/CustomCursor';
import PublicProfileModal from '../Components/PublicProfileModal';

const getAvatarUrl = (user, fallbackLabel = 'Anak Skena') => {
  const pic = user?.profile_picture;
  if (pic?.startsWith('http') || pic?.startsWith('blob:') || pic?.startsWith('data:')) return pic;
  if (user?.avatar_url) return user.avatar_url;
  if (pic) return `/uploads/profile_pictures/${pic}`;
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
  kecamatans = [],
}) {
  const { props, url: pageUrl } = usePage();
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
    kecamatan_id: currentUser?.kecamatan_id || '',
  });
  const [profilePreview, setProfilePreview] = useState(currentUser?.profile_picture || '');
  const [dmInput, setDmInput] = useState('');
  const [alert, setAlert] = useState(null);

  useEffect(() => {
    if (props.flash?.success) {
      setAlert({ type: 'success', message: props.flash.success });
      const timer = setTimeout(() => setAlert(null), 5000);
      return () => clearTimeout(timer);
    }
    if (props.flash?.error) {
      setAlert({ type: 'error', message: props.flash.error });
      const timer = setTimeout(() => setAlert(null), 5000);
      return () => clearTimeout(timer);
    }
  }, [props.flash]);

  // Modals State
  const [resultModal, setResultModal] = useState(null);
  const [publicProfileUser, setPublicProfileUser] = useState(null);
  const [createKomunitasOpen, setCreateKomunitasOpen] = useState(false);
  const [komunitasForm, setKomunitasForm] = useState({ nama_komunitas: '', deskripsi: '', domisili: '' });
  const [selectedKomunitas, setSelectedKomunitas] = useState(null);
  const [komunitasPostInput, setKomunitasPostInput] = useState('');

  const [globalReplyTo, setGlobalReplyTo] = useState(null);
  const [communityReplyTo, setCommunityReplyTo] = useState(null);
  const [deleteTarget, setDeleteTarget] = useState(null);

  // Community Management States
  const [manageKomunitasOpen, setManageKomunitasOpen] = useState(false);
  const [manageTab, setManageTab] = useState('profile'); // 'profile' | 'members'
  const [editKomunitasForm, setEditKomunitasForm] = useState({
    nama_komunitas: '',
    deskripsi: '',
    domisili: '',
    photo: null,
  });
  const [editKomunitasPreview, setEditKomunitasPreview] = useState('');
  const [memberSearchQuery, setMemberSearchQuery] = useState('');
  const [memberSearchResults, setMemberSearchResults] = useState([]);
  const [manageSaving, setManageSaving] = useState(false);
  const [kickConfirmTarget, setKickConfirmTarget] = useState(null);

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
  const lastScrolledUrl = useRef('');

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

  useEffect(() => {
    if (selectedKomunitas) {
      setEditKomunitasForm({
        nama_komunitas: selectedKomunitas.title || '',
        deskripsi: selectedKomunitas.description || '',
        domisili: selectedKomunitas.domisili || '',
        photo: null,
      });
      setEditKomunitasPreview(selectedKomunitas.cover_image || '');
    }
  }, [selectedKomunitas]);

  // Mark direct messages as read when a thread is selected
  useEffect(() => {
    if (selectedDmId) {
      const currentDm = localDirectMessages.find(d => d.id === selectedDmId);
      if (currentDm && currentDm.user?.id && currentDm.user.id !== 99999 && !String(currentDm.id).startsWith('fallback-')) {
        router.post('/dm/read', { contact_id: currentDm.user.id }, { preserveScroll: true });
      }
    }
  }, [selectedDmId]);

  // Mark community posts as read when community is selected
  useEffect(() => {
    if (selectedKomunitas && !String(selectedKomunitas.id).startsWith('fallback-') && !String(selectedKomunitas.id).startsWith('circle-')) {
      router.post(`/komunitas/${selectedKomunitas.id}/read`, {}, { preserveScroll: true });
    }
  }, [selectedKomunitas?.id]);

  // Connect to SSE stream with error handling
  // Removed fake SSE stream to use real DB data
  
  // Polling for new Global Chat messages and DMs every 5 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      router.reload({ only: ['globalChat', 'directMessages', 'forums'], preserveScroll: true, preserveState: true });
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    const focus = params.get('focus');
    
    // Only scroll if the search query changed (e.g. user clicked a notification)
    if (window.location.search !== lastScrolledUrl.current) {
      lastScrolledUrl.current = window.location.search;
      if (tab === 'forum' && forumRef.current) {
        forumRef.current.scrollIntoView({ behavior: 'smooth' });
      } else if (tab === 'dm' && dmRef.current) {
        dmRef.current.scrollIntoView({ behavior: 'smooth' });
      }
    }

    if (tab === 'forum' && focus && Array.isArray(forums)) {
      const community = forums.find(f => f.community_slug === focus || String(f.id) === focus);
      if (community) {
        setSelectedKomunitas(community);
      }
    } else if (tab === 'dm' && focus) {
      setSelectedDmId(focus);
    }
  }, [forums, pageUrl]);

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
    if (profileForm.kecamatan_id) formData.append('kecamatan_id', profileForm.kecamatan_id);
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
        setResultModal({
          type: 'success',
          title: 'Pengajuan Terkirim!',
          message: isAdmin 
            ? 'Komunitas berhasil didirikan dan langsung aktif!' 
            : 'Request pendaftaran komunitas Anda telah berhasil terkirim. Silakan tunggu persetujuan dari admin. Anda bisa memantau perkembangannya pada menu Notifikasi.'
        });
      },
      onError: (errors) => {
        setResultModal({
          type: 'error',
          title: 'Pengajuan Gagal',
          message: Object.values(errors).join('\n') || 'Terjadi kesalahan saat mendirikan komunitas.'
        });
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

  const handleUpdateKomunitas = (e) => {
    e.preventDefault();
    if (!selectedKomunitas) return;
    setManageSaving(true);

    const formData = new FormData();
    formData.append('nama_komunitas', editKomunitasForm.nama_komunitas);
    formData.append('deskripsi', editKomunitasForm.deskripsi || '');
    formData.append('domisili', editKomunitasForm.domisili);
    if (editKomunitasForm.photo) {
      formData.append('photo', editKomunitasForm.photo);
    }

    router.post(`/komunitas/${selectedKomunitas.id}/update`, formData, {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        setManageSaving(false);
        setManageKomunitasOpen(false);
      },
      onError: () => setManageSaving(false),
    });
  };

  const handleKickMember = (userId, memberName) => {
    if (!selectedKomunitas) return;
    setKickConfirmTarget({ id: userId, name: memberName });
  };

  const executeKickMember = () => {
    if (!selectedKomunitas || !kickConfirmTarget) return;
    router.post(`/komunitas/${selectedKomunitas.id}/kick/${kickConfirmTarget.id}`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        setKickConfirmTarget(null);
      }
    });
  };

  useEffect(() => {
    if (!memberSearchQuery.trim() || !selectedKomunitas) {
      setMemberSearchResults([]);
      return;
    }

    const delayDebounceFn = setTimeout(() => {
      fetch(`/komunitas/${selectedKomunitas.id}/search-users?q=${encodeURIComponent(memberSearchQuery)}`)
        .then(res => res.json())
        .then(data => {
          setMemberSearchResults(Array.isArray(data) ? data : []);
        })
        .catch(err => console.error("Error searching users:", err));
    }, 300);

    return () => clearTimeout(delayDebounceFn);
  }, [memberSearchQuery, selectedKomunitas?.id]);

  const handleAddMember = (userId) => {
    if (!selectedKomunitas) return;
    router.post(`/komunitas/${selectedKomunitas.id}/add-member`, { user_id: userId }, {
      preserveScroll: true,
      onSuccess: () => {
        setMemberSearchQuery('');
        setMemberSearchResults([]);
      }
    });
  };

  const handleDeleteMessage = (id, chatType, deleteType) => {
    const cleanId = String(id).replace('dm-', '');
    if (chatType === 'dm') {
      router.post(`/dm/${cleanId}/delete`, { type: deleteType }, { preserveScroll: true });
    } else if (chatType === 'community') {
      router.post(`/komunitas/post/${cleanId}/delete`, { type: deleteType }, { preserveScroll: true });
    }
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

      {/* FLASH TOAST NOTIFICATION */}
      {alert && (
        <div className="fixed top-24 left-1/2 -translate-x-1/2 z-[300] w-[90%] max-w-md animate-in slide-in-from-top-4 duration-300">
          <div className={`border-2 border-[#1A0F0A] p-4 font-mono text-xs font-black uppercase tracking-wide flex justify-between items-center shadow-[4px_4px_0px_0px_#1A0F0A] ${
            alert.type === 'success' ? 'bg-[#C19A6B] text-[#1A0F0A]' : 'bg-red-500 text-white'
          }`}>
            <span>{alert.message}</span>
            <button onClick={() => setAlert(null)} className="ml-4 hover:scale-110 transition-transform">
              <X size={16} />
            </button>
          </div>
        </div>
      )}

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
        </div>

        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-min">
          
          {/* PROFILE CARD - spans 8 cols */}
          <div className="md:col-span-12 lg:col-span-8 border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] p-5 shadow-[6px_6px_0px_0px_#C19A6B] flex flex-col justify-between">
            <div className="flex flex-col md:flex-row gap-6 items-start md:items-center">
              <div className="relative group cursor-pointer" onClick={() => fileInputRef.current?.click()}>
                <img
                  src={getAvatarUrl({ ...currentUser, ...profileForm, profile_picture: profilePreview || profileForm.profile_picture }, profileForm.name)}
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
                <div className="flex items-center gap-2 mb-2">
                  <p className="font-mono text-xs uppercase tracking-widest text-[#FAF6F0]/60">@{profileForm.username}</p>
                  {currentUser?.kecamatan && (
                    <span className="font-mono text-[10px] uppercase bg-[#C19A6B] text-[#1A0F0A] px-2 py-0.5 font-bold">
                      {currentUser.kecamatan.name}
                    </span>
                  )}
                </div>
                <form onSubmit={handleSaveProfile} className="flex flex-col gap-2 w-full mt-3">
                  <input
                    type="text"
                    value={profileForm.bio}
                    onChange={(e) => setProfileForm(s => ({ ...s, bio: e.target.value }))}
                    placeholder="Bio singkat kamu..."
                    className="w-full bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-sm py-1 text-[#FAF6F0]"
                  />
                  <div className="flex gap-2">
                    <select
                      value={profileForm.kecamatan_id}
                      onChange={(e) => setProfileForm(s => ({ ...s, kecamatan_id: e.target.value }))}
                      className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0] min-w-[100px]"
                    >
                      <option value="" className="text-[#1A0F0A]">Pilih Kecamatan</option>
                      {(kecamatans || []).map(kec => (
                        <option key={kec.id} value={kec.id} className="text-[#1A0F0A]">{kec.name}</option>
                      ))}
                    </select>
                    <input type="text" value={profileForm.instagram} onChange={e => setProfileForm(s => ({...s, instagram: e.target.value}))} placeholder="IG handle" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
                    <input type="text" value={profileForm.whatsapp} onChange={e => setProfileForm(s => ({...s, whatsapp: e.target.value}))} placeholder="WA nomor" className="flex-1 bg-transparent border-b-2 border-[#FAF6F0]/30 focus:border-[#C19A6B] outline-none font-mono text-[10px] py-1 text-[#FAF6F0]" />
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
                            <div className="relative shrink-0">
                                <img src={getAvatarUrl(dm.user)} alt="User" className="w-10 h-10 border-2 border-[#1A0F0A] object-cover" />
                                <span className={`absolute bottom-0 right-0 block w-2.5 h-2.5 rounded-full border border-white ${dm.user?.is_online ? 'bg-green-500' : 'bg-gray-400'}`} />
                            </div>
                            <div className="overflow-hidden">
                                <p className="font-mono text-[10px] font-black uppercase truncate">{dm.user?.name}</p>
                                <p className="text-xs text-[#1A0F0A]/60 truncate">{dm.messages?.[dm.messages.length - 1]?.text || 'Mulai chat...'}</p>
                            </div>
                        </button>
                    ))}
                </div>

                {/* Right Pane (Chat Window) */}
                <div className="w-2/3 flex flex-col bg-[#FAF6F0]">
                    {selectedDm && (
                        <div className="border-b-2 border-[#1A0F0A] px-4 py-2 bg-[#FAF6F0] flex justify-between items-center shrink-0">
                            <div className="flex items-center gap-2">
                                <span className="font-mono text-xs font-black uppercase">{selectedDm.user?.name}</span>
                                <span className={`inline-block w-2.5 h-2.5 rounded-full border border-[#1A0F0A] ${selectedDm.user?.is_online ? 'bg-green-500' : 'bg-gray-400'}`} title={selectedDm.user?.is_online ? 'Online' : 'Offline'} />
                                <span className="font-mono text-[9px] uppercase text-[#1A0F0A]/60">{selectedDm.user?.is_online ? 'Online' : 'Offline'}</span>
                            </div>
                            <button 
                                onClick={() => setPublicProfileUser(selectedDm.user)}
                                className="magnetic px-2.5 py-1 border-2 border-[#1A0F0A] bg-white text-[10px] font-mono uppercase font-black hover:bg-[#C19A6B] hover:text-[#1A0F0A] transition-colors shadow-[2px_2px_0px_0px_#1A0F0A] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#1A0F0A]"
                            >
                                Cek Profil
                            </button>
                        </div>
                    )}
                    <div ref={dmChatRef} className="flex-1 overflow-y-auto p-4 custom-scrollbar bg-[#e5ddd5]">
                        {selectedDm ? selectedDm.messages?.map(m => (
                            <div key={m.id} className={`mb-3 flex ${m.user?.id === currentUser?.id ? 'justify-end' : 'justify-start'} group relative`}>
                                <div className={`max-w-[80%] rounded-2xl px-4 py-2 ${m.user?.id === currentUser?.id ? 'bg-[#dcf8c6] rounded-tr-none' : 'bg-white rounded-tl-none'} flex flex-col`}>
                                    <div className="break-words">{m.text}</div>
                                    <div className="flex items-center justify-end gap-1.5 mt-1 self-end">
                                        {m.user?.id === currentUser?.id && !m.is_deleted_for_everyone && (
                                            <span className={`text-[10px] font-bold ${m.read_at ? 'text-blue-500' : 'text-gray-400'}`} title={m.read_at ? 'Dibaca' : 'Terkirim'}>✓✓</span>
                                        )}
                                        {!m.is_deleted_for_everyone && m.id && String(m.id).startsWith('dm-') && (
                                            <button
                                                onClick={() => setDeleteTarget({ id: m.id, chatType: 'dm', isAuthor: m.user?.id === currentUser?.id })}
                                                className="opacity-0 group-hover:opacity-100 ml-1 text-gray-500 hover:text-red-500 text-[10px] font-mono cursor-pointer"
                                                title="Hapus pesan"
                                            >
                                                Hapus
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )) : (
                            <div className="h-full flex items-center justify-center font-mono text-xs text-[#1A0F0A]/50">Pilih obrolan di samping.</div>
                        )}

                        {(selectedDm?.total_messages_count ?? selectedDm?.messages?.length ?? 0) >= 10 && (
                            <div className="mt-6 p-4 border-2 border-[#C19A6B] bg-white text-center">
                                <p className="font-clash font-black uppercase text-[#C19A6B] mb-2">Udah 10 pesan nih!</p>
                                <p className="text-xs mb-4">Lanjut kenalan lebih deket lewat sosmed gih.</p>
                                <div className="flex justify-center gap-2">
                                    {selectedDm.user?.instagram && <a href={`https://instagram.com/${selectedDm.user.instagram.replace('@', '')}`} target="_blank" rel="noreferrer" className="text-[10px] font-mono border-2 border-[#1A0F0A] px-2 py-1 bg-[#1A0F0A] text-white">IG</a>}
                                    {selectedDm.user?.whatsapp && <a href={`https://wa.me/${selectedDm.user.whatsapp}`} target="_blank" rel="noreferrer" className="text-[10px] font-mono border-2 border-[#1A0F0A] px-2 py-1 bg-[#1A0F0A] text-white">WA</a>}
                                </div>
                            </div>
                        )}
                    </div>
                    {selectedDm && (
                        (selectedDm?.total_messages_count ?? selectedDm?.messages?.length ?? 0) < 10 ? (
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
                <select
                  required
                  value={komunitasForm.domisili}
                  onChange={e => setKomunitasForm(s => ({...s, domisili: e.target.value}))}
                  className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B] bg-white font-mono text-xs uppercase"
                >
                  <option value="">Pilih Wilayah (Sidoarjo)</option>
                  {(kecamatans || []).map(k => (
                    <option key={k.id} value={k.name}>
                      {k.name}
                    </option>
                  ))}
                </select>
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
                <div className="flex items-center gap-2">
                  {selectedKomunitas.is_leader && (
                    <button 
                      onClick={() => setManageKomunitasOpen(true)} 
                      className="bg-[#C19A6B] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] text-[#1A0F0A] border-2 border-[#1A0F0A] px-3 py-1.5 text-xs font-black uppercase font-mono shadow-[2px_2px_0px_0px_#1A0F0A] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_0px_#1A0F0A] transition-all"
                    >
                      Kelola Komunitas
                    </button>
                  )}
                  <div className="hidden md:block bg-white/10 backdrop-blur-sm border border-white/20 px-4 py-2 text-white text-xs font-mono">
                    {selectedKomunitas.member_count} Members
                  </div>
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
                    <div className="relative shrink-0">
                      <img src={selectedKomunitas.creator?.avatar_url} alt="Owner" className="w-8 h-8 object-cover border border-[#1A0F0A]" />
                      <span className={`absolute bottom-0 right-0 block w-2.5 h-2.5 rounded-full border border-white ${selectedKomunitas.creator?.is_online ? 'bg-green-500' : 'bg-gray-400'}`} />
                    </div>
                    <div className="overflow-hidden">
                      <p className="font-mono text-[10px] font-black uppercase truncate">{selectedKomunitas.creator?.name}</p>
                      <p className="text-[9px] font-mono text-[#C19A6B]">LEADER</p>
                    </div>
                  </div>
                  {/* Actual Member list */}
                  {(selectedKomunitas.members ?? [])
                    .filter(m => m.id !== selectedKomunitas.creator?.id)
                    .slice(0, 10)
                    .map((m) => (
                      <div key={m.id || m.username} className="flex items-center gap-3 p-2 hover:bg-[#FAF6F0] cursor-pointer" onClick={() => m.id && setPublicProfileUser(m)}>
                        <div className="relative shrink-0">
                          <img src={m.avatar_url} alt="Member" className="w-8 h-8 object-cover border border-[#1A0F0A]/20" />
                          <span className={`absolute bottom-0 right-0 block w-2 h-2 rounded-full border border-white ${m.is_online ? 'bg-green-500' : 'bg-gray-400'}`} />
                        </div>
                        <div className="overflow-hidden">
                          <p className="font-mono text-[10px] font-bold uppercase truncate">{m.name}</p>
                          <p className="text-[9px] font-mono text-[#1A0F0A]/50 uppercase">{m.role || 'MEMBER'}</p>
                        </div>
                      </div>
                    ))}
                  {(selectedKomunitas.members ?? []).filter(m => m.id !== selectedKomunitas.creator?.id).length > 10 && (
                    <p className="text-center text-[10px] font-mono text-[#1A0F0A]/50 pt-2">+ {(selectedKomunitas.members ?? []).filter(m => m.id !== selectedKomunitas.creator?.id).length - 10} lainnya</p>
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
                          <div className="relative shrink-0">
                            <img src={reply.user?.avatar_url} alt="Avatar" className="w-10 h-10 border-2 border-[#1A0F0A] cursor-pointer shrink-0" onClick={() => setPublicProfileUser(reply.user)} />
                            <span className={`absolute bottom-0 right-0 block w-2.5 h-2.5 rounded-full border border-white ${reply.user?.is_online ? 'bg-green-500' : 'bg-gray-400'}`} />
                          </div>
                          <div className="flex-1">
                            <div className="flex items-baseline gap-2 mb-1">
                              <span className="font-mono text-xs font-black uppercase cursor-pointer hover:text-[#C19A6B]" onClick={() => setPublicProfileUser(reply.user)}>
                                {reply.user?.name}
                              </span>
                              {reply.user?.id === currentUser?.id && !reply.is_deleted_for_everyone && (
                                <span className={`text-[10px] font-bold ${reply.read_by_users?.some(uid => uid !== currentUser.id) ? 'text-blue-500' : 'text-gray-400'}`} title={reply.read_by_users?.some(uid => uid !== currentUser.id) ? 'Dibaca' : 'Terkirim'}>✓✓</span>
                              )}
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
                          <div className="absolute top-0 right-0 mt-2 mr-2 flex gap-2 items-center">
                            <button onClick={() => setCommunityReplyTo(reply)} className="text-[#1A0F0A]/30 hover:text-[#C19A6B] hidden group-hover:block bg-white p-1 border border-[#1A0F0A]/20"><MessageSquareText size={14}/></button>
                            {!reply.is_deleted_for_everyone && reply.id && (
                              <button 
                                onClick={() => setDeleteTarget({ id: reply.id, chatType: 'community', isAuthor: reply.user?.id === currentUser?.id })} 
                                className="text-[#1A0F0A]/30 hover:text-red-500 hidden group-hover:block bg-white p-1 border border-[#1A0F0A]/20 cursor-pointer"
                                title="Hapus pesan"
                              >
                                <X size={14} />
                              </button>
                            )}
                          </div>
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
                <select
                  required
                  value={komunitasForm.domisili}
                  onChange={e => setKomunitasForm(s => ({...s, domisili: e.target.value}))}
                  className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B] bg-white font-mono text-xs uppercase"
                >
                  <option value="">Pilih Wilayah (Sidoarjo)</option>
                  {(kecamatans || []).map(k => (
                    <option key={k.id} value={k.name}>
                      {k.name}
                    </option>
                  ))}
                </select>
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

      {/* WHATSAPP-STYLE DELETE MESSAGE DIALOG */}
      {deleteTarget && (
        <div className="fixed inset-0 z-[250] flex items-center justify-center bg-[#1A0F0A]/80 backdrop-blur-sm p-4 animate-in fade-in duration-200">
          <div className="w-full max-w-sm border-2 border-[#1A0F0A] bg-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] animate-in zoom-in-95 duration-200">
            <h4 className="font-clash text-lg font-black uppercase tracking-tight mb-2 text-[#1A0F0A]">Hapus Pesan?</h4>
            <p className="text-xs text-[#1A0F0A]/70 mb-6 font-mono uppercase tracking-wide">Pilih opsi penghapusan pesan ini.</p>
            <div className="flex flex-col gap-2">
              <button
                type="button"
                onClick={() => {
                  handleDeleteMessage(deleteTarget.id, deleteTarget.chatType, 'me');
                  setDeleteTarget(null);
                }}
                className="w-full border-2 border-[#1A0F0A] bg-white text-[#1A0F0A] py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[3px_3px_0px_0px_#1A0F0A] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_#1A0F0A] transition-all cursor-pointer"
              >
                Hapus untuk saya
              </button>
              {deleteTarget.isAuthor && (
                <button
                  type="button"
                  onClick={() => {
                    handleDeleteMessage(deleteTarget.id, deleteTarget.chatType, 'everyone');
                    setDeleteTarget(null);
                  }}
                  className="w-full border-2 border-[#1A0F0A] bg-red-500 text-white py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[3px_3px_0px_0px_#1A0F0A] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_0px_#1A0F0A] transition-all cursor-pointer"
                >
                  Hapus untuk semua orang
                </button>
              )}
              <button
                type="button"
                onClick={() => setDeleteTarget(null)}
                className="w-full border-2 border-[#1A0F0A] bg-gray-200 text-[#1A0F0A] py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] hover:bg-gray-300 transition-colors cursor-pointer mt-2"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MANAGE KOMUNITAS MODAL */}
      {manageKomunitasOpen && selectedKomunitas && (
        <div className="fixed inset-0 z-[200] flex items-center justify-center bg-[#1A0F0A]/95 backdrop-blur-md p-4" onClick={() => setManageKomunitasOpen(false)}>
          <div className="w-full max-w-2xl bg-[#FAF6F0] border-2 border-[#1A0F0A] shadow-[8px_8px_0px_0px_#1A0F0A] flex flex-col h-[70vh] animate-in zoom-in-95" onClick={e => e.stopPropagation()}>
            {/* Header */}
            <div className="p-4 border-b-2 border-[#1A0F0A] flex justify-between items-center bg-white shrink-0">
              <div>
                <h3 className="font-clash text-xl font-black uppercase">Kelola Komunitas</h3>
                <p className="font-mono text-[9px] text-[#1A0F0A]/60 uppercase mt-0.5">{selectedKomunitas.title}</p>
              </div>
              <button onClick={() => setManageKomunitasOpen(false)} className="text-[#1A0F0A] hover:text-[#C19A6B]"><X size={20} /></button>
            </div>

            {/* Tabs Selector */}
            <div className="flex border-b-2 border-[#1A0F0A] bg-white shrink-0">
              <button 
                onClick={() => setManageTab('profile')} 
                className={`flex-1 py-3 text-xs font-mono font-black uppercase tracking-[0.1em] border-r-2 border-[#1A0F0A] ${manageTab === 'profile' ? 'bg-[#C19A6B] text-[#1A0F0A]' : 'bg-white hover:bg-gray-50'}`}
              >
                Edit Profil
              </button>
              <button 
                onClick={() => setManageTab('members')} 
                className={`flex-1 py-3 text-xs font-mono font-black uppercase tracking-[0.1em] ${manageTab === 'members' ? 'bg-[#C19A6B] text-[#1A0F0A]' : 'bg-white hover:bg-gray-50'}`}
              >
                Kelola Anggota
              </button>
            </div>

            {/* Modal Body */}
            <div className="flex-1 overflow-y-auto p-6 custom-scrollbar bg-white">
              {manageTab === 'profile' ? (
                <form onSubmit={handleUpdateKomunitas} className="space-y-4">
                  <div className="flex flex-col md:flex-row gap-6">
                    {/* Cover Preview & Upload */}
                    <div className="w-full md:w-1/3 flex flex-col items-center">
                      <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-2 self-start">Foto Sampul</label>
                      <div className="w-full aspect-video md:h-24 border-2 border-[#1A0F0A] relative overflow-hidden bg-gray-100 flex items-center justify-center">
                        {editKomunitasPreview ? (
                          <img src={editKomunitasPreview} className="w-full h-full object-cover" alt="Preview" />
                        ) : (
                          <span className="text-[10px] font-mono text-gray-400">Belum ada foto</span>
                        )}
                      </div>
                      <label className="mt-3 cursor-pointer w-full text-center border-2 border-[#1A0F0A] py-1.5 font-mono text-[9px] font-black uppercase tracking-[0.12em] bg-gray-100 hover:bg-[#C19A6B] transition-colors">
                        <Upload size={10} className="inline mr-1" /> Unggah Foto
                        <input 
                          type="file" 
                          accept="image/*" 
                          className="hidden" 
                          onChange={(e) => {
                            const file = e.target.files[0];
                            if (file) {
                              setEditKomunitasForm(s => ({ ...s, photo: file }));
                              setEditKomunitasPreview(URL.createObjectURL(file));
                            }
                          }} 
                        />
                      </label>
                    </div>

                    <div className="flex-1 space-y-4">
                      <div>
                        <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-1">Nama Komunitas</label>
                        <input 
                          required 
                          type="text" 
                          value={editKomunitasForm.nama_komunitas} 
                          onChange={e => setEditKomunitasForm(s => ({...s, nama_komunitas: e.target.value}))} 
                          className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B] text-sm" 
                        />
                      </div>
                      <div>
                        <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-1">Domisili / Wilayah</label>
                        <select 
                          required 
                          value={editKomunitasForm.domisili} 
                          onChange={e => setEditKomunitasForm(s => ({...s, domisili: e.target.value}))} 
                          className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B] bg-white font-mono text-xs uppercase" 
                        >
                          <option value="">Pilih Wilayah (Sidoarjo)</option>
                          {(kecamatans || []).map(k => (
                            <option key={k.id} value={k.name}>
                              {k.name}
                            </option>
                          ))}
                        </select>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-1">Deskripsi Komunitas</label>
                    <textarea 
                      rows="4" 
                      value={editKomunitasForm.deskripsi} 
                      onChange={e => setEditKomunitasForm(s => ({...s, deskripsi: e.target.value}))} 
                      className="w-full border-2 border-[#1A0F0A] px-3 py-2 outline-none focus:border-[#C19A6B] text-sm custom-scrollbar"
                    />
                  </div>

                  <div className="pt-2">
                    <button 
                      type="submit" 
                      disabled={manageSaving}
                      className={`${btnPrimary} w-full`}
                    >
                      {manageSaving ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </button>
                  </div>
                </form>
              ) : (
                <div className="space-y-6">
                  {/* Search and Add User form */}
                  <div className="border-2 border-[#1A0F0A] p-4 bg-[#FAF6F0] relative">
                    <h4 className="font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-2">Tambah Anggota Baru</h4>
                    <div className="relative">
                      <div className="flex border-2 border-[#1A0F0A] bg-white items-center px-3">
                        <Search size={16} className="text-[#1A0F0A]/50 mr-2" />
                        <input 
                          type="text" 
                          value={memberSearchQuery} 
                          onChange={e => setMemberSearchQuery(e.target.value)} 
                          placeholder="Cari nama atau username..." 
                          className="flex-1 py-2 outline-none text-xs bg-transparent" 
                        />
                        {memberSearchQuery && (
                          <button onClick={() => setMemberSearchQuery('')}><X size={14} /></button>
                        )}
                      </div>

                      {/* Dropdown Search Results */}
                      {memberSearchResults.length > 0 && (
                        <div className="absolute left-0 right-0 mt-1 border-2 border-[#1A0F0A] bg-white z-[210] shadow-[4px_4px_0px_0px_#1A0F0A] max-h-48 overflow-y-auto custom-scrollbar">
                          {memberSearchResults.map(user => (
                            <div key={user.id} className="flex items-center justify-between p-2 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                              <div className="flex items-center gap-2">
                                <img src={user.avatar_url} alt="Avatar" className="w-6 h-6 object-cover border border-[#1A0F0A]/20" />
                                <div className="text-[10px]">
                                  <p className="font-bold font-mono">{user.name}</p>
                                  <p className="text-[#1A0F0A]/50">@{user.username}</p>
                                </div>
                              </div>
                              <button 
                                onClick={() => handleAddMember(user.id)} 
                                className="bg-[#C19A6B] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] text-white border border-[#1A0F0A] px-2 py-1 text-[9px] font-mono font-bold uppercase tracking-wider"
                              >
                                Tambah
                              </button>
                            </div>
                          ))}
                        </div>
                      )}

                      {memberSearchQuery.trim() !== '' && memberSearchResults.length === 0 && (
                        <div className="absolute left-0 right-0 mt-1 border-2 border-[#1A0F0A] bg-white z-[210] p-3 text-center text-xs font-mono text-gray-400">
                          Tidak menemukan user...
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Members list */}
                  <div>
                    <h4 className="font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-3">Daftar Anggota</h4>
                    <div className="border-2 border-[#1A0F0A] divide-y-2 divide-[#1A0F0A] bg-white">
                      {(selectedKomunitas.members ?? []).map(member => (
                        <div key={member.id || member.username} className="flex items-center justify-between p-3">
                          <div className="flex items-center gap-3">
                            <div className="relative shrink-0">
                              <img src={member.avatar_url} alt="Avatar" className="w-8 h-8 object-cover border border-[#1A0F0A]" />
                              <span className={`absolute bottom-0 right-0 block w-2 h-2 rounded-full border border-white ${member.is_online ? 'bg-green-500' : 'bg-gray-400'}`} />
                            </div>
                            <div>
                              <p className="font-mono text-[10px] font-black uppercase">{member.name}</p>
                              <p className="text-[9px] font-mono text-[#1A0F0A]/50 uppercase">{member.role}</p>
                            </div>
                          </div>
                          {member.role !== 'leader' && member.id !== currentUser?.id && (
                            <button 
                              onClick={() => handleKickMember(member.id, member.name)} 
                              className="text-red-500 hover:text-red-700 p-1 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors"
                              title="Keluarkan anggota"
                            >
                              <Trash2 size={16} />
                            </button>
                          )}
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* KICK MEMBER CONFIRMATION DIALOG */}
      {kickConfirmTarget && (
        <div className="fixed inset-0 z-[260] flex items-center justify-center bg-[#1A0F0A]/85 backdrop-blur-sm p-4 animate-in fade-in duration-200">
          <div className="w-full max-w-sm border-2 border-[#1A0F0A] bg-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] animate-in zoom-in-95 duration-200">
            <h4 className="font-clash text-lg font-black uppercase tracking-tight mb-2 text-[#1A0F0A]">Keluarkan Anggota?</h4>
            <p className="text-xs text-[#1A0F0A]/70 mb-6 font-mono uppercase tracking-wide">
              Apakah Anda yakin ingin mengeluarkan <span className="text-[#C19A6B] font-bold">"{kickConfirmTarget.name}"</span> dari komunitas?
            </p>
            <div className="flex gap-3">
              <button
                type="button"
                onClick={executeKickMember}
                className="flex-1 border-2 border-[#1A0F0A] bg-red-500 text-white py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[3px_3px_0px_0px_#1A0F0A] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[2px_2px_0px_0px_#1A0F0A] transition-all cursor-pointer"
              >
                Keluarkan
              </button>
              <button
                type="button"
                onClick={() => setKickConfirmTarget(null)}
                className="flex-1 border-2 border-[#1A0F0A] bg-gray-200 text-[#1A0F0A] py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] hover:bg-gray-300 transition-colors cursor-pointer"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      )}

      {/* RESULT MODAL POPUP */}
      {resultModal && (
        <div className="fixed inset-0 z-[300] flex items-center justify-center bg-[#1A0F0A]/85 backdrop-blur-sm p-4 animate-in fade-in duration-200">
          <div className="w-full max-w-sm border-2 border-[#1A0F0A] bg-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] animate-in zoom-in-95 duration-200" onClick={e => e.stopPropagation()}>
            <h4 className="font-clash text-xl font-black uppercase tracking-tight mb-2 text-[#1A0F0A]">
              {resultModal.title}
            </h4>
            <p className="text-xs text-[#1A0F0A]/80 mb-6 font-mono uppercase tracking-wide leading-relaxed">
              {resultModal.message}
            </p>
            <button
              type="button"
              onClick={() => setResultModal(null)}
              className="w-full border-2 border-[#1A0F0A] bg-[#C19A6B] text-[#1A0F0A] py-2.5 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[4px_4px_0px_0px_#1A0F0A] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[3px_3px_0px_0px_#1A0F0A] transition-all cursor-pointer"
            >
              OK, Mengerti
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
