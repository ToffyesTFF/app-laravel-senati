<script setup lang="ts">
// Define las props que el componente recibirá de su padre.
// Las letras, título y artista serán pasadas como atributos.
interface Props {
    title: string;
    artist: string;
    lyrics: string[];
}

const props = defineProps<Props>();
</script>

<template>
    <div
        class="relative flex-1 rounded-xl p-6 bg-gray-50 dark:bg-gray-800 border border-sidebar-border/70 dark:border-sidebar-border overflow-y-auto max-h-[400px]"
    >
        <div class="mb-4 text-center">
            <h3 class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ props.title }}</h3>
            <p class="text-lg text-gray-600 dark:text-gray-400">por {{ props.artist }}</p>
        </div>
        
        <div class="text-center text-gray-800 dark:text-gray-200 font-serif whitespace-pre-line">
            <p 
                v-for="(line, index) in props.lyrics" 
                :key="index"
                :class="{'mt-4': line === ''}"
            >
                {{ line }}
            </p>
        </div>
    </div>
</template>