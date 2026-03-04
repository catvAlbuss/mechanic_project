<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DiagnosticController from '@/actions/App/Http/Controllers/DiagnosticController';
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

type User = {
    id: number;

};

type Reservation = {
    id: number;

};

type Diagnostic = {
    id: number;
    id_user: number,
    id_reservation: number,
    description: string,
    registration_date: string,
    cost_aprox: number,
};

type Props = {
    diagnostics: Diagnostic[];
    users: User[];
    reservations: Reservation[];
};

const props = defineProps<Props>();
const diagnostics = computed(() => props.diagnostics);
const users = computed(() => props.users);
const reservations = computed(() => props.reservations);


const editingId = ref<number | null>(null);

const form = useForm({
    id_user: props.users?.[0]?.id ?? '',
    id_reservation: props.reservations?.[0]?.id ?? '',
    description: '',
    registration_date: '',
    cost_aprox: 0,
});

const deleteForm = useForm({});
const deleteError = computed(() => (deleteForm.errors as Record<string, string | undefined>).delete);

const isEditing = computed(() => editingId.value !== null);

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.id_user = props.users?.[0]?.id ?? '';
    form.id_reservation = props.reservations?.[0]?.id ?? '';
};

const startEdit = (diagnostics: Diagnostic): void => {
    editingId.value = diagnostics.id;
    form.clearErrors();
    form.id_user = diagnostics.id_user ?? props.users?.[0]?.id ?? '';
    form.id_reservation = diagnostics.id_reservation ?? props.reservations?.[0]?.id ?? '';
    form.description = diagnostics.description;
    form.registration_date = diagnostics.description;
    form.cost_aprox = diagnostics.cost_aprox;
};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(DiagnosticController.update.url(editingId.value), options);
        return;
    }

    form.post(DiagnosticController.store.url(), options);
};

const remove = (diagnostics: Diagnostic): void => {
    if (!confirm(`Eliminar Diagnóstico N° : "${diagnostics.id}"?`)) {
        return;
    }

    deleteForm.delete(DiagnosticController.destroy.url(diagnostics.id), {
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
                        <Label for="id_user" class="text-sm font-medium text-gray-700">Usuario </Label>
                        <select id="id_user" v-model="form.id_user" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="" disabled>Seleccione</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">{{ u.id }}</option>
                        </select>
                        <InputError :message="form.errors.id_user" />
                    </div>

                    <div class="space-y-2">
                        <Label for="id_reservation" class="text-sm font-medium text-gray-700">N° de cita</Label>
                        <select id="id_reservation" v-model="form.id_reservation" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <option value="" disabled>Seleccione</option>
                            <option v-for="r in reservations" :key="r.id" :value="r.id">{{ r.id }}</option>
                        </select>
                        <InputError :message="form.errors.id_reservation" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Descripción</Label>
                        <Input id="description" v-model="form.description" type="text" />
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cost_aprox">Costo aproximado</Label>
                        <Input id="cost_aprox" v-model="form.cost_aprox" type="text" required />
                        <InputError :message="form.errors.cost_aprox" />
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
                                <th class="px-2 py-2">ID Usuario</th>
                                <th class="px-2 py-2">ID Reservación</th>
                                <th class="px-2 py-2">Descripción</th>
                                <th class="px-2 py-2">Costo aproximado</th>
                                <th class="px-2 py-2">Fecha de registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="diagnostics.length === 0">
                                <td colspan="6" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                            <tr v-for="d in diagnostics" :key="d.id" class="border-b">
                                <td class="px-2 py-2">{{ d.id }}</td>
                                <td class="px-2 py-2">{{ d.id_user }}</td>
                                <td class="px-2 py-2">{{ d.id_reservation }}</td>
                                <td class="px-2 py-2">{{ d.description }}</td>
                                <td class="px-2 py-2">{{ d.cost_aprox }}</td>
                                <td class="px-2 py-2">{{ d.registration_date }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex gap-2">
                                        <Button type="button" variant="secondary" size="sm"
                                            :disabled="form.processing || deleteForm.processing" @click="startEdit(d)">
                                            Editar
                                        </Button>
                                        <Button type="button" variant="destructive" size="sm"
                                            :disabled="form.processing || deleteForm.processing" @click="remove(d)">
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
