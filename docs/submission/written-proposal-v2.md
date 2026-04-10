# RAPIDA — Written Proposal V2 (InnoCentive Submission)

> **Status:** Ready for Abayomi's voice review
> **Deadline:** June 23, 2026
> **Each section:** Max 500 words
> **Voice:** First-person plural ("we") with Abayomi's personal narrative
> **Critical:** Must not read as AI-generated. Foreground human design decisions.

---

## Section 1 — Problem & Opportunity

*Highlight the innovation in your approach to the Problem, its point of difference, and the specific advantages/benefits this brings. Up to 500 words.*

---

When I discovered the UNDP "Build the Future of Crisis Mapping" challenge, I was drawn to a specific sentence: "ordinary folk who have access to smartphones." That phrase changed how I approached the entire problem.

UNDP's RAPIDA methodology generates crisis insights within 72 hours using satellite imagery and remote analytics. But satellites cannot determine whether a hospital is still functioning, whether debris is blocking a road, or whether a community has lost access to water. Comprehensive field assessments that fill this gap take days or weeks — time that costs lives. As Arnold Njogu from UNDP's Crisis Bureau described it: when we talk about damaged buildings, we are talking about real families left without shelter.

I spent weeks in product discovery before writing a single line of code. I mapped the humanitarian technology landscape — Ushahidi, KoboToolbox, Esri's damage assessment suite, Crisis Track — and found a consistent gap: none of these tools treat the community reporter as the primary user. They are built for field assessors, not for someone under acute stress, reporting on a mid-range Android in French or Arabic, with intermittent 3G signal.

That finding led to three decisions that define RAPIDA's innovation.

First, I built a trauma-informed interface design system before touching application code. RAPIDA UI is a 48-component atomic design system — atoms, molecules, organisms, templates — grounded in psychological safety principles. No red anywhere (it triggers stress responses). Minimum 48px touch targets (for shaking hands). Plain-language copy reviewed through Erika Hall's conversational design framework. The design system was completed, documented, and audited before the first Laravel migration ran. This is how we ensure that every screen a crisis-affected person sees was designed for them, not adapted for them.

Second, we built WhatsApp as a complete parallel reporting channel — not a notification bridge. A community member can complete the full five-step assessment (photo, location, damage classification, infrastructure type, modular questions) through a WhatsApp conversation without ever visiting a website. We also built the backend with a channel-agnostic adapter pattern, so Telegram and SMS can be added without restructuring.

Third, we designed for conflict zones from day one. Persona E — a reporter in an active conflict zone — required us to build a mode where the system deliberately reduces its own capability to protect the user: no device fingerprinting, no account prompts, no leaderboard, IndexedDB cleared after submission, and a transparency screen that removes any mention of location sharing. The device stays clean. The server holds the evidence.

No existing tool satisfies all of the challenge's requirements simultaneously. RAPIDA is the only open-source, community-first, offline-capable, multilingual crisis damage reporting platform with building-footprint-level geolocation, WhatsApp as a full intake channel, privacy-conscious aggregated display, and a conflict-safe reporting mode.

**~430 words**

---

## Section 2 — Solution Overview

*Detail the features of your solution and how they address the Solution Requirements. 500 words.*

---

RAPIDA is built on an open-source stack: Laravel 13, Livewire 4, Alpine.js, Tailwind CSS, MapLibre GL, and PostGIS on Neon Serverless Postgres.

**Capture — Reporter Experience**

The reporter interface is a five-step wizard completable in under three minutes: (1) photo with embedded guidance and client-side compression for 2G/3G networks, (2) building location via interactive MapLibre map with Microsoft ML Building Footprints, GPS auto-snap, what3words, or landmark fallback, (3) damage classification at three levels with AI-suggested pre-selection and confidence scores, (4) infrastructure details and crisis type, (5) UNDP operator-configured modular questions including all three Appendix 1 modules.

A transparency screen explains what RAPIDA collects and what it does not — with a separate conflict-mode variant that removes any mention of location sharing. Photo EXIF metadata is stripped at upload. Client-side Canvas API compression reduces 5MB photos to under 500KB, making upload viable on 2G connections.

A complete WhatsApp channel provides the same five-step flow via Twilio — no app installation required. In conflict-mode crises, the WhatsApp channel is automatically disabled or switches to landmark-only location prompts.

All six UN languages are fully supported (Arabic with RTL, Chinese, English, French, Russian, Spanish). A compact language dropdown allows switching from any page.

**Display — Privacy-Conscious Mapping**

The community-facing map displays aggregated H3 hexagonal zones — not individual report locations — satisfying the privacy-conscious display requirement. Each hexagon shows report count and damage level breakdown on tap. Analyst and field coordinator dashboards retain individual pin views for operational response.

The analyst dashboard provides: KPI cards, reports-over-time chart, infrastructure breakdown, top 10 most-reported buildings, a verification workflow (assign → field check → verified/disputed), a separate redundancy queue for duplicate detection, and AI confidence scores on every report.

**Storage — Credible Data**

The credibility scoring system requires three elements for a minimum credible report (score ≥6 of 8): GPS location (+2), photographic evidence (+2), and standardized damage categorization (+2). Supporting infrastructure detail (+1) and building name (+1) complete the scale. Reports below the credibility threshold are flagged, not discarded.

The database uses PostGIS with H3 spatial indexing. Report versioning tracks all submissions per building with a canonical report selected by completeness score. A three-tier authentication model (anonymous → device-verified → account-verified) builds trust incrementally. In conflict mode, only anonymous reporting is available.

**Export — Five Formats**

Data exports in CSV, GeoJSON, KML, Shapefile (.zip), and PDF summary — compatible with QGIS, ArcGIS, Google Earth, and the RAPIDA methodology pipeline. AI confidence scores and suggested classifications are included in all export formats.

**Resilience**

Circuit breakers, backpressure middleware, and queue segregation ensure the system degrades gracefully under load — AI classification pauses before report submission does. Six named rate limiters prevent abuse. Manual pause mode lets operators temporarily halt intake per crisis.

**~460 words**

---

## Section 3 — Solution Feasibility

*Supporting information and rationale to help UNDP evaluate feasibility. Include cost and usability by non-experts. Up to 500 words.*

---

**Usability by non-experts**

The reporter wizard requires no training, no account, and no prior experience. The interface was designed using a purpose-built trauma-informed design system — WCAG AA contrast ratios, minimum 48px touch targets, generous spacing, and plain-language copy in six languages. Client-side image compression means a 5MB camera photo becomes a 300KB upload — viable even on 2G networks where a raw photo would take 13 minutes to transfer.

UNDP operators can activate a new crisis instance, configure modular form fields, set the map region, toggle conflict mode, and manage the landmark database through an admin panel — no code deployment required. A new crisis can be live within four hours of a disaster event.

Field coordinators access a mobile-first dashboard with a full-screen map of live incoming reports, filterable by damage level and infrastructure type. Remote analysts access a desktop dashboard with analytics, verification workflow, and five-format data export.

**Technical feasibility**

Every component is production-proven. Laravel 13 powers major web applications worldwide. Neon Serverless Postgres auto-scales with PostGIS spatial capabilities. MapLibre GL is the open-source mapping engine used by humanitarian organizations globally. Microsoft ML Building Footprints provides open global building polygon data.

The AI damage classification component runs inference via ONNX runtime — no GPU required. Confidence scores are displayed to reporters and analysts, with human verification always the final decision. The system tracks AI suggestion versus reporter override divergence for continuous model performance monitoring.

Offline capability is implemented via a PWA service worker (Workbox) that caches the wizard, map tiles, and self-hosted fonts on first load. Reports submitted offline queue in IndexedDB and sync automatically when connectivity returns. Self-hosted fonts (Inter + Noto Sans) ensure consistent rendering even offline — no CDN dependency.

The resilience layer includes circuit breakers for external services (AI, translation, geocoding), backpressure middleware that monitors queue depth and progressively defers non-essential processing, and a channel-agnostic messaging adapter that currently serves WhatsApp with Telegram architecturally ready.

**Cost**

The platform is built entirely on open-source components (MIT, BSD, Apache 2.0, PostgreSQL license). Infrastructure costs for a medium-scale crisis (250,000 reports) are estimated at under $400/month on Laravel Cloud + Neon, scaling to approximately $1,200/month at national scale (500,000 reports). Twilio WhatsApp messaging adds approximately $0.005 per message. Client-side image compression reduces storage costs proportionally.

**Precedent**

UN ASIGN (UNOSAT) has demonstrated community-sourced crisis mapping using what3words — a component we incorporate as a GPS fallback. ICRC RedSafe established security-by-design benchmarks for humanitarian digital platforms — RAPIDA exceeds its anonymous reporting model with a dedicated conflict mode. Ushahidi proved crowdsourced crisis mapping at scale with 40,000+ reports during the 2010 Haiti earthquake — RAPIDA builds on that precedent with offline-first PWA, building footprints, and trauma-informed design.

**~430 words**

---

## Section 4 — Experience

*Expertise, use cases, and skills relevant to the proposed solution. Up to 500 words.*

---

My name is Abayomi Ogundipe. I am the founder of Festa Design Studio, a social impact design and technology practice. I have spent fifteen years building technology for communities under constraint — and that experience is the foundation of every design decision in RAPIDA.

**Designing for vulnerable populations**

From 2013 to 2023, I led TEKEDU, a nonprofit focused on technology-enabled education for marginalized communities in Moldova. Our flagship program, GirlsGoIT — co-designed with UN Women and the Government of Moldova — reached over 5,200 adolescent girls across 29 camps in 26 of Moldova's 35 districts. It won the With and For Girls Award in 2017 and was featured in UN Women's "Women in ICT and STEM" publication. UPSHIFT, co-designed with UNICEF Moldova, trained NEET youth in social entrepreneurship. STEAMpeRoti ("STEAM on Wheels"), funded by the Swiss Agency for Development and UNICEF, brought mobile STEAM education to rural districts.

Every one of these programs required the same design discipline that RAPIDA demands: interfaces that are accessible, trust-building, and completable under constraint. When you design a coding curriculum for a 13-year-old girl in a rural Moldovan village who has never touched a laptop, you learn that complexity is the enemy of inclusion.

**Humanitarian technology in practice**

At Festa Design Studio, we design for good. Our in-house projects directly informed RAPIDA's approach. The Zika Alert App — a mobile-first PWA for real-time health alerts — uses the same architecture as RAPIDA: Laravel on Laravel Cloud, community-first design, crisis context, and accessible information delivery. Logframe — a visual workspace that turns theories of change into living project trackers — reflects the same structured impact thinking that shapes RAPIDA's incentivization system and recovery outcome feedback loop.

We also teach nonprofits and impact startups evidence-based project design through "Setup Your Project for Success" — an 8-lesson methodology covering Problem Tree Analysis, Stakeholder Mapping, Data Synthesis, Theory of Change, and Logical Frameworks. This is the same methodology I applied to RAPIDA: I did not start with code. I started with the problem tree. I mapped stakeholders. I synthesized competitive research. I built the theory of change. Then I designed the interface. Then I wrote the code.

**Design systems expertise**

RAPIDA UI is a 48-component atomic design system built from scratch — 12 atoms, 10 molecules, 9 organisms, 9 templates — with a trauma-informed colour system, a typography scale for multilingual legibility including Arabic RTL, and a comprehensive state token system. The design system was completed and documented before application development began. Every component went through Erika Hall's conversational design framework to ensure that words, visuals, and behaviour are aligned.

**Technical depth**

I hold an Advanced Diploma in International Development from the University of Cambridge and professional diplomas in UX Design (UX Design Institute, Ireland) and Front-end Development (General Assembly). My engineering practice centres on the Laravel ecosystem — the same stack that powers RAPIDA.

**~440 words**

---

## Section 5 — Solution Risks

*Risks you foresee and how you would plan for them. Up to 500 words.*

---

**Risk 1: Conflict-zone data subpoena**
*Likelihood: Low · Impact: Critical*

In conflict settings, an armed actor or government could demand RAPIDA's data to identify reporters. This is the single highest-impact risk.

*Mitigation:* Conflict mode (`conflict_context=true`) collects zero personally identifiable information. Device fingerprinting is disabled at both the JavaScript and database levels — defense in depth. Anonymous reports cannot be linked to any device or individual. The reporter's device is clean after submission (IndexedDB cleared). Even under subpoena, the server data contains location and photos but nothing that identifies who submitted them.

**Risk 2: Coordinated bad-actor report flooding**
*Likelihood: Low · Impact: High*

Anonymous reporting is vulnerable to coordinated false reporting during politically sensitive crises.

*Mitigation:* Four-layer defense: (1) perceptual hash duplicate photo detection flags near-identical photos to the same building, (2) behavioural rate limiting caps submissions per device and per building per 24-hour window, (3) device fingerprint bans (in non-conflict mode) identify repeat bad actors, (4) analyst redundancy queue separates flagged duplicates from legitimate verification items. All flagged reports enter review before influencing canonical damage levels.

**Risk 3: Building footprint coverage gaps**
*Likelihood: Medium · Impact: Medium*

Microsoft ML Building Footprints provides strong coverage but is incomplete in some rural and informal settlement areas.

*Mitigation:* A full fallback geolocation stack ensures no reporter is blocked: GPS coordinates, what3words codes (3m×3m precision), a structured landmark picker pre-seeded by UNDP operators, and free-text landmark description. The credibility score awards location points regardless of method — building tap, GPS snap, what3words, or landmark all count equally.

**Risk 4: AI classification bias on non-Western building types**
*Likelihood: Medium · Impact: Medium*

The damage classification model trained on xBD (earthquake and wildfire-dominant dataset) may underperform in less-represented crisis types or regional building styles.

*Mitigation:* The AI suggestion is always presented with a confidence percentage — "92% confident: Complete damage. Does this match what you see?" Low-confidence suggestions explicitly invite the reporter's judgment. The reporter's choice overrides the model in all cases. AI versus reporter override divergence is tracked in the analyst dashboard, providing continuous model accuracy signals. Confidence scores are included in all data exports for downstream analysis.

**Risk 5: Low-bandwidth upload failure**
*Likelihood: High · Impact: Medium*

On 2G networks (50 kbps), a raw 5MB phone photo takes approximately 13 minutes to upload — likely to fail.

*Mitigation:* Client-side Canvas API compression resizes photos to max 1280px and exports at adaptive quality (2G: 50%, 3G: 65%, 4G: 70%), producing files under 500KB. Upload time drops from 13 minutes to approximately 60 seconds on 2G. The offline queue with Background Sync retries automatically. Backpressure middleware adjusts sync frequency based on server load, preventing client-side crashes during peak reporting.

**~430 words**

---

## Section 6 — Online References

*Links to publications, articles, or press releases of relevance. Quality over quantity.*

---

**RAPIDA Methodology — UNDP**
UNDP's official documentation of the RAPIDA assessment methodology. Our application is designed to feed directly into this pipeline.
https://www.undp.org/publications/rapid-post-crisis-integrated-digital-assessment-rapida-methodology

**UN ASIGN — UNOSAT**
The UN precedent for community-sourced crisis mapping. RAPIDA incorporates what3words (used by UN ASIGN) as a GPS fallback.
https://unitar.org/sustainable-development-goals/united-nations-satellite-centre-unosat/our-portfolio/un-asign

**Microsoft ML Building Footprints**
The open global building footprint dataset (1.4B+ buildings) used for building-level geolocation.
https://github.com/microsoft/GlobalMLBuildingFootprints

**xBD Dataset — Maxar Technologies**
The open disaster imagery dataset used for AI damage classification model training.
https://xview2.org/

**MapLibre GL JS**
Open-source mapping library (BSD) used for interactive maps and offline tile caching.
https://maplibre.org/

**ICRC RedSafe**
Humanitarian digital platform with security-by-design benchmark. RAPIDA exceeds its anonymous reporting model with a dedicated conflict mode.
https://redsafe.icrc.org

**Festa Design Studio — In-House Projects**
Our portfolio of humanitarian technology projects, including the Zika Alert App (health crisis PWA) and Logframe (impact tracking).
https://festa.design/projects/in-house

**Setup Your Project for Success — Festa Toolkit**
Our evidence-based project design methodology for nonprofits and impact organizations.
https://toolkit.festa.design/about

**Abayomi Ogundipe — Professional Background**
Founder profile with 15+ years of international development experience, UNICEF/UN Women partnerships, and program design credentials.
https://festa.design/about/team/abayomi-ogundipe

**~180 words (references are concise by nature)**

---

## Submission Checklist

- [ ] Section 1 reviewed in Abayomi's voice — personal story authentic
- [ ] Section 2 word count ≤ 500
- [ ] Section 3 word count ≤ 500
- [ ] Section 4 reviewed — personal projects verified, dates correct
- [ ] Section 5 word count ≤ 500
- [ ] Section 6 — all URLs verified live and accessible
- [ ] Participation type: Solver (Organization) — Festa Design Studio
- [ ] TRL: 5-6
- [ ] Partnering interest: Yes
- [ ] Prototype URL + credentials provided
- [ ] Video link provided (≤ 2 minutes)
