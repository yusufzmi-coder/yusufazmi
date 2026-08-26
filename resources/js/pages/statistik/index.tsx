import { Head, usePage } from '@inertiajs/react';
import { FileDown } from 'lucide-react';
import { Meter } from '@/components/meter';
import { PageHeader } from '@/components/page-header';
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
        nama: string;
        guru: string | null;
        pelajar: number;
        kapasiti: number;
        peratus: number;
        lelaki: number;
        perempuan: number;
    }[];
    guru: {
        nama: string;
        kelas: number;
        had: number;
        ketegasan: number;
        ketegasan_label: string;
    }[];
    bilik: {
        nama: string;
        kapasiti: number;
        pertemuan: number;
        peratus: number;
    }[];
}

export default function StatistikIndex() {
    const { kelas, guru, bilik } = usePage<Props>().props;

    return (
        <>
            <Head title="Statistik" />
            <div className="flex flex-col gap-5 p-4 md:p-6">
                <PageHeader
                    title="Statistik"
                    subtitle="Beban kelas, beban guru dan penggunaan bilik."
                />

                <Card className="gap-0 p-0">
                    <div className="border-b p-4">
                        <h2 className="text-base font-semibold">
                            Beban & imbangan setiap kelas
                        </h2>
                    </div>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Kelas</TableHead>
                                <TableHead>Guru</TableHead>
                                <TableHead>Penggunaan</TableHead>
                                <TableHead>Lelaki / Perempuan</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {kelas.map((k) => (
                                <TableRow key={k.nama}>
                                    <TableCell className="font-medium">
                                        {k.nama}
                                    </TableCell>
                                    <TableCell className="text-sm text-muted-foreground">
                                        {k.guru ?? '—'}
                                    </TableCell>
                                    <TableCell className="w-56">
                                        <div className="mb-1 text-xs text-muted-foreground tabular-nums">
                                            {k.pelajar} / {k.kapasiti || '—'}
                                        </div>
                                        <Meter percent={k.peratus} />
                                    </TableCell>
                                    <TableCell className="text-sm tabular-nums">
                                        {k.lelaki} L / {k.perempuan} P
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>

                <div className="grid gap-5 lg:grid-cols-2">
                    <Card className="gap-0 p-0">
                        <div className="border-b p-4">
                            <h2 className="text-base font-semibold">
                                Beban guru
                            </h2>
                        </div>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Guru</TableHead>
                                    <TableHead>Ketegasan</TableHead>
                                    <TableHead>Kelas</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {guru.map((t) => (
                                    <TableRow key={t.nama}>
                                        <TableCell className="font-medium">
                                            {t.nama}
                                        </TableCell>
                                        <TableCell className="text-xs text-muted-foreground">
                                            {t.ketegasan_label}
                                        </TableCell>
                                        <TableCell className="tabular-nums">
                                            {t.kelas} / {t.had}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Card>

                    <Card className="gap-0 p-0">
                        <div className="border-b p-4">
                            <h2 className="text-base font-semibold">
                                Penggunaan bilik
                            </h2>
                        </div>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Bilik</TableHead>
                                    <TableHead>Kapasiti</TableHead>
                                    <TableHead>Penggunaan mingguan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {bilik.map((r) => (
                                    <TableRow key={r.nama}>
                                        <TableCell className="font-medium">
                                            {r.nama}
                                        </TableCell>
                                        <TableCell className="tabular-nums">
                                            {r.kapasiti}
                                        </TableCell>
                                        <TableCell className="w-48">
                                            <Meter percent={r.peratus} />
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Card>
                </div>
            </div>
        </>
    );
}

StatistikIndex.layout = {
    breadcrumbs: [{ title: 'Statistik', href: '/statistik' }],
};
