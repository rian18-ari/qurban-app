<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import axios from 'axios';

const couponCode = ref('');
const result = ref(null);
const loading = ref(false);

const scanCoupon = async () => {
    if (!couponCode.value) return;
    
    loading.value = true;
    result.value = null;
    
    try {
        const response = await axios.post(route('gatekeeper.validate'), { coupon_code: couponCode.value });
        result.value = {
            success: true,
            message: response.data.message,
            data: response.data.data
        };
        couponCode.value = ''; // Reset input
    } catch (error) {
        result.value = {
            success: false,
            type: error.response?.data?.type || 'error',
            message: error.response?.data?.message || 'Terjadi kesalahan sistem.',
            data: error.response?.data?.data
        };
    } finally {
        loading.value = false;
    }
};

const getBgColor = () => {
    if (!result.value) return 'bg-white';
    if (result.value.success) return 'bg-green-500 text-white';
    if (result.value.type === 'wrong_session') return 'bg-yellow-500 text-white';
    return 'bg-red-600 text-white';
};
</script>

<template>
    <Head title="Scanner Gatekeeper" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gatekeeper - Validasi Kupon Mustahiq
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-lg sm:px-6 lg:px-8">
                <div :class="['overflow-hidden shadow-sm sm:rounded-lg p-6 transition-colors duration-300', getBgColor()]">
                    <div v-if="!result" class="text-gray-900">
                        <h3 class="text-lg font-medium mb-4 text-center">Scan QR Code / Masukkan Kode Kupon</h3>
                        <form @submit.prevent="scanCoupon" class="space-y-4">
                            <div>
                                <InputLabel for="coupon_code" value="Kode Kupon" />
                                <TextInput 
                                    id="coupon_code" 
                                    v-model="couponCode" 
                                    type="text" 
                                    class="mt-1 block w-full text-black font-mono text-center text-2xl" 
                                    placeholder="QRB-XXX-XXX"
                                    required 
                                    autofocus
                                />
                            </div>
                            <PrimaryButton class="w-full justify-center py-4" :disabled="loading">
                                VALIDASI KUPON
                            </PrimaryButton>
                        </form>
                    </div>

                    <div v-else class="text-center py-4">
                        <div class="text-6xl mb-4">
                            {{ result.success ? '✅' : (result.type === 'wrong_session' ? '⚠️' : '❌') }}
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ result.message }}</h3>
                        
                        <div v-if="result.data" class="mt-6 p-4 bg-white/20 rounded text-left">
                            <p><strong>Nama:</strong> {{ result.data.name }}</p>
                            <p><strong>Kode:</strong> <span class="font-mono">{{ result.data.coupon_code }}</span></p>
                            <p><strong>Sesi:</strong> {{ result.data.distribution_session?.name }}</p>
                            <p><strong>Waktu:</strong> {{ result.data.distribution_session?.start_time }} - {{ result.data.distribution_session?.end_time }}</p>
                        </div>

                        <button 
                            @click="result = null" 
                            class="mt-8 px-6 py-2 bg-white text-black font-bold rounded-lg shadow"
                        >
                            Scan Selanjutnya
                        </button>
                    </div>
                </div>
                
                <p class="mt-4 text-center text-sm text-gray-500 italic">
                    Tips: Gunakan alat Scanner QR untuk otomatisasi input atau masukkan kode manual.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
