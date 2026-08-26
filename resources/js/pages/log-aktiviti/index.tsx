import { Head, Link, router, usePage } from '@inertiajs/react';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface Row {
    id: number;
    log: string;
    peristiwa: string | null;
    huraian: string;
    subjek: string;
    oleh: string;
    bila: string | null;
    masa: string | null;
    perubahan: { medan: string; dari: string; ke: string }[];
}

interface Props {
    [key: string]: unknown;
    aktiviti: {
        data: Row[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    logs: string[];
    filters: { log?: string };
}

export default function LogAktivitiIndex() {
    const { aktiviti, logs, filters } = usePage<Props>().props;

    return (
        <>
            <Head title="Log Aktiviti" />
            <div className="flex flex-col gap-5 p-4 md:p-6">
                <PageHeader
                    title="Log Aktiviti"
                    subtitle="Rekod audit. Medan sensitif seperti no. K/P dan tarikh lahir tidak pernah dilog."
                />

                <Card className="gap-0 p-0">
                    <div className="flex flex-wrap gap-1.5 border-b p-3">
                        <Button
                            variant={!filters.log ? 'default' : 'ghost'}
                            size="sm"
                            onClick={() => router.get('/log-aktiviti')}
                        >
                            Semua
                        </Button>
                        {logs.map((log) => (
                            <Button
                                key={log}
                                variant={
                                    filters.log === log ? 'default' : 'ghost'
                                }
                                size="sm"
                                onClick={() =>
                                    router.get('/log-aktiviti', { log })
                                }
                            >
                                {log}
                            </Button>
                        ))}
                    </div>

                    {aktiviti.data.length === 0 ? (
                        <p className="p-8 text-center text-sm text-muted-foreground">
                            Tiada aktiviti direkodkan.
                        </p>
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Masa</TableHead>
                                    <TableHead>Log</TableHead>
                                    <TableHead>Huraian</TableHead>
                                    <TableHead>Subjek</TableHead>
                                    <TableHead>Oleh</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {aktiviti.data.map((row) => (
                                    <TableRow key={row.id}>
                                        <TableCell className="text-xs whitespace-nowrap text-muted-foreground">
                                            <div>{row.masa}</div>
                                            <div>{row.bila}</div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">
                                                {row.log}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-sm">
                                            {row.huraian}
                                            {row.perubahan.length > 0 && (
                                                <ul className="mt-1 space-y-0.5">
                                                    {row.perubahan.map((c) => (
                                                        <li
                                                            key={c.medan}
                                                            className="text-[0.7rem] text-muted-foreground"
                                                        >
                                                            <span className="font-medium">
                                                                {c.medan}
                                                            </span>
                                                            : {c.dari} &rarr;{' '}
                                                            <span className="text-foreground">
                                                                {c.ke}
                                                            </span>
                                                        </li>
                                                    ))}
                                                </ul>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-xs text-muted-foreground">
                                            {row.subjek}
                                        </TableCell>
                                        <TableCell className="text-sm">
                                            {row.oleh}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}

                    <div className="flex flex-wrap gap-1 border-t p-3">
                        {aktiviti.links.map((link, i) => (
                            <Button
                                key={i}
                                asChild={Boolean(link.url)}
                                variant={link.active ? 'default' : 'ghost'}
                                size="sm"
                                disabled={!link.url}
                            >
                                {link.url ? (
                                    <Link href={link.url}>
                                        <span
                                            dangerouslySetInnerHTML={{
                                                __html: link.label,
                                            }}
                                        />
                                    </Link>
                                ) : (
                                    <span
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                )}
                            </Button>
                        ))}
                    </div>
                </Card>
            </div>
        </>
    );
}

LogAktivitiIndex.layout = {
    breadcrumbs: [{ title: 'Log Aktiviti', href: '/log-aktiviti' }],
};
