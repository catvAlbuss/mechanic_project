<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CategoryController from '@/actions/App/Http/Controllers/CategoryController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as companiesIndex } from '@/routes/companies';
import { type BreadcrumbItem } from '@/types';
//import CategoryController from '@/actions/CategoryController';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categorias',
        href: companiesIndex().url,
    },
];

type Categories = {
    id: number;
    id_provider: number | null;
    name: string;
    brand: string;
    description: string;
    state: 'active' | 'inactive';
    config: JSON;
};

type Provider = {
    id: number;
    id_company: number;
    ruc: string;
    company_name: string;
    address: string;
    email: string;
    contact: string;
    state: 'active' | 'inactive',
    registration_date: string,
};

type Props = {
    categories: Categories[];
    providers: Provider []; 
};

const props = defineProps<Props>();
const categories = computed(() => props.categories);
const providers = computed(() => props.providers);
// const roles = computed(() => props.roles);

const editingId = ref<number | null>(null);

const form = useForm({
    id_provider: props.providers?.[0]?.id ?? '',
    name: '',
    brand: '',
    description: '',
    state: 'active',
});

const deleteForm = useForm({});
const deleteError = computed(() => (deleteForm.errors as Record<string, string | undefined>).delete);

const isEditing = computed(() => editingId.value !== null);

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const startEdit = (categories: Categories): void => {
    editingId.value = categories.id;
    form.clearErrors();
    form.id_provider = categories.id_provider ?? props.providers?.[0]?.id ?? '';
    form.name = categories.name;
    form.brand = categories.brand;
    form.description = categories.description;
    form.state = categories.state;

};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(CategoryController.update.url(editingId.value), options);
        return;
    }

    form.post(CategoryController.store.url(), options);
};

const remove = (categories: Categories): void => {
    if (!confirm(`Eliminar Categoría: "${categories.name}"?`)) {
        return;
    }

    deleteForm.delete(CategoryController.destroy.url(categories.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Categorías" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-4">
                <h1 class="text-xl font-semibold">
                    {{ isEditing ? 'Editar categoría' : 'Nueva categoría' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Gestiona categorías desde esta misma vista.
                </p>

                <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="submit">

                    <div class="grid gap-2">
                        <Label for="avatar">Proveedor</Label>
                        <select id="avatar" v-model="form.id_provider" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50">
                            <option value="">Seleccionar proveedor</option>
                            <option v-for="provider in providers" :key="provider.id" :value="provider.id">
                                {{ provider.company_name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.id_provider" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="ruc">Nombre</Label>
                        <Input id="ruc" v-model="form.name" type="text" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="company_name">Nombre de la Marca</Label>
                        <Input id="company_name" v-model="form.brand" type="text" required />
                        <InputError :message="form.errors.brand" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Descripción</Label>
                        <Input id="address" v-model="form.description" type="text" required />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="state">Estado</Label>
                        <select id="state" v-model="form.state" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50">
                            <option value="active">🟢 Activo</option>
                            <option value="inactive">🔴 Inactivo</option>
                        </select>
                        <InputError :message="form.errors.state" />
                    </div>

                    <!-- <div class="grid gap-2">
                        <Label for="registration_date"></Label>
                        <Input id="" v-model="form.registration_date" type="date" required />
                    </div> -->

                    <div class="col-span-full flex gap-2">
                        <Button type="submit" :disabled="form.processing || deleteForm.processing">
                            {{ isEditing ? 'Actualizar' : 'Crear' }}
                        </Button>
                        <Button v-if="isEditing" type="button" variant="secondary"
                            :disabled="form.processing || deleteForm.processing" @click="resetForm">
                            Cancelar
                        </Button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-4">
                <h2 class="text-lg font-semibold">Listado de categorías</h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead class="border-b text-left">
                            <tr>
                                <th class="px-2 py-2">ID</th>
                                <th class="px-2 py-2">PROVEEDOR</th>
                                <th class="px-2 py-2">CATEGORÍA</th>
                                <th class="px-2 py-2">MARCA</th>
                                <th class="px-2 py-2">DESCRIPCION</th>
                                <th class="px-2 py-2">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="categories.length === 0">
                                <td colspan="6" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay categorías registradas.
                                </td>
                            </tr>
                            <tr v-for="c in categories" :key="c.id" class="border-b">
                                <td class="px-2 py-2">{{ c.id }}</td>
                                <td class="px-2 py-2">{{ c.id_provider }}</td>
                                <td class="px-2 py-2">{{ c.name }}</td>
                                <td class="px-2 py-2">{{ c.brand }}</td>
                                <td class="px-2 py-2">{{ c.description }}</td>
                                <td class="px-2 py-2">{{ c.state }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex gap-2">
                                        <Button type="button" variant="secondary" size="sm"
                                            :disabled="form.processing || deleteForm.processing" @click="startEdit(c)">
                                            Editar
                                        </Button>
                                        <Button type="button" variant="destructive" size="sm"
                                            :disabled="form.processing || deleteForm.processing" @click="remove(c)">
                                            Eliminar
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <InputError :message="deleteError" class="mt-3" />
            </section>

        </div>
    </AppLayout>
</template>
