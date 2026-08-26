import { Head, Link, usePage } from '@inertiajs/react';
import { CalendarClock, Pencil, Plus } from 'lucide-react';
import { Meter } from '@/components/meter';
import { PageHeader } from '@/components/page-header';
import { StreamDot } from '@/components/stream-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { DeleteButton } from '@/components/delete-button';

interface Kelas {
    id: number;
    nama: string;
    tahun: number;
    stream: string;
    guru: string | null;
    ketegasan: number | null;
    kapasiti: number;
    pelajar: number;
    pertemuan: string[];
    aktif: boolean;
}

interface Props {
    [key: string]: unknown;
    kelas: Kelas[];
}

export default function KelasIndex() {
    const { kelas } = usePage<Props>().props;

    return (
        <>
            <Head title="Kelas" />
            <div className="flex flex-col gap-5 p-4 md:p-6">
                <PageHeader
                    title="Kelas"
                    subtitle="Kelas tanpa slot dalam jadual tidak akan menerima pelajar daripada Auto Assign."
                >
                    <Button asChild>
                        <Link href="/kelas/create">
                            <Plus className="size-4" /> Tambah Kelas
                        </Link>
                    </Button>
                </PageHeader>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    {kelas.map((k) => (
                        <Card
                            key={k.id}
                            className="h-full gap-0 p-4 transition hover:border-primary/40"
                        >
                            <div className="flex items-start justify-between gap-2">
                                <Link
                                    href={`/jadual/kelas/${k.id}`}
                                    className="flex items-center gap-2"
                                >
                                    <StreamDot stream={k.stream} />
                                    <h2 className="text-base font-semibold hover:underline">
                                        {k.nama}
                                    </h2>
                                </Link>
                                <div className="flex items-center gap-1">
                                    {k.pertemuan.length === 0 && (
                                        <Badge variant="outline">
                                            Tiada slot
                                        </Badge>
                                    )}
                                    <Button
                                        asChild
                                        variant="ghost"
                                        size="sm"
                                        aria-label={`Edit ${k.nama}`}
                                    >
                                        <Link href={`/kelas/${k.id}/edit`}>
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <DeleteButton
                                        url={`/kelas/${k.id}`}
                                        title={`Buang kelas ${k.nama}?`}
                                        description="Kelas yang masih ada pelajar tidak boleh dibuang — pindahkan mereka dahulu."
                                    />
                                </div>
                            </div>

                            <p className="mt-1 text-sm text-muted-foreground">
                                {k.guru ?? 'Tiada guru'}
                            </p>

                            <div className="mt-3">
                                <div className="mb-1 flex justify-between text-xs text-muted-foreground">
                                    <span>Pelajar</span>
                                    <span className="tabular-nums">
                                        {k.pelajar} / {k.kapasiti || '—'}
                                    </span>
                                </div>
                                <Meter
                                    percent={
                                        k.kapasiti > 0
                                            ? Math.round(
                                                  (k.pelajar / k.kapasiti) *
                                                      100,
                                              )
                                            : 0
                                    }
                                />
                            </div>

                            <ul className="mt-3 space-y-1">
                                {k.pertemuan.map((m) => (
                                    <li
                                        key={m}
                                        className="flex items-center gap-1.5 text-[0.7rem] text-muted-foreground"
                                    >
                                        <CalendarClock className="size-3 shrink-0" />
                                        {m}
                                    </li>
                                ))}
                                {k.pertemuan.length === 0 && (
                                    <li className="text-[0.7rem] text-muted-foreground">
                                        Belum dimasukkan ke dalam jadual
                                        mingguan.
                                    </li>
                                )}
                            </ul>
                        </Card>
                    ))}
                </div>
            </div>
        </>
    );
}

KelasIndex.layout = { breadcrumbs: [{ title: 'Kelas', href: '/kelas' }] };
