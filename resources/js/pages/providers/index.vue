<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProviderController from '@/actions/App/Http/Controllers/ProviderController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as providersIndex } from '@/routes/providers';
import { type BreadcrumbItem } from '@/types';
import { email } from '@/routes/password';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Proveedor',
        href: providersIndex().url,
    },
];

type Company = {
    id: number;
    avatar: string;
    ruc: string;
    company_name: string;
    address: string;
    district: string;
    province: string;
    department: string;
    state: 'active' | 'inactive';
    registration_date: string;
    config: JSON;
};

type Provider = {
    id: number;
    id_company: number | null;
    ruc: string;
    company_name: string;
    address: string;
    email: string;
    contact: string;
    state: 'active' | 'inactive',
    registration_date: string,
};

type Props = {
    providers: Provider[];
    companies: Company[];
};

const props = defineProps<Props>();
const providers = computed(() => props.providers);
const companies = computed(() => props.companies);

const editingId = ref<number | null>(null);

const form = useForm({
    id_company: props.companies?.[0]?.id ?? '',
    ruc: '',
    company_name: '',
    address: '',
    email: '',
    contact: '',
    state: 'active',
    registration_date: '',
});

const deleteForm = useForm({});
const deleteError = computed(() => (deleteForm.errors as Record<string, string | undefined>).delete);

const isEditing = computed(() => editingId.value !== null);

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.id_company = props.companies?.[0]?.id ?? '';
};

const startEdit = (providers: Provider): void => {
    editingId.value = providers.id;
    form.clearErrors();
    form.id_company = providers.id_company ?? props.companies?.[0]?.id ?? '';
    form.ruc = providers.ruc;
    form.company_name = providers.company_name;
    form.address = providers.address;
    form.email = providers.email;
    form.contact = providers.contact;
    form.state = providers.state;
    form.registration_date = providers.registration_date;
};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(ProviderController.update.url(editingId.value), options);
        return;
    }

    form.post(ProviderController.store.url(), options);
};

const remove = (providers: Provider): void => {
    if (!confirm(`Eliminar Compañia: "${providers.company_name}"?`)) {
        return;
    }

    deleteForm.delete(ProviderController.destroy.url(providers.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Company" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-4">
                <h1 class="text-xl font-semibold">
                    {{ isEditing ? 'Editar usuario' : 'Nuevo usuario' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Gestiona usuarios, rol y personal desde esta misma vista.
                </p>

                <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="submit">

                    <div class="space-y-2">
                        <Label for="id_company" class="text-sm font-medium text-gray-700">Categoría </Label>
                        <select id="id_company" v-model="form.id_company" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="" disabled>Seleccione</option>
                            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.company_name }}</option>
                        </select>
                        <InputError :message="form.errors.id_company" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="ruc">RUC</Label>
                        <Input id="ruc" v-model="form.ruc" type="text" />
                        <InputError :message="form.errors.ruc" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="company_name">Nombre de la compañia</Label>
                        <Input id="company_name" v-model="form.company_name" type="text" required />
                        <InputError :message="form.errors.company_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="address">Dirección</Label>
                        <Input id="address" v-model="form.address" type="text" required />
                        <InputError :message="form.errors.address" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" required />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="contact">Teléfono</Label>
                        <Input id="contact" v-model="form.contact" type="text" required />
                        <InputError :message="form.errors.contact" />
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
                                <th class="px-2 py-2">ID Proveedor</th>
                                <th class="px-2 py-2">RUC</th>
                                <th class="px-2 py-2">Nombre de la compañia</th>
                                <th class="px-2 py-2">Dirección</th>
                                <th class="px-2 py-2">Correo</th>
                                <th class="px-2 py-2">Teléfono</th>
                                <th class="px-2 py-2">Fecha de registro</th>
                                <th class="px-2 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="providers.length === 0">
                                <td colspan="6" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                            <tr v-for="p in providers" :key="p.id" class="border-b">
                                <td class="px-2 py-2">{{ p.id }}</td>
                                <td class="px-2 py-2">{{ p.id_company }}</td>
                                <td class="px-2 py-2">{{ p.ruc }}</td>
                                <td class="px-2 py-2">{{ p.company_name }}</td>
                                <td class="px-2 py-2">{{ p.address }}</td>
                                <td class="px-2 py-2">{{ p.email }}</td>
                                <td class="px-2 py-2">{{ p.contact }}</td>
                                <td class="px-2 py-2">{{ p.registration_date }}</td>
                                <td class="px-2 py-2">{{ p.state }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex gap-2">
                                        <Button type="button" variant="secondary" size="sm"
                                            :disabled="form.processing || deleteForm.processing" @click="startEdit(p)">
                                            Editar
                                        </Button>
                                        <Button type="button" variant="destructive" size="sm"
                                            :disabled="form.processing || deleteForm.processing" @click="remove(p)">
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
