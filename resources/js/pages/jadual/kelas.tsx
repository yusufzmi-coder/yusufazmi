import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    FileDown,
    Lock,
    LockOpen,
    MapPin,
    Plus,
    User,
} from 'lucide-react';
import { useState } from 'react';
import { DeleteButton } from '@/components/delete-button';
import { NativeSelect } from '@/components/form-field';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Props {
    [key: string]: unknown;
    kelas: {
        id: number;
        nama: string;
        tahun: number;
        stream: string;
        guru: string | null;
        ketegasan: number | null;
        kapasiti: number;
        pertemuan: {
            hari: string;
            slot: string | null;
            bilik: string | null;
        }[];
    };
    pelajar: {
        enrolment_id: number;
        id: number;
        nama: string;
        kod: string;
        jantina: string;
        tingkah_laku: string;
        disemat: boolean;
        sebab: string | null;
    }[];
    boleh_tambah: { id: number; nama: string; kod: string }[];
    kelas_lain: {
        id: number;
        nama: string;
        kapasiti: number;
        pelajar: number;
    }[];
}

const firmnessLabel = (level: number | null) =>
    ({
        1: 'Sangat Lembut',
        2: 'Lembut',
        3: 'Sederhana',
        4: 'Tegas',
        5: 'Sangat Tegas',
    })[level ?? 3] ?? '-';

export default function KelasDetail() {
    const { kelas, pelajar, boleh_tambah, kelas_lain } = usePage<Props>().props;
    const [moving, setMoving] = useState<number | null>(null);

    const addForm = useForm({ student_id: '' });

    const lelaki = pelajar.filter((p) => p.jantina === 'L').length;
    const full = kelas.kapasiti > 0 && pelajar.length >= kelas.kapasiti;

    const add = (e: React.FormEvent) => {
        e.preventDefault();
        addForm.post(`/jadual/kelas/${kelas.id}/pelajar`, {
            preserveScroll: true,
            onSuccess: () => addForm.reset(),
        });
    };

    const move = (enrolmentId: number, classId: string) => {
        if (!classId) return;
        router.patch(
            `/enrolan/${enrolmentId}`,
            { class_id: Number(classId) },
            { preserveScroll: true },
        );
        setMoving(null);
    };

    const togglePin = (enrolmentId: number, pinned: boolean) =>
        router.patch(
            `/enrolan/${enrolmentId}/semat`,
            { pinned },
            { preserveScroll: true },
        );

    return (
        <>
            <Head title={`Kelas ${kelas.nama}`} />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header className="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <Button
                            asChild
                            variant="ghost"
                            size="sm"
                            className="mb-1 -ml-2"
                        >
                            <Link href="/jadual">
                                <ArrowLeft className="size-4" /> Kembali ke
                                jadual
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Kelas {kelas.nama}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            {kelas.guru ?? 'Tiada guru'} ·{' '}
                            {firmnessLabel(kelas.ketegasan)} · {pelajar.length}/
                            {kelas.kapasiti || '—'} pelajar · {lelaki} L /{' '}
                            {pelajar.length - lelaki} P
                        </p>
                    </div>

                    <Button asChild variant="outline">
                        <a href={`/eksport/kelas/${kelas.id}.pdf`}>
                            <FileDown className="size-4" /> Senarai PDF
                        </a>
                    </Button>
                </header>

                <Card className="gap-0 p-4">
                    <h2 className="mb-2 text-base font-semibold">
                        Waktu pertemuan
                    </h2>
                    <div className="flex flex-wrap gap-2">
                        {kelas.pertemuan.map((m, i) => (
                            <span
                                key={i}
                                className="inline-flex items-center gap-2 rounded-lg border px-3 py-1.5 text-xs"
                            >
                                <span className="font-medium">{m.hari}</span>
                                <span className="text-muted-foreground">
                                    {m.slot}
                                </span>
                                {m.bilik && (
                                    <span className="inline-flex items-center gap-1 text-muted-foreground">
                                        <MapPin className="size-3" />
                                        {m.bilik}
                                    </span>
                                )}
                            </span>
                        ))}
                        {kelas.pertemuan.length === 0 && (
                            <p className="text-sm text-muted-foreground">
                                Kelas ini belum ada slot dalam jadual.
                            </p>
                        )}
                    </div>
                </Card>

                <Card className="gap-0 p-4">
                    <h2 className="mb-1 flex items-center gap-2 text-base font-semibold">
                        <Plus className="size-4" /> Tambah pelajar
                    </h2>
                    <p className="mb-3 text-xs text-muted-foreground">
                        Hanya pelajar Tahun {kelas.tahun} yang belum ada kelas
                        dipaparkan.
                    </p>

                    {full ? (
                        <p className="text-sm text-muted-foreground">
                            Kelas ini sudah penuh ({pelajar.length}/
                            {kelas.kapasiti}).
                        </p>
                    ) : boleh_tambah.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            Semua pelajar Tahun {kelas.tahun} sudah mempunyai
                            kelas.
                        </p>
                    ) : (
                        <form
                            onSubmit={add}
                            className="flex flex-wrap items-end gap-2"
                        >
                            <NativeSelect
                                value={addForm.data.student_id}
                                onChange={(e) =>
                                    addForm.setData(
                                        'student_id',
                                        e.target.value,
                                    )
                                }
                                className="max-w-xs"
                                aria-label="Pilih pelajar"
                            >
                                <option value="">Pilih pelajar…</option>
                                {boleh_tambah.map((s) => (
                                    <option key={s.id} value={s.id}>
                                        {s.kod} — {s.nama}
                                    </option>
                                ))}
                            </NativeSelect>
                            <Button
                                type="submit"
                                disabled={
                                    !addForm.data.student_id ||
                                    addForm.processing
                                }
                            >
                                Tambah
                            </Button>
                        </form>
                    )}
                </Card>

                <Card className="gap-0 p-0">
                    <div className="border-b p-4">
                        <h2 className="text-base font-semibold">
                            Senarai Pelajar
                        </h2>
                    </div>

                    {pelajar.length === 0 ? (
                        <p className="p-8 text-center text-sm text-muted-foreground">
                            Belum ada pelajar dalam kelas ini. Jalankan Auto
                            Assign atau tambah secara manual.
                        </p>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Kod</TableHead>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Jantina</TableHead>
                                    <TableHead>Tingkah Laku</TableHead>
                                    <TableHead>Sebab penempatan</TableHead>
                                    <TableHead className="text-right">
                                        Tindakan
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pelajar.map((p) => (
                                    <TableRow key={p.enrolment_id}>
                                        <TableCell className="font-mono text-xs text-muted-foreground">
                                            {p.kod}
                                        </TableCell>
                                        <TableCell className="font-medium">
                                            <span className="inline-flex items-center gap-1.5">
                                                {p.nama}
                                                {p.disemat && (
                                                    <Lock className="size-3 text-muted-foreground" />
                                                )}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <span className="inline-flex items-center gap-1 text-sm">
                                                <User className="size-3 text-muted-foreground" />
                                                {p.jantina === 'L'
                                                    ? 'Lelaki'
                                                    : 'Perempuan'}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            {p.tingkah_laku === 'Normal' ? (
                                                <span className="text-xs text-muted-foreground">
                                                    Normal
                                                </span>
                                            ) : (
                                                <Badge variant="secondary">
                                                    {p.tingkah_laku}
                                                </Badge>
                                            )}
                                        </TableCell>
                                        <TableCell className="max-w-md text-xs leading-snug text-muted-foreground">
                                            {p.sebab ?? '—'}
                                        </TableCell>
                                        <TableCell className="text-right whitespace-nowrap">
                                            {moving === p.enrolment_id ? (
                                                <NativeSelect
                                                    autoFocus
                                                    className="inline-block max-w-44"
                                                    defaultValue=""
                                                    onChange={(e) =>
                                                        move(
                                                            p.enrolment_id,
                                                            e.target.value,
                                                        )
                                                    }
                                                    onBlur={() =>
                                                        setMoving(null)
                                                    }
                                                    aria-label={`Pindah ${p.nama}`}
                                                >
                                                    <option value="">
                                                        Pindah ke…
                                                    </option>
                                                    {kelas_lain.map((k) => (
                                                        <option
                                                            key={k.id}
                                                            value={k.id}
                                                        >
                                                            {k.nama} (
                                                            {k.pelajar}/
                                                            {k.kapasiti || '—'})
                                                        </option>
                                                    ))}
                                                </NativeSelect>
                                            ) : (
                                                <>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            togglePin(
                                                                p.enrolment_id,
                                                                !p.disemat,
                                                            )
                                                        }
                                                        aria-label={
                                                            p.disemat
                                                                ? `Buang semat ${p.nama}`
                                                                : `Semat ${p.nama}`
                                                        }
                                                        title={
                                                            p.disemat
                                                                ? 'Disemat — Auto Assign tidak akan mengalihkannya'
                                                                : 'Semat dalam kelas ini'
                                                        }
                                                    >
                                                        {p.disemat ? (
                                                            <Lock className="size-4 text-primary" />
                                                        ) : (
                                                            <LockOpen className="size-4 text-muted-foreground" />
                                                        )}
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        disabled={
                                                            kelas_lain.length ===
                                                            0
                                                        }
                                                        onClick={() =>
                                                            setMoving(
                                                                p.enrolment_id,
                                                            )
                                                        }
                                                        aria-label={`Pindah ${p.nama}`}
                                                    >
                                                        Pindah
                                                    </Button>
                                                    <DeleteButton
                                                        url={`/enrolan/${p.enrolment_id}`}
                                                        title={`Keluarkan ${p.nama}?`}
                                                        description="Pelajar akan dikeluarkan daripada kelas ini dan menunggu penempatan semula. Sejarah penempatan dikekalkan."
                                                    />
                                                </>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </Card>
            </div>
        </>
    );
}

KelasDetail.layout = {
    breadcrumbs: [
        { title: 'Jadual Kelas', href: '/jadual' },
        { title: 'Kelas', href: '#' },
    ],
};
