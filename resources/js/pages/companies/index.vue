<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CompanyController from '@/actions/App/Http/Controllers/CompanyController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as companiesIndex } from '@/routes/companies';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Compañia',
        href: companiesIndex().url,
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

type Props = {
    companies: Company[];
};

const props = defineProps<Props>();
const companies = computed(() => props.companies);
// const roles = computed(() => props.roles);

const editingId = ref<number | null>(null);

const form = useForm({
    avatar: '',
    ruc: '',
    company_name: '',
    address: '',
    district: '',
    province: '',
    department: '',
    state: 'active',
    registration_date: '',
    config: JSON,
});

const deleteForm = useForm({});
const deleteError = computed(() => (deleteForm.errors as Record<string, string | undefined>).delete);

const isEditing = computed(() => editingId.value !== null);

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const startEdit = (companies: Company): void => {
    editingId.value = companies.id;
    form.clearErrors();
    form.avatar = companies.avatar;
    form.ruc = companies.ruc;
    form.company_name = companies.company_name;
    form.address = companies.address;
    form.district = companies.district;
    form.province = companies.province;
    form.department = companies.department;
    form.state = companies.state;
    form.registration_date = companies.registration_date;
    form.config = companies.config;

};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(CompanyController.update.url(editingId.value), options);
        return;
    }

    form.post(CompanyController.store.url(), options);
};

const remove = (companies: Company): void => {
    if (!confirm(`Eliminar Compañia: "${companies.company_name}"?`)) {
        return;
    }

    deleteForm.delete(CompanyController.destroy.url(companies.id), {
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

                    <div class="grid gap-2">
                        <Label for="avatar">Avatar</Label>
                        <Input id="avatar" v-model="form.avatar" type="file"/>
                        <InputError :message="form.errors.avatar" />
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
                        <Label for="district">Distrito</Label>
                        <Input id="district" v-model="form.district" type="text" required />
                        <InputError :message="form.errors.district" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="province">Provincia</Label>
                        <Input id="province" v-model="form.province" type="text" required />
                        <InputError :message="form.errors.province" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="department">Departamento</Label>
                        <Input id="department" v-model="form.department" type="text" required />
                        <InputError :message="form.errors.department" />
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
                            <tr v-if="companies.length === 0">
                                <td colspan="6" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                            <tr v-for="c in companies" :key="c.id" class="border-b">
                                <td class="px-2 py-2">{{ c.id }}</td>
                                <td class="px-2 py-2">{{ c.ruc }}</td>
                                <td class="px-2 py-2">{{ c.company_name }}</td>
                                <td class="px-2 py-2">{{ c.address }}</td>
                                <td class="px-2 py-2">{{ c.district }}</td>
                                <td class="px-2 py-2">{{ c.province }}</td>
                                <td class="px-2 py-2">{{ c.department }}</td>
                                <td class="px-2 py-2">{{ c.registration_date }}</td>
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
