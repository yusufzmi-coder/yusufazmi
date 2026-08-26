import { Head, router, usePage } from '@inertiajs/react';
import { Lock, Scale } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';

interface Rule {
    id: number;
    rule_type: string;
    name: string;
    kind: string;
    is_active: boolean;
    weight: number;
    config: Record<string, unknown>;
    description: string;
}

interface Props {
    [key: string]: unknown;
    rules: Rule[];
    always_on: { key: string; name: string; description: string }[];
}

export default function RulesIndex() {
    const { rules, always_on } = usePage<Props>().props;

    const patch = (
        rule: Rule,
        payload: { is_active?: boolean; weight?: number },
    ) =>
        router.patch(`/peraturan/${rule.id}`, payload, {
            preserveScroll: true,
            preserveState: true,
        });

    return (
        <>
            <Head title="Peraturan" />

            <div className="flex flex-col gap-5 p-4 md:p-6">
                <header>
                    <h1 className="text-2xl font-bold tracking-tight">
                        Peraturan (Rules)
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        Hidupkan, matikan, dan laraskan keutamaan setiap
                        peraturan penempatan.
                    </p>
                </header>

                <div className="grid gap-4">
                    {rules.map((rule) => (
                        <Card key={rule.id} className="gap-0 p-4">
                            <div className="flex flex-wrap items-start justify-between gap-3">
                                <div className="min-w-0 flex-1">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <h2 className="text-base font-semibold">
                                            {rule.name}
                                        </h2>
                                        <Badge
                                            variant={
                                                rule.kind === 'hard'
                                                    ? 'default'
                                                    : 'secondary'
                                            }
                                        >
                                            {rule.kind === 'hard'
                                                ? 'Wajib'
                                                : 'Keutamaan'}
                                        </Badge>
                                    </div>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        {rule.description}
                                    </p>
                                </div>

                                <Switch
                                    checked={rule.is_active}
                                    onCheckedChange={(next: boolean) =>
                                        patch(rule, { is_active: next })
                                    }
                                    aria-label={`Hidupkan ${rule.name}`}
                                />
                            </div>

                            {/* Weight only means anything for soft preferences. */}
                            {rule.kind === 'soft' && rule.is_active && (
                                <div className="mt-4">
                                    <label className="mb-1 flex items-center justify-between text-xs text-muted-foreground">
                                        <span>Keutamaan</span>
                                        <span className="font-semibold text-foreground tabular-nums">
                                            {rule.weight}
                                        </span>
                                    </label>
                                    <input
                                        type="range"
                                        min={0}
                                        max={100}
                                        step={5}
                                        defaultValue={rule.weight}
                                        onMouseUp={(e) =>
                                            patch(rule, {
                                                weight: Number(
                                                    (
                                                        e.target as HTMLInputElement
                                                    ).value,
                                                ),
                                            })
                                        }
                                        onTouchEnd={(e) =>
                                            patch(rule, {
                                                weight: Number(
                                                    (
                                                        e.target as HTMLInputElement
                                                    ).value,
                                                ),
                                            })
                                        }
                                        className="w-full accent-[var(--primary)]"
                                    />
                                    <p className="mt-1 text-[0.7rem] text-muted-foreground">
                                        Semakin tinggi, semakin kuat peraturan
                                        ini mempengaruhi penempatan berbanding
                                        peraturan lain.
                                    </p>
                                </div>
                            )}
                        </Card>
                    ))}
                </div>

                <Card className="gap-0 border-dashed p-4">
                    <h2 className="mb-1 flex items-center gap-2 text-base font-semibold">
                        <Lock className="size-4 text-muted-foreground" />
                        Kekangan tetap
                    </h2>
                    <p className="mb-3 text-xs text-muted-foreground">
                        Ini fakta fizikal, bukan pilihan — ia sentiasa
                        dikuatkuasakan walaupun semua peraturan di atas
                        dimatikan.
                    </p>
                    <ul className="space-y-2">
                        {always_on.map((item) => (
                            <li
                                key={item.key}
                                className="flex items-start gap-2 text-sm"
                            >
                                <Scale className="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                                <span>
                                    <span className="font-medium">
                                        {item.name}
                                    </span>
                                    <span className="block text-xs text-muted-foreground">
                                        {item.description}
                                    </span>
                                </span>
                            </li>
                        ))}
                    </ul>
                </Card>
            </div>
        </>
    );
}

RulesIndex.layout = {
    breadcrumbs: [{ title: 'Peraturan', href: '/peraturan' }],
};
