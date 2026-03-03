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

type PermissionRow = {
    id: number;
    name: string;
    roles_count: number;
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
    permissions: Paginated<PermissionRow>;
};

const props = defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Permisos', href: '/admin/permissions' },
];

/**
 * Naming convention suggestion for juniors:
 * `module.action` -> example: `users.manage`
 */
const createForm = useForm({
    name: '',
});

const editPermissionId = ref<number | null>(null);
const editForm = useForm({
    name: '',
});

const startEdit = (permission: PermissionRow) => {
    editPermissionId.value = permission.id;
    editForm.name = permission.name;
};

const cancelEdit = () => {
    editPermissionId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const submitCreate = () => {
    createForm.post('/admin/permissions', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const submitEdit = () => {
    if (!editPermissionId.value) return;

    editForm.patch(`/admin/permissions/${editPermissionId.value}`, {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
};

const deletePermission = (permission: PermissionRow) => {
    if (!confirm(`Eliminar permiso ${permission.name}?`)) return;

    editForm.delete(`/admin/permissions/${permission.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Administrar permisos" />

        <div class="space-y-8 p-4 md:p-6">
            <Heading
                title="CRUD de Permisos"
                description="Los permisos son la unidad base de autorización y luego se agrupan en roles."
            />

            <div v-if="flash.success" class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ flash.error }}
            </div>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Crear permiso</h3>
                <form class="grid gap-4 md:max-w-xl" @submit.prevent="submitCreate">
                    <div class="space-y-1">
                        <Label for="create-permission-name">Nombre del permiso</Label>
                        <Input id="create-permission-name" v-model="createForm.name" placeholder="users.manage" />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div>
                        <Button :disabled="createForm.processing">Guardar permiso</Button>
                    </div>
                </form>
            </section>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Listado</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b">
                            <tr>
                                <th class="px-2 py-2">Permiso</th>
                                <th class="px-2 py-2">Roles que lo usan</th>
                                <th class="px-2 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="permission in props.permissions.data"
                                :key="permission.id"
                                class="border-b align-top"
                            >
                                <td class="px-2 py-2 font-medium">{{ permission.name }}</td>
                                <td class="px-2 py-2">{{ permission.roles_count }}</td>
                                <td class="space-x-2 px-2 py-2">
                                    <Button size="sm" variant="outline" @click="startEdit(permission)">Editar</Button>
                                    <Button size="sm" variant="destructive" @click="deletePermission(permission)">
                                        Eliminar
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in props.permissions.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'rounded border px-2 py-1 text-xs',
                            link.active ? 'bg-primary text-primary-foreground' : '',
                            !link.url ? 'pointer-events-none opacity-40' : '',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </section>

            <section v-if="editPermissionId" class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Editar permiso #{{ editPermissionId }}</h3>
                <form class="grid gap-4 md:max-w-xl" @submit.prevent="submitEdit">
                    <div class="space-y-1">
                        <Label for="edit-permission-name">Nombre</Label>
                        <Input id="edit-permission-name" v-model="editForm.name" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Button :disabled="editForm.processing">Actualizar</Button>
                        <Button type="button" variant="outline" @click="cancelEdit">Cancelar</Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
