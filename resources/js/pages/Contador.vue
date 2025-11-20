<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';


import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Contador',
        href: dashboard().url,
    },
];

const contador = ref(0);

const incrementar = () => {
    contador.value++;
};

const decrecer = () => {
    contador.value--;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

            <p class="text-2xl text-indigo-400">🎀 Contador 🐼</p>
            
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                </div>
            
            <div class="flex items-center justify-center p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-sidebar-border/70 dark:border-sidebar-border">
                
                <button 
                    @click="decrecer" 
                    class="px-6 py-3 text-xl font-bold text-white bg-red-500 hover:bg-red-600 rounded-l-lg transition duration-150"
                >
                    -
                </button>

                <span 
                    class="px-10 py-3 text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 bg-gray-100 dark:bg-gray-700 border-t border-b border-gray-300 dark:border-gray-600"
                >
                    {{ contador }}
                </span>

                <button 
                    @click="incrementar" 
                    class="px-6 py-3 text-xl font-bold text-white bg-indigo-500 hover:bg-indigo-600 rounded-r-lg transition duration-150"
                >
                    +
                </button>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>