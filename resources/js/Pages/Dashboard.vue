<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import AdminArea from './AdminArea.vue';
import ClientArea from './ClientArea.vue';
const user = usePage().props.value.auth.user;
let isAdmin = false;

if(user.is_admin){
    isAdmin = true;
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div  v-if="isAdmin" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <AdminArea ></AdminArea>
                </div>
                <div  v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <ClientArea></ClientArea>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
