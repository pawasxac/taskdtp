import React, { useEffect, useRef, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import {
  ChevronDown,
  Clock3,
  Coffee,
  Home,
  LayoutDashboard,
  LogOut,
  ShieldCheck,
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

/**
 * Global navigation header used by every active view.
 * Compact, brutalist, with a live WIB clock, public Home link,
 * a Dashboard link, an Admin Control Gate (visible only when
 * auth.user.role === 'admin'), and a robust logout that
 * never traps the user in a routing loop.
 */
export default function Navbar({ current = 'home' }) {
  const { props } = usePage();
  const auth = props?.auth || {};
  // CRITICAL: role is a primitive string. Use strict string comparison
  // everywhere; never call auth?.user?.role?.name.
  const user = auth?.user || null;
  const isAdmin = user?.role === 'admin';

  const [open, setOpen] = useState(false);
  const [clock, setClock] = useState(() =>
    new Date().toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  );

  const menuRef = useRef(null);

  useEffect(() => {
    const tick = () =>
      setClock(
        new Date().toLocaleTimeString('id-ID', {
          hour: '2-digit',
          minute: '2-digit',
          timeZone: 'Asia/Jakarta',
        })
      );
    tick();
    const timer = window.setInterval(tick, 1000);
    return () => window.clearInterval(timer);
  }, []);

  useEffect(() => {
    const handleOutsideClick = (event) => {
      if (menuRef.current && !menuRef.current.contains(event.target)) {
        setOpen(false);
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
    </header>
  );
}
