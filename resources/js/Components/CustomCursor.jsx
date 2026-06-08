import React, { useEffect } from 'react';

export default function CustomCursor() {
  useEffect(() => {
    // Only run on non-touch devices
    if (typeof window === 'undefined' || !window.matchMedia('(pointer: fine)').matches) {
      return;
    }

    const dot = document.getElementById('fluid-cursor-dot');
    const ring = document.getElementById('fluid-cursor-ring');
    if (!dot || !ring) return;

    let mouseX = window.innerWidth / 2;
    let mouseY = window.innerHeight / 2;
    let ringX = mouseX;
    let ringY = mouseY;
    let isHovering = false;

    // Fast mousemove tracking
    const handleMouseMove = (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      
      // Update dot immediately for snappiness
      dot.style.transform = `translate(calc(${mouseX}px - 50%), calc(${mouseY}px - 50%))`;
    };

    // Animation loop for the lagging ring
    let animationFrameId;
    const render = () => {
      // Linear interpolation (lerp) for smooth lag
      ringX += (mouseX - ringX) * 0.15;
      ringY += (mouseY - ringY) * 0.15;

      if (!isHovering) {
        ring.style.transform = `translate(calc(${ringX}px - 50%), calc(${ringY}px - 50%))`;
      }
      animationFrameId = requestAnimationFrame(render);
    };

    // Event delegation on document.body for Magnetic Snapping
    // This perfectly survives Inertia SPA navigations without needing to remount
    const handleMouseOver = (e) => {
      try {
        if (!(e.target instanceof Element)) return;
        const target = e.target.closest('a, button, input, select, textarea, .magnetic');
        if (target) {
          isHovering = true;
          document.body.classList.add('is-magnetic');
          
          const rect = target.getBoundingClientRect();
          const centerX = rect.left + rect.width / 2;
          const centerY = rect.top + rect.height / 2;
          ring.style.transform = `translate(calc(${centerX}px - 50%), calc(${centerY}px - 50%))`;
        }
      } catch (err) {
        console.warn('Cursor hover error:', err);
      }
    };

    const handleMouseOut = (e) => {
      try {
        if (!(e.target instanceof Element)) return;
        const target = e.target.closest('a, button, input, select, textarea, .magnetic');
        if (target) {
          isHovering = false;
          document.body.classList.remove('is-magnetic');
        }
      } catch (err) {
        console.warn('Cursor hover out error:', err);
      }
    };

    window.addEventListener('mousemove', handleMouseMove, { passive: true });
    document.body.addEventListener('mouseover', handleMouseOver);
    document.body.addEventListener('mouseout', handleMouseOut);
    render();

    return () => {
      window.removeEventListener('mousemove', handleMouseMove);
      document.body.removeEventListener('mouseover', handleMouseOver);
      document.body.removeEventListener('mouseout', handleMouseOut);
      cancelAnimationFrame(animationFrameId);
      document.body.classList.remove('is-magnetic');
    };
  }, []);

  // We render the divs here. They will be styled globally by app.css.
  return (
    <>
      <div id="fluid-cursor-dot" />
      <div id="fluid-cursor-ring" />
    </>
  );
}
