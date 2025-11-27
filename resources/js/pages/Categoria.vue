<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

import { router, useForm, usePage } from '@inertiajs/vue3';

import axios from 'axios';
import { onMounted, ref } from 'vue';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    FileDown,
    Plus,
    Save,
    SquarePen,
    Sticker,
    Trash2,
    X,
} from 'lucide-vue-next';

declare const route: (...params: any) => string;

interface CategoriaData {
    id: number;
    nombre_categoria: string;
    descripcion: string;
    estado: number;
    created_at?: string;
    updated_at?: string;
}

interface ApiResponse {
    success: boolean;
    nombre: string;
    data: CategoriaData[];
}

const page = usePage();
const categorias = ref<CategoriaData[]>([]);
const minombre = ref('');
const mostrarModal = ref(false);
const paEditarModal = ref(false);
const isLoading = ref(true);

const formulario = ref(
    {
        nombre_categoria: '',
        descripcion: '',
        estado: true,
    }
);


const breadcrumbs = [
    {
        title: 'Categoria',
        href: '/categoria',
    },
];

const flashSuccess = ref<string | null>(null);
const checkFlashMessage = () => {
    const flash = page.props.flash as { success?: string };
    if (flash?.success) {
        flashSuccess.value = flash.success;
        setTimeout(() => (flashSuccess.value = null), 5000);
    }
};

const listarCategoria = async () => {
    isLoading.value = true;
    try {
        const respuesta = await axios.get<ApiResponse>('/categorias-data');
        if (respuesta.data.success) {
            categorias.value = respuesta.data.data;
            minombre.value = respuesta.data.nombre;
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        isLoading.value = false;
    }
};

const abrirModal = () => {
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
};


const enviarFormulario = () => {
    console.log('Andree Contreras');
    console.log(formulario.value);

    

    
};



onMounted(() => {
    listarCategoria();
    checkFlashMessage();
});
</script>

<template>
    <Head title="Categoria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center justify-center">
            <div class="mt-4 flex flex-col items-center justify-center">
                <p class="text-2xl text-amber-600">🐼 Gestión Categoria 🎀</p>
                <small>{{ minombre }}</small
                ><br />
                <img src="andreewdev.jpg" width="80" :height="80" />
            </div>
            <div class="dashboard-settings">
                <div
                    class="report-section my-4 flex flex-col items-center justify-center"
                >
                    <h3>Reporte de Categorías</h3>
                    <br />
                    <a
                        href="/categorias-exportar-pdf"
                        class="btn btn-success flex flex-col items-center justify-center"
                    >
                        Download
                        <FileDown /> </a
                    ><br />
                    <Button @click="abrirModal">
                        <Plus /> Crear nueva categoría
                        <Sticker /> </Button
                    ><br />
                </div>
            </div>

            <div>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y-2 divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="ltr:text-left rtl:text-right">
                            <tr
                                class="*:font-medium *:text-gray-900 dark:*:text-white"
                            >
                                <th class="px-3 py-2 whitespace-nowrap">
                                    Nombre
                                </th>
                                <th class="px-3 py-2 whitespace-nowrap">
                                    Descripcion
                                </th>
                                <th class="px-3 py-2 whitespace-nowrap">
                                    Estado
                                </th>
                                <th
                                    class="flex flex-col items-center justify-center px-3 py-2 whitespace-nowrap"
                                >
                                    Opciones
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-gray-200 *:even:bg-gray-50 dark:divide-gray-700 dark:*:even:bg-gray-800"
                        >
                            <tr
                                v-for="item in categorias"
                                :key="item.id"
                                class="*:text-gray-900 *:first:font-medium dark:*:text-white"
                            >
                                <td class="px-3 py-2 whitespace-nowrap">
                                    {{ item.nombre_categoria }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    {{ item.descripcion }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-1 text-xs font-semibold',
                                            item.estado === 1
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800',
                                        ]"
                                    >
                                        {{
                                            item.estado === 1
                                                ? 'Activo'
                                                : 'Inactivo'
                                        }}
                                    </span>
                                </td>

                                <td class="space-x-2 px-6 py-4 text-center">
                                    <a
                                        class="group relative inline-block"
                                        href="#"
                                    >
                                        <span
                                            class="absolute inset-0 translate-x-1.5 translate-y-1.5 bg-sky-300 transition-transform group-hover:translate-x-0 group-hover:translate-y-0 group-hover:bg-blue-500"
                                        ></span>

                                        <span
                                            class="relative inline-block border-2 border-current px-8 py-3 text-sm font-bold tracking-widest text-black uppercase"
                                            variant="outline"
                                            size="sm"
                                            @click="openEditDialog(item)"
                                        >
                                            <SquarePen />
                                        </span>
                                    </a>
                                    <a
                                        class="group relative inline-block"
                                        href="#"
                                    >
                                        <span
                                            class="absolute inset-0 flex translate-x-1.5 translate-y-1.5 bg-red-300 transition-transform group-hover:translate-x-0 group-hover:translate-y-0 group-hover:bg-red-500"
                                        ></span>

                                        <span
                                            class="relative inline-block border-2 border-current px-8 py-3 text-sm font-bold tracking-widest text-black uppercase"
                                            variant="destructive"
                                            size="sm"
                                            @click="confirmDelete(item)"
                                        >
                                            <Trash2 />
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Modal registro categoria -->
                <div
                    class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="modalTitle"
                    v-if="mostrarModal"
                >
                    <div
                        class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-900"
                    >
                        <h2
                            id="modalTitle"
                            class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white"
                        >
                            Resgistro categoria
                        </h2>

                        <form
                            @submit.prevent=""
                            class="grid gap-4 py-4"
                        >
                            <div
                                class="mt-2 grid grid-cols-4 items-center gap-4"
                            >
                                <Label for="nombre_categoria" class="text-right"
                                    >Nombre</Label
                                >
                                <div class="col-span-3">
                                    <Input
                                        type="text"
                                        id="nombre_categoria"
                                        v-model="formulario.nombre_categoria"
                                    />
                                </div>
                            </div>
                            <label for="Notes">
                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Descripcion
                                </span>

                                <textarea
                                    id="descripcion"
                                    v-model="formulario.descripcion"
                                    class="mt-0.5 w-full resize-none rounded shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    rows="4"
                                ></textarea>
                            </label>
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="status" class="text-right"
                                    >Estado</Label
                                >
                                <div class="col-span-3 flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        id="status"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    />
                                    <span class="text-sm text-gray-600">{{
                                    }}</span>
                                </div>
                            </div>
                            <footer class="mt-6 flex justify-end gap-2">
                                <button
                                    type="button"
                                    class="flex items-center gap-2 rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                    @click="cerrarModal"
                                >
                                    <X />
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    @click="enviarFormulario"
                                    class="flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    <Save />
                                    {{
                                        paEditarModal
                                            ? 'Guardar Cambios'
                                            : 'Guardar'
                                    }}
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.btn-success {
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.btn-azul {
    padding: 10px 20px;
    background-color: #157dc2;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>
