<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    mustahiqs: Object,
    activePeriod: Object,
});

const isImporting = ref(false);
const importForm = useForm({
    file: null,
});

const openImportModal = () => {
    isImporting.value = true;
    importForm.reset();
    importForm.clearErrors();
};

const closeImportModal = () => {
    isImporting.value = false;
    importForm.reset();
};

const submitImport = () => {
    importForm.post(route('mustahiqs.import'), {
        preserveScroll: true,
        onSuccess: () => closeImportModal(),
    });
};
</script>

<template>
    <Head title="Daftar Mustahiq" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Manajemen Mustahiq - Periode {{ activePeriod.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Penerima Daging</h3>
                            <div class="space-x-2">
                                <Link :href="route('sessions.index')">
                                    <SecondaryButton>Atur Sesi Antrean</SecondaryButton>
                                </Link>
                                <PrimaryButton @click="openImportModal">
                                    Import Excel (Warga)
                                </PrimaryButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sesi Antrean</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="mustahiq in mustahiqs.data" :key="mustahiq.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ mustahiq.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ mustahiq.coupon_code || '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ mustahiq.address || '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs font-bold">
                                                {{ mustahiq.distribution_session?.name || 'Belum Ada Sesi' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="mustahiq.status === 'Received' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                                {{ mustahiq.status === 'Received' ? 'Sudah Diambil' : 'Belum Diambil' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="mustahiqs.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data mustahiq.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-between" v-if="mustahiqs.links.length > 3">
                             <div class="flex space-x-2">
                                <Link
                                    v-for="(link, k) in mustahiqs.links"
                                    :key="k"
                                    :href="link.url || '#'"
                                    class="px-4 py-2 border rounded text-sm"
                                    :class="{'bg-indigo-600 text-white': link.active, 'text-gray-500': !link.url}"
                                    v-html="link.label"
                                />
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <Modal :show="isImporting" @close="closeImportModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Import Data Mustahiq</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Gunakan file Excel (.xlsx) dengan kolom: Nama, NIK, Alamat, No. Telepon.
                    Pastikan Anda sudah membuat Sesi Antrean sebelum melakukan import.
                </p>

                <div class="mt-6">
                    <InputLabel for="file" value="Pilih File Excel" />
                    <input 
                        type="file" 
                        id="file"
                        @input="importForm.file = $event.target.files[0]"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                        accept=".xlsx,.xls,.csv"
                    />
                    <InputError :message="importForm.errors.file" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeImportModal">Batal</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': importForm.processing }" :disabled="importForm.processing" @click="submitImport">Mulai Import</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
