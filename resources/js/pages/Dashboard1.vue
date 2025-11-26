<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const songTitle = "Killer Queen (Primer Minuto)";
const songArtist = "Queen";
// Asegúrate de que este archivo esté en la carpeta /public
const audioPath = "/killer_queen.mp3"; 

// --- LAS LETRAS DE LA CANCIÓN, SINCRONIZADAS CON EL PRIMER MINUTO ---
// NOTA: Los valores 'delayMs' son el tiempo que se espera después de la línea anterior.
// El total de demoras cubre aproximadamente los primeros 50 segundos de la canción.
const killerQueenLyrics: { text: string; delayMs: number; }[] = [
    { text: "She keeps her Moët et Chandon,", delayMs: 8000 },   // La voz empieza ~7s
    { text: "In her pretty cabinet,", delayMs: 3000 },
    { text: "Let them eat cake,  she says,", delayMs: 2000 },
    { text: "Just like Marie Antoinette.", delayMs: 3500 },
    { text: "(Anytime)", delayMs: 1500 },
    
    { text: "", delayMs: 4000 }, // Pausa
    
    { text: "Ooh, recommended at the price,", delayMs: 4000 }, 
    { text: "Insatiable an appetite.", delayMs: 2500 },
    { text: "Wanna try?", delayMs: 1500 },
    
    { text: "", delayMs: 3000 }, // Pausa
    
    { text: "To avoid complication,", delayMs: 3500 },
    { text: "She never keeps the same address.", delayMs: 2500 },
    { text: "In conversation,", delayMs: 3000 },
    { text: "She spoke just like a baroness.", delayMs: 3500 },
];

// Índice de la línea actual (reactivo)
const currentLineIndex = ref(-1);
let audio: HTMLAudioElement | null = null;
let timers: number[] = [];
let wordTimers: number[] = [];

// Variable para rastrear qué palabras han sido "cantadas" en la línea actual
const wordsSungCount = ref(0); 
const isKaraokeActive = ref(false);

// Función auxiliar para obtener palabras
const getWords = (lineIndex: number) => {
    if (lineIndex < 0 || lineIndex >= killerQueenLyrics.length) {
        return [];
    }
    return killerQueenLyrics[lineIndex].text.split(/\s+/).filter(w => w.length > 0);
};

const startKaraoke = () => {
    stopKaraoke(); // Limpiar antes de iniciar
    isKaraokeActive.value = true;
    
    if (audio) {
        audio.currentTime = 0; 
        audio.play().catch(e => console.error("Error al reproducir audio:", e));
    }

    currentLineIndex.value = -1;
    let cumulativeDelay = 0;

    killerQueenLyrics.forEach((line, lineIndex) => {
        cumulativeDelay += line.delayMs;
        
        // Timer principal para cambiar de línea
        const lineTimer = window.setTimeout(() => {
            currentLineIndex.value = lineIndex;
            wordsSungCount.value = 0; 

            const words = getWords(lineIndex);
            if (words.length > 0) {
                // Simular el avance palabra por palabra dentro de la línea
                const timePerWord = line.delayMs / words.length; 
                
                words.forEach((_, wordIndex) => {
                    const wordTimer = window.setTimeout(() => {
                        wordsSungCount.value = wordIndex + 1;
                    }, timePerWord * wordIndex);
                    wordTimers.push(wordTimer);
                });
            }
        }, cumulativeDelay);

        timers.push(lineTimer);
    });
    
    // Configurar un último timer para detener el karaoke después de la última línea
    const finalDelay = cumulativeDelay + 5000; // 5 segundos después de la última línea
    const finalTimer = window.setTimeout(() => {
        stopKaraoke();
    }, finalDelay);
    timers.push(finalTimer);
};

const stopKaraoke = () => {
    if (audio) {
        audio.pause();
        audio.currentTime = 0;
    }
    timers.forEach(clearTimeout);
    wordTimers.forEach(clearTimeout); 
    timers = [];
    wordTimers = [];
    currentLineIndex.value = -1; // Oculta las letras
    wordsSungCount.value = 0;
    isKaraokeActive.value = false;
};


onMounted(() => {
    audio = new Audio(audioPath);
});

onUnmounted(() => {
    stopKaraoke();
    audio = null;
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 overflow-y-auto p-4 min-h-screen">

            <p class="text-2xl text-pink-500">🎤 Modo Karaoke: {{ songTitle }} 🎸</p>
            <div class="flex gap-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-xl">
                <button 
                    @click="startKaraoke" 
                    :disabled="isKaraokeActive"
                    class="px-6 py-3 text-lg font-bold text-white rounded-lg transition duration-150"
                    :class="{'bg-green-600 hover:bg-green-700': !isKaraokeActive, 'bg-gray-400': isKaraokeActive}"
                >
                    ▶️ Iniciar Karaoke
                </button>
                 <button 
                    @click="stopKaraoke" 
                    :disabled="!isKaraokeActive"
                    class="px-6 py-3 text-lg font-bold text-white rounded-lg transition duration-150"
                    :class="{'bg-red-600 hover:bg-red-700': isKaraokeActive, 'bg-gray-400': !isKaraokeActive}"
                >
                    ⏹️ Detener
                </button>
            </div>
            
            <hr class="border-gray-300 dark:border-gray-700 my-4" />

            <div 
                class="relative flex-1 flex flex-col justify-center items-center rounded-xl p-8 bg-black dark:bg-gray-900 min-h-[50vh] shadow-2xl overflow-hidden"
            >
                
                <div class="text-center mb-8 absolute top-8">
                    <h1 class="text-5xl font-extrabold text-white">{{ songTitle }}</h1>
                    <p class="text-2xl text-gray-400">por {{ songArtist }}</p>
                </div>

                <div v-if="currentLineIndex < 0" class="text-center">
                    <p class="text-3xl text-gray-500">Haz clic en "Iniciar Karaoke" para ver la letra sincronizada con el primer minuto.</p>
                </div>

                <div 
                    v-if="currentLineIndex >= 0"
                    class="text-center text-6xl font-black italic 
                           transition-all duration-500 p-2 rounded-xl"
                    :class="{
                        'text-yellow-400 dark:text-yellow-300': killerQueenLyrics[currentLineIndex].text !== '',
                        'bg-gray-800/50 border border-yellow-500': killerQueenLyrics[currentLineIndex].text !== '',
                    }"
                >
                    <span 
                        v-for="(word, wordIndex) in getWords(currentLineIndex)" 
                        :key="wordIndex"
                        class="inline-block mx-1 transition-colors duration-300"
                        :class="{
                            'text-white dark:text-gray-200': wordIndex >= wordsSungCount, // Palabras pendientes
                            'text-yellow-400 dark:text-yellow-300': wordIndex < wordsSungCount, // Palabras ya cantadas
                            'font-extrabold': wordIndex < wordsSungCount,
                        }"
                    >
                        {{ word }}
                    </span>
                    <span v-if="getWords(currentLineIndex).length > 0"> </span> 
                </div>


                <p 
                    v-if="currentLineIndex > 0 && killerQueenLyrics[currentLineIndex - 1].text !== ''"
                    class="absolute bottom-10 text-center text-2xl font-light text-gray-400 dark:text-gray-500
                           transition-opacity duration-1000 opacity-50"
                >
                    {{ killerQueenLyrics[currentLineIndex - 1].text }}
                </p>

            </div>
            
            <hr class="border-gray-300 dark:border-gray-700 my-4" />
            
            </div>
    </AppLayout>
</template>