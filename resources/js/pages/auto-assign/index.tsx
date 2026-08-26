import { Head, Link, router, usePage } from '@inertiajs/react';
import { CheckCircle2, History, Play, Sparkles, Undo2 } from 'lucide-react';
import { useState } from 'react';
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
import type { RunStats } from '@/types/jadual';

interface Run {
    id: number;
    mode: string;
    status: string;
    stats: RunStats;
    duration_ms: number | null;
    created_by: string | null;
    created_at: string | null;
    boleh_buat_asal: boolean;
}

interface Props {
    [key: string]: unknown;
    runs: Run[];
}

const statusLabel: Record<string, string> = {
    draft: 'Menunggu pengesahan',
    committed: 'Disahkan',
    reverted: 'Dibuat asal',
    discarded: 'Dibuang',
    expired: 'Luput',
    failed: 'Gagal',
    running: 'Sedang berjalan',
};

const checklist = [
    'Semak kekangan & rules',
    'Cari slot kelas kosong',
    'Assign pelajar',
    'Sahkan penjadualan',
];

export default function AutoAssignIndex() {
    const { runs } = usePage<Props>().props;
    const [mode, setMode] = useState<'fill_only' | 'rebalance'>('fill_only');
    const [running, setRunning] = useState(false);

    const run = () => {
        setRunning(true);
        router.post(
            '/auto-assign',
            { mode },
            { onFinish: () => setRunning(false) },
        );
    };

    return (
        <>
            <Head title="Auto Assign" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Auto Assign
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Letakkan pelajar dalam kelas secara automatik mengikut
                        peraturan yang aktif.
                    </p>
                </header>

                <div className="grid gap-5 lg:grid-cols-[22rem_minmax(0,1fr)]">
                    <Card className="h-fit gap-0 p-4">
                        <div className="mb-2 flex items-center gap-2">
                            <Sparkles className="size-4 text-primary" />
                            <h2 className="text-base font-semibold">
                                Jalankan sekarang
                            </h2>
                        </div>

                        <ul className="my-3 space-y-2">
                            {checklist.map((step) => (
                                <li
                                    key={step}
                                    className="flex items-center gap-2 text-sm"
                                >
                                    <CheckCircle2 className="size-4 shrink-0 text-[var(--chart-2)]" />
                                    <span className="text-muted-foreground">
                                        {step}
                                    </span>
                                </li>
                            ))}
                        </ul>

                        <fieldset className="mb-3 space-y-2">
                            <legend className="mb-1 text-xs font-medium text-muted-foreground">
                                Mod
                            </legend>
                            {(
                                [
                                    [
                                        'fill_only',
                                        'Isi tempat kosong sahaja',
                                        'Pelajar sedia ada tidak akan dialihkan.',
                                    ],
                                    [
                                        'rebalance',
                                        'Imbang semula',
                                        'Boleh memindahkan pelajar sedia ada.',
                                    ],
                                ] as const
                            ).map(([value, label, hint]) => (
                                <label
                                    key={value}
                                    className="flex cursor-pointer items-start gap-2 rounded-lg border p-2 text-sm has-checked:border-primary has-checked:bg-accent/50"
                                >
                                    <input
                                        type="radio"
                                        name="mode"
                                        value={value}
                                        checked={mode === value}
                                        onChange={() => setMode(value)}
                                        className="mt-1 accent-[var(--primary)]"
                                    />
                                    <span>
                                        <span className="block font-medium">
                                            {label}
                                        </span>
                                        <span className="block text-xs text-muted-foreground">
                                            {hint}
                                        </span>
                                    </span>
                                </label>
                            ))}
                        </fieldset>

                        <Button
                            onClick={run}
                            disabled={running}
                            className="w-full"
                        >
                            <Play className="size-4" />
                            {running
                                ? 'Sedang dijalankan…'
                                : 'Jalankan Auto Assign'}
                        </Button>
                        <p className="mt-2 text-center text-[0.7rem] text-muted-foreground">
                            Cadangan akan dipaparkan dahulu — tiada apa disimpan
                            sehingga anda sahkan.
                        </p>
                    </Card>

                    <Card className="gap-0 p-0">
                        <div className="flex items-center gap-2 border-b p-4">
                            <History className="size-4 text-muted-foreground" />
                            <h2 className="text-base font-semibold">
                                Sejarah larian
                            </h2>
                        </div>

                        {runs.length === 0 ? (
                            <p className="p-8 text-center text-sm text-muted-foreground">
                                Belum ada larian. Tekan “Jalankan Auto Assign”
                                untuk bermula.
                            </p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>#</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Hasil</TableHead>
                                        <TableHead>Masa</TableHead>
                                        <TableHead>Oleh</TableHead>
                                        <TableHead />
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {runs.map((item) => (
                                        <TableRow key={item.id}>
                                            <TableCell className="font-medium">
                                                {item.id}
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    variant={
                                                        item.status ===
                                                        'committed'
                                                            ? 'default'
                                                            : 'secondary'
                                                    }
                                                >
                                                    {statusLabel[item.status] ??
                                                        item.status}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="text-xs text-muted-foreground">
                                                {item.stats?.place ?? 0}{' '}
                                                ditempatkan ·{' '}
                                                {item.stats?.move ?? 0} dipindah
                                                {(item.stats?.unplaceable ??
                                                    0) > 0 && (
                                                    <span className="text-destructive">
                                                        {' '}
                                                        ·{' '}
                                                        {
                                                            item.stats
                                                                .unplaceable
                                                        }{' '}
                                                        gagal
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-xs text-muted-foreground">
                                                {item.created_at}
                                                {item.duration_ms !== null &&
                                                    ` · ${item.duration_ms} ms`}
                                            </TableCell>
                                            <TableCell className="text-xs text-muted-foreground">
                                                {item.created_by}
                                            </TableCell>
                                            <TableCell>
                                                <Button
                                                    asChild
                                                    variant="ghost"
                                                    size="sm"
                                                >
                                                    <Link
                                                        href={`/auto-assign/${item.id}`}
                                                    >
                                                        Lihat
                                                    </Link>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </Card>
                </div>
            </div>
        </>
    );
}

AutoAssignIndex.layout = {
    breadcrumbs: [{ title: 'Auto Assign', href: '/auto-assign' }],
};
