<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

import { usePage } from '@inertiajs/vue3';

import axios from 'axios';
import { onMounted, ref } from 'vue';

import { editarCategoria } from '@/actions/App/Http/Controllers/CategoriaController';
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
import Swal from 'sweetalert2';

declare const route: (...params: any) => string;

interface CategoriaData {
    id: number;
    nombre_categoria: string;
    descripcion: string;
    estado: number | boolean; // Permite 1/0 o true/false
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
const mostrarModal = ref(false); // Modal de CREACIÓN

const paEditarModal = ref(false); // Modal de EDICIÓN
const isLoading = ref(true);

// Estado para la creación
const formulario = ref<CategoriaData>({
    id: 0,
    nombre_categoria: '',
    descripcion: '',
    estado: true,
});

const categoriaAEditar = ref<CategoriaData | null>(null);

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
            categorias.value = respuesta.data.data.map((cat) => ({
                ...cat,
                estado: cat.estado, // Mantenemos 0 o 1
            }));
            minombre.value = respuesta.data.nombre;
        }
    } catch (error) {
        console.error('Error al listar categorías:', error);
    } finally {
        isLoading.value = false;
    }
};

// --- Funciones de CREACIÓN --

const abrirModal = () => {
    formulario.value = {
        id: 0,
        nombre_categoria: '',
        descripcion: '',
        estado: true,
    };
    mostrarModal.value = true;
};

const cerrarModal = () => {
    mostrarModal.value = false;
};

const enviarFormulario = async () => {
    const estadoNumerico = formulario.value.estado ? 1 : 0;

    const respuesta = await axios.post('/categorias-data', {
        nombre_categoria: formulario.value.nombre_categoria,
        descripcion: formulario.value.descripcion,
        estado: estadoNumerico,
    });
    if (respuesta.data.success) {
        cerrarModal();
        listarCategoria();
        Swal.fire({
            title: 'Guardado',
            text: 'La categoría se guardo correctamente :)',
            icon: 'success',
        });
    } else {
        Swal.fire({
            title: 'Cancelado',
            text: 'Hubo un error',
            icon: 'error',
        });
    }
    console.log(respuesta);
};

const eliminarCategoria = async (id: number) => {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger',
        },
        buttonsStyling: false,
    });

    const result = await swalWithBootstrapButtons.fire({
        title: '¿Estás seguro?',
        text: `¡No podrás revertir la eliminación de la categoría (ID: ${id})!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, ¡eliminar!',
        cancelButtonText: 'No, ¡cancelar!',
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/categorias-data/${id}`);
            listarCategoria();

            swalWithBootstrapButtons.fire({
                title: '¡Eliminada!',
                text: 'La categoría ha sido eliminada con éxito.',
                icon: 'success',
            });
        } catch (error) {
            console.error('Error al eliminar la categoría:', error);
            swalWithBootstrapButtons.fire({
                title: 'Error',
                text: 'Hubo un error al eliminar la categoría. Inténtalo de nuevo.',
                icon: 'error',
            });
        }
    } else if (result.dismiss === Swal.DismissReason.cancel) {
        swalWithBootstrapButtons.fire({
            title: 'Cancelado',
            text: 'La categoría está a salvo :)',
            icon: 'error',
        });
    }
};

const cerrarEditarModal = () => {
    paEditarModal.value = false;
    categoriaAEditar.value = null; // Limpiar datos de edición
};

const editarCategoria = async (id: number, item: CategoriaData) => {
    const respuesta = await axios.put(`/categorias-data/${id}`);
    console.log(respuesta);
    categoriaAEditar.value = {
        ...item,
        estado: item.estado === 1,
    };
    paEditarModal.value = true;

    cerrarModal();
    listarCategoria();
};

// --- Funciones de ELIMINACIÓN ---

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
                        <Plus /> Crear nueva categoría <Sticker /> </Button
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
                                    class="px-3 py-2 text-center whitespace-nowrap"
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
                                            @click="abrirEditarModal(item)"
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
                                            @click="eliminarCategoria(item.id)"
                                        >
                                            <Trash2 />
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
                            Registro categoría
                        </h2>

                        <form
                            @submit.prevent="enviarFormulario"
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
                                        required
                                    />
                                </div>
                            </div>
                            <label for="descripcion">
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
                                        v-model="formulario.estado"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    />
                                    <span class="text-sm text-gray-600">{{
                                        formulario.estado
                                            ? 'Activo'
                                            : 'Inactivo'
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
                                    class="flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    <Save />Guardar
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>

                <div
                    class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="editModalTitle"
                    v-if="paEditarModal && categoriaAEditar"
                >
                    <div
                        class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-gray-900"
                    >
                        <h2
                            id="editModalTitle"
                            class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white"
                        >
                            Editar Categoría (ID: {{ categoriaAEditar.id }})
                        </h2>

                        <form
                            @submit.prevent="actualizarCategoria"
                            class="grid gap-4 py-4"
                        >
                            <div
                                class="mt-2 grid grid-cols-4 items-center gap-4"
                            >
                                <Label for="edit_nombre" class="text-right"
                                    >Nombre</Label
                                >
                                <div class="col-span-3">
                                    <Input
                                        type="text"
                                        id="edit_nombre"
                                        v-model="
                                            categoriaAEditar.nombre_categoria
                                        "
                                        required
                                    />
                                </div>
                            </div>
                            <label for="edit_descripcion">
                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-gray-200"
                                >
                                    Descripcion
                                </span>

                                <textarea
                                    id="edit_descripcion"
                                    v-model="categoriaAEditar.descripcion"
                                    class="mt-0.5 w-full resize-none rounded shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    rows="4"
                                ></textarea>
                            </label>
                            <div class="grid grid-cols-4 items-center gap-4">
                                <Label for="edit_status" class="text-right"
                                    >Estado</Label
                                >
                                <div class="col-span-3 flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        id="edit_status"
                                        v-model="categoriaAEditar.estado"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                    />
                                    <span class="text-sm text-gray-600">{{
                                        categoriaAEditar.estado
                                            ? 'Activo'
                                            : 'Inactivo'
                                    }}</span>
                                </div>
                            </div>
                            <footer class="mt-6 flex justify-end gap-2">
                                <button
                                    type="button"
                                    class="flex items-center gap-2 rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                    @click="cerrarEditarModal"
                                >
                                    <X />
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                                >
                                    <Save />Actualizar
                                </button>
                            </footer>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.btn-success {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    background-color: #dc3545;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    cursor: pointer;
}
</style>

<style scoped>
.btn-azul {
    padding: 10px 20px;
    background-color: #157dc2;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>
