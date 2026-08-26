# Jadual Kelas Student Auto

Panel admin untuk menempatkan pelajar ke dalam kelas secara automatik — menggantikan
proses manual Google Sheets.

Admin membina jadual mingguan (kelas + guru + bilik + slot). **Auto Assign** kemudian
mengagihkan pelajar ke dalam kelas yang ada kekosongan mengikut peraturan yang boleh
dihidup/matikan, menunjukkan **cadangan berserta sebab dalam Bahasa Malaysia**, dan
hanya menyimpannya selepas admin mengesahkan.

---

## Susunan teknologi

Laravel 13 · PHP 8.4 · PostgreSQL 17 · Redis · Inertia 3 · React 19 · TypeScript ·
Tailwind 4 · shadcn/ui · Motion · Recharts · Pest · Pint · Larastan (level 7)

---

## Bermula (local)

Mesin pembangunan **tidak perlu PHP atau Composer** — semuanya berjalan dalam Docker.

```bash
cp .env.example .env          # isi kredensial kemudian melalui UI Integrasi
make up                       # bina & naikkan app, nginx, postgres, redis
make art c="key:generate"
make fresh                    # migrasi + data contoh (236 pelajar, 18 kelas)
make npm c="run build"
```

Buka **http://localhost:8080** dan log masuk:

```
admin@jadual.test / password
```

Untuk pembangunan frontend dengan hot reload: `docker compose up vite`.

### Arahan berguna

| Arahan | Kegunaan |
|---|---|
| `make test` | Jalankan semua ujian Pest |
| `make pint` | Format kod PHP |
| `make stan` | Analisis statik |
| `make sh` | Shell dalam container app |
| `make art c="assign:run"` | Jalankan auto assign dari CLI (tambah `--commit` untuk sahkan) |

---

## Bagaimana enjin penempatan berfungsi

Solver ialah **fungsi tulen ke atas snapshot dalam memori** — tiada akses pangkalan
data di dalam gelung. 236 pelajar selesai dalam **kira-kira 20 ms**, jadi ia berjalan
terus dalam permintaan HTTP tanpa queue.

**Greedy "paling terkekang dahulu" + pembaikan carian setempat.** CSP/ILP solver
ditolak: tiada solver PHP yang matang, dan lebih penting lagi ia memulangkan vektor
optimum **tanpa naratif**. Keperluan di sini ialah admin membaca satu ayat Melayu dan
angguk. Tiada siapa dapat bezakan penyelesaian 0.94-optimum daripada 1.00-optimum;
semua orang perasan penyelesaian yang tidak boleh dijelaskan.

### Peraturan

| Jenis | Sifat |
|---|---|
| Padanan Tahun | keras, **sentiasa aktif** |
| Kapasiti Bilik | keras, **sentiasa aktif** |
| Had Maksimum Kelas | keras, boleh dilaraskan |
| Kelas Adik Beradik | lembut, dwiarah (asing / bersama) |
| Pelajar Bermasalah | lembut (guru tegas) |
| Seimbangkan Jantina | lembut |

Kapasiti berkesan = `min(had peraturan, kapasiti bilik)`. Mematikan "Had Maksimum
Kelas" **tidak** membenarkan 40 pelajar masuk bilik 25 tempat — `RoomCapacityRule`
berasingan dan tidak boleh dimatikan.

Menambah jenis peraturan baharu ialah **empat langkah, tiada migrasi**: laksana
antara muka → daftar dalam `config/assignment.php` → tambah teks Melayu dalam
`lang/ms/assignment.php` → tambah borang config React.

### Borang & pengesahan

CRUD penuh untuk **Pelajar, Guru, Bilik dan Kelas**. Mesej pengesahan dalam Bahasa
Malaysia (`lang/ms/validation.php`).

Borang Kelas menguruskan **waktu pertemuan mingguan** — satu kelas boleh bertemu
beberapa kali. Waktu REHAT tidak boleh dipilih. Pertembungan guru atau bilik ditolak
oleh pangkalan data dan dipaparkan sebagai ayat yang menamakan konflik sebenar,
contohnya:

> *Bilik 1 sudah digunakan oleh kelas 4 ALPHA pada Isnin 8:00 AM - 9:00 AM.*

Pemadaman Guru, Bilik dan Kelas adalah *soft delete*, jadi kunci asing tidak
membantah — setiap satu ada semakan eksplisit yang menghalang pembuangan rekod yang
masih digunakan.

### Adik-beradik

Hubungan adik-beradik datang daripada `students.family_id`, **bukan** daripada penjaga
yang dikongsi. "Penjaga" dalam borang selalunya pemandu van atau ejen yang didaftarkan
untuk beberapa kanak-kanak tidak berkaitan — mengambilnya sebagai hubungan keluarga
akan menghasilkan adik-beradik palsu. `families.sibling_policy` membolehkan keluarga
tertentu **meminta anak-anak mereka sekelas**, mengatasi peraturan umum.

### Determinisme

Larian semula **tidak** mengocak semula sekolah:

- susunan menyeluruh di setiap tempat, `student_id` sebagai penambat — tiada `rand()`
- **bonus incumbent** kecil menahan pelajar di kelas asal apabila skor seri
- `input_hash` (sha256 snapshot) — hash sama ⇒ output sama, dan itu boleh diuji

### Pratonton → sahkan

`POST /auto-assign` menyelesaikan dalam memori dan menyimpan **draf** sahaja; sifar
tulisan pada `enrolments`. Semasa commit, `input_hash` disemak semula: jika admin lain
menambah pelajar atau mengubah berat peraturan sementara itu, draf **ditolak dengan
mesej jelas** dan bukan dilaksanakan atas andaian basi.

### Penempatan manual

Panel senarai kelas membenarkan admin **tambah, pindah, keluarkan dan semat** pelajar
satu per satu. Semuanya melalui `EnrolmentManager` yang sama seperti enjin, jadi:

- had tahun dan kapasiti dikuatkuasakan pada laluan manual juga — mustahil untuk
  membina senarai yang enjin anggap tidak sah;
- pemindahan menutup baris lama dan membuka baris baharu, tidak pernah `UPDATE`;
- **semat kekal melintasi pemindahan** — kalau tidak, memindahkan pelajar akan
  senyap-senyap menyerahkannya semula kepada enjin, iaitu bertentangan dengan tujuan
  semat.

### Buat asal (undo)

Larian yang telah disahkan boleh dibuat asal: enrolan yang diciptanya dibuang dan
enrolan yang ditutupnya dibuka semula.

Dua peraturan sengaja ketat:

- **Hanya larian terakhir yang disahkan** boleh dibuat asal. Membuat asal larian lama
  bermakna merungkai segala yang bertindan di atasnya, dan soalan "penempatan mana
  patut kembali?" tiada jawapan yang jujur.
- **Perubahan manual selepas commit dibiarkan.** Keputusan sedar admin mengatasi
  rollback automatik.

### Integriti pangkalan data

Kunci advisory memberikan mesej ralat yang baik, tetapi **sempadan ketepatan sebenar
ialah indeks unik separa PostgreSQL**:

```sql
UNIQUE (enrolments.session_id, student_id) WHERE status = 'active'
UNIQUE (class_meetings.session_id, day, time_slot_id, room_id)    WHERE room_id IS NOT NULL
UNIQUE (class_meetings.session_id, day, time_slot_id, teacher_id) WHERE teacher_id IS NOT NULL
```

Pangkalan data secara fizikal tidak boleh mendaftarkan pelajar dua kali atau
membenarkan pertembungan bilik/guru, walaupun setiap kunci gagal.

Pindah kelas **bukan** `UPDATE class_id` — baris lama ditutup dan baris baharu
dimasukkan, jadi "kenapa Ali dalam 5 BETA" boleh dijawab selama-lamanya.

---

## Integrasi

Semua kredensial diisi melalui **Tetapan → Integrasi** dalam UI. Ia disulitkan dalam
pangkalan data dan **mengatasi `.env`** semasa runtime, jadi kunci boleh ditukar tanpa
deploy semula. Setiap kad ada butang **Uji Sambungan** yang benar-benar menghubungi
perkhidmatan.

- **Google Sign-In** — jemputan sahaja. Akaun Google yang tiada dalam senarai
  Pengguna **ditolak, bukan didaftarkan**. Log masuk Google membuktikan identiti,
  bukan kebenaran, dan sistem ini menyimpan rekod kanak-kanak.
- **Sendscape** — transport mel tersuai memanggil `POST /v1/emails`.
  Tetapkan `MAIL_MAILER=sendscape`.
- **Cloudflare R2** — disk `s3` dengan endpoint R2. Ujian menulis, membaca dan
  memadam fail sebenar.

---

## PDPA & keselamatan

- Medan PII (`national_id` pelajar & penjaga) guna cast `encrypted` — **tidak boleh
  dicari**, dan itu diterima.
- Log aktiviti guna **senarai benarkan**, tidak pernah `logAll()`. `national_id`,
  tarikh lahir, telefon, emel dan catatan tingkah laku **tidak pernah dilog** —
  jika tidak, log audit senyap-senyap menjadi salinan bayangan tidak bersulit bagi
  medan paling sensitif. Ada ujian yang menguatkuasakan ini.
- Header keselamatan asas dipasang melalui middleware; HSTS hanya atas HTTPS.

---

## Deploy (Easypanel)

`Dockerfile` di akar ialah imej produksi berbilang peringkat: bina aset → nginx +
php-fpm + pekerja queue + penjadual, disupervisi.

1. Buat perkhidmatan **App** daripada repo ini (Dockerfile di akar).
2. Tambah perkhidmatan terurus **PostgreSQL 17** dan **Redis**.
3. Tetapkan pembolehubah persekitaran (lihat `.env.example`). **Minimum:**

   ```ini
   APP_NAME="Jadual Kelas Student Auto"
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=            # php artisan key:generate --show
   APP_URL=https://domain-anda.com
   APP_LOCALE=ms
   APP_FALLBACK_LOCALE=en
   APP_TIMEZONE=Asia/Kuala_Lumpur

   DB_CONNECTION=pgsql
   DB_HOST=            # nama perkhidmatan Postgres di Easypanel
   DB_PORT=5432
   DB_DATABASE=jadual
   DB_USERNAME=jadual
   DB_PASSWORD=

   REDIS_HOST=         # nama perkhidmatan Redis di Easypanel
   REDIS_PORT=6379
   CACHE_STORE=redis
   QUEUE_CONNECTION=redis
   SESSION_DRIVER=database

   # Admin pertama — digunakan sekali sahaja, diabaikan selepas ada pengguna.
   ADMIN_NAME="Nama Anda"
   ADMIN_EMAIL=anda@domain.com
   ADMIN_PASSWORD=     # guna kata laluan yang kuat
   ```

4. Deploy. Entrypoint akan menunggu pangkalan data, menjalankan migrasi, seed asas
   (peranan, sesi, slot masa, peraturan — **bukan** data contoh), mencipta admin
   pertama, dan cache konfigurasi.
5. Buka **root URL** — halaman status akan sahkan aplikasi, pangkalan data dan Redis
   semua boleh dihubungi.
6. Log masuk dengan `ADMIN_EMAIL`, kemudian isi kredensial di **Integrasi**.

Kalau anda lebih suka tidak meletakkan kata laluan dalam env, tinggalkan
`ADMIN_EMAIL` kosong dan jalankan sekali dalam terminal container:

```bash
php artisan admin:create anda@domain.com --name="Nama Anda"
```

Ia menjana kata laluan kuat dan memaparkannya sekali sahaja.

**Health check:** `/up` (untuk pemeriksaan Easypanel).

### Di belakang proxy Easypanel

Easypanel meletakkan Traefik di hadapan container dan menamatkan TLS di situ, jadi
aplikasi menerima permintaan sebagai `http` dengan header `X-Forwarded-Proto: https`.
`bootstrap/app.php` mempercayai proxy itu (`trustProxies(at: '*')`). Tanpanya:

- semua URL dijana sebagai `http://` pada tapak `https://` — kandungan bercampur
  dan pengalihan log masuk rosak;
- kuki sesi tidak mendapat bendera `secure`;
- HSTS tidak pernah dihantar;
- had kadar log masuk melihat IP proxy, bukan IP pelawat — seorang penyerang boleh
  mengunci semua orang.

Container hanya boleh dicapai melalui proxy itu, jadi mempercayainya selamat.
Ada ujian yang menguatkuasakan ketiga-tiga tingkah laku ini.

---

## Eksport

- **Jadual mingguan → PDF** (landskap; lima lajur hari tidak muat pada potret)
- **Senarai pelajar setiap kelas → PDF**
- **Semua pelajar → CSV** — tajuk lajurnya sepadan dengan pemetaan import, jadi anda
  boleh eksport, betulkan dalam spreadsheet, dan import semula
- **Statistik kelas → CSV**

CSV ditulis dengan BOM UTF-8; tanpanya Excel memaparkan nama Melayu sebagai sampah.
Eksport pelajar distrim dan dipecah kepada ketulan, jadi penggunaan memori kekal rata
walau berapa ramai pelajar.

---

## Import daripada Google Sheets

**Pelajar → Import.** Tiga langkah: muat naik, semak, sahkan. Tiada apa ditulis
sehingga anda menekan Import.

- Menerima **CSV dan XLSX**; baris pertama mesti tajuk lajur.
- **Pemetaan lajur diteka automatik** daripada tajuk biasa (*Nama*, *Kod*,
  *Jantina*, *Tahun*, *Keluarga*) dan boleh dibetulkan pada skrin.
- Menerima cara sebenar orang menulis dalam sheet: `Lelaki` / `L` / `M` / `Male`,
  dan tingkah laku sebagai label (`Bermasalah`) atau nombor (`2`).
- Baris bermasalah **dilaporkan dengan nombor barisnya** dan dilangkau — tidak
  pernah diteka. Baris sah tetap diimport.
- **Kod pelajar yang sudah wujud dikemas kini, bukan diduplikasi**, jadi selamat
  mengimport semula fail yang dibetulkan.
- Lajur *Keluarga* memautkan adik-beradik: nama keluarga yang sama = satu keluarga.

---

## Belum termasuk

- **Payment gateway** — dikeluarkan atas permintaan; akaun merchant belum ada.
  Widget Integrasi sudah direka untuk menerimanya (CHIP: `gate.chip-in.asia/api/v1/`,
  webhook disahkan melalui `X-Signature`, RSA PKCS#1 v1.5 / SHA-256).
- Portal ibu bapa
- Penandaan kehadiran oleh guru
- Aplikasi mudah alih
