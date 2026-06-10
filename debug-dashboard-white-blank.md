# Debug Session: dashboard-white-blank

**Status**: [OPEN]
**Start Date**: 2026-06-09

## 1. Symptoms
- User reports a "white blank" screen when accessing the `/dashboard` route.
- Other pages might be working (unconfirmed, but user specifically mentioned dashboard).

## 2. Hypotheses
- **H1**: `Dashboard.jsx` crashes during render due to invalid `directMessages` or `forums` data.
- **H2**: PHP Fatal Error in `web.php` during DM grouping or tag generation.
- **H3**: Crash in `Navbar.jsx` due to `auth.user` being in an unexpected state.
- **H4**: Infinite loop or crash in `Dashboard.jsx` `useEffect` or `useMemo`.

## 3. Instrumentation Plan
- [ ] Instrument `web.php` (`/dashboard` route) to log variables before `return inertia(...)`.
- [ ] Instrument `Dashboard.jsx` to log received props in the component body.
- [ ] Instrument `Navbar.jsx` to log `user` object.

## 4. Evidence Collection
(Pending logs)

## 5. Analysis
(Pending)

## 6. Fix
(Pending)

## 7. Verification
(Pending)
