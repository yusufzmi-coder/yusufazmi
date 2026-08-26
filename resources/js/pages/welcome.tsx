import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import { ArrowRight, CheckCircle2, GraduationCap, XCircle } from 'lucide-react';

interface Check {
    ok: boolean;
    label: string;
    detail: string;
}

interface Props {
    [key: string]: unknown;
    status: Record<string, Check>;
    environment: string;
    auth: { user: { name: string } | null };
}

export default function Welcome() {
    const { status, environment, auth } = usePage<Props>().props;
    const checks = Object.values(status);
    const allOk = checks.every((c) => c.ok);

    return (
        <>
            <Head title="Jadual Kelas Student Auto" />

            <div className="flex min-h-screen flex-col items-center justify-center bg-background px-4 py-12">
                <motion.main
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.3 }}
                    className="w-full max-w-lg"
                >
                    <div className="mb-6 flex items-center gap-3">
                        <div className="flex size-12 items-center justify-center rounded-xl bg-primary text-primary-foreground">
                            <GraduationCap className="size-6" />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold tracking-tight">
                                JADUAL KELAS
                            </h1>
                            <p className="text-sm text-muted-foreground">
                                STUDENT AUTO
                            </p>
                        </div>
                    </div>

                    <p className="mb-6 text-sm leading-relaxed text-muted-foreground">
                        Sistem penjadualan kelas automatik. Admin menekan satu
                        butang, sistem mengagihkan pelajar ke dalam kelas
                        mengikut peraturan yang ditetapkan — dan menerangkan
                        sebab setiap penempatan.
                    </p>

                    <div className="rounded-xl border bg-card p-4">
                        <div className="mb-3 flex items-center justify-between">
                            <h2 className="text-sm font-semibold">
                                Status Sistem
                            </h2>
                            <span
                                className={`rounded-full px-2 py-0.5 text-[0.7rem] font-medium ${
                                    allOk
                                        ? 'bg-[color-mix(in_oklch,var(--chart-2)_18%,white)] text-foreground'
                                        : 'bg-[color-mix(in_oklch,var(--destructive)_14%,white)] text-destructive'
                                }`}
                            >
                                {allOk ? 'Semua berfungsi' : 'Ada masalah'}
                            </span>
                        </div>

                        <ul className="space-y-2.5">
                            {checks.map((check) => (
                                <li
                                    key={check.label}
                                    className="flex items-start gap-2.5"
                                >
                                    {check.ok ? (
                                        <CheckCircle2 className="mt-0.5 size-4 shrink-0 text-[var(--chart-2)]" />
                                    ) : (
                                        <XCircle className="mt-0.5 size-4 shrink-0 text-destructive" />
                                    )}
                                    <div className="min-w-0">
                                        <p className="text-sm font-medium">
                                            {check.label}
                                        </p>
                                        <p className="text-xs break-words text-muted-foreground">
                                            {check.detail}
                                        </p>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>

                    <Link
                        href={auth.user ? '/dashboard' : '/login'}
                        className="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground shadow-xs transition hover:brightness-110"
                    >
                        {auth.user
                            ? `Teruskan sebagai ${auth.user.name}`
                            : 'Log Masuk'}
                        <ArrowRight className="size-4" />
                    </Link>

                    <p className="mt-4 text-center text-[0.7rem] text-muted-foreground">
                        Persekitaran: {environment}
                    </p>
                </motion.main>
            </div>
        </>
    );
}
