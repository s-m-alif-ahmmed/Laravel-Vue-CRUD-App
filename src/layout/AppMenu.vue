<script setup>
import { ref } from 'vue';
import { logout as authLogout } from '@/service/Auth';
import { useRouter } from 'vue-router';
import AppMenuItem from './AppMenuItem.vue';

const router = useRouter();

// Logout Function
const logout = async () => {
    await authLogout();
    router.push({ name: 'login' });
};

const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' }]
    },
    {
        label: 'Pages',
        icon: 'pi pi-fw pi-briefcase',
        to: '/pages',
        items: [
            {
                label: 'Crud',
                icon: 'pi pi-fw pi-pencil',
                to: '/pages/crud'
            }
        ]
    },
    {
        label: 'Settings',
        items: [
            {
                label: 'Logout',
                icon: 'pi pi-fw pi-sign-out',
                command: logout
            }
        ]
    }
]);
</script>


<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
