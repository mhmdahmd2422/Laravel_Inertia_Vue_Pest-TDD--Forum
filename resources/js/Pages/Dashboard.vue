<script setup>
import Welcome from '@/Components/Welcome.vue';
import Shell from "@/Components/Shell.vue";
</script>

<template>
    <Shell title="Dashboard">
        <template #header>
            <h2 class="text-3xl font-bold tracking-tight text-white">
                Dashboard
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>
    </Shell>
</template>
