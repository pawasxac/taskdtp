import './bootstrap';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { AuthProvider } from './Components/Shared';
import PageLoader from './Components/PageLoader';
import Lenis from '@studio-freight/lenis';
import { useEffect } from 'react';

const appName = import.meta.env.VITE_APP_NAME || 'NGOPI';

const AppWrapper = ({ App, props }) => {
    useEffect(() => {
        // Safe init Lenis smooth scroll
        let lenis = null;
        let rafId = null;
        try {
            lenis = new Lenis({
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smoothWheel: true,
            });

            const raf = (time) => {
                lenis.raf(time);
                rafId = requestAnimationFrame(raf);
            };
            rafId = requestAnimationFrame(raf);
        } catch (e) {
            console.warn('Lenis init failed', e);
        }

        return () => {
            if (rafId) cancelAnimationFrame(rafId);
            if (lenis && typeof lenis.destroy === 'function') {
                try { lenis.destroy(); } catch {}
            }
        };
    }, []);

    return (
        <AuthProvider initialUser={props?.initialPage?.props?.auth?.user ?? null}>
            <PageLoader />
            <App {...props} />
        </AuthProvider>
    );
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<AppWrapper App={App} props={props} />);
    },
    progress: {
        color: '#C19A6B',
    },
});
