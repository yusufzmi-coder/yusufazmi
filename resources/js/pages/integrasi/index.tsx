import { Head, router, useForm, usePage } from '@inertiajs/react';
import { CheckCircle2, KeyRound, Plug, XCircle } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

interface Field {
    name: string;
    label: string;
    type: 'text' | 'password' | 'url';
    hint?: string;
    required?: boolean;
}

interface IntegrationCard {
    key: string;
    label: string;
    description: string;
    enabled: boolean;
    fields: Field[];
    values: Record<string, string | null>;
    configured: boolean;
    last_test_status: string | null;
    last_test_message: string | null;
    last_tested_at: string | null;
}

interface Props {
    [key: string]: unknown;
    integrations: IntegrationCard[];
}

function IntegrationForm({ item }: { item: IntegrationCard }) {
    const form = useForm<{ enabled: boolean; config: Record<string, string> }>({
        enabled: item.enabled,
        config: Object.fromEntries(item.fields.map((f) => [f.name, ''])),
    });

    const save = (e: React.FormEvent) => {
        e.preventDefault();
        form.patch(`/integrasi/${item.key}`, { preserveScroll: true });
    };

    return (
        <Card className="gap-0 p-4">
            <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                        <Plug className="size-4 text-muted-foreground" />
                        <h2 className="text-base font-semibold">
                            {item.label}
                        </h2>
                        {item.configured ? (
                            <Badge variant="secondary">Dikonfigurasikan</Badge>
                        ) : (
                            <Badge variant="outline">Belum lengkap</Badge>
                        )}
                    </div>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {item.description}
                    </p>
                </div>

                <Switch
                    checked={form.data.enabled}
                    onCheckedChange={(next: boolean) => {
                        form.setData('enabled', next);
                        router.patch(
                            `/integrasi/${item.key}`,
                            { enabled: next, config: {} },
                            { preserveScroll: true, preserveState: true },
                        );
                    }}
                    aria-label={`Aktifkan ${item.label}`}
                />
            </div>

            {item.last_test_status && (
                <div
                    className={`mt-3 flex items-start gap-2 rounded-lg border p-2.5 text-xs ${
                        item.last_test_status === 'ok'
                            ? 'border-[var(--chart-2)]/40 bg-[color-mix(in_oklch,var(--chart-2)_10%,white)]'
                            : 'border-destructive/40 bg-[color-mix(in_oklch,var(--destructive)_8%,white)]'
                    }`}
                >
                    {item.last_test_status === 'ok' ? (
                        <CheckCircle2 className="mt-0.5 size-3.5 shrink-0 text-[var(--chart-2)]" />
                    ) : (
                        <XCircle className="mt-0.5 size-3.5 shrink-0 text-destructive" />
                    )}
                    <span>
                        {item.last_test_message}
                        {item.last_tested_at && (
                            <span className="text-muted-foreground">
                                {' '}
                                · {item.last_tested_at}
                            </span>
                        )}
                    </span>
                </div>
            )}

            <form onSubmit={save} className="mt-4 space-y-3">
                {item.fields.map((field) => (
                    <div key={field.name}>
                        <Label
                            htmlFor={`${item.key}-${field.name}`}
                            className="text-xs"
                        >
                            {field.label}
                            {field.required && (
                                <span className="text-destructive"> *</span>
                            )}
                        </Label>
                        <Input
                            id={`${item.key}-${field.name}`}
                            type={
                                field.type === 'password' ? 'password' : 'text'
                            }
                            value={form.data.config[field.name] ?? ''}
                            placeholder={
                                item.values[field.name] ?? 'Belum diisi'
                            }
                            onChange={(e) =>
                                form.setData('config', {
                                    ...form.data.config,
                                    [field.name]: e.target.value,
                                })
                            }
                            className="mt-1"
                        />
                        {field.hint && (
                            <p className="mt-1 text-[0.7rem] text-muted-foreground">
                                {field.hint}
                            </p>
                        )}
                        {form.errors[
                            `config.${field.name}` as keyof typeof form.errors
                        ] && (
                            <p className="mt-1 text-xs text-destructive">
                                {
                                    form.errors[
                                        `config.${field.name}` as keyof typeof form.errors
                                    ]
                                }
                            </p>
                        )}
                    </div>
                ))}

                <p className="text-[0.7rem] text-muted-foreground">
                    Biarkan kosong untuk mengekalkan nilai sedia ada. Nilai
                    rahsia tidak pernah dipaparkan penuh.
                </p>

                <div className="flex flex-wrap gap-2">
                    <Button type="submit" disabled={form.processing}>
                        <KeyRound className="size-4" />
                        {form.processing ? 'Menyimpan…' : 'Simpan kredensial'}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        disabled={!item.configured}
                        onClick={() =>
                            router.post(
                                `/integrasi/${item.key}/uji`,
                                {},
                                { preserveScroll: true },
                            )
                        }
                    >
                        Uji Sambungan
                    </Button>
                </div>
            </form>
        </Card>
    );
}

export default function IntegrasiIndex() {
    const { integrations } = usePage<Props>().props;

    return (
        <>
            <Head title="Integrasi" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Integrasi
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Isi kredensial anda sendiri di sini. Ia disulitkan dalam
                        pangkalan data dan mengatasi fail .env, jadi kunci boleh
                        ditukar tanpa deploy semula.
                    </p>
                </header>

                <div className="grid gap-4 lg:grid-cols-2">
                    {integrations.map((item) => (
                        <IntegrationForm key={item.key} item={item} />
                    ))}
                </div>
            </div>
        </>
    );
}

IntegrasiIndex.layout = {
    breadcrumbs: [{ title: 'Integrasi', href: '/integrasi' }],
};
