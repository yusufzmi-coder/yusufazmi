import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, Users } from 'lucide-react';
import { useState } from 'react';
import { FormField, NativeSelect } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';

interface Option {
    value: string | number;
    label: string;
}

interface Props {
    [key: string]: unknown;
    pelajar: {
        id: number;
        student_code: string;
        name: string;
        gender: string;
        year_level: number;
        behaviour_level: number;
        date_of_birth: string | null;
        national_id: string | null;
        phone: string | null;
        special_needs: string | null;
        is_active: boolean;
        enrolled_on: string;
        family_id: number | null;
    } | null;
    keluarga: { id: number; nama: string; polisi: string }[];
    pilihan: { jantina: Option[]; tingkah_laku: Option[] };
}

export default function PelajarForm() {
    const { pelajar, keluarga, pilihan } = usePage<Props>().props;
    const editing = pelajar !== null;

    const [newFamily, setNewFamily] = useState(false);

    const form = useForm({
        student_code: pelajar?.student_code ?? '',
        name: pelajar?.name ?? '',
        gender: pelajar?.gender ?? 'L',
        year_level: pelajar?.year_level ?? 1,
        behaviour_level: pelajar?.behaviour_level ?? 0,
        date_of_birth: pelajar?.date_of_birth ?? '',
        national_id: pelajar?.national_id ?? '',
        phone: pelajar?.phone ?? '',
        special_needs: pelajar?.special_needs ?? '',
        is_active: pelajar?.is_active ?? true,
        enrolled_on:
            pelajar?.enrolled_on ?? new Date().toISOString().slice(0, 10),
        family_id: pelajar?.family_id ?? '',
        family_name: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editing) {
            form.put(`/pelajar/${pelajar.id}`);
        } else {
            form.post('/pelajar');
        }
    };

    return (
        <>
            <Head title={editing ? `Edit ${pelajar.name}` : 'Tambah Pelajar'} />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <Button
                        asChild
                        variant="ghost"
                        size="sm"
                        className="mb-1 -ml-2"
                    >
                        <Link href="/pelajar">
                            <ArrowLeft className="size-4" /> Kembali
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">
                        {editing ? `Edit ${pelajar.name}` : 'Tambah Pelajar'}
                    </h1>
                </header>

                <form onSubmit={submit} className="grid max-w-4xl gap-5">
                    <Card className="gap-0 p-4">
                        <h2 className="mb-4 text-base font-semibold">
                            Maklumat asas
                        </h2>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="student_code"
                                label="Kod Pelajar"
                                required
                                error={form.errors.student_code}
                            >
                                <Input
                                    id="student_code"
                                    value={form.data.student_code}
                                    onChange={(e) =>
                                        form.setData(
                                            'student_code',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="P-0001"
                                />
                            </FormField>

                            <FormField
                                id="name"
                                label="Nama Penuh"
                                required
                                error={form.errors.name}
                            >
                                <Input
                                    id="name"
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData('name', e.target.value)
                                    }
                                />
                            </FormField>

                            <FormField
                                id="gender"
                                label="Jantina"
                                required
                                error={form.errors.gender}
                            >
                                <NativeSelect
                                    id="gender"
                                    value={form.data.gender}
                                    onChange={(e) =>
                                        form.setData('gender', e.target.value)
                                    }
                                >
                                    {pilihan.jantina.map((o) => (
                                        <option key={o.value} value={o.value}>
                                            {o.label}
                                        </option>
                                    ))}
                                </NativeSelect>
                            </FormField>

                            <FormField
                                id="year_level"
                                label="Tahun"
                                required
                                error={form.errors.year_level}
                            >
                                <NativeSelect
                                    id="year_level"
                                    value={form.data.year_level}
                                    onChange={(e) =>
                                        form.setData(
                                            'year_level',
                                            Number(e.target.value),
                                        )
                                    }
                                >
                                    {[1, 2, 3, 4, 5, 6].map((y) => (
                                        <option key={y} value={y}>
                                            Tahun {y}
                                        </option>
                                    ))}
                                </NativeSelect>
                            </FormField>

                            <FormField
                                id="behaviour_level"
                                label="Tahap Tingkah Laku"
                                error={form.errors.behaviour_level}
                                hint="Menentukan sama ada pelajar diutamakan untuk kelas cikgu tegas."
                            >
                                <NativeSelect
                                    id="behaviour_level"
                                    value={form.data.behaviour_level}
                                    onChange={(e) =>
                                        form.setData(
                                            'behaviour_level',
                                            Number(e.target.value),
                                        )
                                    }
                                >
                                    {pilihan.tingkah_laku.map((o) => (
                                        <option key={o.value} value={o.value}>
                                            {o.label}
                                        </option>
                                    ))}
                                </NativeSelect>
                            </FormField>

                            <FormField
                                id="enrolled_on"
                                label="Tarikh Daftar"
                                required
                                error={form.errors.enrolled_on}
                            >
                                <Input
                                    id="enrolled_on"
                                    type="date"
                                    value={form.data.enrolled_on}
                                    onChange={(e) =>
                                        form.setData(
                                            'enrolled_on',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>
                        </div>
                    </Card>

                    <Card className="gap-0 p-4">
                        <h2 className="mb-1 flex items-center gap-2 text-base font-semibold">
                            <Users className="size-4" /> Keluarga
                        </h2>
                        <p className="mb-4 text-xs text-muted-foreground">
                            Adik-beradik dikenal pasti melalui keluarga yang
                            sama — bukan melalui penjaga yang dikongsi, kerana
                            penjaga selalunya pemandu van atau ejen.
                        </p>

                        {!newFamily ? (
                            <div className="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
                                <FormField
                                    id="family_id"
                                    label="Keluarga sedia ada"
                                    error={form.errors.family_id}
                                >
                                    <NativeSelect
                                        id="family_id"
                                        value={form.data.family_id}
                                        onChange={(e) =>
                                            form.setData(
                                                'family_id',
                                                e.target.value,
                                            )
                                        }
                                    >
                                        <option value="">
                                            Tiada / anak tunggal
                                        </option>
                                        {keluarga.map((f) => (
                                            <option key={f.id} value={f.id}>
                                                {f.nama} ({f.polisi})
                                            </option>
                                        ))}
                                    </NativeSelect>
                                </FormField>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => {
                                        setNewFamily(true);
                                        form.setData('family_id', '');
                                    }}
                                >
                                    Tambah adik-beradik
                                </Button>
                            </div>
                        ) : (
                            <div className="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
                                <FormField
                                    id="family_name"
                                    label="Nama keluarga baharu"
                                    error={form.errors.family_name}
                                    hint="Pelajar lain dengan nama keluarga yang sama akan dianggap adik-beradik."
                                >
                                    <Input
                                        id="family_name"
                                        value={form.data.family_name}
                                        onChange={(e) =>
                                            form.setData(
                                                'family_name',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Keluarga Abdullah"
                                    />
                                </FormField>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    onClick={() => setNewFamily(false)}
                                >
                                    Batal
                                </Button>
                            </div>
                        )}
                    </Card>

                    <Card className="gap-0 p-4">
                        <h2 className="mb-1 text-base font-semibold">
                            Maklumat peribadi
                        </h2>
                        <p className="mb-4 text-xs text-muted-foreground">
                            Medan ini adalah data peribadi (PDPA). No. kad
                            pengenalan disulitkan dan tidak boleh dicari.
                        </p>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField
                                id="date_of_birth"
                                label="Tarikh Lahir"
                                error={form.errors.date_of_birth}
                            >
                                <Input
                                    id="date_of_birth"
                                    type="date"
                                    value={form.data.date_of_birth}
                                    onChange={(e) =>
                                        form.setData(
                                            'date_of_birth',
                                            e.target.value,
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id="national_id"
                                label="No. Kad Pengenalan"
                                error={form.errors.national_id}
                            >
                                <Input
                                    id="national_id"
                                    value={form.data.national_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'national_id',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="070101-14-5555"
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
                                id="special_needs"
                                label="Keperluan Khas"
                                error={form.errors.special_needs}
                            >
                                <Input
                                    id="special_needs"
                                    value={form.data.special_needs}
                                    onChange={(e) =>
                                        form.setData(
                                            'special_needs',
                                            e.target.value,
                                        )
                                    }
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
                                Pelajar aktif
                                <span className="block text-xs text-muted-foreground">
                                    Hanya pelajar aktif diambil kira oleh Auto
                                    Assign.
                                </span>
                            </label>
                        </div>
                    </Card>

                    <div className="flex gap-2">
                        <Button type="submit" disabled={form.processing}>
                            {form.processing
                                ? 'Menyimpan…'
                                : editing
                                  ? 'Simpan perubahan'
                                  : 'Tambah pelajar'}
                        </Button>
                        <Button asChild variant="ghost">
                            <Link href="/pelajar">Batal</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

PelajarForm.layout = {
    breadcrumbs: [
        { title: 'Pelajar', href: '/pelajar' },
        { title: 'Borang', href: '#' },
    ],
};
