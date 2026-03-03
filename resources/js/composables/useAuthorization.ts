import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Centralized permission helpers for Vue pages.
 *
 * Why this composable:
 * - avoids scattered string comparisons in templates
 * - keeps authorization checks readable for junior developers
 */
export function useAuthorization() {
    const page = usePage();

    const permissions = computed<string[]>(() => {
        const auth = page.props.auth as { permissions?: string[] } | undefined;

        return auth?.permissions ?? [];
    });

    const roles = computed<string[]>(() => {
        const auth = page.props.auth as { roles?: string[] } | undefined;

        return auth?.roles ?? [];
    });

    const can = (permission: string): boolean => permissions.value.includes(permission);
    const hasRole = (role: string): boolean => roles.value.includes(role);

    return {
        permissions,
        roles,
        can,
        hasRole,
    };
}
