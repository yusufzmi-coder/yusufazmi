# Reviews, pain points and a scraping plan for tuition-centre / preschool / school management software (Malaysia & Singapore first)

> **Method note / reliability warning (read first).** This researcher session hit two hard limits: (1) the shared web-search budget was exhausted after ~25 queries, and (2) the sandbox's egress proxy blocked direct fetches of every review site tried (Google Play, App Store, Capterra, G2, GetApp, Trustpilot, AppGrooves, AppBrain, APKPure/APKCombo, Lowyat, Reddit, Quora, Facebook, TikTok, YouTube, apify.com, vendor sites, Wayback Machine). Only GitHub raw files, PyPI and npm were reachable. Consequently:
> - Review content below comes from **search-engine result summaries of the review pages**, not from the pages themselves. Every quote is marked "paraphrase via search summary" unless it is verbatim from a fetched file. Treat star ratings/installs as "as reported by the search summary on 2026-09-29" and re-verify with the scraping plan in Part B.
> - No review was invented. Where a source returned nothing, the note says so.
> - Part B (scraping plan) is the most actionable deliverable: it is exactly what is needed to fill the gaps in Part A, and the actor IDs / prices listed are the ones the search results surfaced. Field names for actor inputs are given as best-known defaults and must be checked against each actor's "Input" tab before running.

---

## KQ1. What do Google Play / App Store reviews say about AOneSchool, LittleTree and other MY/SG centre apps (ratings, installs, last update)?

### Takeaway
AOne's parent/teacher app is confirmed to exist on Google Play (package `com.aoneschool`) and the App Store (id `1422236557`), but no rating, install count or review text could be retrieved this session. **No app or vendor called "LittleTree" could be found at all** across Google Play, App Store, Capterra, G2 or web search — the founder should confirm the exact spelling/package name. The richest retrievable review signal was for Singapore's LittleLives parent app (3.29/5 from ~1.1k ratings, ~300k downloads, heavily bug-related complaints) and Malaysia's Anak2U and illumine parent apps (chat/notification bugs, limited video storage).

### Cited Findings

**AOne / AOneSchools (Malaysia; also SG, PH)**
- Google Play listing: "AOne – Apps on Google Play", package `com.aoneschool`, developer page "Android Apps by AOne" (dev id 6446255935299801019). Store description: "Learning Center Management System and the leading cloud-based education software… manage class schedule, fee payment, student's attendance and many more in one app." (paraphrase via search summary) — [Google Play](https://play.google.com/store/apps/details?id=com.aoneschool); [Developer page](https://play.google.com/store/apps/dev?id=6446255935299801019&hl=en)
- App Store listing: "AOne" by **My Aone Learning**, app id 1422236557; description says it "brings parents, students, teachers, and center owners together"; features: class change/reschedule notices, real-time attendance, "manage payments and keep track of receipts", announcements (paraphrase via search summary) — [App Store (US storefront)](https://apps.apple.com/us/app/aone/id1422236557)
- Also distributed on Huawei AppGallery; parents log in with the phone number/email registered with the centre; **parents cannot log in via web browser, only the mobile app**; one account can hold multiple children across different schools if the same phone/email is used (paraphrase via search summary of AOne help centre) — [AOneSchools Freshdesk help](https://aoneschools-help.freshdesk.com/en/support/solutions/articles/153000142880-how-to-verify-an-account-for-a-new-user-); [AOne download page](https://aone.com.my/download-aoneschools-app/)
- Capterra has a product page "AOneSchools" (p/190964). Search summary says Attendance Tracking is "a feature reviewers rate highly" and lists support channels (email/help desk, FAQ/forum, knowledge base, phone, 24/7 live rep, chat). **No individual review text, count or rating was retrievable.** — [Capterra AOneSchools](https://www.capterra.com/p/190964/AOneSchools/)
- Vendor marketing claim: "trusted by 4,000+ tuition centres, preschools, enrichment centres, sports academies, and language centres" (vendor claim, unverified) — [aone.com.my](https://aone.com.my/); Singapore site — [aoneschools.sg](https://aoneschools.sg/)
- A third-party download mirror exists (Softonic "AOne for Android") that normally mirrors Play metadata, but it was blocked — [Softonic](https://aone.en.softonic.com/android)

**LittleTree**
- Searches for "LittleTree" + kindergarten/preschool/tadika/app/Malaysia returned only unrelated preschools named "Little Tree House" (Damansara), "Little Tree Preschool", a Hong Kong kindergarten (littletree.edu.hk), and other vendors (LittleLives, ClassFlow, KidzyPedia, myTadika). **No software vendor or app named LittleTree surfaced.** — [search result set 1](https://www.thelittletreehouse.com.my/); [search result set 2](https://www.kidzypedia.com/); [myTadika](https://mytadika.app/login)

**LittleLives (Singapore HQ; largest regional preschool platform)**
- Footprint: "over 750 schools in Singapore, 145 in Malaysia, 130 in China, 60 in Vietnam and 2 in Cambodia" (paraphrase via search summary; date of that count not stated) — [Startup Fortune](https://startupfortune.com/littlelives-brings-an-app-to-help-early-childhood-centres-to-keep-parents-updated/); [littlelives.com](https://www.littlelives.com/)
- Parent app "Little Family Room for Parents" (`com.littlelives.familyroom`): **rated 3.29/5 from ~1.1 thousand ratings; ~300 thousand downloads; ~2.2 thousand downloads in the last 30 days; latest version 3.3.12; last update 31 July 2026** (as reported by AppBrain via search summary) — [AppBrain](https://www.appbrain.com/app/little-family-room-for-parents/com.littlelives.familyroom); [Google Play](https://play.google.com/store/apps/details?id=com.littlelives.familyroom&hl=en)
- A successor app "LittleLives For Parents" exists (`com.littlelives.littlefamilyroomv2`; iOS id 6752276737) — [Google Play v2](https://play.google.com/store/apps/details?id=com.littlelives.littlefamilyroomv2&hl=en); [App Store v2](https://apps.apple.com/sg/app/littlelives-for-parents/id6752276737); older iOS app id 1158542738 — [App Store v1](https://apps.apple.com/sg/app/little-family-room-for-parents/id1158542738)
- AppGrooves aggregates ~870 reviews for the LittleLives family app. Negative themes (paraphrase via search summary of AppGrooves' negative-reviews page): inbox messages "cannot open and keep loading"; files attached by schools won't open; "very buggy"; health updates missing information unless individual tabs are clicked; activity dates split across different days; tapping a notification opens the previous day's activity instead of the one notified; after an upgrade, health timings wrong (a diaper change logged at 9:45am shown as 11:00pm); no way to delete photos/documents to reduce app size; no delete button for old bulletins; activity log not sorted by time; alerts for chat updates that cannot be seen once inside the app; the new "LittleLives for Parents" app lacks Face ID auto-login. Positive theme: parents like seeing feeding, bathroom and sleep records — [AppGrooves negative reviews](https://appgrooves.com/app/littlelives-family-room-by-littlelives-inc-pte-ltd/negative)
- G2 has a LittleLives product page; content not retrievable — [G2 LittleLives](https://www.g2.com/products/littlelives/reviews)

**Anak2U (Malaysia; "all-in-one childcare and preschool management")**
- Apps: Anak2U Parent (`com.anak2u.parent`; iOS id 1449444297), Anak2U Teacher (`com.anak2u.teacher`), Anak2U Classroom (`com.anak2u.classroom`), white-label "TKC Parent" (`com.anak2u.tkcparent`) — [Google Play Parent](https://play.google.com/store/apps/details?id=com.anak2u.parent&hl=en_US); [App Store MY](https://apps.apple.com/my/app/anak2u-parent/id1449444297); [TKC Parent](https://play.google.com/store/apps/details?id=com.anak2u.tkcparent&hl=en_IN)
- Complaints surfaced (paraphrase via search summary): parents cannot find a video section / cannot download videos; users must re-open the app to see conversations in the chat room ("appears to be a common complaint among multiple users"); videos sent by teachers won't play. **Star rating and install count were not retrievable.** — [Google Play Parent](https://play.google.com/store/apps/details?id=com.anak2u.parent&hl=en_US)

**illumine (India-built; markets itself as "#1 Taska & Tadika Management App for Malaysia")**
- App `com.illumine.app`; iOS "illumine for Parents" id 1459249394. Praise: "very good app for communication between parents and teachers"; working parents like seeing toddler activities/meals/sleep. Complaints: "very limited video upload space"; a bug where message notifications require closing and reopening the app; some want "quicker customer support" (paraphrase via search summary) — [Google Play](https://play.google.com/store/apps/details?id=com.illumine.app); [Capterra Illumine reviews](https://www.capterra.com/p/184692/Illumine/reviews/); [GetApp](https://www.getapp.com/education-childcare-software/a/illumine/reviews/); [illumine MY page](https://try.illumine.app/mly)

**Other MY/SG apps found (listings only, no reviews retrieved)**
- MyTadika (Yayasan Pahang / KEMAS) `com.yayasanpahang.tadika_kemas_app` — [Google Play](https://play.google.com/store/apps/details?id=com.yayasanpahang.tadika_kemas_app&hl=en)
- "Kindergarten in App" `com.abos.kindergarten_in_app` — [Google Play](https://play.google.com/store/apps/details?id=com.abos.kindergarten_in_app&hl=en)
- ClassFlow (MY tadika system: "face scan attendance, automated billing, parent app") — [classflow.my](https://classflow.my/)
- KidzyPedia (MY kindergarten system, RM pricing) — [kidzypedia.com](https://www.kidzypedia.com/)
- Skooly (tuition-centre software; vendor claims "4,950 schools and 19,950 teachers across 10+ countries, processing over US$10 million in payments") — [Skooly MY](https://getskooly.com/tuition-centre-software-malaysia/); [Skooly SG](https://getskooly.com/tuition-centre-software-singapore/)
- Synorex Tuition (MY; "flat monthly rate regardless of whether you have 50 or 5,000 students") — [Synorex](https://synorex.group/tuition)
- IntelliTuition by Gotchaa Lab (MY; vendor case claim: "weekly admin workload dropping from 30+ hours to under 5 hours and on-time fee collection reaching 95% with automated WhatsApp reminders") — [Gotchaa Lab](https://gotchaa-lab.com/portfolio/intellituition)
- TuitionPost (Prismtech, MY) — [Prismtech](https://prismtechsolution.com.my/tuitionpost-learning-centre-management-system/); Mycampussquare (MY, Facebook page) — [Facebook](https://www.facebook.com/mycampussquare/); yuran.my — [yuran.my](https://yuran.my/tuition-centre-management-system-malaysia/); Sorable — [sorable.com](https://www.sorable.com/industries/education-tuition-software-malaysia); EzFlow — [ezflow.my](https://www.ezflow.my/blog/tuition-centers-class-scheduling-automation)
- Singapore: Edulabs — [edulabs.com.sg](https://www.edulabs.com.sg/); iEduCentre ("from parent enquiry to class scheduling, make-up lessons, attendance, fee collection, staff administration and teacher pay") — [ieducentre.com](https://www.ieducentre.com/); AppTech System (custom builds) — [apptechsystem.com](https://apptechsystem.com/industries/education-centres/); HashMicro — [hashmicro.com](https://www.hashmicro.com/blog/everything-you-need-to-know-about-tuition-management-system/); Tutorbase (SG-focused content) — [tutorbase.com](https://tutorbase.com/blog/tutoring-software-singapore-guide)

### Inferences
- Parent-app complaints in the region cluster on **reliability of the notification/chat/inbox loop** (LittleLives, Anak2U, illumine all have "notification arrives but content won't load / must reopen app" reports) and **media storage limits** (video upload/download). These are more visible in reviews than billing, because parents review the app while owners rarely do.
- LittleLives' 3.29/5 across ~1.1k ratings for a ~300k-download app is a weak score for a market leader; it suggests the incumbent is vulnerable on app quality, not on distribution.
- AOne's decision to block parent web login (mobile-only) is a likely friction point for parents without smartphone space or shared devices, and a plausible source of App Store complaints; it needs verifying with the Part B scrape.

### Gaps
- AOne: no rating, ratings count, install bracket, last-update date or review text retrieved (Play, App Store, Capterra all blocked). Part B Step 1 fills this in ~10 minutes.
- LittleTree: not found anywhere; may be a misspelling, a private-label app (e.g. a white-label build like "TKC Parent"), or listed under a company name. Ask the founder for the Play/App Store link.
- Anak2U, illumine, MyTadika, ClassFlow, KidzyPedia, Skooly: star ratings and install counts not retrieved.
- Developer replies to reviews (a strong signal of support quality) not retrieved for any app.

---

## KQ2. What do Malaysian Facebook groups for tuition-centre / tadika owners discuss about software? Group names and member counts.

### Takeaway
Several relevant public and private groups exist (PPPTMM's association group, "Cikgu, Tutor & Pengusaha Pusat Tuisyen", and a cluster of taska/tadika owner groups), but their software discussions could not be read this session because Facebook is blocked; only one member count (My Taska Tadika, 813 members in 2020) was retrievable.

### Cited Findings
**Tuition-centre owner groups**
- Persatuan Pengusaha Pusat Tuisyen Muslim Malaysia (PPPTMM), Kuala Lumpur-based NGO that "unites entrepreneurs of centralized home tuition for Muslims throughout Malaysia" (paraphrase via search summary). Page and group URLs: [Page](https://www.facebook.com/ppptmm/); [Group](https://www.facebook.com/groups/ppptmm/); [LinkedIn](https://www.linkedin.com/company/ppptmm); [X/Twitter](https://x.com/ppptmm2)
- "Group Iklan Tuisyen oleh ahli PPPTMM" (advertising group for members' centres) — [Group](https://www.facebook.com/groups/iklantuisyenppptmm/)
- "Cikgu, Tutor & Pengusaha Pusat Tuisyen" — posts addressed to "cikgu, tutor dan pengusaha pusat tuisyen" appear in at least two group IDs: [Group 104933506210742](https://www.facebook.com/groups/104933506210742/posts/9700279983342665/) and [Group 975942199248056](https://www.facebook.com/groups/975942199248056/posts/2993317167510539/) (post titles only; body not retrieved)
- Member counts for the above: **not retrieved**.

**Taska / tadika owner groups**
- "My Taska Tadika (MTT)" — described as connecting TASKA and TADIKA entrepreneurs across Malaysia "as a platform for sharing opinions, problems, and early childhood education knowledge"; **813 members as of 2020** (paraphrase via search summary; the count appears in a 2020 Change.org petition text) — [Change.org petition (TASKA)](https://www.change.org/p/majlis-keselamatan-negara-desakan-untuk-membenarkan-taska-beroperasi-semula-semasa-pkpb); [Change.org petition (TADIKA)](https://www.change.org/p/tan-sri-muhyiddin-yassin-desakan-membenarkan-tadika-swasta-beroperasi-seperti-taska-pj-semasa-tempoh-pkp-pkpb-pkpp)
- "Pengasuh & Pengusaha Taska serta Tadika Kuantan" — [Group 377789642661146](https://www.facebook.com/groups/377789642661146/)
- "Group usahawan Tadika & Taska" — [Group 692836694505832](https://www.facebook.com/groups/692836694505832/)
- "Taska & Tadika di Malaysia ~ Group" — [Group 749026381882739](https://www.facebook.com/groups/749026381882739/)
- "Group Tadika/Taska Yang Bagus Di Malaysia" (parent recommendations) — [Group 123589015007890](https://www.facebook.com/groups/123589015007890/)
- "Jawatan Kosong Taska Tadika Malaysia" (jobs) — [Group 2030911127088795](https://www.facebook.com/groups/2030911127088795/)
- Persatuan Pengusaha Taska dan Tadika Putrajaya (PUSPAJAYA), NGO page — [Facebook](https://www.facebook.com/people/Persatuan-Pengusaha-Taska-dan-Tadika-Putrajaya/100095425981704/)
- A public post by consultant "Hailmy" begins "Ramai di kalangan pengusaha taska dan tadika di Malaysia masih menguruskan opera[si]…" (many taska/tadika operators in Malaysia still manage operations… — text truncated in the URL slug; the rest of the post was not retrievable) — [m.facebook.com post](https://m.facebook.com/hailmyrizuan/photos/ramai-di-kalangan-pengusaha-taska-dan-tadika-di-malaysia-masih-menguruskan-opera/4256239481329213/)
- A vendor Facebook page (Mycampussquare) targets "College, School, Learning Centre" owners — [Facebook](https://www.facebook.com/mycampussquare/)

### Inferences
- The tuition-owner community is organised around PPPTMM (association) plus generic "cikgu/tutor/pengusaha" groups that mix job ads, centre ads and operational questions; software chatter will be a minority of posts and needs keyword filtering (see Part B Step 4 keyword list: "sistem", "app", "yuran", "resit", "kehadiran", "auto reminder", "AOne", "excel", "google sheet", "whatsapp").
- Tadika/taska groups are more numerous and regional (Kuantan, Putrajaya) than tuition groups; the tadika owner is a distinct persona (JKM/KPM licensing, daily reports to parents) from the tuition owner (fees, timetable, make-up classes).

### Gaps
- No post text from any group was retrievable; member counts unknown except MTT (813 in 2020).
- "Tuition Centre Owners Malaysia" / "Tadika owners" as literal English group names were not confirmed to exist.
- Singapore equivalents (e.g. tuition-centre owner groups) not searched before the budget ran out.

---

## KQ3. What do Lowyat, Reddit (r/malaysia, r/singapore) and Quora say about choosing tuition-centre software?

### Takeaway
Lowyat has long-running threads on running/starting tuition centres, but their contents (and any Reddit/Quora threads) could not be read; the only retrievable "how centres actually operate" descriptions come from vendor blogs, which consistently describe Excel + WhatsApp + bank-transfer screenshots as the status quo.

### Cited Findings
- Lowyat threads that exist and are the right place to look (contents not retrieved): "Tuition Centre, Puchong, 16 Years in Operation" — [Lowyat topic 5239964](https://forum.lowyat.net/topic/5239964); "Starting tuition business" — [Lowyat topic 4663984](https://forum.lowyat.net/topic/4663984/all); "any good tuition centre?" — [Lowyat topic 2049321](https://forum.lowyat.net/topic/2049321); Lowyat Education FAQ — [topic 598522](https://forum.lowyat.net/topic/598522)
- Reddit and Quora: search queries for r/malaysia, r/singapore and Quora on tuition-centre software returned **only vendor pages**, no forum threads (searches: "reddit malaysia tuition centre management system"; "r/singapore tuition centre owner admin software billing"). This is a null result, not evidence of absence.
- Vendor descriptions of the status quo (all are marketing content, so treat as directional; the search summary did not attribute each sentence to a specific page, candidates listed):
  - "parents fill up their kid's details through WhatsApp or Google Sheet, and fees and student progress are tracked the same way" (paraphrase via search summary) — candidate sources: [Douglas Loh, How to set up a tuition centre in Malaysia](https://www.douglasloh.com/post/how-to-set-up-a-tuition-centre-in-malaysia); [Tutorbase MY guide](https://tutorbase.com/blog/tuition-center-software-malaysia)
  - "A fast-growing tuition centre runs on three things: an Excel sheet for attendance, a WhatsApp group for everything else, and a founder who spends 30+ hours a week chasing fees instead of teaching" (near-verbatim via search summary) — candidate source: [Gotchaa Lab / IntelliTuition](https://gotchaa-lab.com/portfolio/intellituition)
  - "Malaysian tuition and enrichment centres typically track students in Excel, collect fees via bank transfer screenshots, and message parents individually about schedules or arrears" (near-verbatim via search summary) — candidate sources: [Sorable](https://www.sorable.com/industries/education-tuition-software-malaysia); [Tutorbase MY guide](https://tutorbase.com/blog/tuition-center-software-malaysia)
  - "the monthly routine: WhatsApp reminders to the same parents, bank-transfer screenshots to match against a fee spreadsheet, and a timetable that lives in three different group chats" (near-verbatim via search summary) — candidate source: [EzFlow blog](https://www.ezflow.my/blog/tuition-centers-class-scheduling-automation)
- Singapore: "tuition centre owners spend 8-10 hours per week on admin, though three centres in Singapore cut that to under 2 hours using automation" and "fixing scheduling, billing, and reminders typically saves 7–10 admin hours per week and boosts revenue collection by 5–10%" (vendor claims via search summary) — [Tutorbase SG guide](https://tutorbase.com/blog/tutoring-software-singapore-guide); [Dokkaebi Labs](https://dokkaebilabs.com/blog/tuition-centre-automation)
- Local-fit requirements vendors emphasise: "dominant WhatsApp communication and multi-currency support for international students" (paraphrase via search summary) — [ClassFlow blog](https://classflow.my/blog/tuition-centre-management-software-malaysia/); Synorex's positioning of a flat monthly fee "regardless of whether you have 50 or 5,000 students" implies per-student pricing is a known objection — [Synorex](https://synorex.group/tuition)

### Inferences
- The "why owners resist software" answer is only indirectly visible: vendors keep selling against (a) per-student pricing, (b) WhatsApp-native habits, and (c) bank-transfer-screenshot reconciliation. That implies the real objections are cost that scales with enrolment, having to move parents off WhatsApp, and the fear that a system will not match how fees actually arrive (cash/transfer/DuitNow, partial payments, sibling discounts).
- The strongest first-hand evidence (Lowyat/Reddit/FB posts) is scrapeable cheaply (Part B Steps 4–6); vendor blogs should not be quoted as owner sentiment in the final report.

### Gaps
- No first-hand owner quotes from Lowyat, Reddit, Quora, Facebook or TikTok were retrievable.
- No evidence on Singapore-specific forums (HardwareZone EDMW, r/singapore) was gathered.

---

## KQ4. Top recurring complaints on G2/Capterra for childcare, tutoring and school-management software

### Takeaway
Across global review sites the recurring negatives are: confusing/rigid billing (fixed pay dates, no advance billing, no autopay, accounting-sync errors), weak or slow support (especially for parents), clunky teacher workflows and app bugs, subscription cancellation friction, and cost that scales with enrolment. Praise concentrates on time saved chasing fees and paper, and on photo/real-time updates for parents.

### Cited Findings
**Childcare category (Capterra)**
- Capterra's childcare category summary: "some reviewers describe parent billing as unclear, error-prone, and challenging for both staff and families to manage… confusing, with issues in payment processing, statement clarity, and charge accuracy" (paraphrase via search summary). One reviewer: "There was problem with billing and no one that helped so our organization switched to Procare" (quoted in search summary). On parent communication: "some staff find teacher workflows clunky, want direct in-app messaging, and report device limits or posting glitches." Buyer priorities: Parent Portal rated critical by 50% of reviewers, Payment Processing by 58% — [Capterra child-care software category](https://www.capterra.com/child-care-software/); product pages: [Procare](https://www.capterra.com/p/23486/Procare-Child-Care-Management/), [Smartcare](https://www.capterra.com/p/141292/SmartCare/), [Child Care Pro](https://www.capterra.com/p/80715/Child-Care-Pro/#reviews), [Parent](https://www.capterra.com/p/179039/Parent/)

**brightwheel (largest US childcare app; useful as the benchmark parent app)**
- Vendor claims "4.9-star rating across more than 100,000 reviews on app stores and Capterra" (vendor/affiliate claim). Praise: photo sharing and real-time updates; directors "getting back hours each week that were previously lost to billing chases, paper sign-in sheets, and phone tag with parents"; free ACH billing with automatic reminders/receipts. Complaints: cannot let parents pay on dates other than the 1st or 15th; cannot bill in advance for fundraisers or annual registration; parent-facing phone support limited, "a point of friction when billing questions arise"; cancelling requires a phone call and a retention process; "cost scales with enrollment, and the financial tooling is lighter than long-established competitors" (paraphrases via search summary) — [Capterra brightwheel reviews](https://www.capterra.com/p/144060/brightwheel/reviews/); [G2 brightwheel](https://www.g2.com/products/brightwheel/reviews); [Procare's "why centers are switching from Brightwheel"](https://www.procaresoftware.com/blog/why-some-child-care-centers-are-switching-from-brightwheel-in-2026/); [aireplybee review](https://aireplybee.com/blog/brightwheel-review-2025-complete-guide); [aitoolsbakery review](https://aitoolsbakery.com/blog/brightwheel-review/)

**Tutoring category (Teachworks / TutorCruncher)**
- Teachworks cons (per a competitor comparison and Capterra): "No autopay - must manually charge clients for every invoice · Poor online booking experience that's difficult to set up · No integrated CRM or lead pipeline"; "doesn't support split payments, and its QuickBooks sync has known issues with VAT calculations flagged by multiple users" (quoted/paraphrased via search summary) — [Tutorbase alternatives to Teachworks](https://tutorbase.com/blog/top-10-alternatives-to-teachworks); [TutorCruncher vs Teachworks](https://tutorcruncher.com/blog/tutorcruncher-vs-teachworks); [Capterra Teachworks reviews](https://www.capterra.com/p/233485/Teachworks/reviews/); [GetApp comparison](https://www.getapp.com/education-childcare-software/a/teachworks/compare/tutorcruncher/)
- Teachworks praise: "comprehensive drag-and-drop scheduling calendar with multiple views… colour-coded calendar with conflict detection… booking plugins for websites"; "Invoicing is a breeze" (via search summary) — [Software Advice comparison](https://www.softwareadvice.com/tutoring/teachworks-profile/vs/tutorcruncher/)

**Regional apps (see KQ1 for detail)**
- LittleLives, Anak2U and illumine complaints are dominated by app bugs (inbox/notification/chat not loading, wrong timestamps, unsortable logs) and media limits rather than billing — [AppGrooves LittleLives](https://appgrooves.com/app/littlelives-family-room-by-littlelives-inc-pte-ltd/negative); [Anak2U Play](https://play.google.com/store/apps/details?id=com.anak2u.parent&hl=en_US); [illumine Play](https://play.google.com/store/apps/details?id=com.illumine.app)
- Parenting-app complaint taxonomy (secondary source) — [Unstar: what parents complain about](https://unstar.app/blog/kids-parenting-app-reviews-what-parents-complain-about-2026)

### Inferences
- The global complaint list maps onto the MY/SG persona as: (1) billing rigidity vs. messy real-world fee collection (partial/late/DuitNow/cash), (2) support that parents can reach directly (owners get the complaints otherwise), (3) app reliability for the notification→content loop, (4) pricing that punishes growth (per-student), (5) exit friction/data export. A founder-led product can win on (2), (3) and (5) without out-featuring incumbents.
- "Price hikes" and "data lock-in" were not explicitly surfaced in the retrieved summaries for any product; they remain hypotheses to test with the G2/Capterra scrape (Part B Step 7), filtering reviews for "price increase", "renewal", "export", "cancel".

### Gaps
- No verbatim G2/Capterra review text with reviewer role/date was retrievable (sites blocked); star distributions and review counts per product unknown.
- Trustpilot was not reached; Procare/Smartcare/HiMama(Lillio)/Kangarootime complaint detail not gathered.
- No search performed specifically for "price increase" or "data export" complaints before the budget ended.

---

## KQ5. Which Apify actors exist per source, what to feed them, what comes out, and what they cost — plus alternatives and order of operations

### Takeaway
Every source in scope has at least one pay-per-result Apify actor; app-store review scrapers are nearly free (US$0.08–0.40 per 1,000 reviews), social scrapers cost US$0.45–2.30 per 1,000 items, and the entire MY/SG research programme below should run well under US$50 including a one-off Google Maps pull of every "pusat tuisyen"/"tadika" listing in Selangor/KL/Johor/Penang plus their reviews. Prices below are those surfaced in search results on 2026-09-29; verify on each actor page because Apify actors reprice frequently.

### Cited Findings — actor catalogue (exact store IDs)

**A. Google Play reviews (Step 1)**
- `curious_coder/google-play-scraper` — US$0.08 / 1,000 reviews — [store page](https://apify.com/curious_coder/google-play-scraper)
- `neatrat/google-play-store-reviews-scraper` — US$0.10 / 1,000 reviews — [store page](https://apify.com/neatrat/google-play-store-reviews-scraper)
- `scrape.badger/google-play-reviews-scraper` — US$0.20 / 1,000 — [store page](https://apify.com/scrape.badger/google-play-reviews-scraper/api)
- `scrapesignal_labs/google-play-reviews-scraper` — US$0.40 / 1,000 — [store page](https://apify.com/scrapesignal_labs/google-play-reviews-scraper/api)
- `scrapemint/google-play-reviews-scraper` — US$1.00 per 500 reviews; first 2 rows of each run free; a default 200-review run "costs about 40 cents" — [store page](https://apify.com/scrapemint/google-play-reviews-scraper)
- `workmatic/google-play-reviews-scraper` — US$3 / 1,000 — [store page](https://apify.com/workmatic/google-play-reviews-scraper)
- `scrapesage/google-play-reviews-scraper` — pay-per-event, "no monthly rental and no start fee" — [store page](https://apify.com/scrapesage/google-play-reviews-scraper)
- Also listed: `automation-lab/google-play-scraper` (apps + ratings + reviews) — [store page](https://apify.com/automation-lab/google-play-scraper); `webdatalabs/google-play-reviews-scraper` — [store page](https://apify.com/webdatalabs/google-play-reviews-scraper); `clearrun/google-play-reviews` ("rating, text, replies") — [store page](https://apify.com/clearrun/google-play-reviews/api)
- Typical output fields (per the open-source library these actors wrap): `id, userName, date, score, title, text, replyDate, replyText, version, thumbsUp` — verbatim from the google-play-scraper README fetched from GitHub — [facundoolano/google-play-scraper README](https://github.com/facundoolano/google-play-scraper#reviews)
- Important limitation (verbatim from that README): "this method returns reviews in a specific language (english by default), so you need to try different languages to get more reviews. Also, the counter displayed in the Google Play page refers to the total number of 1-5 stars ratings the application has, not the written reviews count." → run each app with `lang` = `en`, `ms`, `zh` and `country` = `my`, `sg`.

**B. App Store reviews (Step 1)**
- `workware/app-store-reviews-scraper` — US$0.10 / 1,000 — [store page](https://apify.com/workware/app-store-reviews-scraper/api)
- `thewolves/appstore-reviews-scraper` — US$0.10 / 1,000 — [store page](https://apify.com/thewolves/appstore-reviews-scraper)
- `santhej/app-store-reviews-scraper` — US$0.10 / 1,000 — [store page](https://apify.com/santhej/app-store-reviews-scraper/issues/open)
- `opaled_hurricane/unified-app-store-reviews-scraper` (iOS + Android in one) — US$0.10 / 1,000 — [store page](https://apify.com/opaled_hurricane/unified-app-store-reviews-scraper)
- `alexmorain/app-store-play-store-scraper` (iOS + Android) — [store page](https://apify.com/alexmorain/app-store-play-store-scraper)
- `workmatic/app-store-reviews-scraper` ("any country") — [store page](https://apify.com/workmatic/app-store-reviews-scraper); `easyapi/app-store-reviews-scraper` — [store page](https://apify.com/easyapi/app-store-reviews-scraper); `steadyscrape/app-store-reviews-scraper` — [store page](https://apify.com/steadyscrape/app-store-reviews-scraper); `epctex/appstore-scraper` (apps, reviews, keyword rank) — [store page](https://apify.com/epctex/appstore-scraper)
- Output fields (from the open-source app-store-scraper README, verbatim): `id, userName, userUrl, version, score, title, text, updated, url`; **limitation**: "page: the review page number to retrieve. Defaults to 1, maximum allowed is 10" and `country` defaults to `us` → run per storefront (`my`, `sg`) — [facundoolano/app-store-scraper README](https://github.com/facundoolano/app-store-scraper#reviews)

**C. Facebook groups / pages / posts (Steps 4–5)**
- Official: `apify/facebook-groups-scraper` — [store page](https://apify.com/apify/facebook-groups-scraper); `apify/facebook-posts-scraper` ("extract data from Facebook posts from multiple pages/profiles" — verbatim from Apify's own MCP README) — [store page](https://apify.com/apify/facebook-posts-scraper); [Apify MCP README listing](https://github.com/apify/apify-mcp-server/blob/master/README.md)
- Community: `crabwalker/facebook-groups-scraper` (posts, comments, engagement) — [store page](https://apify.com/crabwalker/facebook-groups-scraper/api); `whoareyouanas/facebook-group-scraper` (group & page, comments, reactions) — [store page](https://apify.com/whoareyouanas/facebook-group-scraper); `simpleapi/facebook-groups-scraper` — [store page](https://apify.com/simpleapi/facebook-groups-scraper); `amrhassan25/facebook-actor` ("Posts + All Comments & Replies") — [store page](https://apify.com/amrhassan25/facebook-actor); `swerve/fb-group-scraper` — [store page](https://apify.com/swerve/fb-group-scraper); `scrapio/facebook-groups-posts-scraper` — [store page](https://apify.com/scrapio/facebook-groups-posts-scraper); `scrapier/facebook-groups-scraper` — [store page](https://apify.com/scrapier/facebook-groups-scraper); `scraper_one/facebook-posts-scraper` — [store page](https://apify.com/scraper_one/facebook-posts-scraper); `scraper-engine/facebook-groups-scraper` (adds contact finder) — [store page](https://apify.com/scraper-engine/facebook-groups-scraper)
- Access model (paraphrase via search summary of these listings): public groups need no account/API key — "the Actor works from a public group URL alone"; a `cookieString` (logged-in session cookies) is only needed "to improve reliability against login walls or to access closed/private groups you already belong to". Output: post text, top comments, reaction breakdown, images/videos, author profile, timestamps; export JSON/CSV/Excel/XML.
- Prices for FB actors were **not captured** (gap) — check each page; Apify's official FB scrapers are pay-per-result.

**D. Instagram (Step 6)**
- `apify/instagram-scraper` — "from $1.50 / 1K"; Starter plan US$2.30 / 1,000, Scale US$1.90, Business US$1.50 (via search summary) — [store page](https://apify.com/apify/instagram-scraper); [Apify MCP README](https://github.com/apify/apify-mcp-server/blob/master/README.md)

**E. TikTok (Step 6)**
- `clockworks/tiktok-scraper` — US$1.70 / 1,000 (via use-apify.com) — [use-apify.com](https://use-apify.com/docs/best-apify-actors/best-social-media-scrapers)
- `getanyapi/tiktok-search-scraper` — US$0.45 / 1,000 videos, pay per result — [store page](https://apify.com/getanyapi/tiktok-search-scraper)
- `datapilot/tiktok-scraper` — [store page](https://apify.com/datapilot/tiktok-scraper/api)

**F. Google Search results (Step 3)**
- `apify/google-search-scraper` (official SERP scraper; price not captured) — [store page](https://apify.com/apify/google-search-scraper)
- A pay-per-result alternative from the same family as the TikTok one was listed at US$0.45 / 1,000 results — [SociaVault comparison](https://sociavault.com/blog/best-social-media-scraping-apis-2026)

**G. Website content / forum threads (Step 5)**
- `apify/website-content-crawler` (price not captured this session) — [store](https://apify.com/store); lighter alternatives listed by Apify itself: `apify/rag-web-browser` ("search the web, scrape the top N URLs, and return their content") and `apify/web-fetch` ("fetch any URL and return its content as Markdown… with JavaScript rendering and anti-bot protection") — verbatim from [Apify MCP README](https://github.com/apify/apify-mcp-server/blob/master/README.md)

**H. Google Maps (Step 2)**
- The de-facto standard is `compass/crawler-google-places` (Google Maps Scraper) with a companion Google Maps Reviews scraper from the same publisher. **Not fetched this session** (apify.com blocked before the search budget ran out) — verify ID and price on [apify.com/store](https://apify.com/store) by searching "Google Maps Scraper". Community alternatives are plentiful (e.g. the GitHub-published `manthriashishraj-dev/gbp-scraper`, "Scrape all data from Google Business Profile listings on Google Maps") — [GitHub](https://github.com/manthriashishraj-dev/gbp-scraper)

**I. G2 / Capterra / Trustpilot (Step 7)**
- Not searched before the budget ended (gap). Search the store for "Capterra reviews scraper", "G2 reviews scraper", "Trustpilot scraper"; G2 is heavily bot-protected, so expect higher per-result prices or a Bright Data/SerpAPI fallback. Capterra, GetApp and Software Advice share one review pool (all Gartner Digital Markets), so scrape one.

**J. Platform pricing mechanics**
- Apify's plans page (Free / Starter / Scale / Business; pay-as-you-go) — [apify.com/pricing](https://apify.com/pricing). Actor runs execute "in Docker containers, which have a limited amount of resources (memory, CPU, disk size…)" and memory allocation drives compute cost for actors that are not pay-per-result (verbatim from Apify docs on GitHub) — [Apify docs: usage and resources](https://github.com/apify/apify-docs/blob/master/sources/platform/actors/running/usage_and_resources.md)
- Third-party tested cost tables for popular actors — [use-apify.com](https://use-apify.com/)

### Cited Findings — non-Apify alternatives
- **Open-source, zero-cost, run from a laptop** (verified READMEs): Node `google-play-scraper` (`npm i google-play-scraper`; `gplay.reviews({appId:'com.aoneschool', lang:'ms', country:'my', sort: gplay.sort.NEWEST, num: 3000})`) — [README](https://github.com/facundoolano/google-play-scraper); Python `google-play-scraper` v1.2.7 (`pip install google-play-scraper`; `reviews_all('com.aoneschool', lang='en', country='my')`) — [PyPI](https://pypi.org/project/google-play-scraper/); Node `app-store-scraper` (`store.reviews({id: 1422236557, country: 'my', page: 1..10})`) — [README](https://github.com/facundoolano/app-store-scraper). These are what most US$0.10/1k Apify actors wrap.
- **SerpAPI** (hosted Google Play / App Store / Google Maps reviews endpoints, per-search pricing), **Bright Data** (Web Scraper API datasets incl. Google Maps and app-store reviews, proxy network for Facebook), **Phantombuster** (cookie-based Facebook group post/member and Instagram phantoms), **Octoparse** (point-and-click templates for Google Maps/Play), and a **plain Playwright script** (best for Lowyat, which is a classic HTML forum). *Pricing for these was not verified this session; the founder should compare on their sites.*

### Copy-pasteable plan — order of operations, inputs, outputs, cost, and what each dataset answers

> Field names marked `(verify)` are the conventional names for that actor family; open the actor's **Input** tab and adjust before running. Costs assume the pay-per-result prices above.

**Step 0 — Set up (5 min).** Create an Apify account (free tier is enough for Steps 1–3), get `APIFY_TOKEN`. Keep one Google Sheet with tabs per step; export every run as CSV.

**Step 1 — App-store reviews for every MY/SG parent/teacher app (≈US$0.50 total).**
- Actor: `curious_coder/google-play-scraper` or `neatrat/google-play-store-reviews-scraper` (US$0.08–0.10/1k) and `workware/app-store-reviews-scraper` (US$0.10/1k), or the unified `opaled_hurricane/unified-app-store-reviews-scraper`.
- Input — Google Play package names: `com.aoneschool`, `com.littlelives.familyroom`, `com.littlelives.littlefamilyroomv2`, `com.anak2u.parent`, `com.anak2u.teacher`, `com.illumine.app`, `com.yayasanpahang.tadika_kemas_app`, `com.abos.kindergarten_in_app`, plus ClassFlow / KidzyPedia / Skooly / Synorex / LittleTree package names once found (search Play for the vendor name). Run three times per app: `{"lang":"en","country":"my"}`, `{"lang":"ms","country":"my"}`, `{"lang":"en","country":"sg"}`; `sort: "newest"`, `maxReviews: 2000 (verify)`.
- Input — App Store ids: `1422236557` (AOne), `1158542738` and `6752276737` (LittleLives), `1449444297` (Anak2U), `1459249394` (illumine); `country: "my"` then `"sg"`; up to 10 pages each.
- Output: `score, title, text, date, version, thumbsUp, replyText/replyDate` (+ app-level rating, ratings count, install bracket, last-update from the app-details scrapers such as `automation-lab/google-play-scraper`).
- Answers: KQ1 fully (ratings, install counts, last update, complaint themes, whether the vendor replies); build a 1-star vs 5-star theme table per app.

**Step 2 — Google Maps: the universe of centres and what parents say about them (≈US$5–20).**
- Actor: Google Maps Scraper (`compass/crawler-google-places`, verify) with reviews enabled, or its companion reviews scraper.
- Input `(verify)`: `{"searchStringsArray":["pusat tuisyen","tuition centre","tadika","taska","kindergarten","enrichment centre"],"locationQuery":"Selangor, Malaysia","maxCrawledPlacesPerSearch":1000,"maxReviews":30,"language":"ms","reviewsSort":"newest"}`; repeat for "Kuala Lumpur", "Johor Bahru", "Penang", "Singapore".
- Output: `title, address, phone, website, categoryName, totalScore, reviewsCount, openingHours, reviews[{text, stars, publishedAtDate, responseFromOwnerText}]`.
- Answers: market size by district (count of centres), which centres have a website/app link vs. only a phone/WhatsApp number (proxy for digitisation), parent complaints about fees/communication in Maps reviews, and a lead list for interviews.

**Step 3 — Google SERP sweep to find the forum/blog threads (≈US$1).**
- Actor: `apify/google-search-scraper`. Input `(verify)`: `{"queries":"site:forum.lowyat.net pusat tuisyen sistem\nsite:forum.lowyat.net tuition centre software\nsite:reddit.com/r/malaysia tuition centre software\nsite:reddit.com/r/singapore tuition centre admin\nsite:quora.com tuition centre management software Malaysia\n\"pusat tuisyen\" \"google sheet\" OR \"excel\" yuran\n\"AOne\" tuisyen review\n\"LittleLives\" complaint app\nsite:hardwarezone.com.sg tuition centre software","countryCode":"my","languageCode":"en","maxPagesPerQuery":3,"resultsPerPage":100}`.
- Output: `title, url, description, position` per organic result (+ People Also Ask).
- Answers: KQ3 (which Lowyat/Reddit/Quora/HWZ threads exist); feeds Step 5.

**Step 4 — Facebook groups and pages (≈US$5–15 depending on actor price).**
- Actor: `apify/facebook-groups-scraper` (public groups) or `crabwalker/facebook-groups-scraper` / `amrhassan25/facebook-actor` when comments are needed; `apify/facebook-posts-scraper` for vendor pages.
- Input `(verify)`: `{"startUrls":[{"url":"https://www.facebook.com/groups/ppptmm/"},{"url":"https://www.facebook.com/groups/iklantuisyenppptmm/"},{"url":"https://www.facebook.com/groups/104933506210742/"},{"url":"https://www.facebook.com/groups/975942199248056/"},{"url":"https://www.facebook.com/groups/692836694505832/"},{"url":"https://www.facebook.com/groups/749026381882739/"},{"url":"https://www.facebook.com/groups/377789642661146/"}],"resultsLimit":500,"viewOption":"CHRONOLOGICAL"}`; for private groups the founder is already a member of, add `cookieString` from his own logged-in session (only for groups he belongs to). Vendor pages: `https://www.facebook.com/ppptmm/`, `https://www.facebook.com/mycampussquare/`, AOne's and LittleLives' pages.
- Post-filter (keep posts matching any): `sistem|system|app|aplikasi|software|yuran|fee|resit|receipt|invoice|kehadiran|attendance|reminder|excel|google sheet|spreadsheet|whatsapp|AOne|LittleLives|ClassFlow|Skooly|Synorex|Anak2U|illumine|LittleTree`.
- Output: `postId, url, text, time, likes, comments[], shares, author`.
- Answers: KQ2 (what owners ask about software, which vendors get recommended/complained about, what they still do manually, and why they resist — read the comment threads under "recommend sistem" posts).

**Step 5 — Pull the forum threads found in Step 3 (≈US$1, or free with Playwright).**
- Actor: `apify/website-content-crawler` (input `{"startUrls":[{"url":"https://forum.lowyat.net/topic/4663984/all"},{"url":"https://forum.lowyat.net/topic/5239964"}],"maxCrawlPages":50,"crawlerType":"cheerio"}` `(verify)`) or `apify/web-fetch` per URL; Reddit threads via `old.reddit.com/<thread>.json` in a plain script.
- Output: Markdown/text per page. Answers: KQ3 first-hand owner quotes.

**Step 6 — TikTok / Instagram / YouTube comments (optional, ≈US$5–10).**
- Actors: `clockworks/tiktok-scraper` (`{"hashtags":["pusattuisyen","tadika","taska","tuitioncentre"],"resultsPerPage":100}` `(verify)`) or `getanyapi/tiktok-search-scraper` (`searchQueries: ["pusat tuisyen sistem","tadika app"]`); `apify/instagram-scraper` (`{"search":"pusattuisyen","searchType":"hashtag","resultsType":"posts","resultsLimit":300}` `(verify)`), then the same actor with `resultsType:"comments"` on vendor accounts.
- Answers: how centres market themselves (WhatsApp-number-only vs app links), and parent chatter; low priority for software sentiment.

**Step 7 — Global review sites (≈US$5–20).**
- Search the store for Capterra/G2/Trustpilot review actors (not verified this session). Targets: Capterra p/190964 (AOneSchools), p/184692 (Illumine), p/144060 (brightwheel), p/23486 (Procare), p/233485 (Teachworks), G2 LittleLives/brightwheel/TutorCruncher. Filter text for `price|increase|renewal|cancel|export|support|billing|invoice|parent app`.
- Answers: KQ4 with verbatim quotes, star distributions, and explicit price-hike / lock-in evidence.

**Step 8 — Synthesis.** Tag every row with `{source, vendor, persona (owner/teacher/parent), theme (billing, scheduling, parent-app, support, price, lock-in, bugs, manual-workaround), sentiment}` and count. Total spend across Steps 1–7 at the prices captured: roughly US$20–70.

### Inferences
- Steps 1–3 are cheap enough to run today and would close most of the KQ1 gap (AOne rating/installs/last update) within an hour; Step 4 is where the "why owners resist software" evidence actually lives.
- Because Google Play reviews are served per language, a MY-focused scrape that forgets `lang: ms` will systematically under-count Malay-speaking tadika/tuisyen parents.

### Gaps
- Prices not captured for: the official Facebook actors, `apify/google-search-scraper`, `apify/website-content-crawler`, the Google Maps scraper, and any Capterra/G2/Trustpilot actor.
- Exact input-schema field names were not verified for any actor (apify.com blocked); all JSON above is indicative.
- SerpAPI / Bright Data / Phantombuster / Octoparse pricing not verified.
