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

interface Teacher {
    id: number;
    nama: string;
    emel: string | null;
    telefon: string | null;
    ketegasan: number;
    ketegasan_label: string;
    kelas: number;
    had_kelas: number;
    aktif: boolean;
}

interface Props {
    [key: string]: unknown;
    guru: Teacher[];
}

function Firmness({ level, label }: { level: number; label: string }) {
    return (
        <span className="inline-flex items-center gap-1.5" title={label}>
            <span className="flex gap-0.5">
                {[1, 2, 3, 4, 5].map((i) => (
                    <span
                        key={i}
                        className={`size-1.5 rounded-full ${i <= level ? 'bg-[var(--primary)]' : 'bg-muted'}`}
                    />
                ))}
            </span>
            <span className="text-xs text-muted-foreground">{label}</span>
        </span>
    );
}

export default function GuruIndex() {
    const { guru } = usePage<Props>().props;

    return (
        <>
            <Head title="Guru" />
            <div className="flex flex-col gap-5 p-4 md:p-6">
                <PageHeader
                    title="Guru"
                    subtitle="Ketegasan mempengaruhi ke mana pelajar bermasalah ditempatkan."
                >
                    <Button asChild>
                        <Link href="/guru/create">
                            <Plus className="size-4" /> Tambah Guru
                        </Link>
                    </Button>
                </PageHeader>
                <Card className="gap-0 p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Ketegasan</TableHead>
                                <TableHead>Kelas</TableHead>
                                <TableHead>Hubungi</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {guru.map((t) => (
                                <TableRow key={t.id}>
                                    <TableCell className="font-medium">
                                        {t.nama}
                                    </TableCell>
                                    <TableCell>
                                        <Firmness
                                            level={t.ketegasan}
                                            label={t.ketegasan_label}
                                        />
                                    </TableCell>
                                    <TableCell className="tabular-nums">
                                        {t.kelas}
                                        <span className="text-muted-foreground">
                                            {' '}
                                            / {t.had_kelas}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-xs text-muted-foreground">
                                        {t.emel ?? '—'}
                                        {t.telefon && <div>{t.telefon}</div>}
                                    </TableCell>
                                    <TableCell>
                                        {t.aktif ? (
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
                                            aria-label={`Edit ${t.nama}`}
                                        >
                                            <Link href={`/guru/${t.id}/edit`}>
                                                <Pencil className="size-4" />
                                            </Link>
                                        </Button>
                                        <DeleteButton
                                            url={`/guru/${t.id}`}
                                            title={`Buang ${t.nama}?`}
                                            description="Guru yang masih mengajar kelas tidak boleh dibuang — tukar guru kelas itu dahulu."
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

GuruIndex.layout = { breadcrumbs: [{ title: 'Guru', href: '/guru' }] };
