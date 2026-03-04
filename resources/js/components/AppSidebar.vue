<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, KeyRound, LayoutGrid, ShieldCheck, Users } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useAuthorization } from '@/composables/useAuthorization';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

//AGREGAR RUTAS
import companies from '@/routes/companies';
import providers from '@/routes/providers';
import diagnostics from '@/routes/diagnostics';


const { can } = useAuthorization();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    }, 
    {
        title: 'Compañia',
        href: companies.index.url(),
        icon: LayoutGrid,
    },
    {
        title: 'Proveedor',
        href: providers.index.url(),
        icon: LayoutGrid,
    },
    {
        title: 'Diagnóstico',
        href: diagnostics.index.url(),
        icon: LayoutGrid,
    },
    // Educational note: each menu option is tied to a backend permission.
    ...(can('users.manage')
        ? [
            {
                title: 'Usuarios',
                href: '/admin/users',
                icon: Users,
            } satisfies NavItem,
        ]
        : []),
    ...(can('roles.manage')
        ? [
            {
                title: 'Roles',
                href: '/admin/roles',
                icon: ShieldCheck,
            } satisfies NavItem,
        ]
        : []),
    ...(can('permissions.manage')
        ? [
            {
                title: 'Permisos',
                href: '/admin/permissions',
                icon: KeyRound,
            } satisfies NavItem,
        ]
        : []),
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
