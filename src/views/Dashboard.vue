<script setup>
import BestSellingWidget from '@/components/dashboard/BestSellingWidget.vue';
import NotificationsWidget from '@/components/dashboard/NotificationsWidget.vue';
import RecentSalesWidget from '@/components/dashboard/RecentSalesWidget.vue';
import RevenueStreamWidget from '@/components/dashboard/RevenueStreamWidget.vue';
import StatsWidget from '@/components/dashboard/StatsWidget.vue';
</script>

<template>
    <div class="grid grid-cols-12 gap-8 pb-8">
        <StatsWidget />
    </div>
    <div class="grid grid-cols-12 gap-8">

        <div class="col-span-12 xl:col-span-12">
            <RecentSalesWidget />
        </div>
    </div>
</template>
