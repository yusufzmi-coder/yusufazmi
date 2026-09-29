# Demo Playbook — Vendor School / Tuition / Preschool Management System

**Untuk siapa:** founder yang nak tengok satu-persatu sistem vendor (Zoom demo + free trial), faham keseluruhan sistem, dan screenshot / screen-record setiap feature sebagai rujukan masa bina sistem sendiri.
**Sumber:** 6 fail nota research dalam `research_notes/Pasaran school management system Malaysia/` (tarikh research 29 Sept 2026). Semua harga dan angka pelanggan dalam dokumen ni ikut nota tu — kebanyakan datang dari search snippet, bukan dari page vendor terus. Yang ditanda **(unverified)** maksudnya kena confirm masa demo.
**Peraturan asas:** kau ni "pelanggan potential" — pengusaha rangkaian pusat bacaan kanak-kanak ~20 lokasi. Kau **bukan** developer, **bukan** competitor. Jangan sebut pasal sistem sendiri langsung.

---

## 1. Cara guna playbook ni

### 1.1 Aliran kerja (per vendor)

- [ ] Buka section vendor tu dalam playbook ni, baca "Whole system in 5 lines" dulu supaya kau dah ada mental model sebelum demo
- [ ] Hantar mesej booking (template bawah) — email / contact form / WhatsApp
- [ ] Bila dapat slot, masukkan dalam calendar + siapkan satu page Notion / Google Doc untuk vendor tu (guna template section 1.5)
- [ ] Masa demo: record, tanya soalan universal (1.4) + soalan khusus vendor, tick checklist screenshot
- [ ] Lepas demo: minta trial / sandbox account, minta pricing PDF, minta recording mereka kalau ada
- [ ] Dalam 24 jam: rename recording, sort screenshot, isi Master Tracker (section 6) dan comparison matrix (section 7)

### 1.2 Template mesej booking (English — copy paste, tukar nama vendor)

Guna yang sama untuk email, contact form, atau WhatsApp. Pendek, macam pelanggan biasa.

```
Subject: Demo request — multi-branch children's reading centre (Malaysia)

Hi [Vendor] team,

I run a chain of children's reading centres in Malaysia (about 20 locations, mostly
primary-age kids, small group classes). We are evaluating a system to manage
enrolment, class schedules, attendance, fee collection and parent communication
across all branches. Could we book a 45–60 min Zoom demo of [Product]? I would
also appreciate a trial account and your pricing for a multi-branch setup.

Thanks,
[Nama] | [Nama syarikat] | [No. telefon]
```

Nota:
- Kalau vendor tanya "which system are you using now?" — jawab "mostly spreadsheets and WhatsApp groups" (ni memang status quo industri, nota research pun cakap macam tu). Jangan sebut "kami tengah bina sendiri".
- Kalau vendor tanya bajet — jawab "depends on what's included; we want to compare a few options first".
- Kalau vendor tak ada butang demo, cari **Book a Demo / Contact / WhatsApp** di homepage. Kebanyakan vendor MY kecil lebih cepat respond WhatsApp daripada email.

### 1.3 Setup rakaman Zoom (buat sekali, guna setiap demo)

- [ ] Zoom: **Record to the Cloud** on, plus **Local Recording** on sebagai backup (Settings > Recording)
- [ ] Zoom: enable "Record active speaker, gallery view and shared screen separately" supaya screen share bersih tanpa muka
- [ ] Awal demo, minta izin: *"Do you mind if I record this so I can share with my ops team?"* — 99% vendor okay, dan ni juga cover PDPA basic courtesy
- [ ] Backup recorder: **OBS** (free) atau **Loom** jalan sekali gus — kalau Zoom cloud recording gagal, kau masih ada copy
- [ ] Screenshot hotkey: Windows `Win + Shift + S`, Mac `Cmd + Shift + 4`. Snap SETIAP screen yang vendor tunjuk, walaupun kau rasa tak penting — senang sort kemudian
- [ ] Naming convention fail (jangan lari dari ni):
  - Video: `vendor_YYYYMMDD_module.mp4` → contoh `aone_20261006_billing.mp4`, `aone_20261006_parentapp.mp4` (potong video panjang ikut modul lepas demo)
  - Screenshot: `vendor_YYYYMMDD_screen-name.png` → contoh `remmu_20261007_attendance-marking.png`
- [ ] Satu folder per vendor dalam Google Drive / Dropbox: `/Vendor Demos/<vendor>/`
- [ ] Satu page Notion / Google Doc per vendor — guna template 1.5 bawah
- [ ] Kalau vendor bagi trial account: record sendiri satu video "walkthrough" 10–15 minit klik semua menu, sebelum trial expire
- [ ] Sedia dua monitor kalau boleh: satu Zoom, satu playbook ni (untuk tick checklist live)

### 1.4 Soalan universal — tanya SEMUA vendor

Tanya dalam order ni; catat jawapan dalam page vendor. Kalau jawapan "not sure", minta mereka follow up by email (ni sendiri satu signal kualiti support).

**Harga & kontrak**
- [ ] Pricing model: per student? per branch? flat? Ada setup fee? Ada transaction fee atas bayaran?
- [ ] Untuk 20 branch × (anggaran) 100–150 student per branch — berapa sebulan / setahun? Minta quotation bertulis
- [ ] Contract lock-in: monthly? annual? Ada penalty kalau cancel? Harga naik tahun depan macam mana?
- [ ] Onboarding: berapa lama dari sign up sampai semua branch live? Siapa buat data migration (student list dari Excel)? Ada charge?

**Multi-branch**
- [ ] Boleh tengok semua branch dalam satu login? Ada HQ dashboard vs branch dashboard?
- [ ] Boleh set harga / fee plan / jadual berbeza ikut branch?
- [ ] Staff boleh dilimit hanya nampak branch sendiri? Role & permission macam mana?
- [ ] Student boleh pindah branch tanpa hilang history?

**Komunikasi**
- [ ] WhatsApp: automated reminder je, atau two-way inbox? Guna official WhatsApp Business API atau click-to-chat link? Siapa tanggung kos mesej Meta?
- [ ] Ada in-app push? Parent tak install app — fallback apa (SMS / WhatsApp)?

**Bayaran & compliance**
- [ ] FPX / DuitNow QR / e-wallet / kad — gateway mana (Billplz, toyyibPay, CHIP, Curlec, iPay88/Payex, senangPay, Stripe)? Fee per transaksi berapa?
- [ ] Recurring collection: ada FPX e-mandate / auto-debit? Atau parent kena klik link tiap bulan?
- [ ] Bayaran cash / bank transfer manual — macam mana reconcile? Boleh partial payment, sibling discount, deposit?
- [ ] LHDN e-Invoice (MyInvois): live ke? Consolidated e-invoice bulanan boleh? Parent boleh request individual e-invoice sendiri? Ada extra charge?
- [ ] PDPA Malaysia: data hosted mana? Ada DPO? Breach notification process? Boleh export semua data kalau kami keluar?
- [ ] Accounting export: Bukku / SQL Account / AutoCount / Xero — ada integration atau CSV je?

**Produk**
- [ ] Parent app: iOS + Android + Huawei? Parent boleh login web? Bahasa Melayu ada?
- [ ] Teacher app: mark attendance dari phone? Tulis progress note per session?
- [ ] Make-up class / replacement class: macam mana flow? Ada credit system?
- [ ] Waitlist & capacity per class ada?
- [ ] Reports: apa yang boleh export (CSV/Excel/PDF)? Ada BI dashboard?
- [ ] API / webhook / Zapier ada? Dokumentasi public?
- [ ] Roadmap 12 bulan — apa yang tengah dibina? (dengar apa yang mereka rasa kurang)
- [ ] Support: channel apa (WhatsApp group / email / phone)? Jam operasi? SLA?
- [ ] Boleh bagi nama 1–2 pelanggan multi-branch yang boleh kami tanya?

### 1.5 Template page Notion / Google Doc per vendor

```
# <Vendor> — demo <tarikh>
Website | Demo route | Contact person | Follow-up due
## Harga (quote bertulis: link)
## Modul yang ada (tick)
## Flow: enquiry → enrolment → schedule → attendance → billing → reporting
## Apa yang bagus (curi idea)
## Apa yang lemah (jangan ulang)
## Screenshot folder link | Video link
## Jawapan soalan universal (1.4)
## Jawapan soalan khusus vendor
## Red flags
```

---

## 2. Priority tiers

### Tier 1 — WAJIB demo (competitor terus MY/SG + analog paling dekat)

AOne, LittleLives, REMMU, SimTrain, ClassFlow.my, Skooly, Anak2U, Oodlins, Classcard, Taidii, ASIS, Synorex, Yuran.my, Tuis.my, illumine, Mekar, BooknGo.

**Kenapa:** ni sistem yang pelanggan kau (dan pesaing kau) akan jumpa bila Google "sistem pusat tuisyen" / "tadika app". Mereka dah selesaikan masalah Malaysia-specific — FPX/DuitNow, WhatsApp reminder, LHDN e-Invoice, BM UI, harga RM50–250/centre. Kau kena tahu bar minimum yang parent dan admin dah biasa. Semua vendor ni ada aktiviti marketing 2025–2026 dalam nota (kecuali LittleLives MY yang last press 2019 — tapi dia market leader preschool SG dan ada 145–293 tadika MY, jadi tetap wajib).

Ada juga **Tier 1B** (section 3.18) — vendor MY/SG kecil / niche yang tak perlu demo penuh; cukup lawat website, sign up kalau free, atau WhatsApp tanya harga.

### Tier 2 — berbaloi demo atau free trial untuk idea feature

Jackrabbit Class, iClassPro, Pike13, TutorCruncher, Teachworks, Brightwheel, Famly, Playground, Xplor, Classe365, Gradelink, Fedena.

**Kenapa:** tak sesuai untuk Malaysia (USD, tak ada FPX, tak ada WhatsApp, quote-only) tapi mereka lebih matang 5–10 tahun dari vendor MY dalam benda tertentu: multi-location BI dashboard (Jackrabbit), flat per-location pricing (iClassPro), franchise stack + payroll (Pike13), branch model + API + tutor payroll (TutorCruncher), per-lesson billing + calendar (Teachworks), parent app feed + check-in + autopay (Brightwheel, Playground), cross-site occupancy report (Famly), family app 3-in-1 (Xplor), modular SIS pricing (Classe365, Gradelink), open-source-turned-commercial (Fedena). Ambil UI pattern, jangan ambil pricing.

### Tier 3 — self-serve reference, tak perlu demo

Sign up free trial / free plan, klik semua menu sendiri, screenshot. Respond.io, SleekFlow, Wati, Trello, ClickUp, Notion, Airtable, Fresha, Mindbody, Glofox, Zenoti, Calendly, ClassDojo, plus open source: Gibbon, RosarioSIS, openSIS, Unifiedtransform, skuul, FET.

**Kenapa:** ni bukan competitor — ni tempat curi pattern (WhatsApp inbox + lifecycle stage, kanban pipeline, waitlist + make-up credit, booking UX, parent comms freemium, data model SIS). Kebanyakan ada free tier, dan vendor besar tak akan buat demo untuk pusat tuisyen 20 cawangan pun. Open source pula: install / tengok demo site sendiri, baca schema DB.

---

## 3. Tier 1 — section per vendor

### 3.1 AOne (AOneSchool / AOnePay) — Malaysia

**Website & demo route:** https://aone.com.my/ — contact page https://aone.com.my/contact/ (harga "depends on the number of students", kena demo/quote). SG site https://aoneschools.sg/. Product pages: https://aone.com.my/products/, multi-branch https://aoneschools.sg/products/multi-branch/, finance https://aone.com.my/products/finance/. Help centre (baca SEBELUM demo): https://aoneschools-help.freshdesk.com/en/support/solutions/articles/153000242966-what-information-needs-to-be-filled-in-when-e-invoice-is-enabled-

**HQ / segment / pelanggan / harga:** My AOne Learning Sdn Bhd, Malaysia, founded 2018, backed 500 Global. Segment: tuition, preschool, enrichment (music/dance/art/sports), language centre. Claim "4,000+ education providers across SEA" (dulu "2,000+" — vendor claim, tak diaudit). Harga: quote-based ikut bilangan student, tak public. E-Invoice hanya untuk pelanggan **Premium**.

**Whole system in 5 lines:**
1. Tiga produk: **AOneSchool** (pengurusan), **AOnePay** (kutipan yuran bulanan automatik, sejak 2017), **AOneMarketplace** (marketplace kelas, sejak 2016).
2. User: centre owner/admin (web), teacher (app), parent/student (app sahaja — **parent tak boleh login web**, mobile only).
3. Flow: enrolment → class schedule/booking → attendance check-in/check-out digital dengan notifikasi parent real-time → invoice auto + in-app payment (Billplz / iPay88-Payex) → e-Invoice LHDN (Premium) → centralised report semua branch.
4. Multi-branch/franchise: satu account, switch branch, laporan berpusat; role-based permission.
5. App "AOne" di App Store (id 1422236557), Google Play (`com.aoneschool`), Huawei AppGallery. Parent login guna phone/email yang didaftar centre; satu account boleh pegang anak di beberapa sekolah. Support pelanggan melalui **WhatsApp group**.

**Kenapa demo ni penting:** Ni incumbent nombor satu yang parent dan admin di Malaysia paling mungkin dah pernah guna. Satu-satunya vendor tempatan dengan bukti jelas multi-branch + LHDN e-Invoice (termasuk consolidated e-invoice dan parent request e-invoice dalam app) + parent app matang + replacement class request oleh parent. Ini "bar" yang kau kena lepasi.

**Soalan khusus untuk vendor ni:**
- [ ] Harga sebenar: per student berapa? Apa beza plan biasa vs Premium? E-Invoice kena Premium — berapa tambahan?
- [ ] AOnePay: take-rate atas setiap bayaran berapa? Fee Billplz / iPay88-Payex ditanggung siapa? Ada FPX e-mandate (auto-debit) atau parent klik pay tiap bulan?
- [ ] Kenapa parent tak boleh login web? Parent yang tak nak install app macam mana?
- [ ] Parent comms — WhatsApp betul-betul atau in-app push je? (nota: nampaknya push in-app; WhatsApp cuma untuk support vendor)
- [ ] Ada BM UI untuk parent app? Chinese?
- [ ] Replacement class request: parent request → siapa approve → ada limit / expiry?
- [ ] Export data penuh (student, invoice, attendance) kalau kami keluar — format apa?
- [ ] Integration accounting (Bukku / SQL / AutoCount)? Nota research tak jumpa bukti langsung.

**Feature yang WAJIB screenshot / screen record:**
- [ ] HQ dashboard multi-branch + screen "switch branch"
- [ ] Centralised report merentas branch (yang mana boleh filter by branch)
- [ ] Class schedule view (admin) + class booking flow
- [ ] Digital check-in / check-out UI (teacher side) dan notifikasi yang parent terima
- [ ] Student profile: enrolment, guardian, siblings, fee plan
- [ ] AOnePay: setup monthly fee collection, invoice auto-generate, reminder schedule
- [ ] In-app payment flow parent (Billplz/iPay88 screen) sampai receipt
- [ ] E-Invoice: field yang kena isi bila e-invoice enabled, consolidated e-invoice submission screen, parent "request e-invoice" dalam app
- [ ] Parent app home, attendance history, progress view, class materials, announcements
- [ ] Teacher app: mark attendance, progress update
- [ ] Role & permission settings (HQ vs branch admin vs teacher)
- [ ] Settings: fee plan / discount / late fee
- [ ] Report export (CSV/PDF) — senarai report yang ada

**Perangkap / red flags (dari nota):**
- Harga opaque, ikut bilangan student — kos naik bila kau grow. Minta quote bertulis untuk 3 saiz (1,500 / 2,500 / 4,000 student).
- E-Invoice dikunci belakang Premium.
- Bergantung pada gateway pihak ketiga (Billplz / iPay88-Payex) — setup Billplz kena "register then inform AOne support via WhatsApp group" (proses manual).
- Parent mobile-only — friction untuk parent yang phone penuh / kongsi device.
- Rating / install count Play Store tak dapat diambil dalam research — tanya terus atau check sendiri di Play Store.
- Claim 4,000+ naik dari 2,000+ tanpa asas audit.

---

### 3.2 LittleLives — Singapore (preschool)

**Website & demo route:** https://www.littlelives.com/ — pricing page https://www.littlelives.com/pricing ("package to match your price point"; contact sales@littlelives.com, hotline SG 89296707). Product: https://www.littlelives.com/product/school-management-system. FB Malaysia: https://www.facebook.com/littlelivesmalaysia/. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** HQ Singapore, office KL (expand ke MY 2017). Segment: preschool / childcare, bukan tuition. Claim 1,500+ sekolah (SG 750+; MY "293 preschools" satu sumber, "145" sumber lain). PSG-eligible SG. Harga quote-based, tak ada RM/SGD public.

**Whole system in 5 lines:**
1. Modul: attendance / digital check-in ("LittleLives Check In" app), real-time notification, fee management + automated invoicing, parent-teacher portal, student portfolio / progress report, staff & operations.
2. User: principal/admin (web), teacher (app), parent (app "Little Family Room for Parents" `com.littlelives.familyroom`, pengganti "LittleLives For Parents" `com.littlelives.littlefamilyroomv2` / iOS id 6752276737).
3. Flow: enrolment → daily check-in → daily health/activity update ke parent → invoice bulanan → portfolio/laporan perkembangan.
4. Two-way communication app dilancar Jan 2019 (SG + MY).
5. Parent app rating **3.29/5 dari ~1.1k rating, ~300k download**, last update 31 Jul 2026 (AppBrain via snippet).

**Kenapa demo ni penting:** Market leader preschool serantau dengan volume parent paling besar — kau boleh tengok apa yang parent Malaysia dah biasa (feed harian, check-in, portfolio) DAN apa yang mereka benci (rating 3.29). Preschool bukan tuition, tapi pattern parent app + portfolio + invoice terus relevan untuk pusat bacaan.

**Soalan khusus untuk vendor ni:**
- [ ] Ada package untuk enrichment / reading centre, atau preschool je? Term-based class boleh?
- [ ] Harga MY dalam RM untuk 20 centre?
- [ ] Payment gateway MY (FPX/DuitNow) — ada? LHDN e-Invoice — ada?
- [ ] App baru vs app lama: apa beza, bila migrate, kenapa tak ada Face ID auto-login?
- [ ] Complaint pengguna: inbox "keep loading", fail tak boleh buka, timestamp salah lepas upgrade, activity log tak sort by time — dah fix?
- [ ] Media storage: parent boleh delete photo/doc? Limit storage per child?
- [ ] BM UI ada? Multi-language auto-translate?
- [ ] Team support MY masih aktif? (last press MY 2019)

**Feature yang WAJIB screenshot / screen record:**
- [ ] Parent app home feed (activity/health/photo timeline)
- [ ] Check-in screen (teacher/kiosk) + notifikasi parent
- [ ] Inbox / two-way messaging UI (parent & teacher side)
- [ ] Student portfolio / progress report builder + PDF output
- [ ] Bulletin / announcement screen
- [ ] Fee module: fee plan, invoice auto, payment status, reminder
- [ ] Multi-centre / group view untuk HQ (kalau ada)
- [ ] Staff & operations module (roster, leave)
- [ ] Admin report list + export
- [ ] Settings: class, session, capacity

**Perangkap / red flags (dari nota):**
- Rating 3.29/5 — bug inbox, attachment, notifikasi buka hari yang salah, health timing salah (diaper 9:45am ditunjuk 11:00pm), tak boleh delete media, bulletin lama tak boleh delete.
- Preschool tool; segment tuition tak dibina.
- Press MY terakhir 2019 — mungkin fokus SG/China.
- Pricing tak telus; hotline SG.
- Angka MY bercanggah (145 vs 293) — tanya terus.

---

### 3.3 REMMU — Kulim, Kedah

**Website & demo route:** https://remmu.com/ — pricing https://remmu.com/pricing, contact https://remmu.com/contact. Free plan ≤20 student — **sign up sendiri dulu**, then minta demo untuk multi-branch.

**HQ / segment / pelanggan / harga:** HQ Kulim, Kedah. Segment: tuition, martial arts, dance, fitness, swimming, music, daycare, 1-to-1 tutor. Customer count: tak jumpa. Harga: Free ≤20 student (full feature); Lite RM69/bln ≤100 student; Pro RM99/bln ≤300; Elite RM149/bln untuk besar. "All plans include FPX and mobile apps".

**Whole system in 5 lines:**
1. Modul: student, class attendance, fee automation, **WhatsApp reminder automatik**, **FPX payment** — semua plan.
2. User: admin (web), mobile app "REMMU" Android (`com.remmu.app`); iOS tak confirm.
3. Flow: daftar student → class/attendance → invoice bulanan auto → WhatsApp reminder → parent bayar FPX → receipt.
4. Harga ikut tier bilangan student, per centre.
5. Tak ada bukti e-Invoice, BM UI (site English), multi-branch dashboard — kena tanya.

**Kenapa demo ni penting:** Ni "price floor" pasaran MY — RM69–149 dengan FPX + WhatsApp built in. Parent di kawasan luar KL mungkin dah biasa dengan flow ni. Kau kena tahu berapa banyak feature boleh dapat pada harga ni, sebab pelanggan single-centre akan bandingkan kau dengan REMMU.

**Soalan khusus untuk vendor ni:**
- [ ] 20 branch = 20 subscription atau ada plan group? Ada HQ view?
- [ ] WhatsApp reminder: official API atau nombor biasa? Siapa bayar mesej? Template boleh edit?
- [ ] FPX gateway mana di belakang? Fee per transaksi? DuitNow QR ada?
- [ ] E-Invoice LHDN ada dalam roadmap?
- [ ] iOS app ada? Parent app atau admin app je?
- [ ] Berapa centre guna sekarang? Ada pelanggan multi-branch?
- [ ] Elite plan — limit student berapa, apa tambahan?
- [ ] Data export & cancel — boleh bila-bila?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page (asal) — screenshot untuk rekod
- [ ] Dashboard admin (free account kau sendiri)
- [ ] Add student / guardian form
- [ ] Class setup + attendance marking UI
- [ ] Fee plan setup + invoice auto-generate schedule
- [ ] WhatsApp reminder template + log hantar
- [ ] FPX payment page yang parent nampak + receipt
- [ ] Mobile app REMMU: home, attendance, payment
- [ ] Report / export
- [ ] Settings: branch/centre, user roles
- [ ] Upgrade flow (Free → Lite) untuk tengok apa yang dikunci

**Perangkap / red flags (dari nota):**
- Tak ada customer count / testimonial — platform risk (team kecil).
- iOS tak confirm; BM UI tak confirm.
- Tiada bukti e-Invoice atau multi-branch dashboard.
- Harga tier ikut student — tanya apa jadi bila lebih 300.

---

### 3.4 SimTrain / SimTrain Eco (SIMIT Group) — Malaysia

**Website & demo route:** https://simtrainsystem.com/ — MY pricing https://www.simtrainsystem.com/?q=pricing-my, SG pricing https://www.simtrainsystem.com/?q=pricing-sg, tuition success stories https://simtrainsystem.com/tuition-centre, SIMIT Group https://simitgroup.com/simtraineco/. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** SIMIT Group, Malaysia (market MY, SG, Indonesia). Segment: tuition, kindergarten, language school, art, math academy, music. Named customers: Pusat Tuisyen Intensif Hamka (350+ student), MC Plus Tuition Centre. Harga: **RM200–250/centre/bulan** ikut tier (Capterra snippet, unverified) + freemium plan basic.

**Whole system in 5 lines:**
1. Modul: admissions, student records, class scheduling, attendance, fee collection, payments, **e-Invoice** ("in one seamless platform").
2. User: admin (web), parent mobile app (komunikasi, bayaran, attendance real-time).
3. Flow: admission → class → attendance → fee → payment → e-Invoice.
4. Harga flat per centre (bukan per student) — mid-tier MY.
5. Blog 2026 aktif ("Best Tuition Centre Management Software in Southeast Asia Guide 2026").

**Kenapa demo ni penting:** Satu daripada tiga vendor MY dengan bukti e-Invoice dalam billing flow (AOne, SimTrain, ClassFlow). Harga flat per centre RM200–250 — ni band harga yang paling mungkin kau sendiri masuk. Ada pelanggan tuition 350+ student yang boleh dijadikan reference.

**Soalan khusus untuk vendor ni:**
- [ ] Beza plan RM200 vs RM250? Freemium had apa?
- [ ] 20 centre — ada group pricing dan HQ dashboard?
- [ ] E-Invoice: submit terus ke MyInvois atau via accounting software? Consolidated bulanan boleh?
- [ ] Gateway pembayaran mana? FPX / DuitNow fee?
- [ ] WhatsApp reminder ada? (nota tak sebut)
- [ ] BM UI ada untuk parent app?
- [ ] Boleh contact Pusat Tuisyen Intensif Hamka sebagai reference?
- [ ] Indonesia / SG version sama codebase — feature MY-specific dijaga siapa?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page MY (screenshot asal)
- [ ] Admission form / enquiry → enrol flow
- [ ] Class scheduling screen (week view)
- [ ] Attendance marking (admin & parent view real-time)
- [ ] Fee setup + invoice + payment status
- [ ] E-Invoice screen: TIN field, submission, status dari LHDN
- [ ] Parent app: home, payment, attendance, message
- [ ] Multi-centre view (kalau ada)
- [ ] Report list + export
- [ ] User role settings

**Perangkap / red flags (dari nota):**
- Harga hanya dari Capterra snippet — confirm.
- BM UI, WhatsApp, multi-branch dashboard tak disahkan.
- Tiada info API / export.

---

### 3.5 ClassFlow.my — Malaysia (mungkin dev shop luar)

**Website & demo route:** https://classflow.my/ — BM site https://classflow.my/ms/, blog https://classflow.my/blog/tuition-centre-management-software-malaysia/. Sister site Sri Lanka https://classflow.lk/pricing. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Origin tak pasti (ada sister site .lk → mungkin dev shop multi-negara). Segment: kindergarten, tadika, taska, tuition, enrichment. Customer count tak jumpa. Harga: **dari RM89/bulan**.

**Whole system in 5 lines:**
1. Modul: **face-scan attendance**, automated billing, parent app, AI tools, e-Invoice LHDN (disenaraikan dalam services).
2. User: admin web, parent app Android "ClassFlow" (`com.classflowapp`); iOS tak confirm.
3. Flow: student → face-scan check-in → billing auto → parent app → e-Invoice.
4. BM + English site.
5. Blog aktif 2026, positioning "WhatsApp communication + multi-currency" sebagai keperluan MY.

**Kenapa demo ni penting:** Harga RM89 + e-Invoice + BM + face-scan attendance — kombinasi murah yang agresif. Tengok betul ke face-scan tu praktikal untuk kelas kecil, dan apa "AI tools" yang mereka maksudkan (idea untuk progress report AI).

**Soalan khusus untuk vendor ni:**
- [ ] Syarikat mana di belakang ClassFlow.my? Team di Malaysia atau Sri Lanka? Support jam MY?
- [ ] RM89 cover berapa student / berapa branch? Ada per-student add-on?
- [ ] Face-scan attendance: guna device apa (tablet di pintu)? Consent PDPA untuk biometric kanak-kanak macam mana?
- [ ] "AI tools" tu apa sebenarnya? Tunjuk live.
- [ ] E-Invoice: live atau "coming soon"? Tunjuk submission sebenar.
- [ ] WhatsApp: automated atau manual?
- [ ] iOS app ada?
- [ ] Pelanggan multi-branch ada?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Face-scan attendance setup + live check-in screen
- [ ] Attendance report yang terhasil
- [ ] Automated billing setup + invoice
- [ ] Payment method (gateway) + receipt
- [ ] E-Invoice screen
- [ ] Parent app home + notifikasi
- [ ] AI tools screen (apa pun yang ada)
- [ ] BM UI vs EN UI (toggle)
- [ ] Multi-branch view (kalau ada)
- [ ] Pricing page + add-on list
- [ ] Settings & user roles

**Perangkap / red flags (dari nota):**
- Origin tak jelas (.lk sister) — risiko support & PDPA data residency.
- Hanya Android app disahkan.
- E-Invoice dari search snippet je — confirm live.

---

### 3.6 Skooly — global (MY & SG landing page)

**Website & demo route:** https://getskooly.com/tuition-centre-software-malaysia/ (MY), https://getskooly.com/tuition-centre-software-singapore/ (SG), app page https://getskooly.com/schools/en/tuition-software-app.html. **Free 14-day pilot** — sign up sendiri, then minta demo. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** HQ bukan MY (India-origin ikut satu nota; tak disahkan). Segment: tuition, academy. Claim "4,950 schools, 19,950 teachers, 10+ countries, US$10m+ payments processed". Harga: tiga plan "priced openly in ringgit" tapi angka tak dapat diambil (gap) — **DuitNow & FPX dengan 0% Skooly fee**.

**Whole system in 5 lines:**
1. Plan basic: scheduling, attendance, invoicing, parent app. Plan mid ("Collection Suite"): **QR pada invoice**, automated reminder, **auto-reconciliation**, **check-in kiosk**, parent progress update. Plan premium: assessments, **multi-branch**, **tutor payroll**.
2. User: admin web, tutor, parent app.
3. Flow: schedule → attendance/kiosk → invoice dengan QR DuitNow → auto-reconcile → progress update.
4. Multi-branch hanya di plan premium.
5. Aktif 2026 (guide MY/SG).

**Kenapa demo ni penting:** Satu-satunya yang secara jelas tawar 0% fee FPX/DuitNow + auto-reconciliation + QR on invoice — ni exactly masalah "bank transfer screenshot" yang owner MY hadapi. Tutor payroll dan assessment pun jarang di vendor MY. Kau nak tengok macam mana reconciliation tu berfungsi.

**Soalan khusus untuk vendor ni:**
- [ ] Harga RM ketiga-tiga plan (bertulis)? Multi-branch di premium — 20 branch berapa?
- [ ] 0% fee FPX/DuitNow — siapa bayar gateway? Gateway mana? Settlement berapa hari?
- [ ] Auto-reconciliation: macam mana match bayaran ke invoice (reference number? webhook?) — tunjuk live dengan bayaran manual/cash
- [ ] Check-in kiosk: device apa, PIN / QR?
- [ ] Tutor payroll: kira ikut per class / per student / per jam? Export payslip?
- [ ] Team support di Malaysia? Jam?
- [ ] E-Invoice LHDN ada?
- [ ] WhatsApp reminder official API?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page RM (ketiga plan)
- [ ] Invoice dengan QR DuitNow (contoh PDF / app)
- [ ] Auto-reconciliation screen: incoming payment matched vs unmatched
- [ ] Automated reminder rules + template
- [ ] Check-in kiosk UI
- [ ] Parent progress update screen (tutor side + parent side)
- [ ] Assessment module
- [ ] Multi-branch dashboard
- [ ] Tutor payroll calculation + report
- [ ] Class schedule view + attendance marking
- [ ] Parent app home
- [ ] Report & export list

**Perangkap / red flags (dari nota):**
- HQ luar negara — support timezone, PDPA data residency.
- Angka RM tak dapat diambil dalam research; "priced openly" kena verify.
- Multi-branch dikunci belakang plan paling mahal.
- Claim 4,950 schools — vendor claim.

---

### 3.7 Anak2U — Malaysia (preschool)

**Website & demo route:** anak2u.com.my — about https://anak2u.com.my/about/. App: Parent (iOS id1449444297 https://apps.apple.com/my/app/anak2u-parent/id1449444297, Android `com.anak2u.parent`), Classroom (iOS https://apps.apple.com/us/app/anak2u-classroom/id1574878178), Teacher (Android https://play.google.com/store/apps/details?id=com.anak2u.teacher&hl=en_US). FB https://www.facebook.com/anak2uofficial. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Malaysia (founders Wan Muzaffar Wan Hasim, Aniq, Mohamad Faizal Razak). Segment: preschool / kindergarten / childcare. Customer count tak jumpa. Harga (Software Finder / Vulcan Post snippet, **unverified**, basis one-off vs tahunan tak dinyatakan): Starter RM300, Premium Package RM4,000, Website Package RM2,000; **transaction fee per student RM3 atau 5% billing, mana lebih rendah**.

**Whole system in 5 lines:**
1. Modul: data student/class/staff berpusat, **digital daily report** (nap, meal, attendance, toilet), paperless billing/invoice, in-app payment, invoice/receipt dalam app.
2. Tiga app: Parent, Teacher, Classroom (tablet kelas) + white-label "TKC Parent" (`com.anak2u.tkcparent`).
3. Flow: enrol → daily report per anak oleh teacher → parent app → invoice bulanan → bayar in-app.
4. Monetisasi: pakej + transaction fee per student.
5. Complaint app: video tak jumpa / tak boleh download, chat kena reopen app baru nampak, video teacher tak play.

**Kenapa demo ni penting:** Model tiga app (Parent / Teacher / Classroom) dan white-label parent app untuk chain ("TKC Parent") — ni pattern yang kau boleh guna untuk 20 branch. Juga contoh model harga pakej + RM3/student transaction fee.

**Soalan khusus untuk vendor ni:**
- [ ] RM300 / RM4,000 / RM2,000 tu one-off atau tahunan? Apa beza Starter vs Premium?
- [ ] Transaction fee RM3 atau 5% — kena bayar walaupun parent bayar cash?
- [ ] White-label app (macam TKC Parent) — kos & masa? Boleh untuk chain 20 branch satu app?
- [ ] Ada mode untuk reading/enrichment (session-based, bukan daily report)?
- [ ] Bug chat/notification & video — dah fix? Storage limit video?
- [ ] Gateway pembayaran mana? FPX / DuitNow?
- [ ] E-Invoice LHDN?
- [ ] Multi-branch view untuk HQ?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Classroom app (tablet) — daily report entry UI
- [ ] Teacher app — attendance, report, chat
- [ ] Parent app — home, daily report, invoice, receipt, pay
- [ ] Admin web dashboard: student/class/staff
- [ ] Billing: invoice generation, transaction fee display
- [ ] Chat / messaging UI (dua-dua sisi)
- [ ] White-label app contoh (TKC Parent)
- [ ] Multi-centre view (kalau ada)
- [ ] Report & export
- [ ] Pricing / package sheet (minta PDF)

**Perangkap / red flags (dari nota):**
- Struktur harga keliru (one-off vs annual tak jelas) — dapatkan bertulis.
- Transaction fee per student naik dengan enrolment.
- Preschool workflow (nap/meal) bukan tuition.
- Bug chat/video berulang dalam review.

---

### 3.8 Oodlins — Malaysia (preschool)

**Website & demo route:** https://oodlins.com/ — pricing https://oodlins.com/pricing/, about https://oodlins.com/about/, blog e-invoice https://oodlins.com/blog/malaysia-e-invoice-schools-2026/. iOS app https://apps.apple.com/my/app/oodlins/id1469359974. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Malaysia origin, "serves hundreds of schools, expanding SEA". Segment: preschool / school. Harga: **dari RM89/bulan; parent account free; unlimited staff account; no long-term contract; RM5 per active student/bulan lebih had plan**.

**Whole system in 5 lines:**
1. Modul: student, class, attendance, billing, parent app, staff — standard preschool.
2. User: admin, unlimited staff, parent (free).
3. Flow: enrol → attendance → invoice → parent app.
4. Harga base + RM5/active student — model hybrid yang paling telus di MY.
5. Content marketing 2026 (7 best preschool systems; e-invoice prep guide) — tapi e-invoice sendiri "prep guide", bukan bukti live.

**Kenapa demo ni penting:** Model harga RM89 + RM5/active student adalah benchmark "fair pricing" untuk MY. "Unlimited staff, free parent" hilangkan objection per-seat. Tengok apa yang mereka define sebagai "active student" (ni penting untuk pricing kau).

**Soalan khusus untuk vendor ni:**
- [ ] "Active student" define macam mana? Student cuti sebulan kira?
- [ ] Had student dalam RM89 berapa? 20 branch × 120 student — kira berapa?
- [ ] E-Invoice: live dalam produk atau guide je?
- [ ] Gateway: FPX / DuitNow? Fee?
- [ ] WhatsApp reminder?
- [ ] Multi-branch HQ dashboard?
- [ ] Session-based class (bukan preschool harian) support?
- [ ] BM UI?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page + calculator (kalau ada)
- [ ] Dashboard admin
- [ ] Student status (active/inactive) screen — cara mereka kira
- [ ] Attendance UI
- [ ] Billing setup + invoice + reminder
- [ ] Parent app (iOS) — home, invoice, pay
- [ ] Staff account & role
- [ ] Multi-school view
- [ ] Report & export
- [ ] E-Invoice (kalau ada)

**Perangkap / red flags (dari nota):**
- Per-active-student surcharge — kos naik ikut enrolment.
- Preschool focus.
- E-Invoice hanya blog guide setakat nota.

---

### 3.9 Classcard (ex-Reportcard) — Dubai HQ, listing SG

**Website & demo route:** https://www.classcardapp.com/ — pricing https://www.classcardapp.com/pricing, integrations https://www.classcardapp.com/integrations, WhatsApp help https://help.classcardapp.com/en/article/whatsapp-integration-5zsg0f/. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** HQ Dubai, UAE (satu nota; nota MY/SG kata listing SG wujud, HQ tak disahkan). Segment: academies, tuition, sports, music, dance. Claim **3,250+ schools/centres worldwide**. Harga flat: **Starter US$99 / Growth US$199 / Business US$349 sebulan + small per-transaction fee; unlimited staff & student**. Growth = automations, AI assistance, accounting integration, instalment billing. Business = multi-step workflows, gift cards, SSO, AI chat. Enterprise boleh bawa gateway sendiri.

**Whole system in 5 lines:**
1. Modul: scheduling, attendance, billing (instalment), automations, AI assistant, gift cards, workflows.
2. Integration: **WhatsApp via Twilio (template messages untuk class & invoice reminder)**, Zoom, Lessonspace, Stripe, Razorpay, Zoho Books, Xero, ClassMarker, Zapier.
3. Flow: enquiry → enrol → schedule → attendance → invoice (instalment) → auto reminder WhatsApp → accounting sync.
4. Harga flat per centre, unlimited user — model "predictable" yang review suka.
5. Analog antarabangsa paling dekat untuk MY (WhatsApp + flat pricing + Razorpay).

**Kenapa demo ni penting:** Ni rujukan terbaik untuk "automation + WhatsApp template + accounting sync" yang dibuat betul. Tengok automation builder dan macam mana WhatsApp template diluluskan / dihantar. Juga tengok instalment billing dan gift card sebagai produk.

**Soalan khusus untuk vendor ni:**
- [ ] Multi-branch: 20 branch = satu account atau 20? Harga?
- [ ] WhatsApp Twilio: nombor MY boleh daftar? Kos per mesej? Template approval berapa lama?
- [ ] Gateway MY: Stripe MY (3%+RM1 FPX) atau boleh bawa Billplz/CHIP di Enterprise?
- [ ] Per-transaction fee berapa?
- [ ] Xero / Zoho — push invoice & payment? Bukku tak ada?
- [ ] AI assistant buat apa sebenarnya?
- [ ] Support timezone untuk MY?
- [ ] Data residency / PDPA?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page (plan matrix)
- [ ] Automation builder (trigger → action) dan senarai trigger
- [ ] WhatsApp template setup + log
- [ ] Class schedule + attendance
- [ ] Invoice + instalment plan setup
- [ ] Payment page (Stripe/Razorpay) + receipt
- [ ] Accounting integration setup (Xero/Zoho)
- [ ] Multi-step workflow (Business)
- [ ] AI chat / AI assistance screen
- [ ] Gift card / package product
- [ ] Parent/student portal
- [ ] Reports
- [ ] Zapier integration list

**Perangkap / red flags (dari nota):**
- USD pricing (US$99–349 ≈ RM450–1,600/centre) — mahal untuk MY, tapi ambil pattern.
- Twilio markup atas Meta fee.
- Tak ada FPX/DuitNow native.

---

### 3.10 Taidii — Singapore

**Website & demo route:** https://www.taidii.com/ — PSG listing https://grants.gobusiness.gov.sg/support/productivity-solutions-grant/psg-directory/taidii-smart-it-solution-for-preschool-pms-e-form. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Singapore. Segment: kindergarten, childcare, enrichment/tuition, independent & international school. Customers: MindChamps, M.Y World Preschool; claim "over half of private school market SG". Presence China, UAE, Australia, Philippines. PSG-eligible. Harga: tak jumpa.

**Whole system in 5 lines:**
1. **Lima suite**: fee/enrolment, curriculum/child development, parent engagement, teacher development, daily ops.
2. User: HQ, principal, teacher, parent.
3. Flow: e-form enrolment → fee → daily ops → curriculum tracking → parent engagement.
4. Pelanggan chain besar (MindChamps) — bukti multi-branch skala.
5. Enterprise-grade preschool + enrichment.

**Kenapa demo ni penting:** Ni vendor yang serve chain preschool terbesar di SG. Tengok macam mana HQ chain control curriculum + fee + ops merentas puluhan centre. "Teacher development" suite jarang ada — idea untuk retention guru.

**Soalan khusus untuk vendor ni:**
- [ ] Ada pelanggan Malaysia? Harga RM? Team support MY?
- [ ] Enrichment/tuition workflow vs preschool — modul apa berbeza?
- [ ] HQ vs centre control: apa yang HQ lock (fee, curriculum, template)?
- [ ] Gateway MY (FPX/DuitNow)? PayNow je?
- [ ] E-Invoice LHDN?
- [ ] Teacher development suite — apa isi?
- [ ] E-form enrolment: boleh embed dalam website / FB ads?
- [ ] Harga per centre atau per child?

**Feature yang WAJIB screenshot / screen record:**
- [ ] HQ dashboard multi-centre
- [ ] E-form enrolment builder + parent view
- [ ] Fee module (plan, invoice, collection status)
- [ ] Curriculum / child development tracking UI
- [ ] Parent engagement app (feed, message)
- [ ] Teacher development module
- [ ] Daily ops (attendance, roster, incident)
- [ ] Report & analytics
- [ ] Permission matrix HQ / centre / teacher

**Perangkap / red flags (dari nota):**
- Harga & footprint MY tak diketahui; PSG-driven SG product.
- Mungkin overkill (enterprise) — cari bahagian yang relevan je.

---

### 3.11 ASIS — Malaysia (sekolah / tadika / tuisyen)

**Website & demo route:** https://www.asis.my/ — modul page contoh https://koperasitadikaminden.usm.my/perkhidmatan/modul-sistem-asis. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Malaysia, "Pilihan No 1 Sistem Sekolah Malaysia". Claim **500,000+ user, 3,000+ sekolah/preschool/tuition centre**; expand Indonesia, SG, Brunei. Harga tak public.

**Whole system in 5 lines:**
1. Modul "dari pendaftaran ke graduasi": registration, class, fees, **arrears**, invoice, receipt, pencapaian student/sekolah.
2. **Instalment payment** + kutipan via **PIBG**.
3. User: admin sekolah, guru, ibu bapa.
4. Flow: pendaftaran → kelas → yuran/tunggakan → resit → pencapaian.
5. Lebih sekolah-oriented (BM), segment tuition kurang jelas.

**Kenapa demo ni penting:** Incumbent BM untuk sekolah swasta/agama/tadika — 3,000+ install. Modul tunggakan (arrears) dan instalment adalah benda yang chain tuisyen perlukan tapi vendor SaaS baru selalu lupa. Tengok macam mana mereka handle tunggakan berbulan.

**Soalan khusus untuk vendor ni:**
- [ ] Ada versi untuk pusat tuisyen / enrichment (session-based)?
- [ ] Harga: per sekolah? one-off + support tahunan (macam SQL/AutoCount)?
- [ ] Multi-branch HQ view?
- [ ] Arrears: aging report, auto reminder, instalment plan — tunjuk
- [ ] Payment gateway FPX / DuitNow? Atau manual resit?
- [ ] E-Invoice LHDN?
- [ ] Parent app / WhatsApp?
- [ ] Cloud atau on-premise? Export data?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Modul list (menu utama)
- [ ] Pendaftaran student form (field BM — bandingkan dengan APDM style: IC/MyKid, bangsa, agama, penjaga)
- [ ] Fee setup + instalment plan
- [ ] Arrears / tunggakan report + aging
- [ ] Invoice & receipt (BM format)
- [ ] Pencapaian / achievement record
- [ ] Parent portal
- [ ] Multi-school view
- [ ] Report list
- [ ] Settings / role

**Perangkap / red flags (dari nota):**
- Segment tuition tak jelas; UI mungkin school-legacy.
- Harga tak public; tak ada bukti gateway / e-invoice / WhatsApp dalam nota.

---

### 3.12 Synorex Tuition — Malaysia

**Website & demo route:** https://synorex.group/tuition — https://edutech.synorex.group/. Apps: iOS https://apps.apple.com/my/app/synorex-tuition/id6468679664, Android https://play.google.com/store/apps/details?id=synorex.tuition&hl=en. FB https://www.facebook.com/SynorexTuition/. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Malaysia (Synorex Edutech). Segment: tuition. Harga tak jumpa — positioning **"unlimited students", flat monthly "regardless of whether you have 50 or 5,000 students"**, no setup fee, no long-term commitment, 24/7 support, "24 powerful tools".

**Whole system in 5 lines:**
1. Modul: student, class scheduling, attendance, billing, fee collection, parent app, **branches**.
2. **Digital invoice hantar ke parent via WhatsApp dalam satu klik**.
3. User: admin web, iOS + Android app.
4. Flow: enrol → schedule → attendance → invoice → WhatsApp → bayar.
5. Sister product Synorex School (school.synorex.work).

**Kenapa demo ni penting:** Flat "unlimited students" adalah serangan terus pada model per-student AOne — kau kena tahu berapa harga sebenar dan apa yang dikorbankan. WhatsApp one-click invoice adalah UX yang owner MY suka.

**Soalan khusus untuk vendor ni:**
- [ ] Harga flat sebulan berapa? Per branch? 20 branch?
- [ ] "24 tools" — senarai penuh
- [ ] WhatsApp invoice: official API atau buka WhatsApp dengan pre-filled text (click-to-chat)?
- [ ] FPX / DuitNow gateway? Fee?
- [ ] E-Invoice LHDN?
- [ ] Branch HQ dashboard?
- [ ] 24/7 support — betul? channel apa?
- [ ] Berapa centre guna sekarang?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Senarai 24 tools (menu)
- [ ] Class scheduling + attendance UI
- [ ] Invoice + butang "send via WhatsApp" + apa yang parent terima
- [ ] Payment flow parent
- [ ] Parent app (iOS) home
- [ ] Branch switch / HQ view
- [ ] Reports
- [ ] Settings & roles
- [ ] Pricing (minta bertulis)

**Perangkap / red flags (dari nota):**
- Tak ada harga public, tak ada customer count.
- "Unlimited" mungkin cover WhatsApp click-to-chat sahaja (bukan automation).

---

### 3.13 Yuran.my (+ YuranPay, Herepay) — Malaysia (fee-collection-first)

**Website & demo route:** https://yuran.my/ — artikel https://yuran.my/pengurusan-yuran-untuk-pusat-tuisyen/, https://yuran.my/tuition-centre-management-system-malaysia/. FB https://www.facebook.com/yuran.my/. YuranPay https://yuranpay.com/ (Bayarcash gateway). Herepay https://www.herepay.org/sistem-kutipan-yuran-tuisyen/. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Malaysia, BM-first. Segment: tuition, sekolah, sports club, persatuan. Harga tak dapat diambil. Iklan YouTube Shorts "Sistem Kutipan Yuran Harga Berpatutan Untuk Pusat Tuisyen".

**Whole system in 5 lines:**
1. Fokus **kutipan yuran**: FPX, DuitNow, e-wallet, kad; auto reminder via web & **WhatsApp**; "detect dishonest staff" (kawalan kebocoran cash).
2. User: admin, parent (link bayaran).
3. Flow: student → invoice → reminder WhatsApp → bayar → resit auto.
4. Bukan full ops (schedule/attendance lemah atau tiada).
5. YuranPay & Herepay = gateway vendor yang pakai marketing "sistem kutipan yuran".

**Kenapa demo ni penting:** Ni layer bayaran yang paling BM dan paling murah — pelanggan kau yang single-centre mungkin guna ni + Excel. "Detect dishonest staff" adalah selling point sebenar (cash leakage) yang kau patut ada.

**Soalan khusus untuk vendor ni:**
- [ ] Harga: subscription atau per transaksi? Fee FPX/DuitNow berapa?
- [ ] Gateway belakang siapa (Bayarcash? lain)?
- [ ] "Detect dishonest staff" — macam mana secara teknikal (cash log vs bank)?
- [ ] WhatsApp reminder: API atau manual?
- [ ] Ada attendance / schedule langsung?
- [ ] E-Invoice LHDN?
- [ ] Multi-branch reporting?
- [ ] Export ke accounting?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Invoice creation + bulk invoice bulanan
- [ ] Reminder schedule + WhatsApp message contoh
- [ ] Payment link page (FPX/DuitNow QR/e-wallet)
- [ ] Resit auto
- [ ] Cash collection log / staff control screen
- [ ] Outstanding / arrears dashboard
- [ ] Multi-branch report
- [ ] Settings (fee type, discount)
- [ ] Pricing (bertulis)

**Perangkap / red flags (dari nota):**
- Payment-only — tak boleh jadi sistem penuh.
- Harga tak public; bergantung pada gateway partner.

---

### 3.14 Tuis.my — Malaysia (BM-first)

**Website & demo route:** https://www.tuis.my/ — Windows app https://apps.microsoft.com/detail/9p7w4ndpl274?hl=en-US&gl=US. **Free ≤15 student** — sign up sendiri dulu. Cari butang Book a Demo / Contact di homepage (WhatsApp lebih cepat).

**HQ / segment / pelanggan / harga:** Malaysia, BM. Segment: tuisyen, tadika, taska, tahfiz, kelas Quran, akademi bahasa, coding. Free ≤15 student, tiada kad kredit; paid tier tak diambil.

**Whole system in 5 lines:**
1. Modul: student, kelas/jadual, kehadiran digital, invoice, laporan, **multiple branches**.
2. User: admin (web + Windows desktop app), parent (login dengan Centre Code + password yang dihantar **manual via WhatsApp**).
3. Flow: daftar → kelas → kehadiran → invoice → laporan.
4. Tiada bukti gateway, e-invoice, iOS/Android.
5. Kemungkinan produk 1–3 orang.

**Kenapa demo ni penting:** Ni contoh terbaik "BM-first, simple, free" — tengok bahasa UI dan istilah BM yang parent/admin Melayu selesa (kehadiran, yuran, resit, penjaga). Copy vocabulary, bukan feature.

**Soalan khusus untuk vendor ni:**
- [ ] Harga paid tier?
- [ ] Siapa team? Berapa centre?
- [ ] Multi-branch: HQ view ada?
- [ ] Payment gateway / FPX?
- [ ] Mobile app parent?
- [ ] E-Invoice?
- [ ] Export data?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Semua menu BM (label & istilah)
- [ ] Borang daftar student & penjaga (BM)
- [ ] Jadual kelas
- [ ] Kehadiran digital UI
- [ ] Invoice & resit BM
- [ ] Parent portal (Centre Code login)
- [ ] Laporan
- [ ] Windows desktop app (kalau berbeza)
- [ ] Branch setup

**Perangkap / red flags (dari nota):**
- Onboarding parent manual via WhatsApp — tak scale.
- Platform risk (team kecil, dokumentasi nipis).

---

### 3.15 illumine — India origin, MY landing page (preschool)

**Website & demo route:** https://try.illumine.app/mly (MY page) — blog https://illumine.app/blog/school-management-systems-malaysia. App `com.illumine.app`, iOS id 1459249394. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** India-built; "Malaysia's #1 preschool management app", "saves 35+ hours a month". Segment: taska/tadika. Harga RM tak diambil. Billing integrasi **RinggitPay**.

**Whole system in 5 lines:**
1. Modul: **QR check-in/out**, parent app daily report, health/immunisation record, billing, komunikasi.
2. User: admin, teacher app, parent app.
3. Flow: QR check-in → daily activity → parent app → invoice → RinggitPay.
4. Review: bagus untuk komunikasi; complaint video storage limit, notifikasi kena reopen app, support lambat.
5. Content marketing MY aktif 2025–2026.

**Kenapa demo ni penting:** QR check-in/out yang matang + parent app yang parent MY dah review (positif & negatif). Model "India-built, MY landing page" tunjuk macam mana vendor luar localise (RinggitPay) — tengok mana yang mereka tak localise (BM? e-invoice?).

**Soalan khusus untuk vendor ni:**
- [ ] Harga RM per centre / per child?
- [ ] Team support MY? Jam?
- [ ] RinggitPay: FPX / DuitNow? Fee?
- [ ] E-Invoice LHDN?
- [ ] Video storage limit — berapa? Boleh tambah?
- [ ] Bug notifikasi — dah fix versi mana?
- [ ] Mode enrichment / session-based?
- [ ] BM UI?

**Feature yang WAJIB screenshot / screen record:**
- [ ] QR check-in/out (parent scan) + admin attendance log
- [ ] Teacher app: daily report entry, photo/video upload
- [ ] Parent app: feed, health record, invoice, pay
- [ ] Billing setup + RinggitPay flow
- [ ] Messaging UI
- [ ] Multi-centre dashboard
- [ ] Report & export
- [ ] Settings & role

**Perangkap / red flags (dari nota):**
- Support lambat (review), storage limit, notification bug.
- Preschool workflow.

---

### 3.16 Mekar — Malaysia (taska/tadika, BM)

**Website & demo route:** mekar.com.my — https://www.mekar.com.my/my/solutions/tadika/pengurusan-kehadiran/, landing negeri https://www.mekar.com.my/my/lokasi/negeri-sembilan/. **Free 30-day trial** — sign up sendiri. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Malaysia, BM. Segment: taska & tadika. Harga **dari RM89/bulan**, trial 30 hari.

**Whole system in 5 lines:**
1. Modul: kehadiran digital, bil automatik, parent app, **laporan pematuhan JKM**.
2. User: admin, guru, ibu bapa.
3. Flow: daftar → kehadiran → bil → parent app → laporan JKM.
4. SEO landing page per negeri — marketing agresif BM.
5. Tiada info gateway / e-invoice / multi-branch.

**Kenapa demo ni penting:** Contoh regulatory-report-as-feature (JKM). Untuk kau, analognya laporan JPN/KPM untuk pusat tuisyen. Juga tengok BM UI dan parent app yang murah RM89.

**Soalan khusus untuk vendor ni:**
- [ ] RM89 cover berapa student/branch?
- [ ] Laporan JKM apa yang auto-generate?
- [ ] Gateway FPX / DuitNow?
- [ ] WhatsApp?
- [ ] E-Invoice?
- [ ] Multi-branch?
- [ ] Ada versi untuk tuisyen/enrichment?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Menu BM penuh
- [ ] Kehadiran digital UI
- [ ] Bil automatik + resit
- [ ] Parent app
- [ ] Laporan JKM (template)
- [ ] Multi-cawangan (kalau ada)
- [ ] Settings
- [ ] Pricing

**Perangkap / red flags (dari nota):**
- Preschool-only; tiada bukti gateway / e-invoice / WhatsApp.

---

### 3.17 BooknGo — Singapore (booking-centric)

**Website & demo route:** bookngo.app — https://www.bookngo.app/sg/about-us/, guide https://bookngo.app/sg/blog/tuition-centre-booking-system-singapore/. Cari butang Book a Demo / Contact di homepage.

**HQ / segment / pelanggan / harga:** Singapore-built. Segment: tuition, enrichment. Harga tak diambil.

**Whole system in 5 lines:**
1. Modul: **term enrolment**, PayNow at checkout, **automated WhatsApp reminder**, attendance, **parent self-service**, **multi-branch**.
2. User: admin, parent (self-service web).
3. Flow: parent book term online → bayar PayNow → reminder WhatsApp → attendance.
4. Booking engine dulu, ops kemudian.
5. Content 2026 aktif.

**Kenapa demo ni penting:** Parent self-service term enrolment + bayar terus adalah flow "zero admin" yang vendor MY belum buat elok. Tengok macam mana mereka handle capacity, waitlist, dan term-based billing.

**Soalan khusus untuk vendor ni:**
- [ ] Harga? Per branch?
- [ ] MY: FPX/DuitNow ganti PayNow boleh?
- [ ] WhatsApp reminder: API? nombor MY?
- [ ] Waitlist & capacity per class?
- [ ] Make-up class flow?
- [ ] Multi-branch HQ report?
- [ ] Teacher app?
- [ ] Export data?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Parent-facing booking page (term/class pick)
- [ ] Checkout + PayNow screen
- [ ] Admin: class capacity, waitlist
- [ ] WhatsApp reminder template
- [ ] Attendance UI
- [ ] Parent self-service portal (reschedule, invoice)
- [ ] Multi-branch view
- [ ] Reports

**Perangkap / red flags (dari nota):**
- SG-centric payment (PayNow); harga tak diketahui; ops depth mungkin cetek.

---

### 3.18 Tier 1B — vendor MY/SG lain (lawat website / WhatsApp tanya harga; demo kalau ada masa)

| Vendor | URL (dari nota) | Apa dia | Apa nak tengok | Status |
|---|---|---|---|---|
| iEduCentre (SG) | https://www.ieducentre.com/ ; https://www.ieducentre.com/enrichment-centres | Modular: enquiry → scheduling, make-up lesson, attendance, fee, HR, teacher pay | Make-up lesson flow, teacher pay module | - [ ] |
| TadikaPro (HPCS, Shah Alam) | https://tadikapro.my/ ; https://tadikapro.hpcs.com.my/index.php/pakej | Tadika/taska, "dari RM53" | Pakej page, BM UI | - [ ] |
| KindyPro (AWFATECH) | https://ipermata.awfatech.com/ (sister iPermata) | 5 modul, online registration dengan keputusan via SMS/email/WhatsApp | Online registration flow | - [ ] |
| EZSolution | https://www.ezsolution.my/ ; contoh tenant https://www.tadikaceria.ezsolution.my/ | Sejak 2009, subdomain per tadika; student, penjaga, staff, yuran, OT, cuti | Model subdomain per tenant; nampak dormant | - [ ] |
| UrusTuisyen | https://urustuisyen.com/ | BM, sistem pusat tuisyen; harga tak jumpa | Vocabulary BM | - [ ] |
| EduTenant | https://www.edutenant.online/ | BM, tiada detail | Tanya harga je | - [ ] |
| MyCampusSquare | https://mycampussquare.com/tuition-centre-management-system ; FB https://www.facebook.com/mycampussquare/ | Tuition + college system MY | Modul list | - [ ] |
| iCRM | https://www.icrm.com.my/ | WhatsApp Business API + AI auto-reply + membership + e-learning untuk tuition | WhatsApp inbox pattern MY | - [ ] |
| IntelliTuition (Gotchaa Lab, custom) | https://gotchaa-lab.com/portfolio/intellituition | Custom build untuk chain 400+ student Klang Valley; claim admin 30j → <5j/minggu, 95% on-time fee | Baca case study je — apa modul yang chain sebenar minta | - [ ] |
| Sorable (custom) | https://www.sorable.com/industries/education-tuition-software-malaysia | Custom build RM8k–35k | Benchmark kos custom build | - [ ] |
| Tutorbase | https://tutorbase.com/blog/tuition-center-software-malaysia | 1% of invoiced revenue; guide MY/SG; PDPA guide (SG) | Pricing model 1% | - [ ] |
| Schoolber (SG) | https://www.schoolber.com.sg/school-management-software.html | S$2/student/month | Pricing anchor per student | - [ ] |
| Episcript (SG) | https://www.episcript.com/tuition-centre-management-software | Tuition centre software SG | Modul list | - [ ] |
| SchoolTracs (HK/SG) | https://www.schooltracs.com/sg/ | Class booking/scheduling | Booking UX | - [ ] |
| OClass | https://oclass.app/ | Class mgmt untuk gym/enrichment | Booking UX | - [ ] |
| Edulabs (SG) | https://www.edulabs.com.sg/ | Tuition SG | Modul list | - [ ] |
| TuitionPost (Prismtech MY) | https://prismtechsolution.com.my/tuitionpost-learning-centre-management-system/ | Learning centre system MY | Modul list | - [ ] |
| EzFlow (MY) | https://www.ezflow.my/blog/tuition-centers-class-scheduling-automation | Scheduling automation | Blog + tanya produk | - [ ] |
| KidzyPedia (MY) | https://www.kidzypedia.com/ | Kindergarten system, harga RM | Pricing | - [ ] |
| LOLA (MY) | https://lola.my/ | Directory tadika + operator tools | Directory + tools combo | - [ ] |
| HelloParent / EdusysERP | https://www.helloparent.com/school-management-software-malaysia ; https://www.edusyserp.com/en-my/school-management-software.html | India-origin school ERP dengan MY page | Skip kecuali nak tengok school ERP | - [ ] |
| Kumotic KCMS / Cheqdin (Kumon add-on) | https://kumotic.com/ ; https://cheqdin.com/cheqdin-for-kumon | Third-party tool untuk franchisee Kumon: attendance, invoice, parent WhatsApp | Apa franchisee reading programme sebenarnya perlukan | - [ ] |

---

## 4. Tier 2 — section per vendor

### 4.1 Jackrabbit Class — US

**Website & demo route:** https://www.jackrabbitclass.com — pricing https://www.jackrabbitclass.com/pricing/, enterprise https://www.jackrabbitclass.com/enterprise/, multi-location help https://help.jackrabbitclass.com/help/multiple-business-locations-db. Cari butang Book a Demo / Free Trial di homepage.

**HQ / segment / pelanggan / harga:** US (North Carolina, unverified). Dance, gym, swim, cheer, music, enrichment. Claim "12,000+ schools" / "7,000+ customers 25 countries" (dua angka bercanggah). Harga tier student: Class US$49 (0–100 student) → US$245 (3,001+); Plus+ US$89 → US$315; **bil ikut Active + Inactive student pada hari rawak setiap bulan**.

**Whole system in 5 lines:**
1. Modul: class, enrolment, attendance, billing, parent portal, staff portal, reporting.
2. **Business Intelligence Dashboard: 30+ KPI semua lokasi side-by-side**; **Executive Dashboard**: revenue, enrolment, alert semua family tanpa kira lokasi.
3. Multi-location: satu database vs database berasingan (ada help article pros/cons).
4. Flow: online registration → class → attendance → auto-bill → portal.
5. Tiada public API.

**Kenapa demo ni penting:** Rujukan terbaik untuk **cross-location KPI dashboard**. Kau nak tengok 30 KPI tu apa dan macam mana mereka susun. Juga contoh pricing model yang review benci (student sampling) — jangan tiru.

**Soalan khusus:**
- [ ] Tunjuk BI Dashboard & Executive Dashboard penuh — senarai KPI
- [ ] Satu DB vs banyak DB untuk 20 lokasi — mereka syor apa? kenapa?
- [ ] Kira staff pay per class taught? (nota: tak dapat verify)
- [ ] Move student antara class — berapa klik? (review: terlalu banyak)
- [ ] API ada?
- [ ] Support wait time (review: 2 minggu)

**Feature yang WAJIB screenshot / screen record:**
- [ ] BI Dashboard (semua widget)
- [ ] Executive Dashboard
- [ ] Multi-location switcher & settings
- [ ] Class schedule view + enrolment counts/capacity
- [ ] Attendance marking
- [ ] Family ledger / transaction screen (review: susah track)
- [ ] Auto-billing setup + fee posting
- [ ] Parent portal (registration, pay)
- [ ] Staff portal
- [ ] Report list
- [ ] Pricing page (student-count tiers)

**Perangkap / red flags (dari nota):** "Way too many buttons"; outdated UI; move student terlalu banyak klik; transaction tracking per family hampir mustahil; support appointment 2 minggu; payment processing keliru; user delete student untuk kurangkan bil. USD, US-payments.

---

### 4.2 iClassPro — US

**Website & demo route:** https://www.iclasspro.com — pricing https://www.iclasspro.com/iclasspro-pricing, FAQ https://www.iclasspro.com/iclasspro-faqs. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** US (Texas). Gymnastics, swim, dance, cheer, enrichment. Harga **flat per location**: Signature US$129 / Elite US$199 / Premium US$299 sebulan; "location" = tapak fizikal berbeza. **Branded App add-on US$499 one-time + US$150/bln**.

**Whole system in 5 lines:**
1. Modul: class, enrolment, attendance, billing, parent portal, staff, reports.
2. Flat per location — review puji "easy budgeting".
3. Branded parent app sebagai produk berbayar.
4. Flow: registration → class → attendance → bill (manual! — review) → portal.
5. Customisation parent portal "best done by developer".

**Kenapa demo ni penting:** Flat per-location pricing + branded app add-on = dua idea monetisasi untuk 20 branch. Tengok apa yang orang suka (navigation) dan apa yang mereka benci (no automated billing).

**Soalan khusus:**
- [ ] Automated recurring billing sekarang ada? (review lama: tiada)
- [ ] Branded app: apa yang boleh custom? masa deploy?
- [ ] 20 location — enterprise discount?
- [ ] API?
- [ ] Menu yang review kata "hard to find" — tunjuk settings

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page (per location + branded app)
- [ ] Location management screen
- [ ] Class list + schedule + capacity
- [ ] Attendance UI
- [ ] Billing screen (manual vs auto)
- [ ] Parent portal + branded app demo
- [ ] Staff/instructor management
- [ ] Reports
- [ ] Settings menu tree (untuk belajar apa yang confusing)

**Perangkap / red flags (dari nota):** "NO AUTOMATED BILLING"; menu susah cari; customisation perlu developer. USD, US-payments.

---

### 4.3 Pike13 — US (franchise)

**Website & demo route:** https://www.pike13.com — pricing https://www.pike13.com/pricing-and-plans, franchise https://www.pike13.com/franchise-solutions. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** US (Seattle). Activity business & **franchise network** (swim, music, coding, martial arts). Essential US$139 / Advanced US$195 / Premium US$249 sebulan (kontrak 12 bulan); Enterprise untuk franchise; 7-day trial. Claim pelanggan "world's #1 music education franchise", "largest international swim school franchise".

**Whole system in 5 lines:**
1. Modul: scheduling, enrolment, billing (auto-charge), staff time tracking, **payroll**, reporting (**Looker custom reporting**), **API**.
2. Franchise: centralised reporting & billing semua site.
3. Flow: enrol → schedule → check-in → auto-bill → payroll.
4. Review: quick learning curve, support bagus; tapi "buggy", billing auto-charge tanpa invoice, hidden charges, reporting lag.
5. Sesetengah pengguna guna sebab dipaksa franchisor.

**Kenapa demo ni penting:** Ni satu-satunya yang position sebagai **franchise stack** dengan payroll + time tracking + API. Kalau kau nak franchise-kan 20 branch, tengok apa yang franchisor perlukan: royalty report, centralised billing, staff clock-in.

**Soalan khusus:**
- [ ] Franchise: royalty / fee reporting ada? (nota: tak verify)
- [ ] Payroll: kira ikut class taught / jam?
- [ ] Looker reporting — tunjuk contoh dashboard franchise
- [ ] Auto-charge vs invoice-first — boleh pilih?
- [ ] API scope
- [ ] Hidden charges yang review sebut — tunjuk family billing view

**Feature yang WAJIB screenshot / screen record:**
- [ ] Franchise / multi-site dashboard
- [ ] Centralised billing screen
- [ ] Staff time clock + payroll report
- [ ] Looker custom report contoh
- [ ] Schedule + check-in UI
- [ ] Family account billing view
- [ ] Client app / portal
- [ ] API docs page
- [ ] Pricing page

**Perangkap / red flags (dari nota):** Buggy & inconsistent; auto-bill tanpa invoice; charges tersembunyi; settings di tempat pelik; reporting lag; kontrak 12 bulan.

---

### 4.4 TutorCruncher — UK

**Website & demo route:** https://tutorcruncher.com — pricing https://tutorcruncher.com/pricing/us, API https://tutorcruncher.com/api/, branches help https://help.tutorcruncher.com/en/articles/8229034-branches. 2-week trial — sign up sendiri. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** UK (London). Tutoring agency, multi-branch, international. Harga revenue-share: Pay-as-you-go US$30/bln + 1% revenue; Startup 0.65%; Enterprise custom; **+US$50/branch/bln**; priority support US$120/bln; extra untuk custom domain, international card.

**Whole system in 5 lines:**
1. Data model: Client → Recipient (student) → Service → Appointment → Invoice → Payment + Contractor (tutor) payout.
2. **Branches**: reporting, user, tax setting, billing flow berasingan — boleh lain negara.
3. **Tutor payroll**: per student rate, revenue share, auto payout.
4. **API**: REST, 100 req/min, webhook 150+ event, OpenAPI + Postman; integrasi Zapier, GoCardless, Xero, QuickBooks, Lessonspace.
5. Email + SMS, **tiada WhatsApp**.

**Kenapa demo ni penting:** Data model & API paling matang dalam segment — ini schema yang kau boleh pinjam. Branch model dengan tax/billing berasingan relevan kalau kau ada entiti berbeza per negeri. Tutor payout model pun rujukan.

**Soalan khusus:**
- [ ] Tunjuk branch setup + laporan per branch
- [ ] Tutor pay rules: per lesson / per student / % — tunjuk config & payout run
- [ ] Webhook event list + contoh payload
- [ ] Kalender sync (review: masalah)
- [ ] Total kos sebenar untuk 20 branch (1% + $50×20)
- [ ] Support timezone Asia?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Branch settings & branch switcher
- [ ] Client / Recipient / Contractor record
- [ ] Service & Appointment (lesson) creation
- [ ] Invoice generation + payment
- [ ] Contractor payout / payroll screen
- [ ] Dashboard (review: "cluttered" — tengok kenapa)
- [ ] API docs + webhook settings
- [ ] Xero/QuickBooks integration setup
- [ ] Pricing page

**Perangkap / red flags (dari nota):** Learning curve steep; navigation clunky; dashboard makin cluttered; calendar sync issue; support lambat lintas timezone; kos creep (branch, domain, card); no WhatsApp; % of revenue mahal pada yuran MY.

---

### 4.5 Teachworks — Canada

**Website & demo route:** https://www.teachworks.com — API https://teachworks.com/addons/api, pricing help https://teachworks.zendesk.com/hc/en-us/articles/360004915113-Teachworks-Pricing. 3-week trial — sign up sendiri. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** Canada (unverified). Multi-tutor agency, test-prep, learning centre. Harga base + per student-lesson: Starter US$16.49 + US$0.32/lesson; Growth US$47.99 + US$0.189; Premium US$187.99 + US$0.065. API (65+ endpoint) Growth/Premium.

**Whole system in 5 lines:**
1. Modul: scheduling (drag-drop calendar, colour-coded, conflict detection), group lesson, attendance, invoicing, tutor payroll, booking plugin website.
2. Metered per lesson.
3. Review: "invoicing is a breeze", easy; tapi no autopay, no CRM/lead pipeline, no split payment, QuickBooks VAT sync issue, no 2-way Google Calendar.
4. Support response 2 jam (claim) tapi lambat waktu sibuk.
5. Flow: booking → lesson → attendance → invoice (manual charge) → payroll.

**Kenapa demo ni penting:** Kalendar drag-drop dengan conflict detection adalah UI scheduling terbaik dalam segment — screen record penuh. Per-lesson metering pula contoh pricing yang align dengan usage tapi susah forecast.

**Soalan khusus:**
- [ ] Autopay sekarang ada?
- [ ] Lead pipeline / CRM ada?
- [ ] Calendar 2-way sync?
- [ ] Filter multiple instructor sekaligus?
- [ ] Tutor payroll config
- [ ] API endpoint list

**Feature yang WAJIB screenshot / screen record:**
- [ ] Calendar (day/week/month), drag-drop, conflict warning
- [ ] Group lesson + attendance
- [ ] Invoice creation & send
- [ ] Payroll / tutor wage report
- [ ] Booking plugin (client-facing)
- [ ] Student / family profile
- [ ] Reports
- [ ] API/Postman docs
- [ ] Pricing page

**Perangkap / red flags (dari nota):** No autopay; no CRM; no split payment; Google Calendar 1-way; harga "expensive"; onboarding rely on docs.

---

### 4.6 Brightwheel — US (childcare)

**Website & demo route:** https://mybrightwheel.com — features https://mybrightwheel.com/features/, billing https://mybrightwheel.com/features/billing/, kiosk help https://help.mybrightwheel.com/en/articles/1329844-set-up-a-check-in-kiosk, QR https://help.mybrightwheel.com/en/articles/8713191-set-up-a-qr-code-for-check-in-quick-scan. Free basic tier — sign up sendiri. Premium: "speak to a specialist".

**HQ / segment / pelanggan / harga:** US (SF). Childcare/preschool. Raised US$88.8m; claim 4.9★ dari 160,000+ review (vendor); Capterra 4.7 (3,254). Harga Premium quote-only; anggaran pihak ketiga US$2–4/child/bln, processing 2.9% + US$0.30 (**unverified**).

**Whole system in 5 lines:**
1. Parent app: real-time **activity feed** (photo/video/meal/nap), **daily report** automatik, messaging.
2. **Kiosk check-in**: 4-digit code tanpa smartphone; **Quick Scan QR**; digital signature + health screen; staff clock-in kiosk sama.
3. Billing: autopay, auto reminder & receipt, subsidy, payroll, expense.
4. Claim 90% preschool nampak on-time payment naik dengan autopay.
5. Complaint: pay date hanya 1 / 15; tak boleh bill advance; parent phone support terhad; cancel kena call; kos ikut enrolment.

**Kenapa demo ni penting:** Ni spec parent app yang jadi standard dunia: feed + report + check-in + autopay. Untuk pusat bacaan: tukar "daily report" jadi "progress note per session". Screen record kiosk & QR check-in flow penuh.

**Soalan khusus:**
- [ ] Harga per child sebenar (bertulis)
- [ ] Autopay: kad je atau ACH? (MY equivalent: FPX e-mandate)
- [ ] Pay date flexible sekarang?
- [ ] Multi-site owner dashboard?
- [ ] Data export
- [ ] Cancel process

**Feature yang WAJIB screenshot / screen record:**
- [ ] Kiosk check-in (PIN) + Quick Scan QR + signature + health screen
- [ ] Staff clock-in
- [ ] Teacher app: post activity, photo, daily report
- [ ] Parent app: feed, daily report, message, invoice, autopay setup
- [ ] Admin billing: plan, invoice run, reminder, receipt
- [ ] Attendance report
- [ ] Multi-site view
- [ ] Reports & export
- [ ] Settings / permissions

**Perangkap / red flags (dari nota):** Billing rigid (dates), reporting limitations, glitches, cancel friction, cost scales with enrolment, US-only payments.

---

### 4.7 Famly — Denmark/UK (childcare, multi-site)

**Website & demo route:** https://www.famly.co — pricing https://www.famly.co/us/pricing, multi-site https://www.famly.co/us/blog/famly-for-multisiters-childcare-centers, org reports https://help.famly.co/en/articles/5316863-organisational-level-reports, occupancy https://help.famly.co/en/articles/4912131-occupancy-and-future-availability. Free version/trial — sign up sendiri. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** Copenhagen & London. Childcare. Claim 7,000+ centre. Harga **Starter dari US$49/bln**, tier ikut child count, semua feature included.

**Whole system in 5 lines:**
1. Parent app: daily update, photo, messaging **auto-translate 130+ bahasa**, **"Sidekick" AI** semak ejaan/tone.
2. **Organizer Manager**: live cross-site analytics — enrolment, occupancy, revenue, outstanding balance, engagement; drill ke site.
3. **Org-level Occupancy Report** per site + total; CSV export booking semua site 2 tahun.
4. Role-based view: teacher / director / owner.
5. Check-in/out, billing & invoicing.

**Kenapa demo ni penting:** Occupancy / future availability report merentas site adalah exactly "utilisasi slot kelas" yang chain kau perlukan. Auto-translate + AI writing assist untuk mesej parent pun idea terus untuk BM/EN/中文.

**Soalan khusus:**
- [ ] Tunjuk Organizer Manager penuh
- [ ] Occupancy & future availability — logik kira
- [ ] Sidekick AI — apa dia buat, boleh off?
- [ ] Auto-translate BM ada?
- [ ] Harga 20 site ~2,500 child
- [ ] Session-based (bukan full-day) support?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Organizer Manager dashboard (cross-site)
- [ ] Occupancy report + future availability
- [ ] Site drill-down: invoice & payment
- [ ] CSV export screen
- [ ] Parent app: feed, message dengan translate, Sidekick
- [ ] Check-in/out
- [ ] Billing setup & invoice
- [ ] Role views (teacher vs director vs owner)
- [ ] Pricing page

**Perangkap / red flags (dari nota):** Childcare-specific; USD; no MY payments. (Sedikit complaint dalam nota — bagus.)

---

### 4.8 Playground (ex-Kinderlime) — US (childcare)

**Website & demo route:** https://www.tryplayground.com — GetApp https://www.getapp.com/education-childcare-software/a/playground/. Free version & trial tanpa kad — sign up sendiri. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** US. Childcare. Claim 5,000+ program; Capterra 4.8 (193). Harga custom.

**Whole system in 5 lines:**
1. Modul: billing, registration, communication, attendance, payroll.
2. Review: "beats Brightwheel and Procare… app and desktop terrific".
3. Free tier + trial.
4. Flow: registration → attendance → communication → billing → payroll.
5. Lineage Kinderlime tak disahkan.

**Kenapa demo ni penting:** Challenger yang review kata lebih baik dari Brightwheel — tengok apa UX yang buat orang switch. Cepat, free, tak perlu demo pun.

**Soalan khusus:**
- [ ] Apa beza utama vs Brightwheel (dari mereka sendiri)?
- [ ] Multi-site?
- [ ] Autopay?
- [ ] Harga

**Feature yang WAJIB screenshot / screen record:**
- [ ] Onboarding flow (free signup) — belajar activation UX
- [ ] Admin dashboard
- [ ] Registration form builder
- [ ] Attendance / check-in
- [ ] Messaging
- [ ] Billing & autopay
- [ ] Payroll
- [ ] Parent app
- [ ] Reports

**Perangkap / red flags (dari nota):** Sedikit data; harga tak public; US.

---

### 4.9 Xplor Education — Australia (childcare)

**Website & demo route:** https://www.ourxplor.com — pricing https://www.ourxplor.com/pricing/, Home app https://www.ourxplor.com/home-families/, Playground https://www.ourxplor.com/playground-educator-childcare-software/. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** Melbourne. Childcare (tied to Australian CCS subsidy). Harga quote; bundle.

**Whole system in 5 lines:**
1. Produk split: **Office** (admin), **Playground** (educator app), **Home** (family app), **Xplor Pay**.
2. Home app: "Learning Journey" photo/video, chat, health analytics, **book extra sessions**, subsidy & payment due, Messenger.
3. Kiosk check-in sync attendance timestamp.
4. Integrasi OWNA, Xero.
5. Billing engine CCS-specific (tak portable).

**Kenapa demo ni penting:** Split 3 app (admin / educator / family) + Pay adalah arkitektur produk yang bersih — rujukan untuk kau bahagikan app. "Book extra session" dalam family app = make-up class self-service.

**Soalan khusus:**
- [ ] Tunjuk ketiga-tiga app
- [ ] Book extra session flow (capacity check?)
- [ ] Xplor Pay: recurring debit macam mana
- [ ] Xero sync scope
- [ ] Non-AU customer ada?

**Feature yang WAJIB screenshot / screen record:**
- [ ] Office admin dashboard
- [ ] Playground educator app (observations, attendance)
- [ ] Home family app: Learning Journey, chat, book session, payments
- [ ] Kiosk check-in
- [ ] Xplor Pay setup
- [ ] Xero integration
- [ ] Reports

**Perangkap / red flags (dari nota):** CCS-specific billing; AU only; quote pricing.

---

### 4.10 Classe365 — SIS modular

**Website & demo route:** https://www.classe365.com/pricing — rate card https://docs.classe365.com/en/articles/1921193-classe365-features-and-pricing-rate-card. Free trial — sign up sendiri. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** Global SMB SIS/LMS/CRM. Harga: US$100/bln (1–100 student); US$250 (101–500); US$500 (501–1000); enterprise quote; "dari US$23/student/tahun". **Modular — bayar modul yang perlu (LMS / CRM / SIS)**.

**Whole system in 5 lines:**
1. SIS + LMS + CRM dalam satu, pilih modul.
2. Tier ikut student count.
3. Flow: CRM (lead) → admission → SIS (class, attendance, grade) → fee → LMS.
4. Ada CRM/admission funnel — jarang di vendor MY.
5. Enterprise per-student pricing.

**Kenapa demo ni penting:** Rujukan untuk **lead pipeline / admission CRM** yang bersambung terus ke enrolment — sesuatu yang hampir semua Tier 1 tak ada. Juga model modular pricing.

**Soalan khusus:**
- [ ] CRM: pipeline stages, automation, form embed
- [ ] Admission → enrolment handoff
- [ ] Fee module: recurring, gateway
- [ ] Multi-campus
- [ ] Rate card modul (bertulis)

**Feature yang WAJIB screenshot / screen record:**
- [ ] Module selector / rate card
- [ ] CRM pipeline (kanban) + lead form
- [ ] Admission workflow
- [ ] Student profile + attendance + gradebook
- [ ] Fee & invoice
- [ ] LMS (sekilas)
- [ ] Multi-campus settings
- [ ] Reports

**Perangkap / red flags (dari nota):** Tier ladder bercanggah antara sumber (501–750 vs 501–1000) — verify. Generic school, bukan tuition.

---

### 4.11 Gradelink — US (SMB SIS)

**Website & demo route:** URL rasmi tak ada dalam nota; guna listing Capterra https://www.capterra.com/p/118900/Gradelink-SIS/pricing/ dan GetApp https://www.getapp.com/education-childcare-software/a/gradelink-sis/ untuk pautan ke website, then cari butang Book a Demo.

**HQ / segment / pelanggan / harga:** US. Small private school SIS. Harga public: US$121/bln (≤50 student), US$164 (51–100), US$210 (101–150). Review: value bagus, UI "outdated and old".

**Whole system in 5 lines:**
1. SIS standard: enrolment, attendance, gradebook, report card, parent portal, billing.
2. Harga transparent ikut student.
3. Small school focus.
4. Flow: enrol → class → attendance → grade → report card → bill.
5. UI lama.

**Kenapa demo ni penting:** Contoh pricing transparent per-student untuk sekolah kecil (≈RM5–12/student/bulan) — anchor harga untuk segment tadika/sekolah swasta kecil. Report card builder pun rujukan untuk progress report.

**Soalan khusus:**
- [ ] Report card template builder
- [ ] Billing & payment gateway
- [ ] Multi-school
- [ ] Harga >150 student

**Feature yang WAJIB screenshot / screen record:**
- [ ] Pricing page
- [ ] Gradebook + report card builder + PDF
- [ ] Attendance
- [ ] Parent portal
- [ ] Billing
- [ ] Admin dashboard (nota: "outdated" — tengok apa yang buat rasa lama)

**Perangkap / red flags (dari nota):** UI outdated; US; school bukan tuition.

---

### 4.12 Fedena (Foradian) — India (school ERP, open-source roots)

**Website & demo route:** https://fedena.com/pricing-and-plans — community edition GitHub https://github.com/projectfedena/fedena. Cari butang Book a Demo di homepage.

**HQ / segment / pelanggan / harga:** India. School ERP. Free community edition (Rails lama, Apache-2.0) + Pro annual licence (harga tak diambil).

**Whole system in 5 lines:**
1. Modul: administration, student lifecycle, communication, fee, exam, timetable.
2. Foradian pernah fork **FET** untuk auto-timetable headless — bukti SIS komersial embed engine open source.
3. Community edition boleh install sendiri untuk baca data model.
4. Flow: admission → class → attendance → exam → fee.
5. Rails 2-era — jangan reuse code, tengok feature list je.

**Kenapa demo ni penting:** Benchmark "checklist feature" pasaran India/SEA (hostel, transport, library, ID card, SMS) — untuk tahu apa yang pelanggan sekolah swasta akan tanya walaupun kau tak buat. Auto-timetable via FET adalah idea teknikal.

**Soalan khusus:**
- [ ] Beza community vs Pro
- [ ] Auto-timetable masih guna FET?
- [ ] Multi-school
- [ ] Harga Pro (annual)

**Feature yang WAJIB screenshot / screen record:**
- [ ] Module list (Pro)
- [ ] Timetable generator UI
- [ ] Fee module
- [ ] Exam / report card
- [ ] Communication (SMS/email)
- [ ] Multi-school admin
- [ ] Pricing page

**Perangkap / red flags (dari nota):** Codebase lama; India-centric; harga tak jelas.

---

## 5. Tier 3 — self-serve reference (sign up, klik, screenshot)

| Tool | URL (dari nota) | Free tier? | Pattern yang nak curi | Screen wajib screenshot | Status |
|---|---|---|---|---|---|
| Respond.io (KL HQ) | https://respond.io/about ; https://respond.io/pricing ; help https://respond.io/help/quick-start/responding-to-messages | 7-day trial (Starter US$79/bln annual) | Shared WhatsApp inbox; **Lifecycle stage** pada contact; **contact merge**; AI Assist (draft reply, human approve); AI Agent tukar stage | Inbox, contact record + lifecycle, merge dialog, workflow builder, AI Assist suggestion, template message | - [ ] |
| SleekFlow | https://sleekflow.io/blog/whatsapp-business-price ; CTWA help https://help.sleekflow.io/en_US/whatsapp/understanding-click-to-whatsapp-ads-ctwa-and-the-72-hour-free-window | Trial (Pro AI ~RM469/bln) | Multi-channel inbox (WA+IG+FB); broadcast; CTWA 72-jam window | Inbox, broadcast, automation, channel settings | - [ ] |
| Wati | https://www.wati.io/en/blog/set-up-click-to-whatsapp-ads/ ; https://www.wati.io/wati-vs-sleekflow/ | Trial (Growth US$29, 3 user cap; MY reseller RM299) | WhatsApp-first inbox; template manager; no-code bot | Template approval flow, broadcast, chatbot builder | - [ ] |
| Kommo | https://www.kommo.com/whatsapp/ ; https://developers.kommo.com/docs/salesbot-dp | Trial (US$15/user) | **Digital Pipeline**: Salesbot gerakkan card antara stage | Pipeline kanban, Salesbot builder | - [ ] |
| Trello | https://trello.com/guide/automate-anything ; https://trello.com/guide/enterprise/advanced-features | Free (250 automation/bln) | Butler: calendar-recurring card, card template, checklist template, button automation | Board, Butler rules, calendar command, card template | - [ ] |
| ClickUp | https://clickup.com/brain/pricing | Free Forever | Task hierarchy; ClickUp Brain (AI summary, add-on US$9/user) | List/board/calendar view, Brain summary | - [ ] |
| Notion | https://www.notion.com/pricing | Free | Database + views; Notion AI bundled tier Business | Database views, AI summary | - [ ] |
| Airtable | https://www.airtable.com/templates/interfaces ; https://www.airtable.com/templates/sales-and-crm | Free | **Interface Designer**: satu base, interface berbeza ikut role (teacher / branch manager / HQ / parent); CRM kanban | Base, interface builder, kanban pipeline | - [ ] |
| Fresha | https://www.fresha.com/for-business/features/scheduling ; https://www.fresha.com/help-center/knowledge-base/payments/615-set-up-payment-policies | Tiada free plan lagi (trial) | **Intelligent Waitlist** notify order (first in line / high-value / offer-to-all); deposit ditahan bila no-show | Waitlist settings, deposit/cancellation policy, reminder settings | - [ ] |
| Mindbody | https://www.mindbodyonline.com/business/scheduling | Trial (US$129–349) | **Auto no-show 10 minit** lepas start; waitlist stop promote dalam late-cancel window | Class schedule, waitlist, no-show policy, late-cancel window | - [ ] |
| Glofox | https://www.glofox.com/comparison/glofox-vs-mindbody/ ; https://www.glofox.com/blog/mindbody-alternative/ | Quote (~US$99–350) | Class pack, drop-in, capacity, intro offer, no-show fee semua native | Membership/pack setup, class capacity, waitlist | - [ ] |
| Zenoti | https://www.zenoti.com/platform/membership-and-packages ; https://www.zenoti.com/thecheckin/membership-management-software-guide | Quote (enterprise) | **Service-credit membership dengan rollover/expiry per plan** → make-up class credit; no-show → win-back campaign auto | Membership model config, credit rollover rule, automation | - [ ] |
| Calendly | (URL tak ada dalam nota — cari sendiri; ref https://koalendar.com/blog/calendly-vs-acuity) | Free | Booking page, availability, reminder — untuk trial class booking | Event type, booking page, reminder | - [ ] |
| Acuity / Setmore | https://prycedigital.com/blog/calendly-vs-acuity-vs-setmore-vs-custom-booking-system | Setmore free (200 booking/bln) | Intake form, package, group class | Intake form, class booking | - [ ] |
| ClassDojo | https://www.classdojo.com/plus/ ; https://help.classdojo.com/hc/en-us/articles/360018137732-ClassDojo-Plus-FAQ | Free (school tak bayar) | Free core comms; **auto-translate 190+ bahasa**; Portfolio; Class Story; **family-paid Plus** US$15.49/bln (progress report, AI homework help) | Class Story, message + translate, Portfolio, Points, Plus paywall | - [ ] |
| Brightwheel (free tier) | https://mybrightwheel.com/features/ | Free basic | Lihat 4.6 | — | - [ ] |
| Procare | https://www.procaresoftware.com/ | Quote | Desktop-first incumbent; review support teruk — contoh apa yang JANGAN | Skip demo, baca review je | - [ ] |
| Gibbon (OSS) | https://github.com/GibbonEdu/core ; https://gibbonedu.org/download/ | Free (GPL-3) | Timetable display, Student Alerts, behaviour, library, petty cash, 29 bahasa | Install / demo: timetable, alerts, role switching | - [ ] |
| RosarioSIS (OSS) | https://github.com/francoisjacquet/rosariosis ; https://www.rosariosis.org/add-ons/ | Free (GPL-2) + premium add-on | Attendance, scheduling, gradebook/report card, Student Billing | Student Billing module, report card | - [ ] |
| openSIS-Classic (OSS) | https://github.com/OS4ED/openSIS-Classic | Free (GPL) | Single vs **multi-institution** dalam satu install; progress report; bulk import | Multi-school setup, bulk import | - [ ] |
| Unifiedtransform (Laravel, OSS) | https://github.com/changeweb/Unifiedtransform | Free (GPL-3) | Domain model session → semester → class → section → course; role dashboards | Baca schema/migrations, screenshot admin/teacher/student dashboard | - [ ] |
| skuul (Laravel 9, MIT) | https://github.com/yungifez/skuul | Free (MIT — boleh reuse) | **Multi-school** (super admin create schools); timetable; admission | Super admin school switcher, timetable | - [ ] |
| academico (Laravel + Filament) | https://github.com/academico-sis/academico | Free (licence tak jelas) | Filament admin pattern; course/enrolment/resource scheduling | Admin UI | - [ ] |
| FET (timetabling, AGPL) | https://lalescu.ro/liviu/fet/ ; mirror https://github.com/nico-alvz/fet-mirror | Free | Auto-timetable dari constraint; ada command-line mode | Constraint list, generate, output | - [ ] |
| Frappe Education / OpenEduCat (OSS ERP) | https://github.com/frappe/education ; https://github.com/openeducat/openeducat_erp | Free | Fee schedule + student portal online payment (Frappe); admission/fees/timetable (Odoo) | Fee structure, payment schedule | - [ ] |
| CodeCanyon Smart School / Ekattor 8 | https://codecanyon.net/item/smart-school-school-management-system/19426018 ; https://codecanyon.net/item/ekattor-8-school-management-system/39611172 | Bayar (~US$49–70) | Checklist feature yang pembeli MY/ID/BD jangka (hostel, transport, library, ID card, SMS, multi-school SaaS) | Live preview demo — screenshot menu tree | - [ ] |

---

## 6. Master tracker

Tick status ikut urutan: emailed → demo booked → demo done → recording saved. Isi tarikh & nota ringkas (contact person, harga quote, next step).

| Vendor | Tier | Website | Status | Tarikh | Nota |
|---|---|---|---|---|---|
| AOne | 1 | https://aone.com.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| LittleLives | 1 | https://www.littlelives.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| REMMU | 1 | https://remmu.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| SimTrain | 1 | https://simtrainsystem.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| ClassFlow.my | 1 | https://classflow.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Skooly | 1 | https://getskooly.com/tuition-centre-software-malaysia/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Anak2U | 1 | https://anak2u.com.my/about/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Oodlins | 1 | https://oodlins.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Classcard | 1 | https://www.classcardapp.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Taidii | 1 | https://www.taidii.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| ASIS | 1 | https://www.asis.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Synorex Tuition | 1 | https://synorex.group/tuition | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Yuran.my | 1 | https://yuran.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Tuis.my | 1 | https://www.tuis.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| illumine | 1 | https://try.illumine.app/mly | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Mekar | 1 | https://www.mekar.com.my/my/solutions/tadika/pengurusan-kehadiran/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| BooknGo | 1 | https://www.bookngo.app/sg/about-us/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| iEduCentre | 1B | https://www.ieducentre.com/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| TadikaPro | 1B | https://tadikapro.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| iCRM | 1B | https://www.icrm.com.my/ | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Jackrabbit Class | 2 | https://www.jackrabbitclass.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| iClassPro | 2 | https://www.iclasspro.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Pike13 | 2 | https://www.pike13.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| TutorCruncher | 2 | https://tutorcruncher.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Teachworks | 2 | https://www.teachworks.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Brightwheel | 2 | https://mybrightwheel.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Famly | 2 | https://www.famly.co | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Playground | 2 | https://www.tryplayground.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Xplor | 2 | https://www.ourxplor.com | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Classe365 | 2 | https://www.classe365.com/pricing | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Gradelink | 2 | (via https://www.capterra.com/p/118900/Gradelink-SIS/pricing/) | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Fedena | 2 | https://fedena.com/pricing-and-plans | - [ ] emailed - [ ] demo booked - [ ] demo done - [ ] recording saved | | |
| Respond.io | 3 | https://respond.io/pricing | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| SleekFlow | 3 | https://sleekflow.io/blog/whatsapp-business-price | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Wati | 3 | https://www.wati.io/wati-vs-sleekflow/ | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Trello | 3 | https://trello.com/guide/automate-anything | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| ClickUp | 3 | https://clickup.com/brain/pricing | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Notion | 3 | https://www.notion.com/pricing | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Airtable | 3 | https://www.airtable.com/templates/interfaces | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Fresha | 3 | https://www.fresha.com/for-business/features/scheduling | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Mindbody | 3 | https://www.mindbodyonline.com/business/scheduling | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Glofox | 3 | https://www.glofox.com/comparison/glofox-vs-mindbody/ | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Zenoti | 3 | https://www.zenoti.com/platform/membership-and-packages | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Calendly | 3 | (cari sendiri) | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| ClassDojo | 3 | https://www.classdojo.com/plus/ | - [ ] signed up - [ ] explored - [ ] screenshots saved | | |
| Gibbon | 3 | https://github.com/GibbonEdu/core | - [ ] installed - [ ] explored - [ ] screenshots saved | | |
| RosarioSIS | 3 | https://github.com/francoisjacquet/rosariosis | - [ ] installed - [ ] explored - [ ] screenshots saved | | |
| openSIS | 3 | https://github.com/OS4ED/openSIS-Classic | - [ ] installed - [ ] explored - [ ] screenshots saved | | |
| Unifiedtransform | 3 | https://github.com/changeweb/Unifiedtransform | - [ ] installed - [ ] explored - [ ] screenshots saved | | |
| skuul | 3 | https://github.com/yungifez/skuul | - [ ] installed - [ ] explored - [ ] screenshots saved | | |
| FET | 3 | https://lalescu.ro/liviu/fet/ | - [ ] installed - [ ] explored - [ ] screenshots saved | | |

**Cadangan urutan (6 minggu):**
- Minggu 1: hantar SEMUA email/WhatsApp Tier 1 sekaligus (vendor MY lambat respond). Sambil tunggu, buat Tier 3 (Respond.io, Trello, Airtable, Fresha, ClassDojo) dan sign up free plan REMMU / Tuis.my / Mekar / Skooly pilot.
- Minggu 2–3: demo Tier 1 (target 2 sehari max — lebih dari tu kau tak ingat apa-apa).
- Minggu 4: demo Tier 2 (Jackrabbit, TutorCruncher, Brightwheel, Famly dulu — paling banyak idea).
- Minggu 5: Tier 2 baki + open source install.
- Minggu 6: isi comparison matrix, tulis "apa yang kita curi / apa yang kita elak".

---

## 7. Selepas semua demo — comparison matrix

Isi cell dengan ✔ (ada, tengok sendiri), ✘ (tiada / vendor confirm tiada), ◐ (partial / add-on berbayar / "coming soon"), ? (tak tanya). Tambah column ikut vendor yang kau demo; copy table ni ke Google Sheet lebih senang.

| # | Feature | AOne | REMMU | SimTrain | ClassFlow | Skooly | Synorex | Classcard | Oodlins | LittleLives | Anak2U | Taidii | ASIS | Yuran.my | Tuis.my | illumine | Mekar | BooknGo | Jackrabbit | TutorCruncher | Brightwheel | Famly |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | Multi-branch dalam satu login + HQ dashboard | | | | | | | | | | | | | | | | | | | | | |
| 2 | Branch-scoped role & permission | | | | | | | | | | | | | | | | | | | | | |
| 3 | Class allocation (student ↔ slot ↔ teacher) + capacity per class | | | | | | | | | | | | | | | | | | | | | |
| 4 | Timetable / schedule view (week, per teacher, per room) | | | | | | | | | | | | | | | | | | | | | |
| 5 | Attendance marking (teacher app / kiosk / QR / face) | | | | | | | | | | | | | | | | | | | | | |
| 6 | Auto no-show + parent notification | | | | | | | | | | | | | | | | | | | | | |
| 7 | Make-up / replacement class credit (dengan expiry) | | | | | | | | | | | | | | | | | | | | | |
| 8 | Waitlist (auto-fill bila slot kosong) | | | | | | | | | | | | | | | | | | | | | |
| 9 | Fee plan + invoice auto bulanan | | | | | | | | | | | | | | | | | | | | | |
| 10 | FPX / DuitNow QR / e-wallet (gateway & fee) | | | | | | | | | | | | | | | | | | | | | |
| 11 | Recurring collection (FPX e-mandate / card token / autopay) | | | | | | | | | | | | | | | | | | | | | |
| 12 | Cash / manual transfer reconciliation + partial payment + sibling discount | | | | | | | | | | | | | | | | | | | | | |
| 13 | Arrears / aging report + auto reminder | | | | | | | | | | | | | | | | | | | | | |
| 14 | LHDN e-Invoice (consolidated bulanan + parent request individual) | | | | | | | | | | | | | | | | | | | | | |
| 15 | WhatsApp reminder (official API? template? siapa bayar) | | | | | | | | | | | | | | | | | | | | | |
| 16 | WhatsApp two-way inbox / lifecycle stage | | | | | | | | | | | | | | | | | | | | | |
| 17 | Parent app (iOS/Android/Huawei) + web login | | | | | | | | | | | | | | | | | | | | | |
| 18 | Teacher app (attendance + progress note) | | | | | | | | | | | | | | | | | | | | | |
| 19 | Progress report / portfolio per student (PDF, BM/EN) | | | | | | | | | | | | | | | | | | | | | |
| 20 | Lead pipeline / enquiry CRM → trial → enrol | | | | | | | | | | | | | | | | | | | | | |
| 21 | Online enrolment / booking form (parent self-service) | | | | | | | | | | | | | | | | | | | | | |
| 22 | Reports / BI dashboard cross-branch (enrolment, attendance rate, revenue, outstanding, utilisation) | | | | | | | | | | | | | | | | | | | | | |
| 23 | Teacher payroll per class / per student | | | | | | | | | | | | | | | | | | | | | |
| 24 | Data export penuh (CSV/Excel) + accounting sync (Bukku/SQL/AutoCount/Xero) | | | | | | | | | | | | | | | | | | | | | |
| 25 | Public API / webhook / Zapier | | | | | | | | | | | | | | | | | | | | | |
| 26 | BM UI (admin + parent) + auto-translate | | | | | | | | | | | | | | | | | | | | | |
| 27 | PDPA: data residency, DPO, breach process, export on exit | | | | | | | | | | | | | | | | | | | | | |
| 28 | Pricing model (per student / per branch / flat / % revenue) + RM/bulan untuk 20 branch | | | | | | | | | | | | | | | | | | | | | |
| 29 | Contract lock-in & onboarding time | | | | | | | | | | | | | | | | | | | | | |
| 30 | Support channel & jam (MY) | | | | | | | | | | | | | | | | | | | | | |

**Lepas isi matrix, jawab 3 soalan ni dalam satu page:**
- [ ] Feature mana yang SEMUA Tier 1 ada (= table stakes, kau wajib ada hari pertama)?
- [ ] Feature mana yang TIADA di semua Tier 1 tapi ada di Tier 2/3 (= peluang beza: cross-branch BI, make-up credit, waitlist, lead pipeline, teacher payroll, auto-translate)?
- [ ] Red flag mana yang berulang (harga per student, parent mobile-only, WhatsApp manual, e-invoice add-on, app bug notifikasi, support WhatsApp group je) — ni senarai "jangan buat".
