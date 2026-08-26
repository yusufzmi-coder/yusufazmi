import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowLeft,
    CheckCircle2,
    FileSpreadsheet,
    Upload,
} from 'lucide-react';
import { NativeSelect } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Preview {
    path: string;
    nama_fail: string;
    headers: string[];
    mapping: Record<string, string | null>;
    jumlah_baris: number;
    sah: number;
    ralat: { baris: number; ralat: string[] }[];
    contoh: Record<string, string | number | null>[];
}

interface Props {
    [key: string]: unknown;
    medan: Record<string, { label: string; wajib: boolean }>;
    preview?: Preview;
}

export default function PelajarImport() {
    const { medan, preview } = usePage<Props>().props;

    const upload = useForm<{ fail: File | null }>({ fail: null });
    const mapForm = useForm({
        path: preview?.path ?? '',
        nama_fail: preview?.nama_fail ?? '',
        mapping: (preview?.mapping ?? {}) as Record<string, string | null>,
    });

    const submitUpload = (e: React.FormEvent) => {
        e.preventDefault();
        upload.post('/pelajar/import/pratonton', { forceFormData: true });
    };

    const changeMapping = (field: string, header: string) => {
        const next = { ...mapForm.data.mapping, [field]: header || null };
        mapForm.setData('mapping', next);
        router.post(
            '/pelajar/import/peta',
            { ...mapForm.data, mapping: next },
            { preserveScroll: true },
        );
    };

    const commit = () => mapForm.post('/pelajar/import');

    const missingRequired = Object.entries(medan)
        .filter(([field, meta]) => meta.wajib && !mapForm.data.mapping[field])
        .map(([, meta]) => meta.label);

    return (
        <>
            <Head title="Import Pelajar" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <Button
                        asChild
                        variant="ghost"
                        size="sm"
                        className="mb-1 -ml-2"
                    >
                        <Link href="/pelajar">
                            <ArrowLeft className="size-4" /> Kembali
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Import Pelajar
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Muat naik fail CSV atau XLSX yang dieksport daripada
                        Google Sheets. Tiada apa disimpan sehingga anda menekan
                        Import.
                    </p>
                </header>

                {!preview ? (
                    <Card className="max-w-2xl gap-0 p-4">
                        <form onSubmit={submitUpload} className="space-y-4">
                            <div>
                                <Label htmlFor="fail" className="text-xs">
                                    Fail CSV atau XLSX
                                </Label>
                                <input
                                    id="fail"
                                    type="file"
                                    accept=".csv,.txt,.xlsx"
                                    onChange={(e) =>
                                        upload.setData(
                                            'fail',
                                            e.target.files?.[0] ?? null,
                                        )
                                    }
                                    className="mt-1 block w-full text-sm file:mr-3 file:rounded-md file:border file:border-input file:bg-background file:px-3 file:py-1.5 file:text-sm"
                                />
                                {upload.errors.fail && (
                                    <p className="mt-1 text-xs text-destructive">
                                        {upload.errors.fail}
                                    </p>
                                )}
                            </div>

                            <div className="rounded-lg border bg-muted/40 p-3 text-xs text-muted-foreground">
                                <p className="mb-1 font-medium text-foreground">
                                    Baris pertama mesti tajuk lajur.
                                </p>
                                <p>
                                    Tajuk biasa dikenali automatik —{' '}
                                    <em>Nama</em>, <em>Kod</em>,{' '}
                                    <em>Jantina</em>, <em>Tahun</em>,{' '}
                                    <em>Keluarga</em>. Anda boleh membetulkan
                                    pemetaan pada skrin seterusnya.
                                </p>
                            </div>

                            <Button
                                type="submit"
                                disabled={
                                    !upload.data.fail || upload.processing
                                }
                            >
                                <Upload className="size-4" />
                                {upload.processing
                                    ? 'Membaca fail…'
                                    : 'Baca fail'}
                            </Button>
                        </form>
                    </Card>
                ) : (
                    <div className="grid gap-5">
                        <Card className="gap-0 p-4">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div className="flex items-center gap-2">
                                    <FileSpreadsheet className="size-4 text-muted-foreground" />
                                    <span className="font-medium">
                                        {preview.nama_fail}
                                    </span>
                                </div>
                                <div className="flex flex-wrap gap-x-6 text-sm">
                                    <span>
                                        <span className="text-muted-foreground">
                                            Jumlah baris:{' '}
                                        </span>
                                        <span className="font-semibold tabular-nums">
                                            {preview.jumlah_baris}
                                        </span>
                                    </span>
                                    <span>
                                        <span className="text-muted-foreground">
                                            Sah:{' '}
                                        </span>
                                        <span className="font-semibold text-[var(--chart-2)] tabular-nums">
                                            {preview.sah}
                                        </span>
                                    </span>
                                    <span>
                                        <span className="text-muted-foreground">
                                            Ralat:{' '}
                                        </span>
                                        <span
                                            className={`font-semibold tabular-nums ${preview.ralat.length > 0 ? 'text-destructive' : ''}`}
                                        >
                                            {preview.ralat.length}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </Card>

                        <Card className="gap-0 p-4">
                            <h2 className="mb-3 text-base font-semibold">
                                Padankan lajur
                            </h2>
                            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                {Object.entries(medan).map(([field, meta]) => (
                                    <div key={field}>
                                        <Label
                                            htmlFor={`map-${field}`}
                                            className="text-xs"
                                        >
                                            {meta.label}
                                            {meta.wajib && (
                                                <span className="text-destructive">
                                                    {' '}
                                                    *
                                                </span>
                                            )}
                                        </Label>
                                        <NativeSelect
                                            id={`map-${field}`}
                                            className="mt-1"
                                            value={
                                                mapForm.data.mapping[field] ??
                                                ''
                                            }
                                            onChange={(e) =>
                                                changeMapping(
                                                    field,
                                                    e.target.value,
                                                )
                                            }
                                        >
                                            <option value="">— tiada —</option>
                                            {preview.headers.map((h) => (
                                                <option key={h} value={h}>
                                                    {h}
                                                </option>
                                            ))}
                                        </NativeSelect>
                                    </div>
                                ))}
                            </div>

                            {missingRequired.length > 0 && (
                                <p className="mt-3 text-xs text-destructive">
                                    Lajur wajib belum dipadankan:{' '}
                                    {missingRequired.join(', ')}.
                                </p>
                            )}
                        </Card>

                        {preview.ralat.length > 0 && (
                            <Card className="gap-0 border-destructive/30 p-4">
                                <h2 className="mb-2 flex items-center gap-2 text-base font-semibold">
                                    <AlertTriangle className="size-4 text-destructive" />
                                    Baris bermasalah ({preview.ralat.length})
                                </h2>
                                <p className="mb-3 text-xs text-muted-foreground">
                                    Baris ini akan dilangkau. Betulkan fail dan
                                    muat naik semula jika anda mahukan mereka.
                                </p>
                                <ul className="max-h-64 space-y-1.5 overflow-y-auto text-sm">
                                    {preview.ralat.slice(0, 50).map((row) => (
                                        <li key={row.baris}>
                                            <span className="font-medium">
                                                Baris {row.baris}:
                                            </span>{' '}
                                            <span className="text-muted-foreground">
                                                {row.ralat.join('; ')}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                                {preview.ralat.length > 50 && (
                                    <p className="mt-2 text-xs text-muted-foreground">
                                        …dan {preview.ralat.length - 50} lagi.
                                    </p>
                                )}
                            </Card>
                        )}

                        {preview.contoh.length > 0 && (
                            <Card className="gap-0 p-0">
                                <div className="border-b p-4">
                                    <h2 className="flex items-center gap-2 text-base font-semibold">
                                        <CheckCircle2 className="size-4 text-[var(--chart-2)]" />
                                        Pratonton baris sah
                                    </h2>
                                </div>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Baris</TableHead>
                                            <TableHead>Kod</TableHead>
                                            <TableHead>Nama</TableHead>
                                            <TableHead>Jantina</TableHead>
                                            <TableHead>Tahun</TableHead>
                                            <TableHead>Keluarga</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {preview.contoh.map((row) => (
                                            <TableRow key={String(row.baris)}>
                                                <TableCell className="text-xs text-muted-foreground">
                                                    {row.baris}
                                                </TableCell>
                                                <TableCell className="font-mono text-xs">
                                                    {row.student_code}
                                                </TableCell>
                                                <TableCell className="font-medium">
                                                    {row.name}
                                                </TableCell>
                                                <TableCell>
                                                    {row.gender === 'L'
                                                        ? 'Lelaki'
                                                        : 'Perempuan'}
                                                </TableCell>
                                                <TableCell className="tabular-nums">
                                                    {row.year_level}
                                                </TableCell>
                                                <TableCell className="text-xs text-muted-foreground">
                                                    {row.family_name || '—'}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </Card>
                        )}

                        <div className="flex flex-wrap gap-2">
                            <Button
                                onClick={commit}
                                disabled={
                                    mapForm.processing ||
                                    preview.sah === 0 ||
                                    missingRequired.length > 0
                                }
                            >
                                {mapForm.processing
                                    ? 'Mengimport…'
                                    : `Import ${preview.sah} pelajar`}
                            </Button>
                            <Button asChild variant="ghost">
                                <Link href="/pelajar/import">
                                    Muat naik fail lain
                                </Link>
                            </Button>
                        </div>

                        <p className="text-xs text-muted-foreground">
                            Kod pelajar yang sudah wujud akan dikemas kini,
                            bukan diduplikasi — jadi selamat untuk mengimport
                            semula fail yang dibetulkan.
                        </p>
                    </div>
                )}
            </div>
        </>
    );
}

PelajarImport.layout = {
    breadcrumbs: [
        { title: 'Pelajar', href: '/pelajar' },
        { title: 'Import', href: '/pelajar/import' },
    ],
};
