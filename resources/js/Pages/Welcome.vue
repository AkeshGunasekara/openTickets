<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>

    <Head title="Welcome" />

    <div class="row mt-5">
        <img src="../../assets/images.png" class="logoImg pb-4" />
    </div>
    <div class="row mt-2 mb-2">
        <h2 class="customH2 mb-3">ONLINE-SUPPORT</h2>
    </div>
    <div class="row">
        <center>

            <Link :href="route('login')"><button class="button"><span>Login </span></button></Link>

            <Link :href="route('register')"><button class="button"><span>New-Ticket</span></button></Link>

        </center>
    </div>
    <div class="mt-3 ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
        Developed by Akesh.A.A.Gunsekara
    </div>
     
</template>
