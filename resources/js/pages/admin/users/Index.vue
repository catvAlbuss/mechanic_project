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

type UserRow = {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    roles: string[];
    role_ids: number[];
    permissions: string[];
    permission_ids: number[];
    created_at: string | null;
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
    users: Paginated<UserRow>;
    roles: CatalogItem[];
    permissions: CatalogItem[];
};

const props = defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Usuarios', href: '/admin/users' },
];

/**
 * Creation form:
 * - password is required for new users
 * - roles/permissions are optional arrays
 */
const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_active: true,
    roles: [] as number[],
    permissions: [] as number[],
});

/**
 * Edit flow:
 * - selected user is copied into the edit form
 * - password remains optional to avoid forced resets
 */
const editingUserId = ref<number | null>(null);
const editForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_active: true,
    roles: [] as number[],
    permissions: [] as number[],
});

const startEdit = (user: UserRow) => {
    editingUserId.value = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.password = '';
    editForm.password_confirmation = '';
    editForm.is_active = user.is_active;
    editForm.roles = [...user.role_ids];
    editForm.permissions = [...user.permission_ids];
};

const cancelEdit = () => {
    editingUserId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const submitCreate = () => {
    createForm.post('/admin/users', {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const submitEdit = () => {
    if (!editingUserId.value) return;

    editForm.patch(`/admin/users/${editingUserId.value}`, {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
};

const toggleRole = (form: typeof createForm | typeof editForm, roleId: number) => {
    form.roles = form.roles.includes(roleId)
        ? form.roles.filter((id) => id !== roleId)
        : [...form.roles, roleId];
};

const togglePermission = (form: typeof createForm | typeof editForm, permissionId: number) => {
    form.permissions = form.permissions.includes(permissionId)
        ? form.permissions.filter((id) => id !== permissionId)
        : [...form.permissions, permissionId];
};

const toggleActive = (user: UserRow) => {
    editForm.patch(`/admin/users/${user.id}/toggle-active`, {
        preserveScroll: true,
    });
};

const deleteUser = (user: UserRow) => {
    if (!confirm(`Eliminar a ${user.name}? Esta acción no se puede deshacer.`)) return;

    editForm.delete(`/admin/users/${user.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Administrar usuarios" />

        <div class="space-y-8 p-4 md:p-6">
            <Heading
                title="CRUD de Usuarios"
                description="Crear, editar, activar o desactivar usuarios, con asignación de roles y permisos."
            />

            <div v-if="flash.success" class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ flash.error }}
            </div>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Crear usuario</h3>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
                    <div class="space-y-1">
                        <Label for="create-name">Nombre</Label>
                        <Input id="create-name" v-model="createForm.name" />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div class="space-y-1">
                        <Label for="create-email">Correo</Label>
                        <Input id="create-email" type="email" v-model="createForm.email" />
                        <InputError :message="createForm.errors.email" />
                    </div>

                    <div class="space-y-1">
                        <Label for="create-password">Password</Label>
                        <Input id="create-password" type="password" v-model="createForm.password" />
                        <InputError :message="createForm.errors.password" />
                    </div>

                    <div class="space-y-1">
                        <Label for="create-password-confirmation">Confirmar password</Label>
                        <Input
                            id="create-password-confirmation"
                            type="password"
                            v-model="createForm.password_confirmation"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" v-model="createForm.is_active" />
                            Cuenta activa
                        </label>
                        <InputError :message="createForm.errors.is_active" />
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Roles</p>
                        <div class="grid gap-1">
                            <label
                                v-for="role in props.roles"
                                :key="`create-role-${role.id}`"
                                class="inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    type="checkbox"
                                    :checked="createForm.roles.includes(role.id)"
                                    @change="toggleRole(createForm, role.id)"
                                />
                                {{ role.name }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Permisos directos</p>
                        <div class="grid gap-1">
                            <label
                                v-for="permission in props.permissions"
                                :key="`create-permission-${permission.id}`"
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

                    <div class="md:col-span-2">
                        <Button :disabled="createForm.processing">Guardar usuario</Button>
                    </div>
                </form>
            </section>

            <section class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Listado</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b">
                            <tr>
                                <th class="px-2 py-2">Usuario</th>
                                <th class="px-2 py-2">Roles</th>
                                <th class="px-2 py-2">Estado</th>
                                <th class="px-2 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in props.users.data" :key="user.id" class="border-b align-top">
                                <td class="px-2 py-2">
                                    <p class="font-medium">{{ user.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                </td>
                                <td class="px-2 py-2">
                                    {{ user.roles.join(', ') || 'Sin roles' }}
                                </td>
                                <td class="px-2 py-2">
                                    <span :class="user.is_active ? 'text-green-700' : 'text-red-700'">
                                        {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="space-x-2 px-2 py-2">
                                    <Button variant="outline" size="sm" @click="startEdit(user)">Editar</Button>
                                    <Button variant="outline" size="sm" @click="toggleActive(user)">
                                        {{ user.is_active ? 'Desactivar' : 'Activar' }}
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="deleteUser(user)">Eliminar</Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in props.users.links"
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

            <section v-if="editingUserId" class="rounded-lg border p-4">
                <h3 class="mb-4 text-sm font-semibold">Editar usuario #{{ editingUserId }}</h3>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitEdit">
                    <div class="space-y-1">
                        <Label for="edit-name">Nombre</Label>
                        <Input id="edit-name" v-model="editForm.name" />
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
                        <Input id="edit-password-confirmation" type="password" v-model="editForm.password_confirmation" />
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
                            <label
                                v-for="role in props.roles"
                                :key="`edit-role-${role.id}`"
                                class="inline-flex items-center gap-2 text-sm"
                            >
                                <input
                                    type="checkbox"
                                    :checked="editForm.roles.includes(role.id)"
                                    @change="toggleRole(editForm, role.id)"
                                />
                                {{ role.name }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium">Permisos directos</p>
                        <div class="grid gap-1">
                            <label
                                v-for="permission in props.permissions"
                                :key="`edit-permission-${permission.id}`"
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

                    <div class="flex items-center gap-2 md:col-span-2">
                        <Button :disabled="editForm.processing">Actualizar</Button>
                        <Button type="button" variant="outline" @click="cancelEdit">Cancelar</Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
