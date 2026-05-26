<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    sessions: Array,
    activePeriod: Object,
});

const isCreatingSession = ref(false);

const form = useForm({
    name: '',
    start_time: '',
    end_time: '',
    quota: 50,
});

const openCreateModal = () => {
    isCreatingSession.value = true;
    form.reset();
    form.clearErrors();
};

const closeCreateModal = () => {
    isCreatingSession.value = false;
    form.reset();
};

const storeSession = () => {
    form.post(route('sessions.store'), {
        preserveScroll: true,
        onSuccess: () => closeCreateModal(),
    });
};
</script>

<template>
    <Head title="Sesi Antrean" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Manajemen Sesi Antrean - {{ activePeriod.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Sesi Distribusi</h3>
                            <PrimaryButton @click="openCreateModal">
                                Tambah Sesi Baru
                            </PrimaryButton>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div v-for="session in sessions" :key="session.id" class="border rounded-lg p-4 shadow-sm bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-lg text-indigo-700">{{ session.name }}</h4>
                                    <span class="text-xs font-mono bg-white px-2 py-1 rounded border">
                                        {{ session.start_time }} - {{ session.end_time }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>Terisi: {{ session.mustahiqs_count }} / {{ session.quota }}</span>
                                        <span class="font-bold" :class="session.remaining_quota > 0 ? 'text-green-600' : 'text-red-600'">
                                            Sisa: {{ session.remaining_quota }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="bg-indigo-600 h-2 rounded-full transition-all duration-500" 
                                            :style="{ width: Math.min((session.mustahiqs_count / session.quota) * 100, 100) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="sessions.length === 0" class="col-span-full py-10 text-center text-gray-500 border-2 border-dashed rounded-lg">
                                Belum ada sesi antrean. Silakan tambah sesi terlebih dahulu.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="isCreatingSession" @close="closeCreateModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tambah Sesi Antrean</h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Nama Sesi" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="e.g. Sesi 1 / Pagi" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="start_time" value="Jam Mulai" />
                            <TextInput id="start_time" v-model="form.start_time" type="time" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.start_time" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="end_time" value="Jam Selesai" />
                            <TextInput id="end_time" v-model="form.end_time" type="time" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.end_time" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="quota" value="Kuota (Orang)" />
                        <TextInput id="quota" v-model="form.quota" type="number" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.quota" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeCreateModal">Batal</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="storeSession">Simpan</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
