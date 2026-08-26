import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { FormField } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/lib/utils';

interface Props {
    [key: string]: unknown;
    guru: {
        id: number;
        name: string;
        email: string | null;
        phone: string | null;
        firmness: number;
        max_classes: number;
        is_active: boolean;
        notes: string | null;
    } | null;
}

const FIRMNESS = [
    { value: 1, label: 'Sangat Lembut' },
    { value: 2, label: 'Lembut' },
    { value: 3, label: 'Sederhana' },
    { value: 4, label: 'Tegas' },
    { value: 5, label: 'Sangat Tegas' },
];

export default function GuruForm() {
    const { guru } = usePage<Props>().props;
    const editing = guru !== null;

    const form = useForm({
        name: guru?.name ?? '',
        email: guru?.email ?? '',
        phone: guru?.phone ?? '',
        firmness: guru?.firmness ?? 3,
        max_classes: guru?.max_classes ?? 6,
        is_active: guru?.is_active ?? true,
        notes: guru?.notes ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        editing ? form.put(`/guru/${guru.id}`) : form.post('/guru');
    };

    return (
        <>
            <Head title={editing ? `Edit ${guru.name}` : 'Tambah Guru'} />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <Button
                        asChild
                        variant="ghost"
                        size="sm"
                        className="mb-1 -ml-2"
                    >
                        <Link href="/guru">
                            <ArrowLeft className="size-4" /> Kembali
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">
                        {editing ? `Edit ${guru.name}` : 'Tambah Guru'}
                    </h1>
                </header>

                <form onSubmit={submit} className="grid max-w-2xl gap-5">
                    <Card className="gap-0 p-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="name"
                                label="Nama"
                                required
                                error={form.errors.name}
                                className="sm:col-span-2"
                            >
                                <Input
                                    id="name"
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData('name', e.target.value)
                                    }
                                    placeholder="Cikgu Farah"
                                />
                            </FormField>

                            <FormField
                                id="email"
                                label="Emel"
                                error={form.errors.email}
                            >
                                <Input
                                    id="email"
                                    type="email"
                                    value={form.data.email}
                                    onChange={(e) =>
                                        form.setData('email', e.target.value)
                                    }
                                />
                            </FormField>

                            <FormField
                                id="phone"
                                label="Telefon"
                                error={form.errors.phone}
                            >
                                <Input
                                    id="phone"
                                    value={form.data.phone}
                                    onChange={(e) =>
                                        form.setData('phone', e.target.value)
                                    }
                                />
                            </FormField>

                            <FormField
                                id="max_classes"
                                label="Had Kelas"
                                required
                                error={form.errors.max_classes}
                            >
                                <Input
                                    id="max_classes"
                                    type="number"
                                    min={1}
                                    max={20}
                                    value={form.data.max_classes}
                                    onChange={(e) =>
                                        form.setData(
                                            'max_classes',
                                            Number(e.target.value),
                                        )
                                    }
                                />
                            </FormField>
                        </div>
                    </Card>

                    <Card className="gap-0 p-4">
                        <h2 className="text-base font-semibold">Ketegasan</h2>
                        <p className="mb-4 text-xs text-muted-foreground">
                            Pelajar yang ditanda bermasalah akan diutamakan
                            untuk guru yang lebih tegas. Skala digunakan (bukan
                            ya/tidak) supaya sistem tahu siapa paling sesuai
                            apabila guru tegas terhad.
                        </p>

                        <div className="grid gap-2 sm:grid-cols-5">
                            {FIRMNESS.map((f) => (
                                <button
                                    key={f.value}
                                    type="button"
                                    onClick={() =>
                                        form.setData('firmness', f.value)
                                    }
                                    className={cn(
                                        'rounded-lg border p-2 text-center text-xs transition',
                                        form.data.firmness === f.value
                                            ? 'border-primary bg-accent font-semibold'
                                            : 'hover:border-primary/40',
                                    )}
                                >
                                    <span className="mb-1 flex justify-center gap-0.5">
                                        {[1, 2, 3, 4, 5].map((i) => (
                                            <span
                                                key={i}
                                                className={cn(
                                                    'size-1.5 rounded-full',
                                                    i <= f.value
                                                        ? 'bg-[var(--primary)]'
                                                        : 'bg-muted',
                                                )}
                                            />
                                        ))}
                                    </span>
                                    {f.label}
                                </button>
                            ))}
                        </div>
                        {form.errors.firmness && (
                            <p className="mt-2 text-xs text-destructive">
                                {form.errors.firmness}
                            </p>
                        )}

                        <div className="mt-4 flex items-center gap-3">
                            <Switch
                                id="is_active"
                                checked={form.data.is_active}
                                onCheckedChange={(v: boolean) =>
                                    form.setData('is_active', v)
                                }
                            />
                            <label htmlFor="is_active" className="text-sm">
                                Guru aktif
                            </label>
                        </div>
                    </Card>

                    <div className="flex gap-2">
                        <Button type="submit" disabled={form.processing}>
                            {form.processing
                                ? 'Menyimpan…'
                                : editing
                                  ? 'Simpan perubahan'
                                  : 'Tambah guru'}
                        </Button>
                        <Button asChild variant="ghost">
                            <Link href="/guru">Batal</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

GuruForm.layout = {
    breadcrumbs: [
        { title: 'Guru', href: '/guru' },
        { title: 'Borang', href: '#' },
    ],
};
