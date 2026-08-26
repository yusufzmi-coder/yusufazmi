import { Head, Link, router, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    AlertTriangle,
    ArrowLeft,
    Check,
    Lock,
    MoveRight,
    Plus,
    Trash2,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { cn } from '@/lib/utils';
import type { RunAction, RunStats } from '@/types/jadual';

interface Item {
    id: number;
    action: RunAction;
    pelajar: string;
    tahun: number;
    dari: string | null;
    ke: string | null;
    sebab: string;
    ada_pertukaran: boolean;
    skor: number | null;
}

interface Props {
    [key: string]: unknown;
    run: {
        id: number;
        mode: string;
        status: string;
        stats: RunStats;
        duration_ms: number;
        objective: number | null;
        created_at: string | null;
        boleh_sahkan: boolean;
    };
    items: Item[];
    ringkasan: {
        kelas: string;
        baharu: number;
        masuk: number;
        kekal: number;
        jumlah: number;
    }[];
}

const actionMeta: Record<
    RunAction,
    { label: string; icon: typeof Plus; tone: string }
> = {
    place: {
        label: 'Ditempatkan',
        icon: Plus,
        tone: 'bg-[color-mix(in_oklch,var(--chart-2)_18%,white)] text-foreground',
    },
    move: {
        label: 'Dipindah',
        icon: MoveRight,
        tone: 'bg-[color-mix(in_oklch,var(--chart-3)_22%,white)] text-foreground',
    },
    keep: {
        label: 'Kekal',
        icon: Check,
        tone: 'bg-muted text-muted-foreground',
    },
    pinned: {
        label: 'Disemat',
        icon: Lock,
        tone: 'bg-muted text-muted-foreground',
    },
    unplaceable: {
        label: 'Tidak dapat ditempatkan',
        icon: AlertTriangle,
        tone: 'bg-[color-mix(in_oklch,var(--destructive)_14%,white)] text-foreground',
    },
};

export default function Preview() {
    const { run, items, ringkasan } = usePage<Props>().props;
    const [excluded, setExcluded] = useState<number[]>([]);
    const [submitting, setSubmitting] = useState(false);

    // Only these two actually change anything, so only these are reviewable.
    const changes = useMemo(
        () => items.filter((i) => i.action === 'place' || i.action === 'move'),
        [items],
    );
    const unplaceable = useMemo(
        () => items.filter((i) => i.action === 'unplaceable'),
        [items],
    );

    const toggle = (id: number) =>
        setExcluded((prev) =>
            prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id],
        );

    const commit = () => {
        setSubmitting(true);
        router.post(
            `/auto-assign/${run.id}/sahkan`,
            { excluded },
            { onFinish: () => setSubmitting(false) },
        );
    };

    const discard = () => router.delete(`/auto-assign/${run.id}`);

    const chips: { label: string; value: number; tone?: string }[] = [
        { label: 'Ditempatkan', value: run.stats.place ?? 0 },
        { label: 'Dipindah', value: run.stats.move ?? 0 },
        { label: 'Kekal', value: run.stats.keep ?? 0 },
        { label: 'Disemat', value: run.stats.pinned ?? 0 },
        {
            label: 'Tidak dapat ditempatkan',
            value: run.stats.unplaceable ?? 0,
            tone:
                (run.stats.unplaceable ?? 0) > 0
                    ? 'text-destructive'
                    : undefined,
        },
    ];

    return (
        <>
            <Head title={`Cadangan #${run.id}`} />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header className="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <Button
                            asChild
                            variant="ghost"
                            size="sm"
                            className="mb-1 -ml-2"
                        >
                            <Link href="/auto-assign">
                                <ArrowLeft className="size-4" /> Kembali
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Cadangan Penempatan #{run.id}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Dijana {run.created_at} · {run.duration_ms} ms ·{' '}
                            {run.mode === 'fill_only'
                                ? 'Isi tempat kosong sahaja'
                                : 'Imbang semula'}
                        </p>
                    </div>

                    {run.boleh_sahkan && (
                        <div className="flex gap-2">
                            <Button variant="outline" onClick={discard}>
                                <Trash2 className="size-4" /> Buang
                            </Button>
                            <Button onClick={commit} disabled={submitting}>
                                <Check className="size-4" />
                                {submitting
                                    ? 'Menyimpan…'
                                    : `Sahkan ${changes.length - excluded.length} penempatan`}
                            </Button>
                        </div>
                    )}
                </header>

                <Card className="gap-0 p-4">
                    <div className="flex flex-wrap gap-x-6 gap-y-2">
                        {chips.map((chip) => (
                            <div key={chip.label}>
                                <p className="text-xs text-muted-foreground">
                                    {chip.label}
                                </p>
                                <p
                                    className={cn(
                                        'text-xl font-bold tabular-nums',
                                        chip.tone,
                                    )}
                                >
                                    {chip.value}
                                </p>
                            </div>
                        ))}
                        <div>
                            <p className="text-xs text-muted-foreground">
                                Pertukaran nilai
                            </p>
                            <p className="text-xl font-bold tabular-nums">
                                {run.stats.violations ?? 0}
                            </p>
                        </div>
                    </div>
                </Card>

                {unplaceable.length > 0 && (
                    <Card className="gap-0 border-destructive/30 p-4">
                        <h2 className="mb-2 flex items-center gap-2 text-base font-semibold">
                            <AlertTriangle className="size-4 text-destructive" />
                            Tidak dapat ditempatkan ({unplaceable.length})
                        </h2>
                        <ul className="space-y-1.5 text-sm">
                            {unplaceable.map((item) => (
                                <li
                                    key={item.id}
                                    className="flex flex-wrap gap-x-2"
                                >
                                    <span className="font-medium">
                                        {item.pelajar}
                                    </span>
                                    <span className="text-muted-foreground">
                                        Tahun {item.tahun}
                                    </span>
                                    <span className="text-muted-foreground">
                                        — {item.sebab}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </Card>
                )}

                <div className="grid gap-5 xl:grid-cols-[minmax(0,1fr)_18rem]">
                    <Card className="gap-0 p-0">
                        <div className="border-b p-4">
                            <h2 className="text-base font-semibold">
                                Perubahan yang dicadangkan
                            </h2>
                            <p className="text-xs text-muted-foreground">
                                Nyahtanda mana-mana baris yang anda tidak mahu
                                sahkan.
                            </p>
                        </div>

                        {changes.length === 0 ? (
                            <p className="p-8 text-center text-sm text-muted-foreground">
                                Tiada perubahan — semua pelajar sudah berada di
                                kelas yang sesuai.
                            </p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-10" />
                                        <TableHead>Pelajar</TableHead>
                                        <TableHead>Tindakan</TableHead>
                                        <TableHead>Kelas</TableHead>
                                        <TableHead>Sebab</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {changes.map((item, i) => {
                                        const meta = actionMeta[item.action];
                                        const isExcluded = excluded.includes(
                                            item.id,
                                        );

                                        return (
                                            <motion.tr
                                                key={item.id}
                                                initial={{ opacity: 0 }}
                                                animate={{ opacity: 1 }}
                                                transition={{
                                                    duration: 0.15,
                                                    delay: Math.min(
                                                        i * 0.01,
                                                        0.3,
                                                    ),
                                                }}
                                                className={cn(
                                                    'border-b transition-colors hover:bg-muted/50',
                                                    isExcluded && 'opacity-40',
                                                )}
                                            >
                                                <TableCell>
                                                    <Checkbox
                                                        checked={!isExcluded}
                                                        onCheckedChange={() =>
                                                            toggle(item.id)
                                                        }
                                                        aria-label={`Sahkan ${item.pelajar}`}
                                                    />
                                                </TableCell>
                                                <TableCell>
                                                    <div className="font-medium">
                                                        {item.pelajar}
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        Tahun {item.tahun}
                                                    </div>
                                                </TableCell>
                                                <TableCell>
                                                    <span
                                                        className={cn(
                                                            'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium',
                                                            meta.tone,
                                                        )}
                                                    >
                                                        <meta.icon className="size-3" />
                                                        {meta.label}
                                                    </span>
                                                </TableCell>
                                                <TableCell className="text-sm whitespace-nowrap">
                                                    {item.dari ? (
                                                        <span className="text-muted-foreground">
                                                            {item.dari}{' '}
                                                            <MoveRight className="inline size-3" />{' '}
                                                        </span>
                                                    ) : null}
                                                    <span className="font-semibold">
                                                        {item.ke}
                                                    </span>
                                                </TableCell>
                                                <TableCell className="max-w-md">
                                                    <p
                                                        className={cn(
                                                            'text-xs leading-snug',
                                                            item.ada_pertukaran
                                                                ? 'text-foreground'
                                                                : 'text-muted-foreground',
                                                        )}
                                                    >
                                                        {item.sebab}
                                                    </p>
                                                    {item.ada_pertukaran && (
                                                        <Badge
                                                            variant="outline"
                                                            className="mt-1 text-[0.65rem]"
                                                        >
                                                            Ada pertukaran nilai
                                                        </Badge>
                                                    )}
                                                </TableCell>
                                            </motion.tr>
                                        );
                                    })}
                                </TableBody>
                            </Table>
                        )}
                    </Card>

                    <Card className="h-fit gap-0 p-4">
                        <h2 className="mb-3 text-base font-semibold">
                            Kesan pada setiap kelas
                        </h2>
                        <ul className="space-y-2 text-sm">
                            {ringkasan.map((row) => (
                                <li
                                    key={row.kelas}
                                    className="flex items-center justify-between gap-2"
                                >
                                    <span className="font-medium">
                                        {row.kelas}
                                    </span>
                                    <span className="text-xs text-muted-foreground">
                                        {row.baharu > 0 && (
                                            <span className="text-foreground">
                                                +{row.baharu} baharu{' '}
                                            </span>
                                        )}
                                        {row.masuk > 0 && (
                                            <span>+{row.masuk} masuk </span>
                                        )}
                                        <span className="tabular-nums">
                                            {row.jumlah} pelajar
                                        </span>
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </Card>
                </div>
            </div>
        </>
    );
}

Preview.layout = {
    breadcrumbs: [
        { title: 'Auto Assign', href: '/auto-assign' },
        { title: 'Cadangan', href: '#' },
    ],
};
