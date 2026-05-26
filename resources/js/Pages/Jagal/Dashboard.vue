<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    animals: Array,
    activePeriod: Object,
});

const animalList = ref([...props.animals]);

const updateStatus = (animal, newStatus) => {
    useForm({ status: newStatus }).post(route('jagal.update-status', animal.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Update local state if needed (though Echo should handle it)
        }
    });
};

onMounted(() => {
    window.Echo.channel('animals')
        .listen('AnimalStatusUpdated', (e) => {
            const index = animalList.value.findIndex(a => a.id === e.animalGroup.id);
            if (index !== -1) {
                animalList.value[index] = e.animalGroup;
            }
        });
});

const getStatusColor = (status) => {
    switch (status) {
        case 'Antre': return 'bg-gray-100 text-gray-800';
        case 'Disembelih': return 'bg-red-100 text-red-800';
        case 'Dikuliti': return 'bg-yellow-100 text-yellow-800';
        case 'Selesai Cacah': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100';
    }
};
</script>

<template>
    <Head title="Dashboard Jagal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Jagal - Hari Eksekusi Qurban
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="animal in animalList" :key="animal.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold">{{ animal.name }}</h3>
                                <span :class="['px-3 py-1 rounded-full text-sm font-bold', getStatusColor(animal.status)]">
                                    {{ animal.status }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <button 
                                    @click="updateStatus(animal, 'Disembelih')"
                                    :disabled="animal.status !== 'Antre'"
                                    class="w-full py-2 px-4 rounded text-white font-bold bg-red-600 hover:bg-red-700 disabled:opacity-30"
                                >
                                    🔴 Sembelih Sekarang
                                </button>
                                
                                <button 
                                    @click="updateStatus(animal, 'Dikuliti')"
                                    :disabled="animal.status !== 'Disembelih'"
                                    class="w-full py-2 px-4 rounded text-white font-bold bg-yellow-500 hover:bg-yellow-600 disabled:opacity-30"
                                >
                                    🔪 Mulai Kuliti
                                </button>

                                <button 
                                    @click="updateStatus(animal, 'Selesai Cacah')"
                                    :disabled="animal.status !== 'Dikuliti'"
                                    class="w-full py-2 px-4 rounded text-white font-bold bg-green-600 hover:bg-green-700 disabled:opacity-30"
                                >
                                    📦 Selesai Cacah & Packing
                                </button>
                            </div>
                            
                            <p class="mt-4 text-xs text-gray-500 text-center">
                                Mudhohi akan otomatis menerima WA saat status "Disembelih".
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
