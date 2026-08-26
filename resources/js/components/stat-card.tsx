import { motion } from 'motion/react';
import type { LucideIcon } from 'lucide-react';
import { Card } from '@/components/ui/card';
import { cn } from '@/lib/utils';

interface Props {
    label: string;
    value: number;
    icon: LucideIcon;
    tone: 'indigo' | 'green' | 'amber' | 'rose';
    breakdown: { label: string; value: number }[];
    index?: number;
}

const tones: Record<Props['tone'], string> = {
    indigo: 'bg-[color-mix(in_oklch,var(--chart-1)_16%,white)] text-[color-mix(in_oklch,var(--chart-1)_75%,black)]',
    green: 'bg-[color-mix(in_oklch,var(--chart-2)_18%,white)] text-[color-mix(in_oklch,var(--chart-2)_70%,black)]',
    amber: 'bg-[color-mix(in_oklch,var(--chart-3)_20%,white)] text-[color-mix(in_oklch,var(--chart-3)_65%,black)]',
    rose: 'bg-[color-mix(in_oklch,var(--chart-4)_16%,white)] text-[color-mix(in_oklch,var(--chart-4)_72%,black)]',
};

export function StatCard({
    label,
    value,
    icon: Icon,
    tone,
    breakdown,
    index = 0,
}: Props) {
    return (
        <motion.div
            initial={{ opacity: 0, y: 8 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.25, delay: index * 0.05 }}
        >
            <Card className="h-full gap-0 p-4">
                <div className="flex items-start gap-3">
                    <div
                        className={cn(
                            'flex size-11 shrink-0 items-center justify-center rounded-xl',
                            tones[tone],
                        )}
                    >
                        <Icon className="size-5" />
                    </div>
                    <div className="min-w-0">
                        <p className="truncate text-sm text-muted-foreground">
                            {label}
                        </p>
                        <p className="text-2xl font-bold text-foreground tabular-nums">
                            {value}
                        </p>
                    </div>
                </div>
                <div className="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-xs text-muted-foreground">
                    {breakdown.map((b) => (
                        <span key={b.label}>
                            {b.label}:{' '}
                            <span className="font-semibold text-foreground/80 tabular-nums">
                                {b.value}
                            </span>
                        </span>
                    ))}
                </div>
            </Card>
        </motion.div>
    );
}
