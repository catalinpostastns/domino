<template>
    <Head title="Domino game" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <Link :href="route('login')" class="text-sm text-amber-400 underline">
            Log in
        </Link>

        <Link :href="route('register')" class="ml-4 text-sm text-amber-400 underline">
            Register
        </Link>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3';

export default {
    components: {
      Head,
      Link,
    },
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
    },
}
</script>
