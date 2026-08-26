import {
    Cell,
    Legend,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
} from 'recharts';

interface Props {
    data: { tahun: number; jumlah: number }[];
}

// Sequential-but-distinct: each year gets its own hue from the chart ramp.
const COLOURS = [
    'var(--chart-1)',
    'var(--chart-2)',
    'var(--chart-3)',
    'var(--chart-5)',
    'var(--chart-4)',
    'color-mix(in oklch, var(--chart-1) 60%, var(--chart-2))',
];

export function StudentDistribution({ data }: Props) {
    const total = data.reduce((sum, d) => sum + d.jumlah, 0);
    const rows = data.map((d) => ({ ...d, nama: `Tahun ${d.tahun}` }));

    if (total === 0) {
        return (
            <p className="py-8 text-center text-sm text-muted-foreground">
                Tiada data pelajar lagi.
            </p>
        );
    }

    return (
        <div className="h-64 w-full">
            <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                    <Pie
                        data={rows}
                        dataKey="jumlah"
                        nameKey="nama"
                        innerRadius="58%"
                        outerRadius="82%"
                        paddingAngle={2}
                    >
                        {rows.map((row, i) => (
                            <Cell
                                key={row.tahun}
                                fill={COLOURS[i % COLOURS.length]}
                                stroke="var(--card)"
                                strokeWidth={2}
                            />
                        ))}
                    </Pie>
                    <Tooltip
                        formatter={(value, name) => {
                            const jumlah = Number(value ?? 0);
                            return [
                                `${jumlah} pelajar (${Math.round((jumlah / total) * 100)}%)`,
                                String(name),
                            ];
                        }}
                        contentStyle={{
                            background: 'var(--popover)',
                            border: '1px solid var(--border)',
                            borderRadius: 'var(--radius)',
                            fontSize: '0.8rem',
                        }}
                    />
                    <Legend
                        verticalAlign="middle"
                        align="right"
                        layout="vertical"
                        iconType="circle"
                        formatter={(value: string, entry) => {
                            const jumlah =
                                (entry?.payload as { jumlah?: number })
                                    ?.jumlah ?? 0;
                            return (
                                <span className="text-xs text-muted-foreground">
                                    {value}{' '}
                                    <span className="font-medium text-foreground">
                                        {Math.round((jumlah / total) * 100)}% (
                                        {jumlah})
                                    </span>
                                </span>
                            );
                        }}
                    />
                </PieChart>
            </ResponsiveContainer>
        </div>
    );
}
