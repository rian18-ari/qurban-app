<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    mudhohis: Object,
    activePeriod: Object,
});
</script>

<template>
    <Head title="Daftar Mudhohi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Daftar Mudhohi - Periode {{ activePeriod.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Peserta Qurban</h3>
                            <Link :href="route('mudhohis.create')">
                                <PrimaryButton>Daftar Mudhohi Baru</PrimaryButton>
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Hewan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok / No. Urut</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Daftar</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="mudhohi in mudhohis.data" :key="mudhohi.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ mudhohi.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ mudhohi.phone_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ mudhohi.type === 'Cow' ? 'Sapi' : 'Kambing' }}
                                            <span v-if="mudhohi.is_full_animal" class="ml-1 text-xs text-indigo-600 font-bold">(1 Ekor)</span>
                                            <span v-else-if="mudhohi.type === 'Cow'" class="ml-1 text-xs text-green-600 font-bold">(Patungan)</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-semibold">{{ mudhohi.animal_group?.name || '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(mudhohi.created_at).toLocaleString('id-ID') }}
                                        </td>
                                    </tr>
                                    <tr v-if="mudhohis.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada peserta terdaftar.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (Simple) -->
                        <div class="mt-4 flex justify-between" v-if="mudhohis.links.length > 3">
                             <div class="flex space-x-2">
                                <Link
                                    v-for="(link, k) in mudhohis.links"
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
    </AuthenticatedLayout>
</template>
