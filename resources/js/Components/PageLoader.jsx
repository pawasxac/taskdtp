import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';

/**
 * PageLoader — muncul di setiap navigasi Inertia (pindah page)
 * dan saat form submit. Desain mengikuti splash screen NGOPI yang
 * sudah ada di Welcome.jsx.
 */
export default function PageLoader() {
    // Helper to check if a pathname is for global chat, DM, or community chat
    const isExcludedPath = (path) => {
        if (!path) return false;
        return (
            path.startsWith('/dashboard') ||
            path.startsWith('/chat/send') ||
            path.startsWith('/dm/send') ||
            /^\/komunitas\/[^/]+\/post/.test(path)
        );
    };

    const [visible, setVisible] = useState(() => {
        // Jangan tampil saat pertama kali berada di path yang dikecualikan
        return !isExcludedPath(window.location.pathname);
    });
    const [fadeOut, setFadeOut] = useState(false);

    useEffect(() => {
        let fadeTimer = null;
        let hideTimer = null;

        const show = () => {
            // Jangan tampil jika path saat ini dikecualikan
            if (isExcludedPath(window.location.pathname)) return;
            setFadeOut(false);
            setVisible(true);
        };

        const hide = () => {
            setFadeOut(true);
            hideTimer = setTimeout(() => {
                setVisible(false);
                setFadeOut(false);
            }, 400);
        };

        // ── Saat refresh/first load: sembunyikan setelah 1.8 detik ──
        // Hanya jika BUKAN di path yang dikecualikan
        let initialFade = null;
        let initialHide = null;
        if (!isExcludedPath(window.location.pathname)) {
            initialFade = setTimeout(() => setFadeOut(true), 1400);
            initialHide = setTimeout(() => {
                setVisible(false);
                setFadeOut(false);
            }, 1800);
        }

        // ── Setiap navigasi Inertia ──
        const removeStart = router.on('start', (event) => {
            // Cek URL tujuan navigasi — jangan tampil jika menuju ke path yang dikecualikan
            const targetUrl = event?.detail?.visit?.url?.pathname ?? '';
            if (isExcludedPath(targetUrl)) return;
            show();
        });
        const removeFinish = router.on('finish', hide);

        // ── Setiap form submit ──
        const handleFormSubmit = (e) => {
            // Cek target action dari form jika ada
            const action = e.target?.action ? new URL(e.target.action).pathname : '';
            if (isExcludedPath(window.location.pathname) || isExcludedPath(action)) return;
            show();
            fadeTimer = setTimeout(hide, 4000);
        };
        document.addEventListener('submit', handleFormSubmit);

        return () => {
            removeStart();
            removeFinish();
            document.removeEventListener('submit', handleFormSubmit);
            if (initialFade) clearTimeout(initialFade);
            if (initialHide) clearTimeout(initialHide);
            if (fadeTimer) clearTimeout(fadeTimer);
            if (hideTimer) clearTimeout(hideTimer);
        };
    }, []);

    if (!visible) return null;

    return (
        <div
            className={`
                fixed inset-0 z-[9999] flex flex-col items-center justify-center
                bg-[#1A0F0A] text-[#FAF6F0]
                transition-opacity duration-400 ease-in-out
                ${fadeOut ? 'opacity-0 pointer-events-none' : 'opacity-100'}
            `}
        >
            <div className="flex flex-col items-center gap-4">
                {/* Coffee cup SVG dengan steam animasi */}
                <div className="relative mb-2">
                    <svg
                        width="48"
                        height="48"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#C19A6B"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        className="animate-bounce"
                    >
                        {/* Steam lines */}
                        <path d="M8 2v2M12 2v2M16 2v2" />
                        {/* Cup body */}
                        <path d="M3 10h18l-2 9H5L3 10z" />
                        {/* Saucer */}
                        <path d="M2 20h20" />
                        {/* Handle */}
                        <path d="M21 12h1a2 2 0 0 1 0 4h-1" />
                    </svg>
                </div>

                <h1
                    className="font-clash text-4xl font-black uppercase tracking-widest"
                    style={{ fontFamily: "'Clash Display', 'Inter', sans-serif" }}
                >
                    NGOPI
                </h1>

                <div className="flex items-center gap-1">
                    {/* Dot loading animation */}
                    {[0, 1, 2].map((i) => (
                        <span
                            key={i}
                            className="block w-1.5 h-1.5 rounded-full bg-[#C19A6B]"
                            style={{
                                animation: `bounce 0.8s ease-in-out ${i * 0.15}s infinite`,
                            }}
                        />
                    ))}
                </div>

                <p
                    className="font-mono text-[10px] uppercase tracking-[0.3em] text-[#C19A6B] mt-1"
                    style={{ fontFamily: "'Space Mono', monospace" }}
                >
                    Loading Vibes...
                </p>
            </div>

            <style>{`
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50%       { transform: translateY(-6px); }
                }
            `}</style>
        </div>
    );
}
