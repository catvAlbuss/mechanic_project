<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CompanyController from '@/actions/App/Http/Controllers/CompanyController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as vehiclesIndex } from '@/routes/vehicles';
import { type BreadcrumbItem } from '@/types';
import VehicleController from '@/actions/App/Http/Controllers/VehicleController';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Vehículo',
        href: vehiclesIndex().url,
    },
];

type Vehicle = {
    id: number;
    id_user: string;
    plate: string;
    tipe: string;
    vin: string;
    engine: string;
    color: string;
    brand: string;
    year: string;
    model: string;
    mileage:number;
    state: string;
    
};

type Props = {
    vehicles: Vehicle[];
};

const props = defineProps<Props>();
const vehicles = computed(() => props.vehicles);
// const roles = computed(() => props.roles);

const editingId = ref<number | null>(null);

const form = useForm({
    
    id_user: '',
    plate: '',
    tipe: '',
    vin: '',
    engine: '',
    color: '',
    brand: '',
    year: '',
    state: 'active',
    mileage: 0,
    model: ''
   
});

const deleteForm = useForm({});
const deleteError = computed(() => (deleteForm.errors as Record<string, string | undefined>).delete);

const isEditing = computed(() => editingId.value !== null);

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const startEdit = (vehicle: Vehicle): void => {
    editingId.value = vehicle.id;
    form.clearErrors();
    form.id_user = vehicle.id_user;
    form.plate = vehicle.plate;
    form.tipe = vehicle.tipe;
    form.vin = vehicle.vin;
    form.engine = vehicle.engine;
    form.color = vehicle.color;
    form.brand = vehicle.brand;
    form.year = vehicle.year;
    form.mileage = vehicle.mileage;
 
    form.model = vehicle.model;
    form.state=vehicle.state;
};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(VehicleController.update.url(editingId.value), options);
        return;
    }
    console.log(form);

    form.post(VehicleController.store.url(), options);
};

const remove = (vehicle: Vehicle): void => {
    if (!confirm(`Eliminar Vehículo: "${vehicle.plate}"?`)) {
        return;
    }

    deleteForm.delete(VehicleController.destroy.url(vehicle.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="vehicle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-4">
                <h1 class="text-xl font-semibold">
                    {{ isEditing ? 'Editar vehículo' : 'Nuevo vehículo' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Gestiona tu vehículo.
                </p>

                <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="submit">

                    <div class="grid gap-2">
                        <Label for="id_user">ID Usuario</Label>
                        <Input id="id_user" v-model="form.id_user" type="text" required />
                        <InputError :message="form.errors.id_user" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="plate">Placa</Label>
                        <Input id="plate" v-model="form.plate" type="text" required />
                        <InputError :message="form.errors.plate" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="tipe">Tipo</Label>
                       
                        <select id="tipe" v-model="form.tipe" required
                         class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50">
                            <option value="motorcycle">motocicleta</option>
                            <option value="car">carro</option>
                            <option value="truck">camion</option>
                        </select>
                    </div>

                    <div class="grid gap-2">
                        <Label for="vin">VIN</Label>
                        <Input id="vin" v-model="form.vin" type="text" required />
                        <InputError :message="form.errors.vin" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="engine">Motor</Label>
                        <Input id="engine" v-model="form.engine" type="text" required />
                        <InputError :message="form.errors.engine" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="color">Color</Label>
                        <Input id="color" v-model="form.color" type="text" required />
                        <InputError :message="form.errors.color" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="brand">Marca</Label>
                        <Input id="brand" v-model="form.brand" type="text" required />
                        <InputError :message="form.errors.brand" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="year">Año</Label>
                        <Input id="year" v-model="form.year" type="date" required />
                        <InputError :message="form.errors.year" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="mileage">Kilometraje</Label>
                        <Input id="mileage" v-model="form.mileage" type="text" required />
                        <InputError :message="form.errors.mileage" />
                    </div>


                    <div class="grid gap-2">
                        <Label for="model">Modelo</Label>
                        <Input id="model" v-model="form.model" type="text" required />
                        <InputError :message="form.errors.model" />
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
                                <th class="px-2 py-2">Id_usuario</th>
                                <th class="px-2 py-2">Plate</th>
                                <th class="px-2 py-2">Tipo</th>
                                <th class="px-2 py-2">Vin</th>
                                <th class="px-2 py-2">Motor</th>
                                <th class="px-2 py-2">Color</th>
                                <th class="px-2 py-2">Marca</th>
                                <th class="px-2 py-2">Año</th>
                                <th class="px-2 py-2">Kilometraje</th>
                                <th class="px-2 py-2">Modelo</th>
                                <th class="px-2 py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="vehicles.length === 0">
                                <td colspan="12" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay vehículos registrados.
                                </td>
                            </tr>
                            <tr v-for="c in vehicles" :key="c.id" class="border-b">
                                <td class="px-2 py-2">{{ c.id_user }}</td>
                                <td class="px-2 py-2">{{ c.plate }}</td>
                                <td class="px-2 py-2">{{ c.tipe }}</td>
                                <td class="px-2 py-2">{{ c.vin }}</td>
                                <td class="px-2 py-2">{{ c.engine }}</td>
                                <td class="px-2 py-2">{{ c.color }}</td>
                                <td class="px-2 py-2">{{ c.brand }}</td>
                                <td class="px-2 py-2">{{ c.year }}</td>
                                <td class="px-2 py-2">{{ c.mileage }}</td>
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
