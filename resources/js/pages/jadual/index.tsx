import { Head, usePage } from '@inertiajs/react';
import { FileDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { StreamDot } from '@/components/stream-badge';
import { TimetableGrid } from '@/components/timetable-grid';
import type { Grid } from '@/types/jadual';

interface Props {
    [key: string]: unknown;
    grid: Grid;
    sesi: string;
}

export default function JadualIndex() {
    const { grid, sesi } = usePage<Props>().props;

    return (
        <>
            <Head title="Jadual Kelas" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header className="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">
                            Jadual Kelas
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Sesi {sesi} · klik mana-mana kelas untuk lihat
                            senarai pelajar
                        </p>
                    </div>

                    <div className="flex items-center gap-4 text-xs text-muted-foreground">
                        {/* A plain anchor, not Inertia: this is a file download. */}
                        <Button asChild variant="outline" size="sm">
                            <a href="/eksport/jadual.pdf">
                                <FileDown className="size-4" /> PDF
                            </a>
                        </Button>
                        {(['ALPHA', 'BETA', 'GAMMA'] as const).map((stream) => (
                            <span
                                key={stream}
                                className="inline-flex items-center gap-1.5"
                            >
                                <StreamDot stream={stream} />
                                {stream}
                            </span>
                        ))}
                    </div>
                </header>

                <Card className="gap-0 p-4">
                    <TimetableGrid grid={grid} />
                </Card>
            </div>
        </>
    );
}

JadualIndex.layout = {
    breadcrumbs: [{ title: 'Jadual Kelas', href: '/jadual' }],
};
