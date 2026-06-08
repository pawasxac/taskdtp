import React, { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import {
  ArrowUpRight, CheckCircle2, ChevronDown, Coffee, Lock, MapPin,
  MessageSquareText, Search, Sparkles, Star, Users, X,
} from 'lucide-react';
import Navbar from '../Components/Navbar';

/**
 * Premium Roastery Brutalist landing page.
 * - 8-cafe grid (1 / 2 / 4 cols)
 * - Search by name + dropdown filter by Kecamatan
 * - 3-slide rotating hero (skena quote / spotlight / live stats) every 4s
 * - Fixed-viewport cafe detail modal with clickable Maps iframe
 * - Restricted-access toast for guests trying to review / join / DM
 */

const HERO_INTERVAL = 4000;
const HERO_TICK_FADE = 240;
const INITIAL_GRID_LIMIT = 12;

const formatRupiah = (value) => {
  if (value === null || value === undefined || value === '') return 'Belum ke-tag';
  return new Intl.NumberFormat('id-ID').format(Number(value));
};
const normalize = (value) => (value || '').toString().toLowerCase();
const cafePhoto = (cafe) => cafe?.photo_url || null;
const districtLabel = (cafe) =>
  cafe?.district_name || cafe?.kecamatan?.name || cafe?.kecamatan || cafe?.daerah || '';

const getAvatarUrl = (user, fallbackLabel = 'Anak Skena') => {
  if (user?.profile_picture?.startsWith('http')) return user.profile_picture;
  if (user?.profile_picture) return `/uploads/profile_pictures/${user.profile_picture}`;
  const label = encodeURIComponent(user?.name || user?.username || fallbackLabel);
  return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
};

export default function Welcome({ coffeeShops = [], kecamatans = [], communities = [] }) {
  const { props } = usePage();
  const auth = props?.auth || {};
  const user = auth?.user || null;

  const [clock, setClock] = useState(() =>
    new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' })
  );
  const [keyword, setKeyword] = useState('');
  const [activeKecamatan, setActiveKecamatan] = useState('');
  const [activeCafe, setActiveCafe] = useState(null);
  const [toast, setToast] = useState(null);
  const [heroIndex, setHeroIndex] = useState(0);
  const [spotlightIndex, setSpotlightIndex] = useState(0);
  const [heroVisible, setHeroVisible] = useState(true);
  const [displayCount, setDisplayCount] = useState(INITIAL_GRID_LIMIT);
  const swapTimeoutRef = useRef(null);

  useEffect(() => {
    const tick = () =>
      setClock(new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta' }));
    tick();
    const timer = window.setInterval(tick, 1000);
    return () => window.clearInterval(timer);
  }, []);

  useEffect(() => {
    if (!toast) return undefined;
    const timer = window.setTimeout(() => setToast(null), 3200);
    return () => window.clearTimeout(timer);
  }, [toast]);

  useEffect(() => {
    if (typeof document === 'undefined') return undefined;
    document.body.style.overflow = activeCafe ? 'hidden' : '';
    return () => { document.body.style.overflow = ''; };
  }, [activeCafe]);

  const filtered = useMemo(() => {
    return coffeeShops.filter((cafe) => {
      const matchName = normalize(cafe.nama).includes(normalize(keyword));
      const kecamatanLabel = districtLabel(cafe);
      const matchKecamatan = activeKecamatan
        ? normalize(kecamatanLabel) === normalize(activeKecamatan)
        : true;
      return matchName && matchKecamatan;
    });
  }, [coffeeShops, keyword, activeKecamatan]);

  const totalReviewCount = useMemo(
    () => coffeeShops.reduce((s, c) => s + (Array.isArray(c.reviews) ? c.reviews.length : 0), 0),
    [coffeeShops]
  );
  const activeCommunityCount = useMemo(
    () =>
      communities.filter((c) => (c?.members || []).length > 0 || (c?.posts || []).length > 0).length
      || communities.length,
    [communities]
  );
  const sortedCafes = useMemo(
    () => [...coffeeShops].sort((a, b) => Number(b.rating || 0) - Number(a.rating || 0)),
    [coffeeShops]
  );
  const spotlightCafe = useMemo(() => {
    if (sortedCafes.length === 0) return null;
    return sortedCafes[spotlightIndex % sortedCafes.length];
  }, [sortedCafes, spotlightIndex]);

  /**
   * Hero rotation array — 3 slides:
   *  - A) Skena Joke (witty coffee punchline, Indonesian slang)
   *  - B) Dynamic Curated Cafe (real cafe card from props)
   *  - C) Live Status Tracker (community + review counters)
   */
  const heroSlides = useMemo(() => {
    return [
      {
        id: 'quote',
        eyebrow: 'Skena Quote',
        title: 'Sruput dulu, mikir belakangan.',
        description: 'Kopi item, playlist lo-fi, dan meja pojok yang colokannya nggak pelit. Itu rumus dasar anak skena biar tetep waras sepanjang hari.',
        pills: ['#ngopitem', '#mbois', '#deadliner'],
      },
      {
        id: 'spotlight',
        eyebrow: 'Spotlight Cafe',
        title: spotlightCafe?.nama || 'Belum ada spotlight',
        description: spotlightCafe?.alamat || 'Lagi nyiapin spotlight dari database. Sabar ya, mesinnya sedang nyeduh.',
        cafe: spotlightCafe,
      },
      {
        id: 'stats',
        eyebrow: 'Live Stats',
        title: 'Komunitas hidup, feed jalan, meja aman.',
        description: 'Angka live dari database Roastery Skena. Cocok buat mantau tongkrongan yang lagi rame.',
        stats: [
          { label: 'Forum Aktif', value: activeCommunityCount, icon: Users },
          { label: 'Pesan Lounge', value: totalReviewCount, icon: MessageSquareText },
          { label: 'Total Review', value: totalReviewCount, icon: Coffee },
        ],
      },
    ];
  }, [spotlightCafe, activeCommunityCount, totalReviewCount]);

  /**
   * STABLE interval loop that rotates the hero every 4 seconds.
   * - uses useEffect with explicit cleanup
   * - applies a 240ms fade-out, swaps the slide, then fades in
   * - re-syncs the spotlightIndex to the same modulo so the
   *   right-side "Spotlight Highlight" panel never desyncs.
   */
  useEffect(() => {
    if (heroSlides.length <= 1) return undefined;
    const interval = window.setInterval(() => {
      setHeroVisible(false);
      if (swapTimeoutRef.current) window.clearTimeout(swapTimeoutRef.current);
      swapTimeoutRef.current = window.setTimeout(() => {
        setHeroIndex((current) => (current + 1) % heroSlides.length);
        if (sortedCafes.length > 0) setSpotlightIndex((current) => (current + 1) % sortedCafes.length);
        setHeroVisible(true);
      }, HERO_TICK_FADE);
    }, HERO_INTERVAL);
    return () => {
      window.clearInterval(interval);
      if (swapTimeoutRef.current) window.clearTimeout(swapTimeoutRef.current);
    };
  }, [heroSlides.length, sortedCafes.length]);

  const heroCard = heroSlides[heroIndex] || heroSlides[0];

  const relatedCommunityPosts = useMemo(() => {
    if (!activeCafe) return [];
    const district = normalize(districtLabel(activeCafe));
    const daerah = normalize(activeCafe.daerah);
    return (communities || [])
      .filter((c) => {
        const domisili = normalize(c?.domisili);
        if (district && (domisili.includes(district) || domisili.includes(daerah))) return true;
        return daerah ? domisili.includes(daerah) : false;
      })
      .flatMap((c) => (c.posts || []).map((p) => ({ ...p, namaKomunitas: c.nama_komunitas, domisiliKomunitas: c.domisili })))
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      .slice(0, 3);
  }, [activeCafe, communities]);

  /**
   * Guest-interaction barrier.
   * Guests can browse reviews and forum lists, but the moment they
   * tap any "active" CTA, we show a brutalist modal saying they
   * must login/register first.
   */
  const [guestModal, setGuestModal] = useState(false);
  const handleLockedAction = () => setGuestModal(true);
  const handleCloseModal = () => setActiveCafe(null);
  const scrollToGrid = () =>
    document.getElementById('coffee-grid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });

  const btnPrimary =
    'inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-4 py-2 font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] shadow-[3px_3px_0px_0px_#1A0F0A] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[1px_1px_0px_0px_#1A0F0A]';
  const btnSecondary =
    'inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-white px-4 py-2 font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] shadow-[3px_3px_0px_0px_#1A0F0A] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[1px_1px_0px_0px_#1A0F0A]';

  const visibleCafes = filtered.slice(0, displayCount);
  const hasMore = displayCount < filtered.length;

  const handleLoadMore = () => {
    setDisplayCount(prev => prev + 12);
  };

  return (
    <div className="min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Roastery Skena" />
      <Navbar current="home" />

      {toast && (
        <div className="fixed right-4 top-20 z-[80] w-[calc(100%-2rem)] max-w-sm">
          <div className="border-2 border-[#1A0F0A] bg-white p-3 shadow-[4px_4px_0px_0px_#1A0F0A]">
            <div className="flex items-start gap-3">
              <div className="mt-0.5">
                {toast.type === 'warning' ? <Lock size={16} /> : <CheckCircle2 size={16} />}
              </div>
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Notif Seduhan</p>
                <p className="mt-1 text-sm leading-6">{toast.message}</p>
              </div>
            </div>
          </div>
        </div>
      )}

      <main className="py-6">
        <section className="px-4 md:px-8">
          <div className="mx-auto flex max-w-7xl flex-col gap-4">
            <div className="overflow-hidden border-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] shadow-[4px_4px_0px_0px_#C19A6B]">
              <div className="flex w-max min-w-full gap-12 whitespace-nowrap py-2 font-mono text-[10px] font-black uppercase tracking-[0.2em]">
                <span>NGOPI SKENA • KOPI AMAN • SERVER KENCENG • SIAP NUGAS</span>
                <span>NGOPI SKENA • KOPI AMAN • SERVER KENCENG • SIAP NUGAS</span>
              </div>
            </div>

            <div className="grid gap-4 2xl:grid-cols-[1.1fr_0.9fr]">
              <div className="border-2 border-[#1A0F0A] bg-white p-5 shadow-[6px_6px_0px_0px_#1A0F0A] md:p-6">
                <div className="inline-flex items-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-3 py-1.5 font-mono text-[10px] font-black uppercase tracking-[0.18em]">
                  <Sparkles size={13} /> Hero Seduhan
                </div>
                <p className="mt-3 font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B]">Landing Page</p>
                <h1 className="mt-2 break-words font-clash text-2xl font-black uppercase leading-[0.95] tracking-tight md:text-4xl">
                  Cari seduhan, suasana, dan spot ngopi anti-drama.
                </h1>
                <p className="mt-3 max-w-2xl text-sm leading-relaxed text-[#1A0F0A]/80 md:text-base">
                  Roastery Skena nyiapin database 180 coffee shop biar kamu tinggal pilih: manual brew yang niat, meja pojok buat ngerjain tugas, atau circle komunitas yang vibes-nya nyambung.
                </p>
                <div className="mt-4 flex flex-wrap gap-3">
                  <Link href={user ? '/dashboard' : '/login'} className={btnPrimary}>
                    {user ? 'Masuk Dashboard' : 'Gas Login'}
                  </Link>
                  <button type="button" onClick={scrollToGrid} className={btnSecondary}>Lihat Kedai</button>
                </div>

                <div
                  className={`mt-5 grid grid-cols-1 gap-3 border-2 border-[#1A0F0A] bg-[#FAF6F0] p-4 shadow-[4px_4px_0px_0px_#1A0F0A] md:grid-cols-[1.4fr_1fr] transform-gpu transition-all duration-500 ${heroVisible ? 'translate-y-0 opacity-100' : 'translate-y-1 opacity-0'}`}
                >
                  <div>
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">{heroCard?.eyebrow}</p>
                    <h2 className="mt-1 break-words font-clash text-xl font-black uppercase leading-tight md:text-2xl">{heroCard?.title}</h2>
                    <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/76 md:text-base">{heroCard?.description}</p>
                    {heroCard?.id === 'quote' && (
                      <div className="mt-3 flex flex-wrap gap-2">
                        {(heroCard.pills || []).map((pill) => (
                          <span key={pill} className="inline-flex items-center gap-2 border-2 border-[#1A0F0A] bg-white px-2 py-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">{pill}</span>
                        ))}
                      </div>
                    )}
                    {heroCard?.id === 'spotlight' && heroCard.cafe && (
                      <div className="mt-3 grid gap-2 sm:grid-cols-2">
                        <div className="border-2 border-[#1A0F0A] bg-white p-2">
                          <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Kecamatan</p>
                          <p className="mt-1 text-sm font-semibold">{districtLabel(heroCard.cafe) || 'Belum kebaca'}</p>
                        </div>
                        <div className="border-2 border-[#1A0F0A] bg-white p-2">
                          <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Harga</p>
                          <p className="mt-1 text-sm font-semibold">Rp {formatRupiah(heroCard.cafe.harga_min)} - Rp {formatRupiah(heroCard.cafe.harga_max)}</p>
                        </div>
                      </div>
                    )}
                    {heroCard?.id === 'stats' && (
                      <div className="mt-3 grid gap-2 sm:grid-cols-3">
                        {heroCard.stats.map((item) => {
                          const Icon = item.icon;
                          return (
                            <div key={item.label} className="border-2 border-[#1A0F0A] bg-white p-2">
                              <div className="flex items-center gap-2">
                                <Icon size={12} />
                                <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">{item.label}</p>
                              </div>
                              <p className="mt-1 font-clash text-xl font-black">{item.value}</p>
                            </div>
                          );
                        })}
                      </div>
                    )}
                  </div>
                  <div className="grid gap-2">
                    <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] p-3 text-[#FAF6F0]">
                      <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Spotlight Highlight</p>
                      <p className="mt-2 text-sm font-semibold leading-5">{spotlightCafe?.nama || 'Belum ada spotlight'}</p>
                      <p className="mt-1 text-sm text-white/75 line-clamp-2">{spotlightCafe?.alamat || 'Spot masih loading dari database.'}</p>
                      <div className="mt-3 flex items-center justify-between border-t-2 border-white/30 pt-2">
                        <span className="inline-flex items-center gap-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                          <Star size={12} className="fill-current text-[#C19A6B]" /> {spotlightCafe?.rating || 'N/A'}
                        </span>
                        <span className="font-mono text-[10px] uppercase tracking-[0.12em] text-white/70">{districtLabel(spotlightCafe) || 'Lokasi'}</span>
                      </div>
                    </div>
                    <div className="grid grid-cols-3 gap-2">
                      <div className="border-2 border-[#1A0F0A] bg-white p-2 text-center">
                        <p className="font-mono text-[9px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Cafe</p>
                        <p className="mt-1 font-clash text-base font-black">{filtered.length}</p>
                      </div>
                      <div className="border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2 text-center">
                        <p className="font-mono text-[9px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Forum</p>
                        <p className="mt-1 font-clash text-base font-black">{communities.length}</p>
                      </div>
                      <div className="border-2 border-[#1A0F0A] bg-white p-2 text-center">
                        <p className="font-mono text-[9px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Jam</p>
                        <p className="mt-1 font-mono text-[10px] font-black uppercase tracking-[0.12em]">{clock} WIB</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="grid gap-4">
                <div className="border-2 border-[#1A0F0A] bg-white p-5 shadow-[6px_6px_0px_0px_#1A0F0A] md:p-6">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Filter Kedai</p>
                  <h3 className="mt-2 font-clash text-2xl font-black uppercase md:text-3xl">Cari spot yang nyambung</h3>
                  <div className="mt-4 grid gap-3">
                    <label className="flex items-center gap-3 border-2 border-[#1A0F0A] bg-[#FAF6F0] px-3 py-2.5">
                      <Search size={15} />
                      <input
                        type="text"
                        value={keyword}
                        onChange={(event) => setKeyword(event.target.value)}
                        placeholder="Cari nama kedai yang lagi kamu incar"
                        className="w-full border-0 bg-transparent p-0 text-sm outline-none placeholder:text-[#1A0F0A]/40 md:text-base"
                      />
                    </label>
                    <label className="relative flex items-center">
                      <select
                        value={activeKecamatan}
                        onChange={(event) => setActiveKecamatan(event.target.value)}
                        className="w-full appearance-none border-2 border-[#1A0F0A] bg-[#FAF6F0] px-3 py-2.5 pr-10 text-sm outline-none md:text-base"
                      >
                        <option value="">Semua kecamatan</option>
                        {kecamatans.map((kecamatan) => (
                          <option key={kecamatan.id} value={kecamatan.name}>{kecamatan.name}</option>
                        ))}
                      </select>
                      <ChevronDown size={16} className="pointer-events-none absolute right-3 text-[#1A0F0A]" />
                    </label>
                  </div>
                </div>

                <div className="border-2 border-[#1A0F0A] bg-[#1A0F0A] p-5 text-white shadow-[6px_6px_0px_0px_#C19A6B] md:p-6">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Rekomendasi Mbois</p>
                  <h3 className="mt-2 break-words font-clash text-2xl font-black leading-tight md:text-3xl">
                    {spotlightCafe?.nama || 'Belum ada spotlight'}
                  </h3>
                  <div className="mt-3 grid gap-2 sm:grid-cols-2">
                    <div className="border-2 border-white/70 bg-white/10 p-2">
                      <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Kecamatan</p>
                      <p className="mt-1 text-sm font-semibold">{districtLabel(spotlightCafe) || 'Belum kebaca'}</p>
                    </div>
                    <div className="border-2 border-white/70 bg-white/10 p-2">
                      <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em] text-[#C19A6B]">Budget</p>
                      <p className="mt-1 text-sm font-semibold">Rp {formatRupiah(spotlightCafe?.harga_min)} - Rp {formatRupiah(spotlightCafe?.harga_max)}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section id="coffee-grid" className="px-4 py-6 md:px-8">
          <div className="mx-auto max-w-7xl">
            <div className="mb-4 flex flex-wrap items-end justify-between gap-3">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Daftar Spot</p>
                <h3 className="mt-2 break-words font-clash text-2xl font-black md:text-3xl">Pilihan Kedai Buat Duduk Lama</h3>
              </div>
              <div className="border-2 border-[#1A0F0A] bg-white px-3 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em] shadow-[3px_3px_0px_0px_#1A0F0A]">
                Tampil {visibleCafes.length} dari {filtered.length} spot
              </div>
            </div>

            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
              {visibleCafes.map((cafe) => (
                <button
                  type="button"
                  key={cafe.id}
                  onClick={() => setActiveCafe(cafe)}
                  className="group flex h-full flex-col overflow-hidden border-2 border-[#1A0F0A] bg-white text-left shadow-[4px_4px_0px_0px_#1A0F0A] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[2px_2px_0px_0px_#1A0F0A]"
                >
                  <div className="h-32 overflow-hidden border-b-2 border-[#1A0F0A] bg-[#F2E8DD]">
                    {cafePhoto(cafe) ? (
                      <img src={cafePhoto(cafe)} alt={`Foto ${cafe.nama}`} className="h-full w-full object-cover transition-all duration-200 group-hover:scale-[1.02]" />
                    ) : (
                      <div className="flex h-full items-center justify-center bg-[linear-gradient(135deg,#F6EEE4_0%,#E7D6C2_100%)] px-4 text-center">
                        <div>
                          <Coffee size={18} className="mx-auto text-[#1A0F0A]" />
                          <p className="mt-1 text-sm font-semibold md:text-base">Belum ada foto</p>
                        </div>
                      </div>
                    )}
                  </div>
                  <div className="flex flex-1 flex-col gap-3 p-3">
                    <div className="flex items-start justify-between gap-3">
                      <div>
                        <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">{districtLabel(cafe) || 'Area nongki'}</p>
                        <h4 className="mt-1 break-words font-clash text-lg font-black uppercase leading-tight md:text-xl">{cafe.nama}</h4>
                      </div>
                      <ArrowUpRight size={16} className="mt-1 shrink-0" />
                    </div>
                    <p className="line-clamp-2 text-sm leading-6 text-[#1A0F0A]/74 md:text-base">{cafe.alamat}</p>
                    <div className="mt-auto flex items-center justify-between border-2 border-[#1A0F0A] bg-[#FAF6F0] px-3 py-2 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                      <span>Rp {formatRupiah(cafe.harga_min)} - {formatRupiah(cafe.harga_max)}</span>
                      <span className="inline-flex items-center gap-1"><Star size={12} className="fill-current text-[#C19A6B]" /> {cafe.rating || 'N/A'}</span>
                    </div>
                  </div>
                </button>
              ))}
            </div>

            {hasMore && (
              <div className="mt-8 flex justify-center">
                <button
                  type="button"
                  onClick={handleLoadMore}
                  className="inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-8 py-4 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] shadow-[4px_4px_0px_0px_#1A0F0A] transition-all duration-150 hover:-translate-y-0.5 hover:translate-x-0.5 hover:shadow-[2px_2px_0px_0px_#1A0F0A]"
                >
                  Tampilkan Lebih Banyak
                </button>
              </div>
            )}

            {filtered.length === 0 && (
              <div className="mt-6 border-2 border-[#1A0F0A] bg-white p-6 text-center shadow-[4px_4px_0px_0px_#1A0F0A]">
                <p className="font-clash text-2xl font-black uppercase md:text-4xl">Belum nemu spot yang pas.</p>
                <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/72 md:text-base">Coba longgarkan keyword-nya. Siapa tahu jodoh kopi kamu nongkrong di kecamatan sebelah.</p>
              </div>
            )}
          </div>
        </section>

        <footer className="border-t-2 border-[#1A0F0A] bg-[#1A0F0A] px-4 py-6 text-white md:px-8">
          <div className="mx-auto grid max-w-7xl gap-4 md:grid-cols-2">
            <div>
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Catatan Penutup</p>
              <h4 className="mt-2 break-words font-clash text-2xl font-black uppercase leading-tight md:text-3xl">Sruput pelan, pilih spot yang niat.</h4>
              <p className="mt-2 max-w-2xl text-sm leading-6 text-white/76 md:text-base">Review jalan, komunitas hidup, dan meja kerja tetap terasa premium walau deadline lagi sok galak.</p>
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              <div className="border-2 border-white bg-white p-3 text-[#1A0F0A]">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Vibe Check</p>
                <p className="mt-2 text-sm leading-6 md:text-base">Ada spot buat meeting tipis, me time, sampai kabur dari grup kerja.</p>
              </div>
              <div className="border-2 border-white bg-[#C19A6B] p-3 text-[#1A0F0A]">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em]">Mood Booster</p>
                <p className="mt-2 text-sm leading-6 md:text-base">Kalau hari lagi berat, minimal kopinya nggak ikut bikin drama.</p>
              </div>
            </div>
          </div>
        </footer>
      </main>

      {activeCafe && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-[#1A0F0A]/70 backdrop-blur-sm p-4"
          onClick={handleCloseModal}
        >
          <div
            className="relative max-w-3xl w-full max-h-[80vh] overflow-y-auto bg-white border-4 border-[#1A0F0A] p-4 shadow-[8px_8px_0px_0px_#C19A6B]"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="sticky top-0 z-10 flex items-center justify-between gap-3 border-b-2 border-[#1A0F0A] bg-white px-2 pb-3">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Detail Kedai</p>
                <h5 className="mt-1 break-words font-clash text-xl font-black uppercase md:text-2xl">{activeCafe.nama}</h5>
              </div>
              <button type="button" onClick={handleCloseModal} className="inline-flex h-10 w-10 items-center justify-center border-2 border-[#1A0F0A] bg-white">
                <X size={16} />
              </button>
            </div>

            <div className="grid gap-3 pt-3">
              <div className="overflow-hidden border-2 border-[#1A0F0A] bg-white">
                {cafePhoto(activeCafe) ? (
                  <img src={cafePhoto(activeCafe)} alt={`Foto ${activeCafe.nama}`} className="h-44 w-full object-cover" />
                ) : (
                  <div className="flex h-44 items-center justify-center bg-[linear-gradient(135deg,#F6EEE4_0%,#E7D6C2_100%)] px-6 text-center">
                    <div>
                      <Coffee size={20} className="mx-auto text-[#1A0F0A]" />
                      <p className="mt-2 text-sm font-semibold md:text-base">Belum ada foto, tapi vibes-nya udah pas buat duduk lama.</p>
                    </div>
                  </div>
                )}
              </div>

              <div className="grid gap-3 md:grid-cols-2">
                <div className="border-2 border-[#1A0F0A] bg-white p-3">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Jam Oper</p>
                  <p className="mt-1 text-sm font-semibold md:text-base">{activeCafe.jam_buka} - {activeCafe.jam_tutup}</p>
                </div>
                <div className="border-2 border-[#1A0F0A] bg-white p-3">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Lokasi</p>
                  <p className="mt-1 text-sm font-semibold md:text-base">{districtLabel(activeCafe) || 'Lokasi belum kebaca'}</p>
                </div>
              </div>

              <div className="border-2 border-[#1A0F0A] bg-white p-3">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Peta</p>
                <div className="mt-2">
                  {activeCafe.latitude && activeCafe.longitude ? (
                    <iframe
                      src={`https://maps.google.com/maps?q=${activeCafe.latitude},${activeCafe.longitude}&z=15&output=embed`}
                      className="w-full h-44 border-2 border-[#1A0F0A]"
                      loading="lazy"
                      title={`Peta ${activeCafe.nama}`}
                    />
                  ) : (
                    <div className="flex h-44 items-center justify-center border-2 border-[#1A0F0A] bg-[#FAF6F0]">
                      <p className="text-sm font-semibold text-[#1A0F0A]/70 md:text-base">Koordinatnya belum nongol. Tanya barista dulu ya.</p>
                    </div>
                  )}
                </div>
              </div>

              <div className="border-2 border-[#1A0F0A] bg-white p-3">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Harga & Rating</p>
                <p className="mt-2 text-sm leading-6 md:text-base">
                  Budget sekitar <span className="font-semibold">Rp {formatRupiah(activeCafe.harga_min)}</span> sampai{' '}
                  <span className="font-semibold">Rp {formatRupiah(activeCafe.harga_max)}</span>.
                </p>
                <p className="mt-1 inline-flex items-center gap-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                  <Star size={12} className="fill-current text-[#C19A6B]" /> {activeCafe.rating || 'Belum dinilai'}
                </p>
              </div>

              <div className="border-2 border-[#1A0F0A] bg-white p-3">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Thread Komunitas</p>
                <div className="mt-2 grid gap-2">
                  {relatedCommunityPosts.length > 0 ? (
                    relatedCommunityPosts.map((post) => (
                      <div key={post.id} className="border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2">
                        <div className="flex items-start justify-between gap-2">
                          <div className="flex items-start gap-2">
                            <img src={getAvatarUrl(post.user, post.namaKomunitas)} alt={post.user?.name || post.namaKomunitas} className="h-8 w-8 border-2 border-[#1A0F0A] object-cover" />
                            <div>
                              <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em]">{post.namaKomunitas}</p>
                              <p className="font-mono text-[10px] uppercase tracking-[0.12em] text-[#1A0F0A]/55">{post.domisiliKomunitas}</p>
                            </div>
                          </div>
                          <span className="inline-flex items-center gap-1 font-mono text-[10px] font-black uppercase tracking-[0.12em]">
                            <MessageSquareText size={12} /> {(post.comments || []).length}
                          </span>
                        </div>
                        <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/76 md:text-base">{post.content}</p>
                      </div>
                    ))
                  ) : (
                    <div className="border-2 border-dashed border-[#1A0F0A] p-2">
                      <p className="text-sm leading-6 text-[#1A0F0A]/70 md:text-base">Belum ada obrolan yang masuk. Mungkin semua lagi fokus milih manual brew.</p>
                    </div>
                  )}
                </div>
              </div>

              <div className="border-2 border-[#1A0F0A] bg-white p-3">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B]">Feed Ulasan</p>
                <div className="mt-2 grid gap-2">
                  {(activeCafe.reviews || []).length > 0 ? (
                    activeCafe.reviews.map((review) => (
                      <div key={review.id} className="border-2 border-[#1A0F0A] bg-[#FAF6F0] p-2">
                        <div className="flex items-center justify-between gap-2">
                          <div className="flex items-center gap-2">
                            <img src={getAvatarUrl(review.user, review.user?.name)} alt={review.user?.name || 'Anon'} className="h-8 w-8 border-2 border-[#1A0F0A] object-cover" />
                            <p className="font-mono text-[10px] font-black uppercase tracking-[0.14em]">{review.user?.name || 'Anon yang lagi santai'}</p>
                          </div>
                          <span className="inline-flex items-center gap-1 font-mono text-[10px] font-black uppercase tracking-[0.14em]">
                            <Star size={12} className="fill-current text-[#C19A6B]" /> {review.rating}
                          </span>
                        </div>
                        <p className="mt-2 text-sm leading-6 text-[#1A0F0A]/76 md:text-base">{review.review || 'Belum ada cerita panjang, tapi tempatnya udah bikin penasaran.'}</p>
                      </div>
                    ))
                  ) : (
                    <div className="border-2 border-dashed border-[#1A0F0A] p-2">
                      <p className="text-sm leading-6 text-[#1A0F0A]/70 md:text-base">Belum ada yang cerita. Mungkin semuanya lagi sibuk nyeruput tanpa banyak komentar.</p>
                    </div>
                  )}
                </div>
              </div>

              <div className="grid gap-3 sm:grid-cols-3">
                <a
                  href={activeCafe.latitude && activeCafe.longitude ? `https://www.google.com/maps/search/?api=1&query=${activeCafe.latitude},${activeCafe.longitude}` : '#'}
                  target="_blank"
                  rel="noreferrer"
                  className={btnSecondary}
                >
                  <MapPin size={14} /> Buka Maps
                </a>
                {user ? (
                  <Link href="/dashboard" className={btnPrimary}>Tulis Review</Link>
                ) : (
                  <button type="button" onClick={handleLockedAction} className={btnPrimary}>Tulis Review</button>
                )}
                {user ? (
                  <Link href="/dashboard" className={btnPrimary}>Gabung Komunitas</Link>
                ) : (
                  <button type="button" onClick={handleLockedAction} className={btnPrimary}>Gabung Komunitas</button>
                )}
              </div>
            </div>
          </div>
        </div>
      )}

      {guestModal && (
        <div
          className="fixed inset-0 z-[90] flex items-center justify-center bg-[#1A0F0A]/80 backdrop-blur-sm p-4"
          onClick={() => setGuestModal(false)}
        >
          <div
            className="relative max-w-md w-full bg-white border-4 border-[#1A0F0A] p-5 shadow-[8px_8px_0px_0px_#C19A6B]"
            onClick={(event) => event.stopPropagation()}
          >
            <div className="flex items-start gap-3">
              <span className="inline-flex h-10 w-10 items-center justify-center border-2 border-[#1A0F0A] bg-[#C19A6B]">
                <Lock size={18} />
              </span>
              <div className="flex-1">
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.18em] text-[#C19A6B]">Akses Dibatasi</p>
                <h4 className="mt-1 font-clash text-xl font-black uppercase md:text-2xl">Eits, dilarang menyusup!</h4>
                <p className="mt-2 text-sm leading-6 md:text-base">
                  Login/Daftar dulu biar bisa nge-gas bareng. Akun baru cuma butuh email + username, kok cepat.
                </p>
              </div>
            </div>
            <div className="mt-4 grid gap-2 sm:grid-cols-2">
              <Link href="/login" className={btnPrimary}>Login Sekarang</Link>
              <Link href="/register" className={btnSecondary}>Daftar Akun</Link>
            </div>
            <button
              type="button"
              onClick={() => setGuestModal(false)}
              className="mt-3 inline-flex w-full items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-white px-4 py-2 font-mono text-[10px] font-black uppercase tracking-[0.16em]"
            >
              Nanti Aja
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
