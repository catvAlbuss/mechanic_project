<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ComponentController from '@/actions/App/Http/Controllers/ComponentController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as componentsIndex } from '@/routes/components';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Components',
        href: componentsIndex().url,
    },
];

type Component = {
    id: number;
    id_category: number;
    sku: string;
    name: string;
    brand: string;
    cost_price: number;
    sale_price: number;
    stock: number;
    factory_date: string;
    made: string;
    state: 'active' | 'inactive';
    
};

type Category = {
    id: number;
    id_provider: number | null;
    name: string;
    brand: string;
    description: string;
    state: 'active' | 'inactive';
    config: JSON;
};

type Props = {
    components: Component[];
    categories: Category [];
};

const props = defineProps<Props>();
const components = computed(() => props.components);
const categories = computed(() => props.categories);
// const roles = computed(() => props.roles);

const editingId = ref<number | null>(null);

const form = useForm({
    id_category: props.categories?.[0]?.id ?? '',
    sku: '',
    name: '',
    brand: '',
    cost_price: 0,
    sale_price: 0,
    stock: 0,
    factory_date: '',
    made: '',
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

const startEdit = (component: Component): void => {
    editingId.value = component.id;
    form.clearErrors();
    form.id_category = component.id_category;
    form.sku = component.sku;
    form.name = component.name;
    form.brand = component.brand;
    form.cost_price = component.cost_price;
    form.sale_price = component.sale_price;
    form.stock = component.stock;
    form.factory_date = component.factory_date;
    form.made = component.made;
    form.state = component.state;
};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(ComponentController.update.url(editingId.value), options);
        return;
    }

    form.post(ComponentController.store.url(), options);
};

const remove = (component: Component): void => {
    if (!confirm(`Eliminar Componente: "${component.name}"?`)) {
        return;
    }

    deleteForm.delete(ComponentController.destroy.url(component.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Components" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-4">
                <h1 class="text-xl font-semibold">
                    {{ isEditing ? 'Editar componente' : 'Nuevo componente' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Gestiona componentes desde esta misma vista.
                </p>

                <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="submit">

                    <div class="grid gap-2">
                        <Label for="id_category">Categoría</Label>
                        <select id="id_category" v-model="form.id_category" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50">
                            <option value="">Seleccionar categoría</option>
                            <option v-for="category in categories" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.id_category" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="sku">SKU</Label>
                        <Input id="sku" v-model="form.sku" type="text" />
                        <InputError :message="form.errors.sku" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Nombre del Repuesto</Label>
                        <Input id="name" v-model="form.name" type="text" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="brand">Marca</Label>
                        <Input id="brand" v-model="form.brand" type="text" required />
                        <InputError :message="form.errors.brand" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cost_price">Precio de Costo</Label>
                        <Input id="cost_price" v-model="form.cost_price" type="number" step="0.01" required />
                        <InputError :message="form.errors.cost_price" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="sale_price">Precio de Venta</Label>
                        <Input id="sale_price" v-model="form.sale_price" type="number" step="0.01" required />
                        <InputError :message="form.errors.sale_price" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="stock">Stock</Label>
                        <Input id="stock" v-model="form.stock" type="number" required />
                        <InputError :message="form.errors.stock" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="factory_date">Fecha de Fabricación</Label>
                        <Input id="factory_date" v-model="form.factory_date" type="date" required />
                        <InputError :message="form.errors.factory_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="made">Hecho</Label>
                        <Input id="made" v-model="form.made" type="date" required />
                        <InputError :message="form.errors.made" />
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
                <h2 class="text-lg font-semibold">Listado de usuarios</h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead class="border-b text-left">
                            <tr>
                                <th class="px-2 py-2">ID</th>
                                <th class="px-2 py-2">RUC</th>
                                <th class="px-2 py-2">Nombre de la compañia</th>
                                <th class="px-2 py-2">Dirección</th>
                                <th class="px-2 py-2">Distrito</th>
                                <th class="px-2 py-2">Provincia</th>
                                <th class="px-2 py-2">Departamento</th>
                                <th class="px-2 py-2">Fecha de registro</th>
                                <th class="px-2 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="components.length === 0">
                                <td colspan="6" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                            <tr v-for="c in components" :key="c.id" class="border-b">
                                <td class="px-2 py-2">{{ c.id }}</td>
                                <td class="px-2 py-2">{{ c.id_category }}</td>
                                <td class="px-2 py-2">{{ c.sku }}</td>
                                <td class="px-2 py-2">{{ c.name }}</td>
                                <td class="px-2 py-2">{{ c.brand }}</td>
                                <td class="px-2 py-2">{{ c.cost_price }}</td>
                                <td class="px-2 py-2">{{ c.sale_price }}</td>
                                <td class="px-2 py-2">{{ c.stock }}</td>
                                <td class="px-2 py-2">{{ c.factory_date }}</td>
                                <td class="px-2 py-2">{{ c.made }}</td>
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
