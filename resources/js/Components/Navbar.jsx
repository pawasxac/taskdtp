import React, { useEffect, useRef, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
  Bell,
  ChevronDown,
  Clock3,
  Coffee,
  Home,
  LayoutDashboard,
  LogOut,
  ShieldCheck,
  X,
} from 'lucide-react';

/**
 * Resolves the absolute avatar URL for the navbar.
 * - http(s) URLs are returned as-is
 * - relative filenames are resolved against /uploads/profile_pictures
 * - empty values fall back to a deterministic ui-avatars avatar
 */
const buildAvatarUrl = (user) => {
  if (!user) {
    return 'https://ui-avatars.com/api/?name=Ngopi+User&background=1A0F0A&color=FAF6F0&bold=true';
  }

  if (user.profile_picture?.startsWith('http')) {
    return user.profile_picture;
  }

  if (user.profile_picture) {
    return `/uploads/profile_pictures/${user.profile_picture}`;
  }

  const label = encodeURIComponent(user.name || user.username || 'Ngopi User');
  return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
};

const navLinkClass = (active) =>
  `inline-flex items-center gap-2 border-2 border-[#1A0F0A] px-3 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] transition-all duration-150 ${
    active
      ? 'bg-[#1A0F0A] text-[#FAF6F0] shadow-[3px_3px_0px_0px_#C19A6B]'
      : 'bg-white text-[#1A0F0A] shadow-[3px_3px_0px_0px_#1A0F0A] hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[1px_1px_0px_0px_#1A0F0A]'
  }`;

export default function Navbar({ current = 'home', notifications = [] }) {
  const { props } = usePage();
  const auth = props?.auth || {};
  // CRITICAL: role is a primitive string. Use strict string comparison
  // everywhere; never call auth?.user?.role?.name.
  const user = auth?.user || null;
  const isAdmin = user?.role === 'admin';

  const [clock, setClock] = useState(() => {
    try {
      return new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta',
      });
    } catch (e) {
      const d = new Date();
      return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
    }
  });
  const [open, setOpen] = useState(false);

  const [notifOpen, setNotifOpen] = useState(false);

  const menuRef = useRef(null);
  const notifRef = useRef(null);

  useEffect(() => {
    const tick = () => {
      try {
        setClock(
          new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta',
          })
        );
      } catch (e) {
        const d = new Date();
        setClock(`${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`);
      }
    };
    tick();
    const timer = window.setInterval(tick, 1000);
    return () => window.clearInterval(timer);
  }, []);

  const [localNotifications, setLocalNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [confirmDeleteId, setConfirmDeleteId] = useState(null);

  const activeNotifications = (notifications && notifications.length > 0) ? notifications : (props.notifications || []);

  useEffect(() => {
    try {
      const dismissed = JSON.parse(localStorage.getItem('dismissed_notifications') || '[]');
      const filtered = (activeNotifications || []).filter(n => !dismissed.includes(n.id));
      setLocalNotifications(filtered);
      setUnreadCount(filtered.length);
    } catch (_) {
      setLocalNotifications(activeNotifications || []);
      setUnreadCount((activeNotifications || []).length);
    }
  }, [activeNotifications]);

  const handleDeleteNotif = (notifId) => {
    try {
      const dismissed = JSON.parse(localStorage.getItem('dismissed_notifications') || '[]');
      if (!dismissed.includes(notifId)) {
        dismissed.push(notifId);
        localStorage.setItem('dismissed_notifications', JSON.stringify(dismissed));
      }
      setLocalNotifications(prev => prev.filter(n => n.id !== notifId));
      setUnreadCount(prev => Math.max(0, prev - 1));
    } catch (_) {}
  };

  const prevNotifs = useRef(activeNotifications);

  useEffect(() => {
    // Check for new notifications by comparing IDs
    if (prevNotifs.current && activeNotifications && activeNotifications.length > prevNotifs.current.length) {
      const newNotifs = activeNotifications.filter(n => !prevNotifs.current.some(pn => pn.id === n.id));
      if (newNotifs.length > 0) {
        setNotifOpen(true);
        // Optionally auto-close after 5 seconds if you don't interact with it
        const timer = setTimeout(() => {
          setNotifOpen(false);
        }, 5000);
        return () => clearTimeout(timer);
      }
    }
    prevNotifs.current = activeNotifications;
  }, [activeNotifications]);

  useEffect(() => {
    const handleOutsideClick = (event) => {
      if (menuRef.current && !menuRef.current.contains(event.target)) {
        setOpen(false);
      }
      if (notifRef.current && !notifRef.current.contains(event.target)) {
        setNotifOpen(false);
      }
    };
    document.addEventListener('mousedown', handleOutsideClick);
    return () => document.removeEventListener('mousedown', handleOutsideClick);
  }, []);

  /**
   * Robust logout handler.
   * 1) closes the popover immediately
   * 2) clears any local avatar state (avoids stale PFP)
   * 3) fires the POST /logout route via Inertia so the CSRF token,
   *    session invalidation and redirect to `/` happen server-side.
   */
  const handleLogout = (event) => {
    if (event) event.preventDefault();
    setOpen(false);

    try {
      // Pre-clear any user-bound cached state to avoid ghost avatars.
      window.sessionStorage.removeItem('rs:active_dm');
      window.sessionStorage.removeItem('rs:focus_thread');
    } catch (_) {
      /* ignore storage errors */
    }

    router.post('/logout', {}, { preserveScroll: true });
  };

  return (
    <header className="sticky top-0 z-40 border-b-2 border-[#1A0F0A] bg-[#FAF6F0]/95 backdrop-blur">
      <div className="container mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 md:px-8">
        <div className="flex items-center gap-3">
          <span className="inline-flex h-10 w-10 items-center justify-center border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0]">
            <Coffee size={18} />
          </span>
          <div className="space-y-0.5">
            <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B]">
              Roastery Skena
            </p>
            <Link
              href="/"
              className="font-clash text-xl font-black uppercase tracking-tight md:text-2xl"
            >
              NGOPI.
            </Link>
          </div>
        </div>

        <div className="flex flex-wrap items-center justify-end gap-3">
          <div
            aria-label="Jam lokal WIB"
            className="hidden items-center gap-2 border-2 border-[#1A0F0A] bg-white px-3 py-2 font-mono text-[10px] font-black uppercase tracking-[0.16em] shadow-[3px_3px_0px_0px_#1A0F0A] md:inline-flex"
          >
            <Clock3 size={13} />
            <span>{clock} WIB</span>
          </div>

          <Link href="/" className={navLinkClass(current === 'home')}>
            <Home size={14} />
            Home
          </Link>

          {user && (
            <Link
              href="/dashboard"
              className={navLinkClass(current === 'dashboard')}
            >
              <LayoutDashboard size={14} />
              Dashboard
            </Link>
          )}

          {isAdmin && (
            <Link
              href="/admin/gateway"
              className={navLinkClass(current === 'admin')}
            >
              <ShieldCheck size={14} />
              Admin Gate
            </Link>
          )}

          {user && localNotifications && (
            <div className="relative" ref={notifRef}>
              <button
                type="button"
                onClick={() => {
                  setNotifOpen(!notifOpen);
                  setUnreadCount(0);
                }}
                className="inline-flex h-9 w-9 items-center justify-center border-2 border-[#1A0F0A] bg-white shadow-[3px_3px_0px_0px_#1A0F0A] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[1px_1px_0px_0px_#1A0F0A]"
              >
                <Bell size={16} />
                {unreadCount > 0 && (
                  <span className="absolute -right-2 -top-2 inline-flex h-5 w-5 items-center justify-center border-2 border-[#1A0F0A] bg-[#C19A6B] font-mono text-[9px] font-black">
                    {unreadCount}
                  </span>
                )}
              </button>
              {notifOpen && (
                <div className="absolute right-0 top-12 z-50 w-80 border-2 border-[#1A0F0A] bg-white p-3 shadow-[6px_6px_0px_0px_#1A0F0A] animate-in slide-in-from-top-2">
                  <div className="flex items-center justify-between border-b-2 border-[#1A0F0A] pb-2 mb-2">
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Notifikasi</p>
                    <button type="button" onClick={() => setNotifOpen(false)} className="magnetic hover:text-[#C19A6B]"><X size={16} /></button>
                  </div>
                  <div className="grid gap-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                    {localNotifications.length === 0 ? (
                      <p className="font-mono text-[10px] text-center p-2">Belum ada notifikasi.</p>
                    ) : localNotifications.map(n => (
                      <div key={n.id} className="group relative border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2 hover:bg-white flex gap-2">
                        <button
                          type="button"
                          onClick={() => {
                            setNotifOpen(false);
                            if (n?.route) router.visit(n.route);
                          }}
                          className="magnetic text-left flex-1 flex gap-2"
                        >
                          <div className="shrink-0 mt-1"><Bell size={14} className="text-[#C19A6B]" /></div>
                          <div>
                            <p className="font-clash text-sm font-black uppercase">{n.title}</p>
                            <p className="text-xs text-[#1A0F0A]/70 line-clamp-2 mt-1">{n.body}</p>
                          </div>
                        </button>
                        <button
                          type="button"
                          onClick={(e) => {
                            e.stopPropagation();
                            setConfirmDeleteId(n.id);
                          }}
                          className="shrink-0 h-fit self-center border-2 border-[#1A0F0A] p-1 bg-white hover:bg-red-500 hover:text-white transition-colors shadow-[2px_2px_0px_0px_#1A0F0A] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#1A0F0A]"
                          title="Hapus notifikasi"
                        >
                          <X size={10} />
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}

          {user ? (
            <div className="relative" ref={menuRef}>
              <button
                type="button"
                onClick={() => setOpen((value) => !value)}
                className="inline-flex items-center gap-3 border-2 border-[#1A0F0A] bg-white px-2 py-1.5 shadow-[3px_3px_0px_0px_#1A0F0A] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[1px_1px_0px_0px_#1A0F0A]"
                aria-haspopup="menu"
                aria-expanded={open}
              >
                <img
                  src={buildAvatarUrl(user)}
                  alt={user.name || user.username}
                  className="h-9 w-9 border-2 border-[#1A0F0A] object-cover"
                />
                <div className="hidden text-left md:block">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">
                    {isAdmin ? 'Lagi Admin' : 'Lagi Online'}
                  </p>
                  <p className="max-w-[140px] truncate text-sm font-semibold leading-5">
                    {user.name || user.username}
                  </p>
                </div>
                <ChevronDown
                  size={16}
                  className={`transition-transform duration-200 ${open ? 'rotate-180' : ''}`}
                />
              </button>

              {open && (
                <div className="absolute right-0 z-50 mt-3 w-64 border-2 border-[#1A0F0A] bg-white p-3 shadow-[4px_4px_0px_0px_#1A0F0A]">
                  <div className="border-b-2 border-[#1A0F0A] pb-3">
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">
                      Akun Aktif
                    </p>
                    <p className="mt-2 truncate text-sm font-semibold">
                      {user.name || user.username}
                    </p>
                    <p className="mt-1 truncate font-mono text-[10px] uppercase tracking-[0.14em] text-[#1A0F0A]/60">
                      {user.email}
                    </p>
                    <p className="mt-2 inline-flex border-2 border-[#1A0F0A] bg-[#FAF6F0] px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                      Role: {isAdmin ? 'Admin' : 'User'}
                    </p>
                  </div>

                  <div className="mt-3 flex items-center gap-2 border-2 border-[#1A0F0A] bg-[#FAF6F0] px-3 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                    <Clock3 size={13} />
                    {clock} WIB
                  </div>

                  {/*
                   * The logout button is a real <button type="button">
                   * that calls handleLogout. We deliberately avoid a
                   * plain form-action link so the request is a
                   * controlled POST without a hard navigation that
                   * could freeze if the server stalls.
                   */}
                  <button
                    type="button"
                    onClick={handleLogout}
                    className="mt-3 inline-flex w-full items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#1A0F0A] px-4 py-2.5 font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#FAF6F0] shadow-[3px_3px_0px_0px_#C19A6B] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[1px_1px_0px_0px_#C19A6B]"
                  >
                    <LogOut size={15} />
                    Logout
                  </button>
                </div>
              )}
            </div>
          ) : (
            <>
              <Link href="/login" className={navLinkClass(false)}>
                Login
              </Link>
              <Link href="/register" className={navLinkClass(false)}>
                Daftar
              </Link>
            </>
          )}
        </div>
      </div>
      {confirmDeleteId && (
        <div className="fixed inset-0 z-[200] flex items-center justify-center bg-[#1A0F0A]/80 backdrop-blur-sm p-4 animate-in fade-in duration-200">
          <div className="w-full max-w-sm border-2 border-[#1A0F0A] bg-[#FAF6F0] p-6 shadow-[6px_6px_0px_0px_#C19A6B] animate-in zoom-in-95 duration-200">
            <h4 className="font-clash text-lg font-black uppercase tracking-tight mb-2 text-[#1A0F0A]">Hapus Notifikasi?</h4>
            <p className="text-xs text-[#1A0F0A]/70 mb-6 font-mono uppercase tracking-wide">Tindakan ini tidak bisa dibatalkan, notifikasi akan disembunyikan permanen.</p>
            <div className="flex gap-3 justify-end">
              <button
                type="button"
                onClick={() => setConfirmDeleteId(null)}
                className="magnetic border-2 border-[#1A0F0A] bg-white text-[#1A0F0A] px-4 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[2px_2px_0px_0px_#1A0F0A] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#1A0F0A] transition-all cursor-pointer"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={() => {
                  handleDeleteNotif(confirmDeleteId);
                  setConfirmDeleteId(null);
                }}
                className="magnetic border-2 border-[#1A0F0A] bg-red-500 text-white px-4 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[2px_2px_0px_0px_#1A0F0A] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#1A0F0A] transition-all cursor-pointer"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
      )}
    </header>
  );
}
