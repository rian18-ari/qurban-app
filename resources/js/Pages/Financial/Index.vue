<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    logs: Array,
    summary: Object,
    activePeriod: Object,
});

const isAddingTransaction = ref(false);
const form = useForm({
    description: '',
    type: 'Expense',
    amount: 0,
    transaction_date: new Date().toISOString().split('T')[0],
    category: '',
});

const openModal = () => {
    isAddingTransaction.value = true;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.post(route('financial.store'), {
        onSuccess: () => isAddingTransaction.value = false,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};
</script>

<template>
    <Head title="Keuangan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Laporan Keuangan Qurban - {{ activePeriod.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                        <p class="text-sm text-gray-500 uppercase font-bold">Total Pemasukan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(summary.total_income) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                        <p class="text-sm text-gray-500 uppercase font-bold">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(summary.total_expense) }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                        <p class="text-sm text-gray-500 uppercase font-bold">Sisa Saldo Kas</p>
                        <p class="text-2xl font-bold text-indigo-700">{{ formatCurrency(summary.balance) }}</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium">Log Transaksi</h3>
                            <div class="space-x-2">
                                <a :href="route('reports.export')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Export LPJ (Excel)
                                </a>
                                <PrimaryButton @click="openModal">Catat Transaksi</PrimaryButton>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in logs" :key="log.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ log.transaction_date }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ log.description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ log.category }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold" :class="log.type === 'Income' ? 'text-green-600' : 'text-red-600'">
                                            {{ log.type === 'Income' ? '+' : '-' }} {{ formatCurrency(log.amount) }}
                                        </td>
                                    </tr>
                                    <tr v-if="logs.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada transaksi.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Transaction Modal -->
        <Modal :show="isAddingTransaction" @close="isAddingTransaction = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Catat Transaksi Kas</h2>
                <form @submit.prevent="submit" class="mt-6 space-y-4">
                    <div>
                        <InputLabel value="Tipe Transaksi" />
                        <div class="mt-2 space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" v-model="form.type" value="Income" class="text-indigo-600">
                                <span class="ml-2">Pemasukan</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" v-model="form.type" value="Expense" class="text-indigo-600">
                                <span class="ml-2">Pengeluaran</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <InputLabel for="description" value="Keterangan" />
                        <TextInput id="description" v-model="form.description" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="category" value="Kategori" />
                            <TextInput id="category" v-model="form.category" type="text" class="mt-1 block w-full" placeholder="e.g. Pakan, Tenda, Upah" />
                        </div>
                        <div>
                            <InputLabel for="amount" value="Jumlah (Rp)" />
                            <TextInput id="amount" v-model="form.amount" type="number" class="mt-1 block w-full" required />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="transaction_date" value="Tanggal" />
                        <TextInput id="transaction_date" v-model="form.transaction_date" type="date" class="mt-1 block w-full" required />
                    </div>
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="isAddingTransaction = false">Batal</SecondaryButton>
                        <PrimaryButton class="ms-3" :disabled="form.processing">Simpan Transaksi</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
