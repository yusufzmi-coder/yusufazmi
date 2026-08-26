import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';

interface Props {
    [key: string]: unknown;
    bilik: {
        id: number;
        name: string;
        capacity: number;
        location: string | null;
        is_active: boolean;
    } | null;
}

export default function BilikForm() {
    const { bilik } = usePage<Props>().props;
    const editing = bilik !== null;

    const form = useForm({
        name: bilik?.name ?? '',
        capacity: bilik?.capacity ?? 30,
        location: bilik?.location ?? '',
        is_active: bilik?.is_active ?? true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        editing ? form.put(`/bilik/${bilik.id}`) : form.post('/bilik');
    };

    return (
        <>
            <Head title={editing ? `Edit ${bilik.name}` : 'Tambah Bilik'} />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <Button
                        asChild
                        variant="ghost"
                        size="sm"
                        className="mb-1 -ml-2"
                    >
                        <Link href="/bilik">
                            <ArrowLeft className="size-4" /> Kembali
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">
                        {editing ? `Edit ${bilik.name}` : 'Tambah Bilik'}
                    </h1>
                </header>

                <form onSubmit={submit} className="grid max-w-xl gap-5">
                    <Card className="gap-0 p-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="name"
                                label="Nama Bilik"
                                required
                                error={form.errors.name}
                            >
                                <Input
                                    id="name"
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData('name', e.target.value)
                                    }
                                    placeholder="Bilik 1"
                                />
                            </FormField>

                            <FormField
                                id="capacity"
                                label="Kapasiti"
                                required
                                error={form.errors.capacity}
                                hint="Had fizikal — dikuatkuasakan walaupun peraturan had kelas dimatikan."
                            >
                                <Input
                                    id="capacity"
                                    type="number"
                                    min={1}
                                    max={200}
                                    value={form.data.capacity}
                                    onChange={(e) =>
                                        form.setData(
                                            'capacity',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id="location"
                                label="Lokasi"
                                error={form.errors.location}
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="location"
                                    value={form.data.location}
                                    onChange={(e) =>
                                        form.setData('location', e.target.value)
                                    }
                                    placeholder="Aras 2"
                                />
                            </FormField>
                        </div>

                        <div className="mt-4 flex items-center gap-3">
                            <Switch
                                id="is_active"
                                checked={form.data.is_active}
                                onCheckedChange={(v: boolean) =>
                                    form.setData('is_active', v)
                                }
                            />
                            <label htmlFor="is_active" className="text-sm">
                                Bilik aktif
                            </label>
                        </div>
                    </Card>

                    <div className="flex gap-2">
                        <Button type="submit" disabled={form.processing}>
                            {form.processing
                                ? 'Menyimpan…'
                                : editing
                                  ? 'Simpan perubahan'
                                  : 'Tambah bilik'}
                        </Button>
                        <Button asChild variant="ghost">
                            <Link href="/bilik">Batal</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

BilikForm.layout = {
    breadcrumbs: [
        { title: 'Bilik', href: '/bilik' },
        { title: 'Borang', href: '#' },
    ],
};
