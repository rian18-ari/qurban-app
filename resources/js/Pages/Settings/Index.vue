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
    periods: Array,
});

const isCreatingPeriod = ref(false);
const isEditingPeriod = ref(false);
const editingPeriodId = ref(null);

const form = useForm({
    name: '',
    cow_patungan_cost: 0,
    goat_operational_cost: 0,
});

const openCreateModal = () => {
    isCreatingPeriod.value = true;
    form.reset();
    form.clearErrors();
};

const closeCreateModal = () => {
    isCreatingPeriod.value = false;
    form.reset();
};

const storePeriod = () => {
    form.post(route('settings.periods.store'), {
        preserveScroll: true,
        onSuccess: () => closeCreateModal(),
    });
};

const openEditModal = (period) => {
    isEditingPeriod.value = true;
    editingPeriodId.value = period.id;
    form.name = period.name;
    form.cow_patungan_cost = period.cow_patungan_cost;
    form.goat_operational_cost = period.goat_operational_cost;
    form.clearErrors();
};

const closeEditModal = () => {
    isEditingPeriod.value = false;
    editingPeriodId.value = null;
    form.reset();
};

const updatePeriod = () => {
    form.put(route('settings.periods.update', editingPeriodId.value), {
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
    });
};

const setActivePeriod = (periodId) => {
    useForm({}).post(route('settings.periods.set-active', periodId), {
        preserveScroll: true,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};
</script>

<template>
    <Head title="Pengaturan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pengaturan Periode Qurban
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Periode</h3>
                            <PrimaryButton @click="openCreateModal">
                                Tambah Periode Baru
                            </PrimaryButton>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Periode / Tahun</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Sapi (Patungan)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Ops. Kambing</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="period in periods" :key="period.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ period.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(period.cow_patungan_cost) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(period.goat_operational_cost) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="period.is_active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Tidak Aktif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button v-if="!period.is_active" @click="setActivePeriod(period.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Set Aktif</button>
                                            <button @click="openEditModal(period)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                        </td>
                                    </tr>
                                    <tr v-if="periods.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data periode.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="isCreatingPeriod || isEditingPeriod" @close="isCreatingPeriod ? closeCreateModal() : closeEditModal()">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isCreatingPeriod ? 'Tambah Periode Baru' : 'Edit Periode' }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Nama Periode (Tahun)" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. 1446 H"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="cow_patungan_cost" value="Biaya Patungan Sapi (Rp)" />
                        <TextInput
                            id="cow_patungan_cost"
                            v-model="form.cow_patungan_cost"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="3500000"
                            required
                        />
                        <InputError :message="form.errors.cow_patungan_cost" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="goat_operational_cost" value="Biaya Operasional Kambing (Rp)" />
                        <TextInput
                            id="goat_operational_cost"
                            v-model="form.goat_operational_cost"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="200000"
                            required
                        />
                        <InputError :message="form.errors.goat_operational_cost" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="isCreatingPeriod ? closeCreateModal() : closeEditModal()">
                        Batal
                    </SecondaryButton>

                    <PrimaryButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="isCreatingPeriod ? storePeriod() : updatePeriod()"
                    >
                        Simpan
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
