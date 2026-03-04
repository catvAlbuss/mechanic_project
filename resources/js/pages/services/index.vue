<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as serviceIndex } from '@/routes/services';
import { type BreadcrumbItem } from '@/types';
import ServiceController from '@/actions/App/Http/Controllers/ServiceController';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Servicio',
        href: serviceIndex().url,
    },
];

type Service = {
    id: number;
    id_diagnostic: number;
    id_user: number;
    orden_service: string;
    check_in_date: string;
    check_out_date: string;  
    
};

type Props = {
    services: Service[];
};

const props = defineProps<Props>();
const services = computed(() => props.services);
// const roles = computed(() => props.roles);

const editingId = ref<number | null>(null);

const form = useForm({
    id: 0,
    id_diagnostic: 0,
    id_user: 0,
    orden_service: '',
    check_in_date: '',
    check_out_date: '',
   
   
});

const deleteForm = useForm({});
const deleteError = computed(() => (deleteForm.errors as Record<string, string | undefined>).delete);

const isEditing = computed(() => editingId.value !== null);

const resetForm = (): void => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const startEdit = (service: Service): void => {
    editingId.value = service.id;

    form.clearErrors();

    form.id = service.id;
    form.id_diagnostic = service.id_diagnostic;
    form.id_user = service.id_user;
    form.orden_service = service.orden_service;
    form.check_in_date= service.check_in_date;
    form.check_out_date = service.check_out_date;
};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: () => resetForm(),
    };

    if (isEditing.value && editingId.value !== null) {
        form.put(ServiceController.update.url(editingId.value), options);
        return;
    }
    console.log(form);

    form.post(ServiceController.store.url(), options);
};

const remove = (service: Service): void => {
    if (!confirm(`Eliminar Servicio: "${service.orden_service}"?`)) {
        return;
    }

    deleteForm.delete(ServiceController.destroy.url(service.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="services" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">

            <section class="rounded-xl border border-sidebar-border/70 bg-background p-4">
                <h1 class="text-xl font-semibold">
                    {{ isEditing ? 'Editar servicio' : 'Nuevo servicio' }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Gestiona tu servicio.
                </p>

                <form class="mt-4 grid gap-4 md:grid-cols-2" @submit.prevent="submit">

                    <div class="grid gap-2">
                        <Label for="id_diagnostic">ID Diagnóstico</Label>
                        <Input id="id_diagnostic" v-model="form.id_diagnostic" type="text" required />
                        <InputError :message="form.errors.id_diagnostic" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="id_user">ID Usuario</Label>
                        <Input id="id_user" v-model="form.id_user" type="text" required />
                        <InputError :message="form.errors.id_user" />
                    </div>

                        
                    <div class="grid gap-2">
                        <Label for="orden_service">Orden de Servicio</Label>
                        <Input id="orden_service" v-model="form.orden_service" type="text" required />
                        <InputError :message="form.errors.orden_service" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="check_in_date">Fecha de Entrada</Label>
                        <Input id="check_in_date" v-model="form.check_in_date" type="date" required />
                        <InputError :message="form.errors.check_in_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="check_out_date">Fecha de Salida</Label>
                        <Input id="check_out_date" v-model="form.check_out_date" type="date" required />
                        <InputError :message="form.errors.check_out_date" />
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
                <h2 class="text-lg font-semibold">Listado de servicios</h2>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead class="border-b text-left">
                            <tr>
                                <th class="px-2 py-2">Id</th>
                                <th class="px-2 py-2">id_diagnostico</th>
                                <th class="px-2 py-2">id_usuario</th>
                                <th class="px-2 py-2">orden_service</th>
                                <th class="px-2 py-2">check_in_date</th>
                                <th class="px-2 py-2">check_out_date</th>
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="services.length === 0">
                                <td colspan="12" class="px-2 py-4 text-center text-muted-foreground">
                                    No hay servicios registrados.
                                </td>
                            </tr>
                            <tr v-for="c in services" :key="c.id" class="border-b">
                                <td class="px-2 py-2">{{ c.id }}</td>
                                <td class="px-2 py-2">{{ c.id_diagnostic }}</td>
                                <td class="px-2 py-2">{{ c.id_user }}</td>
                                <td class="px-2 py-2">{{ c.orden_service }}</td>
                                <td class="px-2 py-2">{{ c.check_in_date }}</td>
                                <td class="px-2 py-2">{{ c.check_out_date }}</td>
                                
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
