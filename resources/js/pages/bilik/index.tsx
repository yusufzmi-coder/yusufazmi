import { Head, Link, usePage } from '@inertiajs/react';
import { Pencil, Plus } from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { DeleteButton } from '@/components/delete-button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Room {
    id: number;
    nama: string;
    kapasiti: number;
    lokasi: string | null;
    pertemuan: number;
    aktif: boolean;
}

interface Props {
    [key: string]: unknown;
    bilik: Room[];
}

export default function BilikIndex() {
    const { bilik } = usePage<Props>().props;

    return (
        <>
            <Head title="Bilik" />
            <div className="flex flex-col gap-5 p-4 md:p-6">
                <PageHeader
                    title="Bilik"
                    subtitle="Kapasiti bilik ialah had fizikal — ia dikuatkuasakan walaupun peraturan had kelas dimatikan."
                >
                    <Button asChild>
                        <Link href="/bilik/create">
                            <Plus className="size-4" /> Tambah Bilik
                        </Link>
                    </Button>
                </PageHeader>
                <Card className="gap-0 p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kapasiti</TableHead>
                                <TableHead>Lokasi</TableHead>
                                <TableHead>Pertemuan seminggu</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {bilik.map((r) => (
                                <TableRow key={r.id}>
                                    <TableCell className="font-medium">
                                        {r.nama}
                                    </TableCell>
                                    <TableCell className="tabular-nums">
                                        {r.kapasiti}
                                    </TableCell>
                                    <TableCell className="text-sm text-muted-foreground">
                                        {r.lokasi ?? '—'}
                                    </TableCell>
                                    <TableCell className="tabular-nums">
                                        {r.pertemuan}
                                    </TableCell>
                                    <TableCell>
                                        {r.aktif ? (
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
                                            aria-label={`Edit ${r.nama}`}
                                        >
                                            <Link href={`/bilik/${r.id}/edit`}>
                                                <Pencil className="size-4" />
                                            </Link>
                                        </Button>
                                        <DeleteButton
                                            url={`/bilik/${r.id}`}
                                            title={`Buang ${r.nama}?`}
                                            description="Bilik yang masih digunakan dalam jadual tidak boleh dibuang."
                                        />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </Card>
            </div>
        </>
    );
}

BilikIndex.layout = { breadcrumbs: [{ title: 'Bilik', href: '/bilik' }] };
