<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    CakeSlice,
    History,
    LayoutGrid,
    MessageCircleHeart,
    PartyPopper,
    Settings2,
} from '@lucide/vue';
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
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Pregled',
        href: dashboard(),
        icon: LayoutGrid,
    },
    { title: 'Rođendani', href: '/birthdays', icon: CakeSlice },
    { title: 'Pozivnice', href: '/invitations', icon: PartyPopper },
    { title: 'Poruke', href: '/messages', icon: MessageCircleHeart },
    { title: 'Istorija', href: '/deliveries', icon: History },
    { title: 'Podešavanja', href: '/settings/birthdays', icon: Settings2 },
];

const footerNavItems: NavItem[] = [];
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

        <div
            class="mx-3 mb-5 overflow-hidden rounded-2xl bg-gradient-to-br from-violet-600 via-fuchsia-500 to-rose-400 p-4 text-white shadow-lg shadow-violet-200/60 group-data-[collapsible=icon]:hidden dark:shadow-none"
        >
            <div class="flex items-center justify-between">
                <span class="text-2xl">🎉</span>
                <span class="text-lg">🎈</span>
            </div>
            <p class="mt-3 text-sm font-black">Neka slavlje počne!</p>
            <p class="mt-1 text-xs leading-5 text-white/80">
                Svaki važan datum je pod kontrolom.
            </p>
        </div>

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
