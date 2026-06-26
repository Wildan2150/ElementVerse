<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        roles: Array<{ name: string }>;
    };
    roles: Array<{ id: number; name: string }>;
}>();

// Set default value dropdown ke role pertama yang dimiliki user
const form = useForm({
    role: props.user.roles[0]?.name || 'SISWA',
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head title="Ubah Role Pengguna" />

    <div class="min-h-screen bg-transparent px-6 py-8 font-sans lg:px-10">
        <div class="mx-auto max-w-3xl">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Ubah Role Pengguna
                </h2>
                <Link :href="route('admin.users.index')">
                    <Button
                        variant="outline"
                        class="h-9 border-slate-200 bg-white text-slate-800 shadow-sm dark:border-slate-800 dark:bg-[#070814]/80 dark:text-slate-200"
                    >
                        <i class="pi pi-arrow-left mr-2 text-xs"></i> Kembali
                    </Button>
                </Link>
            </div>

            <Card
                class="overflow-hidden rounded-xl border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#0b0c16]/50 dark:backdrop-blur-md"
            >
                <CardHeader
                    class="border-b border-slate-100 bg-slate-50/50 p-6 dark:border-slate-800 dark:bg-slate-900/20"
                >
                    <CardTitle
                        class="text-lg font-bold text-slate-800 dark:text-white"
                        >Detail Pengguna</CardTitle
                    >
                    <CardDescription
                        >Ubah hak akses untuk pengguna <b>{{ user.name }}</b
                        >.</CardDescription
                    >
                </CardHeader>

                <CardContent class="p-6">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                font-medium
                                text-slate-700
                                >Nama Lengkap</label
                            >
                            <input
                                type="text"
                                :value="user.name"
                                disabled
                                class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900/40 dark:text-slate-400"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                >Email</label
                            >
                            <input
                                type="text"
                                :value="user.email"
                                disabled
                                class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900/40 dark:text-slate-400"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300"
                                >Pilih Role Baru
                                <span class="text-rose-500">*</span></label
                            >
                            <select
                                v-model="form.role"
                                required
                                class="border-slate-305 w-full cursor-pointer rounded-lg border bg-white px-4 py-2 text-sm text-slate-800 shadow-sm transition-colors outline-none focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-800 dark:bg-[#070814]/80 dark:text-slate-200"
                            >
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.name"
                                >
                                    {{ role.name }}
                                </option>
                            </select>
                        </div>

                        <div class="flex justify-end pt-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-indigo-600 text-white shadow-sm hover:bg-indigo-700"
                            >
                                <i class="pi pi-save mr-2 text-xs"></i> Simpan
                                Perubahan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
