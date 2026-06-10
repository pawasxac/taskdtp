import React, { useEffect, useRef, useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
  ArrowUpRight, ChevronDown, Coffee, Lock, MapPin,
  Search, Sparkles, Star, X, Map
} from 'lucide-react';

import Navbar from '../Components/Navbar';
import CustomCursor from '../Components/CustomCursor';

const formatRupiah = (value) => {
  if (!value) return 'Tanya Barista';
  return new Intl.NumberFormat('id-ID').format(Number(value));
};
const cafePhoto = (cafe) => cafe?.photo_url || null;
const districtLabel = (cafe) =>
  cafe?.district_name || cafe?.kecamatan?.name || cafe?.kecamatan || cafe?.daerah || '';

const getAvatarUrl = (user, fallbackLabel = 'Anak Skena') => {
  if (user?.profile_picture?.startsWith('http')) return user.profile_picture;
  if (user?.profile_picture) return `/uploads/profile_pictures/${user.profile_picture}`;
  const label = encodeURIComponent(user?.name || user?.username || fallbackLabel);
  return `https://ui-avatars.com/api/?name=${label}&background=1A0F0A&color=FAF6F0&bold=true`;
};


const useReveal = () => {
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
    );

    document.querySelectorAll('.reveal-up').forEach((el) => observer.observe(el));
    return () => observer.disconnect();
  }, []);
};

export default function Welcome({ coffeeShops = {}, kecamatans = [], communities = [], filters = {} }) {
  useReveal();
  const { props: pageProps } = usePage();
  const user = pageProps?.auth?.user || null;


  const [localCafes, setLocalCafes] = useState(coffeeShops?.data || []);
  const nextPageUrl = coffeeShops?.next_page_url || null;
  const [isLoadingMore, setIsLoadingMore] = useState(false);


  useEffect(() => {
    setLocalCafes(coffeeShops?.data || []);
  }, [coffeeShops]);


  const [keyword, setKeyword] = useState(filters?.search || '');
  const [activeKecamatan, setActiveKecamatan] = useState(filters?.kecamatan || '');
  const [activePrice, setActivePrice] = useState(filters?.price || '');

  const [activeCafe, setActiveCafe] = useState(null);
  const [guestModal, setGuestModal] = useState(false);


  const [showIntro, setShowIntro] = useState(() => {
    try {
      if (sessionStorage.getItem('ngopi_intro_shown')) return false;
      sessionStorage.setItem('ngopi_intro_shown', '1');
      return true;
    } catch (_) {
      return false;
    }
  });
  const [fadeOutIntro, setFadeOutIntro] = useState(false);
  const [reviewInput, setReviewInput] = useState('');
  const [isSubmittingReview, setIsSubmittingReview] = useState(false);

  useEffect(() => {
    if (showIntro) {
      const fadeTimer = setTimeout(() => {
        setFadeOutIntro(true);
      }, 2000);
      const removeTimer = setTimeout(() => {
        setShowIntro(false);
      }, 2500);
      return () => {
        clearTimeout(fadeTimer);
        clearTimeout(removeTimer);
      };
    }
  }, [showIntro]);


  const updateFilters = (type, value) => {

    if (type === 'search') setKeyword(value);
    if (type === 'kecamatan') setActiveKecamatan(value);
    if (type === 'price') setActivePrice(value);


    const finalKeyword = type === 'search' ? value : keyword;
    const finalKecamatan = type === 'kecamatan' ? value : activeKecamatan;
    const finalPrice = type === 'price' ? value : activePrice;


    const params = new URLSearchParams();
    if (finalKeyword) params.set('search', finalKeyword);
    if (finalKecamatan) params.set('kecamatan', finalKecamatan);
    if (finalPrice) params.set('price', finalPrice);

    const newUrl = params.toString() ? `/?${params.toString()}` : '/';


    window.location.href = newUrl;
  };

  const handleLoadMore = () => {
    if (!nextPageUrl || isLoadingMore) return;
    setIsLoadingMore(true);
    router.get(
      nextPageUrl,
      {},
      {
        preserveState: true,
        preserveScroll: true,
        only: ['coffeeShops'],
        onSuccess: (page) => {
          const newCafes = page?.props?.coffeeShops?.data || [];
          setLocalCafes((prev) => {
            const existingIds = new Set(prev.map(c => c.id));
            const uniqueNew = newCafes.filter(c => !existingIds.has(c.id));
            return [...prev, ...uniqueNew];
          });
          setIsLoadingMore(false);
        },
        onError: () => setIsLoadingMore(false)
      }
    );
  };

  const scrollToGrid = () =>
    document.getElementById('coffee-grid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });

  const btnPrimary =
    'magnetic inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-[#C19A6B] px-6 py-3 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] transition-all duration-300 hover:bg-[#1A0F0A] hover:text-[#FAF6F0]';
  const btnSecondary =
    'magnetic inline-flex items-center justify-center gap-2 border-2 border-[#1A0F0A] bg-transparent px-6 py-3 font-mono text-[12px] font-black uppercase tracking-[0.16em] text-[#1A0F0A] transition-all duration-300 hover:bg-[#1A0F0A] hover:text-[#FAF6F0]';


  const [heroTextIndex, setHeroTextIndex] = useState(0);
  const heroLines = [
    "Sruput dulu, overthinking belakangan.",
    "Bukan soal lambung, ini soal vibes.",
    "Manual brew elit, ngerjain tugas sulit.",
    "Cari colokan lebih penting dari cari jodoh."
  ];

  useEffect(() => {
    const timer = setInterval(() => {
      setHeroTextIndex((prev) => (prev + 1) % heroLines.length);
    }, 4000);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="flex flex-col min-h-screen bg-[#FAF6F0] text-[#1A0F0A] selection:bg-[#C19A6B] selection:text-[#1A0F0A]">
      <Head title="Superior Premium Ngopi" />

      {/* Intro Loading Screen — only on true first visit, not filter changes */}
      {showIntro && (
        <div className={`fixed inset-0 z-[999] flex flex-col items-center justify-center bg-[#1A0F0A] text-[#FAF6F0] transition-opacity duration-500 ease-in-out ${fadeOutIntro ? 'opacity-0 pointer-events-none' : 'opacity-100'}`}>
          <div className="flex flex-col items-center gap-4">
            <Coffee size={48} className="text-[#C19A6B] animate-bounce" />
            <h1 className="font-clash text-4xl font-black uppercase tracking-widest">NGOPI</h1>
            <p className="font-mono text-xs uppercase tracking-[0.3em] text-[#C19A6B]">Loading Vibes...</p>
          </div>
        </div>
      )}

        <Navbar current="home" />
        <CustomCursor />

      <main className="pt-24 pb-12 flex-grow">
        {/* Dynamic Hero Section */}
        <section className="px-4 md:px-8 mb-24 max-w-7xl mx-auto">
          <div className="reveal-up text-center flex flex-col items-center justify-center min-h-[50vh]">
            <div className="inline-flex items-center gap-2 border-2 border-[#1A0F0A] px-4 py-2 font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#1A0F0A] mb-6">
              <Sparkles size={14} /> Kopi Aman, Vibes Premium
            </div>

            <h1 className="font-clash text-4xl md:text-6xl lg:text-7xl font-black uppercase leading-[1.1] tracking-tight mb-6 h-[140px] md:h-[160px] flex items-center justify-center overflow-hidden">
              <span className="block transition-all duration-500 transform-gpu ease-out text-[#1A0F0A]">
                {heroLines[heroTextIndex]}
              </span>
            </h1>

            <p className="max-w-2xl text-base md:text-lg leading-relaxed text-[#1A0F0A]/80 mb-10">
              Database spot ngopi paling legit se-Sidoarjo dan Surabaya. Entah lu butuh meja pojok buat nugas semalaman, atau sekadar nyari manual brew yang niat bikinnya. Kita filterin biar lu nggak salah masuk circle.
            </p>

            <div className="flex flex-wrap justify-center gap-4">
              <button type="button" onClick={scrollToGrid} className={btnPrimary}>
                Cari Spot Sekarang
              </button>
              {!user && (
                <Link href="/register" className={btnSecondary}>
                  Join Komunitas
                </Link>
              )}
            </div>
          </div>
        </section>

        {/* Filter Section */}
        <section id="coffee-grid" className="px-4 md:px-8 max-w-7xl mx-auto mb-16">
          <div className="border-2 border-[#1A0F0A] bg-white p-6 md:p-8 flex flex-col md:flex-row gap-6 items-end">
            <div className="flex-1 w-full">
              <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-2 text-[#C19A6B]">Keyword / Nama Kedai</label>
              <div className="relative">
                <Search size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-[#1A0F0A]/50" />
                <input
                  type="text"
                  value={keyword}
                  onChange={(e) => updateFilters('search', e.target.value)}
                  placeholder="Ketik nama spot yang lo incer..."
                  className="w-full border-2 border-[#1A0F0A] bg-[#FAF6F0] pl-12 pr-4 py-3 text-sm outline-none focus:border-[#C19A6B] transition-colors"
                />
              </div>
            </div>

            <div className="w-full md:w-64 shrink-0">
              <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-2 text-[#C19A6B]">Area Tongkrongan</label>
              <div className="relative">
                <select
                  value={activeKecamatan}
                  onChange={(e) => updateFilters('kecamatan', e.target.value)}
                  className="w-full appearance-none border-2 border-[#1A0F0A] bg-[#FAF6F0] px-4 py-3 pr-10 text-sm outline-none focus:border-[#C19A6B] transition-colors"
                >
                  <option value="">Semua Kecamatan</option>
                  {kecamatans.map((kec) => (
                    <option key={kec.id} value={kec.name}>{kec.name}</option>
                  ))}
                </select>
                <ChevronDown size={18} className="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1A0F0A]" />
              </div>
            </div>

            <div className="w-full md:w-64 shrink-0">
              <label className="block font-mono text-[10px] font-black uppercase tracking-[0.16em] mb-2 text-[#C19A6B]">Budget Max</label>
              <div className="relative">
                <select
                  value={activePrice}
                  onChange={(e) => updateFilters('price', e.target.value)}
                  className="w-full appearance-none border-2 border-[#1A0F0A] bg-[#FAF6F0] px-4 py-3 pr-10 text-sm outline-none focus:border-[#C19A6B] transition-colors"
                >
                  <option value="">Bebas, Sultan</option>
                  <option value="30000">Di bawah Rp 30K (Akhir Bulan)</option>
                  <option value="50000">Di bawah Rp 50K (Aman)</option>
                  <option value="100000">Di bawah Rp 100K (Treat Yoself)</option>
                </select>
                <ChevronDown size={18} className="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[#1A0F0A]" />
              </div>
            </div>
          </div>
        </section>

        {/* Cafe Grid */}
        <section className="px-4 md:px-8 max-w-7xl mx-auto" style={{ opacity: 1 }}>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" style={{ opacity: 1 }}>
            {localCafes.map((cafe, idx) => (
              <button
                type="button"
                key={cafe.id}
                onClick={() => setActiveCafe(cafe)}
                style={{ opacity: 1 }}
                className="group text-left border-2 border-[#1A0F0A] bg-white transition-all duration-500 ease-out hover:-translate-y-2 hover:shadow-[8px_8px_0px_0px_#1A0F0A] flex flex-col"
              >
                <div className="h-48 overflow-hidden border-b-2 border-[#1A0F0A] relative">
                  {cafePhoto(cafe) ? (
                    <img src={cafePhoto(cafe)} alt={cafe.nama} className="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110" />
                  ) : (
                    <div className="flex h-full items-center justify-center bg-[#1A0F0A] text-[#FAF6F0]">
                      <Coffee size={32} opacity={0.5} />
                    </div>
                  )}
                  <div className="absolute top-3 right-3 bg-white border-2 border-[#1A0F0A] px-2 py-1 font-mono text-[10px] font-black uppercase flex items-center gap-1">
                    <Star size={12} className="fill-current text-[#C19A6B]" /> {cafe.rating || 'N/A'}
                  </div>
                </div>

                <div className="p-4 flex-1 flex flex-col">
                  <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-1">{districtLabel(cafe) || 'Skena Area'}</p>
                  <h3 className="font-clash text-xl font-black uppercase mb-2 line-clamp-1">{cafe.nama}</h3>
                  <p className="text-sm leading-relaxed text-[#1A0F0A]/70 line-clamp-2 mb-4 flex-1">
                    {cafe.alamat}
                  </p>
                  <div className="mt-auto border-t-2 border-[#1A0F0A]/10 pt-4 flex items-center justify-between">
                    <span className="font-mono text-[10px] font-black uppercase tracking-[0.1em]">
                      Rp {formatRupiah(cafe.harga_min)} - {formatRupiah(cafe.harga_max)}
                    </span>
                    <ArrowUpRight size={16} className="transition-transform duration-300 group-hover:translate-x-1 group-hover:-translate-y-1" />
                  </div>
                </div>
              </button>
            ))}
          </div>

          {localCafes.length === 0 && (
            <div className="border-2 border-[#1A0F0A] bg-white p-12 text-center">
              <Coffee size={48} className="mx-auto mb-4 opacity-50" />
              <h3 className="font-clash text-2xl font-black uppercase mb-2">Kedai Nggak Ketemu</h3>
              <p className="text-[#1A0F0A]/70">Coba ganti filter lu. Mungkin standarnya ketinggian buat area ini.</p>
            </div>
          )}

          {nextPageUrl && (
            <div className="mt-12 text-center">
              <button
                onClick={handleLoadMore}
                disabled={isLoadingMore}
                className="magnetic border-2 border-[#1A0F0A] bg-white px-8 py-4 font-mono text-[12px] font-black uppercase tracking-[0.16em] hover:bg-[#1A0F0A] hover:text-[#FAF6F0] transition-colors disabled:opacity-50"
              >
                {isLoadingMore ? 'Lagi Nyeduh Data...' : 'Load Lebih Banyak Spot'}
              </button>
            </div>
          )}
        </section>
      </main>

        {/* Footer Restoration */}
        <footer className="mt-auto border-t-2 border-[#1A0F0A] bg-[#1A0F0A] text-[#FAF6F0] pt-16 pb-8 px-4 md:px-8">
          <div className="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            <div>
              <h4 className="font-clash text-3xl font-black uppercase mb-4 text-[#C19A6B]">Roastery Skena</h4>
              <p className="text-[#FAF6F0]/70 leading-relaxed text-sm">
                Nggak cuma soal biji kopi, tapi soal di mana lu nyaman buka laptop, ngobrol ngalor ngidul, atau sekadar bengong ngeliatin hujan. Superior Premium vibes, exclusively.
              </p>
            </div>
            <div>
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B] mb-6">Navigasi</p>
              <ul className="space-y-3 font-mono text-sm uppercase tracking-wide">
                <li><a href="#" className="hover:text-[#C19A6B] transition-colors">Home</a></li>
                <li><a href="#coffee-grid" className="hover:text-[#C19A6B] transition-colors">Direktori Kedai</a></li>
                <li><Link href="/dashboard" className="hover:text-[#C19A6B] transition-colors">Forum Skena</Link></li>
                <li><Link href="/register" className="hover:text-[#C19A6B] transition-colors">Join Member</Link></li>
              </ul>
            </div>
            <div>
              <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B] mb-6">Mantra Hari Ini</p>
              <div className="border-2 border-[#FAF6F0]/20 p-4 bg-white/5">
                <p className="font-clash text-xl font-black italic">"Kopi boleh pahit, hidup jangan ikutan melilit."</p>
              </div>
            </div>
          </div>
          <div className="max-w-7xl mx-auto border-t border-[#FAF6F0]/20 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-mono uppercase tracking-wider text-[#FAF6F0]/50">
            <p>&copy; {new Date().getFullYear()} Roastery Skena. All Vibes Reserved.</p>
            <p>Crafted for the Culture.</p>
          </div>
        </footer>

      {/* Fullscreen Wide Modal for Cafe Details */}
      {activeCafe && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-[#1A0F0A]/90 backdrop-blur-md p-4 sm:p-8"
          onClick={() => setActiveCafe(null)}
        >
          <div
            className="w-full max-w-6xl h-full max-h-[90vh] bg-[#FAF6F0] border-2 border-[#1A0F0A] overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-300"
            onClick={(e) => e.stopPropagation()}
          >
            {/* Modal Header */}
            <div className="flex items-center justify-between border-b-2 border-[#1A0F0A] p-4 bg-white">
              <div>
                <p className="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-[#C19A6B]">{districtLabel(activeCafe)}</p>
                <h2 className="font-clash text-2xl font-black uppercase">{activeCafe.nama}</h2>
              </div>
              <button
                onClick={() => setActiveCafe(null)}
                className="magnetic p-2 border-2 border-[#1A0F0A] hover:bg-[#1A0F0A] hover:text-white transition-colors"
              >
                <X size={24} />
              </button>
            </div>

            {/* Modal Body - Scrollable */}
            <div className="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Col: Image & Map */}
                <div className="lg:col-span-2 space-y-8">
                  <div className="aspect-video border-2 border-[#1A0F0A] overflow-hidden bg-[#1A0F0A]">
                    {cafePhoto(activeCafe) ? (
                      <img src={cafePhoto(activeCafe)} alt={activeCafe.nama} className="w-full h-full object-cover" />
                    ) : (
                      <div className="w-full h-full flex items-center justify-center text-white/20">
                        <Coffee size={64} />
                      </div>
                    )}
                  </div>

                  <div>
                    <h3 className="font-clash text-2xl font-black uppercase mb-4 flex items-center gap-2">
                      <Map size={24} /> Peta Lokasi
                    </h3>
                    <div className="border-2 border-[#1A0F0A] p-2 bg-white">
                      {activeCafe.nama ? (
                        <iframe
                          src={`https://maps.google.com/maps?q=${encodeURIComponent(activeCafe.nama + ' ' + (activeCafe.alamat || ''))}&output=embed`}
                          className="w-full h-[300px] bg-[#FAF6F0]"
                          loading="lazy"
                          title="Peta"
                        />
                      ) : (
                        <div className="h-[300px] flex items-center justify-center bg-[#FAF6F0] font-mono text-sm">
                          Loading Peta...
                        </div>
                      )}
                    </div>
                  </div>
                </div>

                {/* Right Col: Info & Actions */}
                <div className="space-y-6">
                  <div className="border-2 border-[#1A0F0A] p-6 bg-white">
                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-1">Status Operasional</p>
                    <p className="font-clash text-xl font-black">{activeCafe.jam_buka} - {activeCafe.jam_tutup}</p>

                    <div className="my-6 border-t-2 border-[#1A0F0A] border-dashed" />

                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-1">Rating Skena</p>
                    <div className="flex items-center gap-2">
                      <Star size={24} className="fill-current text-[#C19A6B]" />
                      <span className="font-clash text-3xl font-black">{activeCafe.rating || 'N/A'}</span>
                    </div>

                    <div className="my-6 border-t-2 border-[#1A0F0A] border-dashed" />

                    <p className="font-mono text-[10px] font-black uppercase tracking-[0.16em] text-[#C19A6B] mb-1">Budget Setup</p>
                    <p className="font-mono font-bold">Rp {formatRupiah(activeCafe.harga_min)} - Rp {formatRupiah(activeCafe.harga_max)}</p>
                  </div>

                  <div className="grid gap-3">
                    <a
                      href={activeCafe.latitude && activeCafe.longitude ? `https://www.google.com/maps/search/?api=1&query=${activeCafe.latitude},${activeCafe.longitude}` : '#'}
                      target="_blank"
                      rel="noreferrer"
                      className={btnPrimary + ' w-full'}
                    >
                      Buka Google Maps <ArrowUpRight size={16} />
                    </a>
                  </div>

                  {/* Leave Review Form */}
                  <div className="mt-4">
                    {user ? (
                      <form onSubmit={(e) => {
                        e.preventDefault();
                        if (!reviewInput.trim() || isSubmittingReview) return;
                        setIsSubmittingReview(true);
                        router.post(`/coffee-shops/${activeCafe.id}/review`, { review: reviewInput }, {
                          preserveScroll: true,
                          onSuccess: () => {
                            const newReview = {
                              id: Date.now(),
                              review: reviewInput,
                              user: user
                            };
                            setActiveCafe(prev => ({ ...prev, reviews: [newReview, ...(prev.reviews || [])] }));
                            setReviewInput('');
                            setIsSubmittingReview(false);
                          },
                          onError: () => setIsSubmittingReview(false)
                        });
                      }} className="flex flex-col gap-2">
                        <textarea
                          value={reviewInput}
                          onChange={(e) => setReviewInput(e.target.value)}
                          placeholder="Tulis kata anak skena di sini..."
                          className="w-full border-2 border-[#1A0F0A] p-2 bg-[#FAF6F0] text-sm min-h-[80px] outline-none focus:border-[#C19A6B] custom-scrollbar"
                        ></textarea>
                        <button type="submit" disabled={isSubmittingReview} className={btnSecondary + ' w-full'}>
                          {isSubmittingReview ? 'Mengirim...' : 'Kirim Review'}
                        </button>
                      </form>
                    ) : (
                      <button onClick={() => setGuestModal(true)} className={btnSecondary + ' w-full'}>
                        Tinggalkan Review
                      </button>
                    )}
                  </div>

                  {/* Quick Reviews */}
                  <div>
                    <h4 className="font-clash text-lg font-black uppercase mb-3">Kata Anak Skena</h4>
                    <div className="space-y-3">
                      {(activeCafe.reviews || []).slice(0, 3).map(rev => (
                        <div key={rev.id} className="border-2 border-[#1A0F0A] p-3 bg-white">
                          <div className="flex items-center gap-2 mb-2">
                            <img src={getAvatarUrl(rev.user)} className="w-6 h-6 border border-[#1A0F0A]" alt="User" />
                            <span className="font-mono text-[10px] font-bold uppercase">{rev.user?.name || 'Anon'}</span>
                          </div>
                          <p className="text-sm italic text-[#1A0F0A]/80">"{rev.review}"</p>
                        </div>
                      ))}
                      {(!activeCafe.reviews || activeCafe.reviews.length === 0) && (
                        <p className="text-sm font-mono text-[#1A0F0A]/50">Belum ada review. Jadilah perintis!</p>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Guest Barrier Modal */}
      {guestModal && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-[#1A0F0A]/80 backdrop-blur-sm p-4" onClick={() => setGuestModal(false)}>
          <div className="w-full max-w-sm bg-white border-2 border-[#1A0F0A] p-6 shadow-[8px_8px_0px_0px_#C19A6B] animate-in slide-in-from-bottom-4" onClick={e => e.stopPropagation()}>
            <Lock size={32} className="text-[#C19A6B] mb-4" />
            <h3 className="font-clash text-2xl font-black uppercase mb-2">Login Dulu Boss</h3>
            <p className="text-sm text-[#1A0F0A]/80 mb-6">Buat nulis review atau gabung forum, lu harus punya kartu akses (baca: akun). Tenang, gratis kok.</p>
            <div className="flex flex-col gap-3">
              <Link href="/login" className={btnPrimary}>Gas Login</Link>
              <button onClick={() => setGuestModal(false)} className={btnSecondary}>Nanti Aja</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
