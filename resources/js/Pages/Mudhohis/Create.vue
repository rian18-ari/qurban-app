<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    activePeriod: Object,
});

const form = useForm({
    name: '',
    phone_number: '',
    address: '',
    type: 'Cow',
    is_full_animal: false,
});

const submit = () => {
    form.post(route('mudhohis.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Daftar Mudhohi Baru" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Daftar Mudhohi Baru - Periode {{ activePeriod.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="max-w-xl space-y-6">
                            <div>
                                <InputLabel for="name" value="Nama Mudhohi" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="phone_number" value="Nomor Telepon/WA" />
                                <TextInput
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.phone_number" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="address" value="Alamat (Opsional)" />
                                <textarea
                                    id="address"
                                    v-model="form.address"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel value="Jenis Hewan Qurban" />
                                <div class="mt-2 space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.type" value="Cow" class="text-indigo-600">
                                        <span class="ml-2">Sapi</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.type" value="Goat" class="text-indigo-600">
                                        <span class="ml-2">Kambing</span>
                                    </label>
                                </div>
                            </div>

                            <div v-if="form.type === 'Cow'">
                                <InputLabel value="Opsi Pembelian Sapi" />
                                <div class="mt-2 space-y-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" :value="false" v-model="form.is_full_animal" class="text-indigo-600">
                                        <span class="ml-2">Patungan (1/7 Sapi)</span>
                                    </label>
                                    <br>
                                    <label class="inline-flex items-center">
                                        <input type="radio" :value="true" v-model="form.is_full_animal" class="text-indigo-600">
                                        <span class="ml-2">Utuh (1 Ekor Sapi)</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Daftarkan</PrimaryButton>
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Berhasil disimpan.</p>
                                </Transition>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
