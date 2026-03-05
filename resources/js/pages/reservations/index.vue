<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { index } from '@/routes/reservations';
import ReservationController from '@/actions/App/Http/Controllers/ReservationController';

type Vehicle ={
    id:number;
    plate: string;
}

type Reservations = {
    id: number;
    id_vehicle: number;
    plate: string;
    description: string;
    reservation_date: string;
    state: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
};

type Props = {
    reservations: Paginated<Reservations>;
    vehicles: Vehicle[];
};

const props = defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reservaciones', href: index().url },
];

/**
 * Creation form:
 * - password is required for new users
 * - roles/permissions are optional arrays
 */
const form = useForm({
    id_vehicle: 0,
    plate: '',
    description: '',
    state: '',
    reservation_date: '',
});

/**
 * Edit flow:
 * - selected user is copied into the edit form
 * - password remains optional to avoid forced resets
 */
const editingReservationId = ref<number | null>(null);
    const isEditing = computed(() => editingReservationId.value !== null);
// const editForm = useForm({
//     id_vehicle: 0,
//     plate: '',
//     description: '',
//     state: ''
// });

const startEdit = (reservation: Reservations) => {
    editingReservationId.value = reservation.id;
    form.id_vehicle = reservation.id_vehicle;
    form.plate = reservation.plate;
    form.description = reservation.description;
    form.state = reservation.state;
    console.log(form);
};

const cancelEdit = () => {
    editingReservationId.value = null;
    form.reset();
    form.clearErrors();
};

const submitCreate = () => {

    if (isEditing.value && editingReservationId.value !== null) {
        form.put(ReservationController.update.url(editingReservationId.value), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
        return;
    }

    form.post(ReservationController.store.url(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const resetForm = (): void => {
    editingReservationId.value = null;
    form.reset();
    form.clearErrors();
};

// const submitEdit = () => {
//     if (!editingReservationId.value) return;

//     form.patch(`/reservations/${editingReservationId.value}`, {
//         preserveScroll: true,
//         onSuccess: () => cancelEdit(),
//     });
// };

const deleteUser = (reservation: Reservations) => {
    if (!confirm(`Eliminar a ${reservation.plate}? Esta acción no se puede deshacer.`)) return;

    form.delete(ReservationController.destroy.url(reservation.id), {
        preserveScroll: true,
    });
};

const selectClass =
    'w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#2b4485] focus:border-[#2b4485] transition-colors';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Administrar usuarios" />

        <div class="space-y-8 p-4 md:p-6">
            <Heading title="CRUD de Usuarios"
                description="Crear, editar, activar o desactivar usuarios, con asignación de roles y permisos." />

            <div v-if="flash.success" class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ flash.error }}
            </div>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Crear Reservacion</h3>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
                   
                    <div class="grid gap-2">
                        <Label for="state">Vehiculo</Label>
                        <select id="state" v-model="form.id_vehicle" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50">
                            <option v-for="vehicle in props.vehicles" :value="vehicle.id">{{ vehicle.plate }}</option>
                        </select>
                        <InputError :message="form.errors.state" />
                    </div>
                    
                    <div class="space-y-1">
                        <Label for="create-dni">Description</Label>
                        <Input id="create-dni" v-model="form.description" type="text" required/>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="space-y-1">
                        <Label for="create-name">Fecha de reservacion</Label>
                        <Input id="create-name" v-model="form.reservation_date" type="date" required/>
                        <InputError :message="form.errors.reservation_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="state">Estado</Label>
                        <select id="state" v-model="form.state" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50">
                            <option value="accepted">Aceptado</option>
                            <option value="rejected">Rechazado</option>
                        </select>
                        <InputError :message="form.errors.state" />
                    </div>

                    <div class="col-span-full flex gap-2">
                        <Button type="submit" :disabled="form.processing ">
                            {{ isEditing ? 'Actualizar' : 'Crear' }}
                        </Button>
                        <Button v-if="editingReservationId" type="button" variant="secondary"
                            :disabled="form.processing " @click="resetForm">
                            Cancelar
                        </Button>
                    </div>
                </form>
            </section>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Listado</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b">
                            <tr>
                                <th class="px-2 py-2">Placa</th>
                                <th class="px-2 py-2">Fecha Reservacion</th>
                                <th class="px-2 py-2">Direccion</th>
                                <th class="px-2 py-2">Estado</th>
                                <th class="px-2 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="reservation in props.reservations.data" :key="reservation.id" class="border-b align-top">
                                <td class="px-2 py-2">
                                    <p class="font-medium">{{ reservation.plate }}</p>
                                    <!-- <p class="text-xs text-muted-foreground">{{ reservation.user }}</p> -->
                                </td>
                                <td class="px-2 py-2">
                                    <p class="font-medium">{{ reservation.reservation_date }}</p>
                                </td>
                                <td class="px-2 py-2">
                                    <p class="font-medium">{{ reservation.description }}</p>
                                </td>
                                <td class="px-2 py-2">
                                    <p class="font-medium">{{ reservation.state }}</p>
                                </td>
                                <td class="space-x-2 px-2 py-2">
                                    <Button variant="outline" size="sm" @click="startEdit(reservation)">Editar</Button>
                                    <Button variant="destructive" size="sm" @click="deleteUser(reservation)">Eliminar</Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- <section v-if="editingReservationId" class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Editar usuario #{{ editingReservationId }}</h3>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitEdit">
                    <div class="space-y-1">
                        <Label for="edit-dni">Dni</Label>
                        <Input id="edit-dni" v-model="editForm.dni" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-name">Nombre</Label>
                        <Input id="edit-name" v-model="editForm.name" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-lastname">Apellido</Label>
                        <Input id="edit-lastname" v-model="editForm.lastname" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-phone">Celular</Label>
                        <Input id="edit-phone" v-model="editForm.phone" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-address">Direccion</Label>
                        <Input id="edit-address" v-model="editForm.address" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-email">Correo</Label>
                        <Input id="edit-email" type="email" v-model="editForm.email" />
                        <InputError :message="editForm.errors.email" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-password">Nuevo password (opcional)</Label>
                        <Input id="edit-password" type="password" v-model="editForm.password" />
                        <InputError :message="editForm.errors.password" />
                    </div>

                    <div class="space-y-1">
                        <Label for="edit-password-confirmation">Confirmar nuevo password</Label>
                        <Input id="edit-password-confirmation" type="password"
                            v-model="editForm.password_confirmation" />
                    </div>

                    <div>
                        <Label for="create-company">Compañia</Label>
                        <select v-model="editForm.id_company" :class="selectClass" required>
                            <option value="0" disabled>
                                Seleccionar...
                            </option>
                            <option v-for="comp in props.company" :key="comp.id" :value="comp.id">{{ comp.company_name }}</option>
                        </select>
                        <InputError :message="editForm.errors.id_company" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" v-model="editForm.is_active" />
                            Cuenta activa
                        </label>
                        <InputError :message="editForm.errors.is_active" />
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Roles</p>
                        <div class="grid gap-1">
                            <label v-for="role in props.roles" :key="`edit-role-${role.id}`"
                                class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" :checked="editForm.roles.includes(role.id)"
                                    @change="toggleRole(editForm, role.id)" />
                                {{ role.name }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Permisos directos</p>
                        <div class="grid gap-1">
                            <label v-for="permission in props.permissions" :key="`edit-permission-${permission.id}`"
                                class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" :checked="editForm.permissions.includes(permission.id)"
                                    @change="togglePermission(editForm, permission.id)" />
                                {{ permission.name }}
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 md:col-span-2">
                        <Button :disabled="editForm.processing">Actualizar</Button>
                        <Button type="button" variant="outline" @click="cancelEdit">Cancelar</Button>
                    </div>
                </form>
            </section> -->
        </div>
    </AppLayout>
</template>
