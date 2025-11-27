<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

import { router, useForm, usePage } from '@inertiajs/vue3';

import axios from 'axios';
import { onMounted, ref } from 'vue';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

declare const route: (...params: any) => string;

interface CategoriaData 
{
    id: number;
    nombre_categoria: string;
    descripcion: string;
    estado: number;
    created_at?: string;
    updated_at?: string;
}

interface ApiResponse 
{
    success: boolean;
    nombre: string;
    data: CategoriaData[];
}

const page = usePage();
const categorias = ref<CategoriaData[]>([]);
const minombre = ref('');
const showDialog = ref(false);
const isEditMode = ref(false);
const isLoading = ref(true);

const form = useForm
({
    id: null as number | null,
    nombre_categoria: '',
    descripcion: '',
    estado: true,
});

const breadcrumbs = 
[
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

const openCreateDialog = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
};

const openEditDialog = (item: CategoriaData) => {
    isEditMode.value = true;
    form.clearErrors();
    form.id = item.id;
    form.nombre_categoria = item.nombre_categoria;
    form.descripcion = item.descripcion;
    form.estado = item.estado === 1;
    showDialog.value = true;
};

const submitForm = () => {
    const url =
        isEditMode.value && form.id
            ? `/categorias/${form.id}`
            : '/categorias';

    const method = isEditMode.value ? 'patch' : 'post';

    form[method](url, {
        onSuccess: () => {
            showDialog.value = false;
            listarCategoria();
            checkFlashMessage();
        },
    });
};

const confirmDelete = (item: CategoriaData) => {
    if (confirm(`¿Eliminar la categoría "${item.nombre_categoria}"?`)) {
        router.delete(`/categorias/${item.id}`, {
            onSuccess: () => {
                listarCategoria();
                checkFlashMessage();
            },
        });
    }
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
                <small>{{ minombre }}</small>
            </div>
            <div class="dashboard-settings">
                <div class="report-section my-4 flex flex-col items-center justify-center">
                    <h3>Reporte de Categorías</h3>
                    <br>
                    <a href="/categorias-exportar-pdf" class="btn btn-success">
                        Descargar
                    </a><br>
                    <Button @click="openCreateDialog">
                        + Nueva Categoría
                    </Button><br>    
                </div>
            </div>

            <div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y-2 divide-gray-200 dark:divide-gray-700">
                        <thead class="ltr:text-left rtl:text-right">
                            <tr class="*:font-medium *:text-gray-900 dark:*:text-white">
                                <th class="px-3 py-2 whitespace-nowrap">
                                    Nombre
                                </th>
                                <th class="px-3 py-2 whitespace-nowrap">
                                    Descripcion
                                </th>
                                <th class="px-3 py-2 whitespace-nowrap">
                                    Estado
                                </th>
                                <th class="px-3 py-2 whitespace-nowrap flex flex-col items-center justify-center">
                                    Opciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 *:even:bg-gray-50 dark:divide-gray-700 dark:*:even:bg-gray-800">
                            <tr v-for="item in categorias" :key="item.id" class="*:text-gray-900 *:first:font-medium dark:*:text-white">
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
                                    <Button class="btn-azul" variant="outline" size="sm" @click="openEditDialog(item)">
                                        Editar
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="confirmDelete(item)">
                                        Eliminar
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Dialog :open="showDialog" @update:open="showDialog = $event">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{
                        isEditMode ? 'Editar Categoría' : 'Crear Categoría'
                    }}</DialogTitle>
                    <DialogDescription>
                        Completa la información de la categoría aquí.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="name" class="text-right">Nombre</Label>
                        <div class="col-span-3">
                            <Input id="name" v-model="form.nombre_categoria" />
                            <InputError
                                :message="form.errors.nombre_categoria"
                                class="mt-1"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="desc" class="text-right">Descripción</Label>
                        <div class="col-span-3">
                            <Input id="desc" v-model="form.descripcion" />
                            <InputError
                                :message="form.errors.descripcion"
                                class="mt-1"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-4 items-center gap-4">
                        <Label for="status" class="text-right">Estado</Label>
                        <div class="col-span-3 flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="status"
                                v-model="form.estado"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            />
                            <span class="text-sm text-gray-600">{{
                                form.estado ? 'Activo' : 'Inactivo'
                            }}</span>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditMode ? 'Guardar Cambios' : 'Crear' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
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
