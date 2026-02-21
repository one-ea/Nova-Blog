# Surface Harmony UI Polish Design

**Date:** 2026-02-20
**Version:** 2.4.0 → 2.5.0
**Approach:** Surface Harmony — M3 elevation + subtle gradients + geometric decorations + scroll-in animations

## Design Principles

1. Enhance within M3's Surface + Elevation system
2. Primary→Tertiary gradient as recurring decorative language
3. Scroll-in animations for dynamic content reveal
4. Zero PHP template changes (pure CSS + JS enhancement)

## Changes

### A. Homepage

- A1: Hero gradient background (primary-container → tertiary-container) + CSS geometric decoration
- A2: Card hover enhancement (translateY -4px, elevation-level-3)
- A3: Featured post visual emphasis (extra-large corners, surface-container background)

### B. Article Detail

- B1: Reading progress bar (3px, primary color, scroll-driven)
- B2: Heading accent bars (h2/h3 left border in primary)
- B3: Blockquote gradient border (primary → tertiary)
- B4: Code block language labels (::before pseudo-element)

### C. Components & Animation

- C1: Scroll-in fade-up animation (IntersectionObserver + .scroll-in utility class)
- C2: Card hover image overlay (primary 0.03 opacity tint)
- C3: FAB bounce-in animation

### D. Global Rhythm

- D1: Hero bottom curve (clip-path)
- D2: Footer gradient accent line (primary → tertiary, 4px)
- D3: Section spacing increase (48px/64px breathing room)
- D4: Surface hierarchy consistency check

## Files

| Change | File | Type |
|--------|------|------|
| Hero gradient + decoration | theme.css | CSS |
| Card hover enhancement | theme.css, components.css | CSS |
| Reading progress bar | theme.css, scroll-animate.js | CSS+JS |
| Heading/blockquote decoration | base.css | CSS |
| Code block labels | base.css | CSS |
| Scroll-in animation | theme.css, scroll-animate.js | CSS+JS |
| Section curves | theme.css | CSS |
| Footer gradient line | theme.css | CSS |
| Spacing adjustments | theme.css | CSS |
| FAB bounce | components.css | CSS |

~200 lines CSS + ~40 lines JS. No PHP changes.
