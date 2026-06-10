import React, { useEffect, useRef, useState } from 'react';
import gsap from 'gsap';

export const CustomCursor = () => {
  const dot = useRef(null);
  const ring = useRef(null);
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {

    if (!dot.current || !ring.current) return;

    const xToDot = gsap.quickTo(dot.current, "x", { duration: 0.1, ease: "power3" });
    const yToDot = gsap.quickTo(dot.current, "y", { duration: 0.1, ease: "power3" });
    const xToRing = gsap.quickTo(ring.current, "x", { duration: 0.3, ease: "power3" });
    const yToRing = gsap.quickTo(ring.current, "y", { duration: 0.3, ease: "power3" });

    const move = (e) => {
      if (!isVisible) setIsVisible(true);
      xToDot(e.clientX); yToDot(e.clientY);
      xToRing(e.clientX); yToRing(e.clientY);
    };

    window.addEventListener('mousemove', move);
    return () => window.removeEventListener('mousemove', move);
  }, [isVisible]);

  return (
    <div style={{ opacity: isVisible ? 1 : 0, transition: 'opacity 0.2s', pointerEvents: 'none', position: 'fixed', zIndex: 99999, top: 0, left: 0 }}>
      <div ref={dot} className="fixed top-0 left-0 w-2 h-2 bg-terracotta rounded-full pointer-events-none mix-blend-difference -translate-x-1/2 -translate-y-1/2" />
      <div ref={ring} className="fixed top-0 left-0 w-8 h-8 border border-terracotta rounded-full pointer-events-none mix-blend-difference -translate-x-1/2 -translate-y-1/2" />
    </div>
  );
};
