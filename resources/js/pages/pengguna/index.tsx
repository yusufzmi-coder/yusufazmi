import { Head, router, useForm, usePage } from '@inertiajs/react';
import { ShieldCheck, Trash2, UserPlus } from 'lucide-react';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Person {
    id: number;
    nama: string;
    emel: string;
    peranan: string[];
    disahkan: boolean;
    dijemput: string | null;
}

interface Props {
    [key: string]: unknown;
    pengguna: Person[];
    peranan: string[];
}

export default function PenggunaIndex() {
    const { pengguna, peranan } = usePage<Props>().props;
    const form = useForm({ name: '', email: '', role: peranan[0] ?? 'Admin' });

    const invite = (e: React.FormEvent) => {
        e.preventDefault();
        form.post('/pengguna', {
            preserveScroll: true,
            onSuccess: () => form.reset('name', 'email'),
        });
    };

    return (
        <>
            <Head title="Pengguna" />
            <div className="flex flex-col gap-5 p-4 md:p-6">
                <PageHeader
                    title="Pengguna"
                    subtitle="Google Sign-In menolak sesiapa yang tiada dalam senarai ini — inilah satu-satunya pintu masuk."
                />

                <div className="grid gap-5 lg:grid-cols-[20rem_minmax(0,1fr)]">
                    <Card className="h-fit gap-0 p-4">
                        <h2 className="mb-3 flex items-center gap-2 text-base font-semibold">
                            <UserPlus className="size-4" /> Jemput pengguna
                        </h2>

                        <form onSubmit={invite} className="space-y-3">
                            <div>
                                <Label htmlFor="name" className="text-xs">
                                    Nama
                                </Label>
                                <Input
                                    id="name"
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData('name', e.target.value)
                                    }
                                    className="mt-1"
                                />
                                {form.errors.name && (
                                    <p className="mt-1 text-xs text-destructive">
                                        {form.errors.name}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="email" className="text-xs">
                                    Emel Google
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={form.data.email}
                                    onChange={(e) =>
                                        form.setData('email', e.target.value)
                                    }
                                    className="mt-1"
                                />
                                {form.errors.email && (
                                    <p className="mt-1 text-xs text-destructive">
                                        {form.errors.email}
                                    </p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="role" className="text-xs">
                                    Peranan
                                </Label>
                                <select
                                    id="role"
                                    value={form.data.role}
                                    onChange={(e) =>
                                        form.setData('role', e.target.value)
                                    }
                                    className="mt-1 h-9 w-full rounded-md border border-input bg-transparent px-2 text-sm"
                                >
                                    {peranan.map((r) => (
                                        <option key={r} value={r}>
                                            {r}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <Button
                                type="submit"
                                disabled={form.processing}
                                className="w-full"
                            >
                                {form.processing ? 'Menjemput…' : 'Jemput'}
                            </Button>
                        </form>
                    </Card>

                    <Card className="gap-0 p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Emel</TableHead>
                                    <TableHead>Peranan</TableHead>
                                    <TableHead>Dijemput</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pengguna.map((p) => (
                                    <TableRow key={p.id}>
                                        <TableCell className="font-medium">
                                            {p.nama}
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {p.emel}
                                        </TableCell>
                                        <TableCell>
                                            {p.peranan.map((r) => (
                                                <Badge
                                                    key={r}
                                                    variant={
                                                        r === 'Super Admin'
                                                            ? 'default'
                                                            : 'secondary'
                                                    }
                                                >
                                                    {r === 'Super Admin' && (
                                                        <ShieldCheck className="size-3" />
                                                    )}
                                                    {r}
                                                </Badge>
                                            ))}
                                        </TableCell>
                                        <TableCell className="text-xs text-muted-foreground">
                                            {p.dijemput}
                                        </TableCell>
                                        <TableCell>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() =>
                                                    router.delete(
                                                        `/pengguna/${p.id}`,
                                                        {
                                                            preserveScroll: true,
                                                        },
                                                    )
                                                }
                                            >
                                                <Trash2 className="size-4 text-destructive" />
                                            </Button>
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

PenggunaIndex.layout = {
    breadcrumbs: [{ title: 'Pengguna', href: '/pengguna' }],
};
