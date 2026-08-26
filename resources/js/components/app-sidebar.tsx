import { Link, usePage } from '@inertiajs/react';
import {
    Activity,
    BarChart3,
    CalendarDays,
    DoorOpen,
    LayoutGrid,
    Plug,
    Scale,
    Settings,
    Sparkles,
    UserCog,
    Users,
    Users2,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

type Item = { title: string; href: string; icon: typeof LayoutGrid };
type Group = { label: string | null; items: Item[] };

// Mirrors the navigation grouping in the approved design.
const groups: Group[] = [
    {
        label: null,
        items: [{ title: 'Dashboard', href: '/dashboard', icon: LayoutGrid }],
    },
    {
        label: 'Core',
        items: [
            { title: 'Jadual Kelas', href: '/jadual', icon: CalendarDays },
            { title: 'Pelajar', href: '/pelajar', icon: Users },
            { title: 'Guru', href: '/guru', icon: Users2 },
            { title: 'Kelas', href: '/kelas', icon: LayoutGrid },
            { title: 'Bilik', href: '/bilik', icon: DoorOpen },
        ],
    },
    {
        label: 'Auto Assign',
        items: [
            { title: 'Auto Assign', href: '/auto-assign', icon: Sparkles },
            { title: 'Peraturan', href: '/peraturan', icon: Scale },
        ],
    },
    {
        label: 'Laporan',
        items: [{ title: 'Statistik', href: '/statistik', icon: BarChart3 }],
    },
    {
        label: 'Sistem',
        items: [
            { title: 'Integrasi', href: '/integrasi', icon: Plug },
            { title: 'Tetapan', href: '/settings/profile', icon: Settings },
            { title: 'Pengguna', href: '/pengguna', icon: UserCog },
            { title: 'Log Aktiviti', href: '/log-aktiviti', icon: Activity },
        ],
    },
];

export function AppSidebar() {
    const { url } = usePage();

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {groups.map((group, index) => (
                    <SidebarGroup key={group.label ?? `group-${index}`}>
                        {group.label && (
                            <SidebarGroupLabel className="text-[0.68rem] font-semibold tracking-widest uppercase">
                                {group.label}
                            </SidebarGroupLabel>
                        )}
                        <SidebarMenu>
                            {group.items.map((item) => (
                                <SidebarMenuItem key={item.href}>
                                    <SidebarMenuButton
                                        asChild
                                        isActive={url.startsWith(item.href)}
                                        tooltip={{ children: item.title }}
                                    >
                                        <Link href={item.href} prefetch>
                                            <item.icon />
                                            <span>{item.title}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroup>
                ))}
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
