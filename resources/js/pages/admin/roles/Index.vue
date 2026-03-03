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

type CatalogItem = {
    id: number;
    name: string;
};

type RoleRow = {
    id: number;
    name: string;
    users_count: number;
    permissions: string[];
    permission_ids: number[];
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
    roles: Paginated<RoleRow>;
    permissions: CatalogItem[];
};

const props = defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roles', href: '/admin/roles' },
];

/**
 * Uniform forms:
 * - createForm and editForm share field names to reduce cognitive load.
 */
const createForm = useForm({
    name: '',
    permissions: [] as number[],
});

const editingRoleId = ref<number | null>(null);
const editForm = useForm({
    name: '',
    permissions: [] as number[],
});

const startEdit = (role: RoleRow) => {
    editingRoleId.value = role.id;
    editForm.name = role.name;
    editForm.permissions = [...role.permission_ids];
};

const cancelEdit = () => {
    editingRoleId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const togglePermission = (form: typeof createForm | typeof editForm, permissionId: number) => {
    form.permissions = form.permissions.includes(permissionId)
        ? form.permissions.filter((id) => id !== permissionId)
        : [...form.permissions, permissionId];
};

const submitCreate = () => {
    createForm.post('/admin/roles', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const submitEdit = () => {
    if (!editingRoleId.value) return;

    editForm.patch(`/admin/roles/${editingRoleId.value}`, {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
};

const deleteRole = (role: RoleRow) => {
    if (!confirm(`Eliminar el rol ${role.name}?`)) return;

    editForm.delete(`/admin/roles/${role.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Administrar roles" />

        <div class="space-y-8 p-4 md:p-6">
            <Heading
                title="CRUD de Roles"
                description="Cada rol agrupa permisos y se asigna a usuarios."
            />

            <div v-if="flash.success" class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ flash.error }}
            </div>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Crear rol</h3>
                <form class="grid gap-4" @submit.prevent="submitCreate">
                    <div class="space-y-1">
                        <Label for="create-role-name">Nombre del rol</Label>
                        <Input id="create-role-name" v-model="createForm.name" />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Permisos</p>
                        <div class="grid gap-1 md:grid-cols-2">
                            <label
                                v-for="permission in props.permissions"
                                :key="`create-role-permission-${permission.id}`"
                                class="inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    type="checkbox"
                                    :checked="createForm.permissions.includes(permission.id)"
                                    @change="togglePermission(createForm, permission.id)"
                                />
                                {{ permission.name }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <Button :disabled="createForm.processing">Guardar rol</Button>
                    </div>
                </form>
            </section>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Listado</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b">
                            <tr>
                                <th class="px-2 py-2">Rol</th>
                                <th class="px-2 py-2">Permisos</th>
                                <th class="px-2 py-2">Usuarios</th>
                                <th class="px-2 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="role in props.roles.data" :key="role.id" class="border-b align-top">
                                <td class="px-2 py-2 font-medium">{{ role.name }}</td>
                                <td class="px-2 py-2">{{ role.permissions.join(', ') || 'Sin permisos' }}</td>
                                <td class="px-2 py-2">{{ role.users_count }}</td>
                                <td class="space-x-2 px-2 py-2">
                                    <Button size="sm" variant="outline" @click="startEdit(role)">Editar</Button>
                                    <Button size="sm" variant="destructive" @click="deleteRole(role)">Eliminar</Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in props.roles.links"
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

            <section v-if="editingRoleId" class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Editar rol #{{ editingRoleId }}</h3>
                <form class="grid gap-4" @submit.prevent="submitEdit">
                    <div class="space-y-1">
                        <Label for="edit-role-name">Nombre del rol</Label>
                        <Input id="edit-role-name" v-model="editForm.name" />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Permisos</p>
                        <div class="grid gap-1 md:grid-cols-2">
                            <label
                                v-for="permission in props.permissions"
                                :key="`edit-role-permission-${permission.id}`"
                                class="inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    type="checkbox"
                                    :checked="editForm.permissions.includes(permission.id)"
                                    @change="togglePermission(editForm, permission.id)"
                                />
                                {{ permission.name }}
                            </label>
                        </div>
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
