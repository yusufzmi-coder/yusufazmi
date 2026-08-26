import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { ArrowLeft, CalendarClock, Plus, X } from 'lucide-react';
import { FormField, NativeSelect } from '@/components/form-field';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';

interface Meeting {
    day: string;
    time_slot_id: number | string;
    room_id: number | string;
}

interface Props {
    [key: string]: unknown;
    kelas: {
        id: number;
        year_level: number;
        stream: string;
        teacher_id: number | null;
        capacity_override: number | null;
        is_active: boolean;
        notes: string | null;
        meetings: Meeting[];
    } | null;
    pilihan: {
        guru: { id: number; nama: string; ketegasan: string }[];
        bilik: { id: number; nama: string; kapasiti: number }[];
        slot: { id: number; label: string }[];
        hari: { value: string; label: string }[];
    };
}

export default function KelasForm() {
    const { kelas, pilihan } = usePage<Props>().props;
    const editing = kelas !== null;

    const form = useForm({
        year_level: kelas?.year_level ?? 1,
        stream: kelas?.stream ?? 'ALPHA',
        teacher_id: kelas?.teacher_id ?? '',
        capacity_override: kelas?.capacity_override ?? '',
        is_active: kelas?.is_active ?? true,
        notes: kelas?.notes ?? '',
        meetings: (kelas?.meetings ?? []) as Meeting[],
    });

    const addMeeting = () =>
        form.setData('meetings', [
            ...form.data.meetings,
            {
                day: pilihan.hari[0]?.value ?? 'isnin',
                time_slot_id: pilihan.slot[0]?.id ?? '',
                room_id: '',
            },
        ]);

    const updateMeeting = (index: number, patch: Partial<Meeting>) =>
        form.setData(
            'meetings',
            form.data.meetings.map((m, i) =>
                i === index ? { ...m, ...patch } : m,
            ),
        );

    const removeMeeting = (index: number) =>
        form.setData(
            'meetings',
            form.data.meetings.filter((_, i) => i !== index),
        );

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        editing ? form.put(`/kelas/${kelas.id}`) : form.post('/kelas');
    };

    // The "year + stream already exists" rule is hung off a synthetic field name on
    // the server, so it is not part of the typed form data.
    const streamCombinationError = (
        form.errors as Record<string, string | undefined>
    ).session_year_stream;

    const errorFor = (
        index: number,
        field: keyof Meeting,
    ): string | undefined =>
        form.errors[
            `meetings.${index}.${field}` as keyof typeof form.errors
        ] as string | undefined;

    return (
        <>
            <Head
                title={
                    editing
                        ? `Edit ${kelas.year_level} ${kelas.stream}`
                        : 'Tambah Kelas'
                }
            />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <Button
                        asChild
                        variant="ghost"
                        size="sm"
                        className="mb-1 -ml-2"
                    >
                        <Link href="/kelas">
                            <ArrowLeft className="size-4" /> Kembali
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-bold tracking-tight">
                        {editing
                            ? `Edit Kelas ${kelas.year_level} ${kelas.stream}`
                            : 'Tambah Kelas'}
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Nama kelas dijana automatik daripada tahun dan aliran,
                        contohnya “4 ALPHA”.
                    </p>
                </header>

                <form onSubmit={submit} className="grid max-w-3xl gap-5">
                    <Card className="gap-0 p-4">
                        <div className="grid gap-4 sm:grid-cols-2">
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
                                id="stream"
                                label="Aliran"
                                required
                                error={
                                    form.errors.stream ?? streamCombinationError
                                }
                                hint="Huruf besar sahaja, contohnya ALPHA."
                            >
                                <Input
                                    id="stream"
                                    value={form.data.stream}
                                    onChange={(e) =>
                                        form.setData(
                                            'stream',
                                            e.target.value.toUpperCase(),
                                        )
                                    }
                                />
                            </FormField>

                            <FormField
                                id="teacher_id"
                                label="Guru"
                                error={form.errors.teacher_id}
                                hint="Kelas tanpa guru tidak boleh menerima pelajar bermasalah."
                            >
                                <NativeSelect
                                    id="teacher_id"
                                    value={form.data.teacher_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'teacher_id',
                                            e.target.value,
                                        )
                                    }
                                >
                                    <option value="">Belum ditetapkan</option>
                                    {pilihan.guru.map((g) => (
                                        <option key={g.id} value={g.id}>
                                            {g.nama} — {g.ketegasan}
                                        </option>
                                    ))}
                                </NativeSelect>
                            </FormField>

                            <FormField
                                id="capacity_override"
                                label="Had kapasiti (pilihan)"
                                error={form.errors.capacity_override}
                                hint="Kosongkan untuk guna kapasiti bilik terkecil."
                            >
                                <Input
                                    id="capacity_override"
                                    type="number"
                                    min={1}
                                    max={200}
                                    value={form.data.capacity_override}
                                    onChange={(e) =>
                                        form.setData(
                                            'capacity_override',
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
                                Kelas aktif
                                <span className="block text-xs text-muted-foreground">
                                    Hanya kelas aktif menerima pelajar daripada
                                    Auto Assign.
                                </span>
                            </label>
                        </div>
                    </Card>

                    <Card className="gap-0 p-4">
                        <div className="mb-1 flex items-center justify-between gap-2">
                            <h2 className="flex items-center gap-2 text-base font-semibold">
                                <CalendarClock className="size-4" /> Waktu
                                pertemuan
                            </h2>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={addMeeting}
                            >
                                <Plus className="size-3.5" /> Tambah waktu
                            </Button>
                        </div>
                        <p className="mb-4 text-xs text-muted-foreground">
                            Satu kelas boleh bertemu beberapa kali seminggu.
                            Waktu REHAT tidak boleh dipilih. Sistem akan
                            menghalang pertembungan guru atau bilik.
                        </p>

                        {form.data.meetings.length === 0 ? (
                            <p className="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                                Belum ada waktu. Kelas tanpa waktu tidak akan
                                muncul dalam jadual dan tidak menerima pelajar.
                            </p>
                        ) : (
                            <ul className="space-y-2">
                                {form.data.meetings.map((meeting, index) => (
                                    <li
                                        key={index}
                                        className="grid gap-2 rounded-lg border p-2 sm:grid-cols-[1fr_1fr_1fr_auto] sm:items-end"
                                    >
                                        <FormField
                                            id={`day-${index}`}
                                            label="Hari"
                                            error={errorFor(index, 'day')}
                                        >
                                            <NativeSelect
                                                id={`day-${index}`}
                                                value={meeting.day}
                                                onChange={(e) =>
                                                    updateMeeting(index, {
                                                        day: e.target.value,
                                                    })
                                                }
                                            >
                                                {pilihan.hari.map((d) => (
                                                    <option
                                                        key={d.value}
                                                        value={d.value}
                                                    >
                                                        {d.label}
                                                    </option>
                                                ))}
                                            </NativeSelect>
                                        </FormField>

                                        <FormField
                                            id={`slot-${index}`}
                                            label="Waktu"
                                            error={errorFor(
                                                index,
                                                'time_slot_id',
                                            )}
                                        >
                                            <NativeSelect
                                                id={`slot-${index}`}
                                                value={meeting.time_slot_id}
                                                onChange={(e) =>
                                                    updateMeeting(index, {
                                                        time_slot_id: Number(
                                                            e.target.value,
                                                        ),
                                                    })
                                                }
                                            >
                                                {pilihan.slot.map((s) => (
                                                    <option
                                                        key={s.id}
                                                        value={s.id}
                                                    >
                                                        {s.label}
                                                    </option>
                                                ))}
                                            </NativeSelect>
                                        </FormField>

                                        <FormField
                                            id={`room-${index}`}
                                            label="Bilik"
                                            error={errorFor(index, 'room_id')}
                                        >
                                            <NativeSelect
                                                id={`room-${index}`}
                                                value={meeting.room_id}
                                                onChange={(e) =>
                                                    updateMeeting(index, {
                                                        room_id: e.target.value,
                                                    })
                                                }
                                            >
                                                <option value="">
                                                    Belum ditetapkan
                                                </option>
                                                {pilihan.bilik.map((b) => (
                                                    <option
                                                        key={b.id}
                                                        value={b.id}
                                                    >
                                                        {b.nama} ({b.kapasiti})
                                                    </option>
                                                ))}
                                            </NativeSelect>
                                        </FormField>

                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => removeMeeting(index)}
                                            aria-label="Buang waktu"
                                        >
                                            <X className="size-4" />
                                        </Button>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </Card>

                    <div className="flex gap-2">
                        <Button type="submit" disabled={form.processing}>
                            {form.processing
                                ? 'Menyimpan…'
                                : editing
                                  ? 'Simpan perubahan'
                                  : 'Tambah kelas'}
                        </Button>
                        <Button asChild variant="ghost">
                            <Link href="/kelas">Batal</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

KelasForm.layout = {
    breadcrumbs: [
        { title: 'Kelas', href: '/kelas' },
        { title: 'Borang', href: '#' },
    ],
};
