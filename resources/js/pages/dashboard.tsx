import { Head, Link, router, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    Activity as ActivityIcon,
    ArrowRight,
    CalendarDays,
    CheckCircle2,
    DoorOpen,
    Play,
    Plus,
    Sparkles,
    Users,
    Users2,
} from 'lucide-react';
import { useState } from 'react';
import { StatCard } from '@/components/stat-card';
import { StudentDistribution } from '@/components/student-distribution';
import { TimetableGrid } from '@/components/timetable-grid';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import type { DashboardStats, Grid, RuleSummary } from '@/types/jadual';

interface Props {
    [key: string]: unknown;
    stats: DashboardStats;
    grid: Grid;
    rules: RuleSummary[];
    distribution: { tahun: number; jumlah: number }[];
    activities: {
        id: number;
        description: string;
        log_name: string;
        when: string | null;
    }[];
}

const checklist = [
    'Semak kekangan & rules',
    'Cari slot kelas kosong',
    'Assign pelajar',
    'Sahkan penjadualan',
];

export default function Dashboard() {
    const { stats, grid, rules, distribution, activities } =
        usePage<Props>().props;
    const [running, setRunning] = useState(false);

    const runAutoAssign = () => {
        setRunning(true);
        router.post(
            '/auto-assign',
            { mode: 'fill_only' },
            { onFinish: () => setRunning(false), preserveScroll: true },
        );
    };

    const toggleRule = (rule: RuleSummary, next: boolean) => {
        router.patch(
            `/peraturan/${rule.id}`,
            { is_active: next },
            { preserveScroll: true, preserveState: true },
        );
    };

    return (
        <>
            <Head title="Dashboard" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Selamat datang, Admin!
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Sistem penjadualan kelas automatik untuk pelajar
                    </p>
                </header>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <StatCard
                        index={0}
                        label="Jumlah Pelajar"
                        value={stats.pelajar.jumlah}
                        icon={Users}
                        tone="indigo"
                        breakdown={[
                            { label: 'Aktif', value: stats.pelajar.aktif },
                            {
                                label: 'Tidak Aktif',
                                value: stats.pelajar.tidak_aktif,
                            },
                        ]}
                    />
                    <StatCard
                        index={1}
                        label="Jumlah Kelas"
                        value={stats.kelas.jumlah}
                        icon={CalendarDays}
                        tone="green"
                        breakdown={[
                            { label: 'Terisi', value: stats.kelas.terisi },
                            { label: 'Kosong', value: stats.kelas.kosong },
                        ]}
                    />
                    <StatCard
                        index={2}
                        label="Jumlah Guru"
                        value={stats.guru.jumlah}
                        icon={Users2}
                        tone="amber"
                        breakdown={[
                            { label: 'Aktif', value: stats.guru.aktif },
                            {
                                label: 'Tidak Aktif',
                                value: stats.guru.tidak_aktif,
                            },
                        ]}
                    />
                    <StatCard
                        index={3}
                        label="Jumlah Bilik"
                        value={stats.bilik.jumlah}
                        icon={DoorOpen}
                        tone="rose"
                        breakdown={[
                            { label: 'Tersedia', value: stats.bilik.tersedia },
                            {
                                label: 'Dalam Guna',
                                value: stats.bilik.dalam_guna,
                            },
                        ]}
                    />
                </div>

                <div className="grid gap-5 xl:grid-cols-[minmax(0,1fr)_20rem]">
                    <Card className="gap-0 p-4">
                        <div className="mb-3 flex flex-wrap items-center justify-between gap-3">
                            <h2 className="text-base font-semibold">
                                Jadual Kelas — Minggu Ini
                            </h2>
                            <Button asChild variant="ghost" size="sm">
                                <Link href="/jadual">
                                    Lihat penuh{' '}
                                    <ArrowRight className="size-4" />
                                </Link>
                            </Button>
                        </div>
                        <TimetableGrid grid={grid} />
                    </Card>

                    <div className="flex flex-col gap-5">
                        <Card className="gap-0 p-4">
                            <div className="mb-2 flex items-center gap-2">
                                <Sparkles className="size-4 text-primary" />
                                <h2 className="text-base font-semibold">
                                    Auto Assign
                                </h2>
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Letakkan pelajar dalam kelas secara automatik
                            </p>

                            <ul className="my-4 space-y-2">
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

                            <Button
                                onClick={runAutoAssign}
                                disabled={running}
                                className="w-full"
                            >
                                <Play className="size-4" />
                                {running
                                    ? 'Sedang dijalankan…'
                                    : 'Jalankan Auto Assign'}
                            </Button>
                            <p className="mt-2 text-center text-[0.7rem] text-muted-foreground">
                                Anda akan lihat cadangan dahulu sebelum ia
                                disimpan.
                            </p>
                        </Card>

                        <Card className="gap-0 p-4">
                            <div className="mb-3 flex items-center justify-between gap-2">
                                <h2 className="text-base font-semibold">
                                    Peraturan (Rules)
                                </h2>
                                <Button asChild variant="outline" size="sm">
                                    <Link href="/peraturan">
                                        <Plus className="size-3.5" /> Tambah
                                    </Link>
                                </Button>
                            </div>

                            <ul className="space-y-3">
                                {rules.map((rule) => (
                                    <li
                                        key={rule.id}
                                        className="flex items-start gap-3"
                                    >
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm font-medium">
                                                {rule.name}
                                            </p>
                                            <p className="text-xs leading-snug text-muted-foreground">
                                                {rule.description}
                                            </p>
                                        </div>
                                        <Switch
                                            checked={rule.is_active}
                                            onCheckedChange={(next: boolean) =>
                                                toggleRule(rule, next)
                                            }
                                            aria-label={`Hidupkan ${rule.name}`}
                                        />
                                    </li>
                                ))}
                            </ul>

                            <Button
                                asChild
                                variant="ghost"
                                size="sm"
                                className="mt-3 w-full"
                            >
                                <Link href="/peraturan">
                                    Lihat semua rules{' '}
                                    <ArrowRight className="size-4" />
                                </Link>
                            </Button>
                        </Card>
                    </div>
                </div>

                <div className="grid gap-5 lg:grid-cols-2">
                    <Card className="gap-0 p-4">
                        <h2 className="mb-3 text-base font-semibold">
                            Aktiviti Terkini
                        </h2>
                        {activities.length === 0 ? (
                            <p className="py-6 text-center text-sm text-muted-foreground">
                                Belum ada aktiviti direkodkan.
                            </p>
                        ) : (
                            <ul className="space-y-3">
                                {activities.map((activity, i) => (
                                    <motion.li
                                        key={activity.id}
                                        initial={{ opacity: 0, x: -4 }}
                                        animate={{ opacity: 1, x: 0 }}
                                        transition={{
                                            duration: 0.2,
                                            delay: i * 0.04,
                                        }}
                                        className="flex items-center justify-between gap-3 text-sm"
                                    >
                                        <span className="flex min-w-0 items-center gap-2">
                                            <ActivityIcon className="size-4 shrink-0 text-muted-foreground" />
                                            <span className="truncate">
                                                {activity.description}
                                            </span>
                                        </span>
                                        <span className="shrink-0 text-xs text-muted-foreground">
                                            {activity.when}
                                        </span>
                                    </motion.li>
                                ))}
                            </ul>
                        )}
                    </Card>

                    <Card className="gap-0 p-4">
                        <h2 className="mb-1 text-base font-semibold">
                            Taburan Pelajar
                        </h2>
                        <StudentDistribution data={distribution} />
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }],
};
