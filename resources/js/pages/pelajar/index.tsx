import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    FileDown,
    Pencil,
    Plus,
    Search,
    TriangleAlert,
    Upload,
    Users,
} from 'lucide-react';
import { useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { DeleteButton } from '@/components/delete-button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Student {
    id: number;
    kod: string;
    nama: string;
    tahun: number;
    jantina: string;
    tingkah_laku: number;
    tingkah_laku_label: string;
    keluarga: string | null;
    kelas: string | null;
    aktif: boolean;
}

interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    total: number;
    from: number | null;
    to: number | null;
}

interface Props {
    [key: string]: unknown;
    pelajar: Paginated<Student>;
    filters: { cari?: string; tahun?: number; status?: string };
}

export default function PelajarIndex() {
    const { pelajar, filters } = usePage<Props>().props;
    const [cari, setCari] = useState(filters.cari ?? '');

    const apply = (next: Record<string, string | number | undefined>) =>
        router.get(
            '/pelajar',
            { ...filters, ...next },
            { preserveState: true, replace: true },
        );

    return (
        <>
            <Head title="Pelajar" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header className="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Pelajar
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            {pelajar.total} pelajar · memaparkan{' '}
                            {pelajar.from ?? 0}–{pelajar.to ?? 0}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <Button asChild variant="outline">
                            <a href="/eksport/pelajar.csv">
                                <FileDown className="size-4" /> CSV
                            </a>
                        </Button>
                        <Button asChild variant="outline">
                            <Link href="/pelajar/import">
                                <Upload className="size-4" /> Import
                            </Link>
                        </Button>
                        <Button asChild>
                            <Link href="/pelajar/create">
                                <Plus className="size-4" /> Tambah Pelajar
                            </Link>
                        </Button>
                    </div>
                </header>

                <Card className="gap-0 p-0">
                    <div className="flex flex-wrap items-center gap-2 border-b p-3">
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                apply({ cari });
                            }}
                            className="relative flex-1 sm:max-w-xs"
                        >
                            <Search className="absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                value={cari}
                                onChange={(e) => setCari(e.target.value)}
                                placeholder="Cari nama atau kod…"
                                className="pl-8"
                            />
                        </form>

                        <select
                            value={filters.tahun ?? ''}
                            onChange={(e) =>
                                apply({ tahun: e.target.value || undefined })
                            }
                            className="h-9 rounded-md border border-input bg-transparent px-2 text-sm"
                        >
                            <option value="">Semua tahun</option>
                            {[1, 2, 3, 4, 5, 6].map((t) => (
                                <option key={t} value={t}>
                                    Tahun {t}
                                </option>
                            ))}
                        </select>

                        <select
                            value={filters.status ?? ''}
                            onChange={(e) =>
                                apply({ status: e.target.value || undefined })
                            }
                            className="h-9 rounded-md border border-input bg-transparent px-2 text-sm"
                        >
                            <option value="">Semua status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidak_aktif">Tidak aktif</option>
                            <option value="belum_ada_kelas">
                                Belum ada kelas
                            </option>
                        </select>
                    </div>

                    {pelajar.data.length === 0 ? (
                        <p className="p-8 text-center text-sm text-muted-foreground">
                            Tiada pelajar sepadan.
                        </p>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Kod</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Tahun</TableHead>
                                    <TableHead>Jantina</TableHead>
                                    <TableHead>Kelas</TableHead>
                                    <TableHead>Keluarga</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pelajar.data.map((s) => (
                                    <TableRow key={s.id}>
                                        <TableCell className="font-mono text-xs text-muted-foreground">
                                            {s.kod}
                                        </TableCell>
                                        <TableCell className="font-medium">
                                            <span className="inline-flex items-center gap-1.5">
                                                {s.nama}
                                                {s.tingkah_laku >= 2 && (
                                                    <TriangleAlert
                                                        className="size-3.5 text-[var(--chart-3)]"
                                                        aria-label={
                                                            s.tingkah_laku_label
                                                        }
                                                    />
                                                )}
                                            </span>
                                        </TableCell>
                                        <TableCell className="tabular-nums">
                                            {s.tahun}
                                        </TableCell>
                                        <TableCell>
                                            {s.jantina === 'L'
                                                ? 'Lelaki'
                                                : 'Perempuan'}
                                        </TableCell>
                                        <TableCell>
                                            {s.kelas ? (
                                                <Badge variant="secondary">
                                                    {s.kelas}
                                                </Badge>
                                            ) : (
                                                <span className="text-xs text-muted-foreground">
                                                    Belum ada
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-xs text-muted-foreground">
                                            {s.keluarga ? (
                                                <span className="inline-flex items-center gap-1">
                                                    <Users className="size-3" />
                                                    {s.keluarga}
                                                </span>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {s.aktif ? (
                                                <span className="text-xs text-muted-foreground">
                                                    Aktif
                                                </span>
                                            ) : (
                                                <Badge variant="outline">
                                                    Tidak aktif
                                                </Badge>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-right whitespace-nowrap">
                                            <Button
                                                asChild
                                                variant="ghost"
                                                size="sm"
                                                aria-label={`Edit ${s.nama}`}
                                            >
                                                <Link
                                                    href={`/pelajar/${s.id}/edit`}
                                                >
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <DeleteButton
                                                url={`/pelajar/${s.id}`}
                                                title={`Buang ${s.nama}?`}
                                                description="Rekod pelajar akan dibuang dan penempatan kelas semasa ditutup. Sejarah penempatan dikekalkan."
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}

                    <div className="flex flex-wrap gap-1 border-t p-3">
                        {pelajar.links.map((link, i) => (
                            <Button
                                key={i}
                                asChild={Boolean(link.url)}
                                variant={link.active ? 'default' : 'ghost'}
                                size="sm"
                                disabled={!link.url}
                            >
                                {link.url ? (
                                    <Link href={link.url} preserveState>
                                        <span
                                            dangerouslySetInnerHTML={{
                                                __html: link.label,
                                            }}
                                        />
                                    </Link>
                                ) : (
                                    <span
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                )}
                            </Button>
                        ))}
                    </div>
                </Card>
            </div>
        </>
    );
}

PelajarIndex.layout = {
    breadcrumbs: [{ title: 'Pelajar', href: '/pelajar' }],
};
