<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

type ResetRequest = {
    id: number;
    user: {
        id: number;
        name: string;
        email: string;
        roles: Array<{ name: string }>;
    };
    token: string;
    status: 'pending' | 'approved' | 'completed' | 'rejected';
    created_at: string;
};

defineProps<{
    resetRequests: {
        data: ResetRequest[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        from: number;
        to: number;
        total: number;
    };
}>();

const isProcessing = ref<Record<number, boolean>>({});

const handleApprove = (id: number) => {
    isProcessing.value[id] = true;
    router.post(
        route('admin.password-resets.approve', id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Permintaan reset password berhasil disetujui!', {
                    icon: '✅',
                });
            },
            onFinish: () => {
                isProcessing.value[id] = false;
            },
        },
    );
};

const handleReject = (id: number) => {
    isProcessing.value[id] = true;
    router.post(
        route('admin.password-resets.reject', id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Permintaan reset password ditolak.', {
                    icon: '⚠️',
                });
            },
            onFinish: () => {
                isProcessing.value[id] = false;
            },
        },
    );
};

const getRoleBadgeClass = (roles: Array<{ name: string }>) => {
    const roleName = roles[0]?.name || 'SISWA';

    if (roleName === 'GURU') {
        return 'border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400';
    }

    return 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400';
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'pending':
            return {
                label: 'Menunggu Persetujuan',
                class: 'border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-950/30 text-yellow-700 dark:text-yellow-400 animate-pulse',
            };
        case 'approved':
            return {
                label: 'Disetujui (Menunggu User)',
                class: 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400',
            };
        case 'completed':
            return {
                label: 'Selesai',
                class: 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400',
            };
        case 'rejected':
            return {
                label: 'Ditolak / Kedaluwarsa',
                class: 'border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400',
            };
        default:
            return {
                label: status,
                class: 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-355',
            };
    }
};

const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);

    return date.toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};

const page = usePage();
const authUser = computed(() => page.props.auth?.user);
</script>

<template>
    <Head title="Kelola Reset Password" />

    <div class="min-h-screen px-4 py-6 font-sans md:px-8 lg:px-10">
        <div
            class="mx-auto mb-8 flex max-w-7xl flex-col items-start justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h1
                    class="text-[26px] font-bold tracking-tight text-slate-900 dark:text-white"
                >
                    Kelola Reset Password
                </h1>
                <p class="mt-1 text-[14px] font-medium text-slate-500">
                    Setujui atau tolak permintaan reset password siswa dan guru
                    secara real-time.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <Button
                    variant="outline"
                    size="icon"
                    class="h-9 w-9 rounded-lg border-slate-200 bg-white text-slate-800 shadow-sm transition-colors hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-800 dark:bg-[#0b0c16]/80 dark:text-slate-300 dark:hover:bg-indigo-950/50 dark:hover:text-indigo-400"
                >
                    <i class="pi pi-bell text-[14px]"></i>
                </Button>

                <div
                    class="flex cursor-pointer items-center gap-3 rounded-full border border-slate-200 bg-white py-1.5 pr-4 pl-1.5 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-[#0b0c16]/80 dark:hover:bg-slate-900/50"
                >
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-[11px] font-bold text-white shadow-inner"
                    >
                        <i class="pi pi-shield text-[10px]"></i>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-[13px] leading-none font-bold text-slate-800 dark:text-slate-200"
                            >{{ authUser?.name || 'Admin' }}</span
                        >
                        <span
                            class="mt-0.5 text-[9px] font-bold tracking-wider text-indigo-500"
                            >ROLE: ADMIN</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl">
            <Card
                class="overflow-hidden rounded-xl border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#0b0c16]/50 dark:backdrop-blur-md"
            >
                <CardHeader class="border-b border-transparent p-6">
                    <div>
                        <CardTitle
                            class="text-[17px] font-bold text-slate-800 dark:text-white"
                            >Daftar Pengajuan Reset</CardTitle
                        >
                        <CardDescription
                            class="mt-1 text-[13px] font-medium text-slate-500"
                        >
                            Menampilkan
                            <span
                                class="font-semibold text-slate-700 dark:text-slate-300"
                                >{{ resetRequests.from || 0 }} -
                                {{ resetRequests.to || 0 }}</span
                            >
                            dari
                            <span
                                class="font-semibold text-slate-700 dark:text-slate-300"
                                >{{ resetRequests.total || 0 }}</span
                            >
                            pengajuan
                        </CardDescription>
                    </div>
                </CardHeader>

                <CardContent class="p-0">
                    <Table>
                        <TableHeader
                            class="border-y border-slate-200 bg-slate-50/80 dark:border-slate-800 dark:bg-slate-900/60"
                        >
                            <TableRow class="border-none hover:bg-transparent">
                                <TableHead
                                    class="h-11 px-6 align-middle text-[11px] font-bold tracking-wider text-slate-500 uppercase"
                                    >Pengguna</TableHead
                                >
                                <TableHead
                                    class="h-11 align-middle text-[11px] font-bold tracking-wider text-slate-500 uppercase"
                                    >Role</TableHead
                                >
                                <TableHead
                                    class="h-11 align-middle text-[11px] font-bold tracking-wider text-slate-500 uppercase"
                                    >Waktu Pengajuan</TableHead
                                >
                                <TableHead
                                    class="h-11 align-middle text-[11px] font-bold tracking-wider text-slate-500 uppercase"
                                    >Status</TableHead
                                >
                                <TableHead
                                    class="h-11 pr-6 text-right align-middle text-[11px] font-bold tracking-wider text-slate-500 uppercase"
                                    >Aksi</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody class="dark:bg-transparent">
                            <TableRow
                                v-for="req in resetRequests.data"
                                :key="req.id"
                                class="border-b border-slate-100 transition-colors hover:bg-slate-50/60 dark:border-slate-800/60 dark:hover:bg-slate-800/30"
                            >
                                <TableCell class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[14px] font-semibold text-slate-900 dark:text-slate-100"
                                            >{{
                                                req.user
                                                    ? req.user.name
                                                    : 'User Terhapus'
                                            }}</span
                                        >
                                        <span
                                            class="text-[12px] font-medium text-slate-500 dark:text-slate-400"
                                            >{{
                                                req.user ? req.user.email : '-'
                                            }}</span
                                        >
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        v-if="req.user"
                                        variant="outline"
                                        :class="
                                            getRoleBadgeClass(
                                                req.user?.roles || [],
                                            )
                                        "
                                        class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase"
                                    >
                                        {{
                                            req.user?.roles[0]?.name || 'SISWA'
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell
                                    class="text-[13px] font-medium text-slate-600 dark:text-slate-400"
                                >
                                    {{ formatDateTime(req.created_at) }}
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        variant="outline"
                                        :class="
                                            getStatusBadge(req.status).class
                                        "
                                        class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase"
                                    >
                                        {{ getStatusBadge(req.status).label }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="py-4 pr-6 text-right">
                                    <div
                                        v-if="req.status === 'pending'"
                                        class="flex justify-end gap-2"
                                    >
                                        <Button
                                            size="sm"
                                            class="h-8 rounded-lg bg-blue-600 px-3 text-[11px] font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                                            :disabled="isProcessing[req.id]"
                                            @click="handleApprove(req.id)"
                                        >
                                            Setujui
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            class="dark:hover:text-rose-350 h-8 rounded-lg border-slate-200 px-3 text-[11px] font-bold text-rose-600 shadow-sm transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 dark:border-slate-800 dark:text-rose-400 dark:hover:border-rose-900 dark:hover:bg-rose-950/30"
                                            :disabled="isProcessing[req.id]"
                                            @click="handleReject(req.id)"
                                        >
                                            Tolak
                                        </Button>
                                    </div>
                                    <div
                                        v-else-if="req.status === 'approved'"
                                        class="flex justify-end"
                                    >
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            class="dark:hover:text-rose-350 h-8 rounded-lg border-slate-200 px-3 text-[11px] font-bold text-rose-600 shadow-sm transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700 dark:border-slate-800 dark:text-rose-400 dark:hover:border-rose-900 dark:hover:bg-rose-950/30"
                                            :disabled="isProcessing[req.id]"
                                            @click="handleReject(req.id)"
                                        >
                                            Batalkan Persetujuan
                                        </Button>
                                    </div>
                                    <span
                                        v-else
                                        class="text-[12px] font-medium text-slate-400 dark:text-slate-500"
                                        >-</span
                                    >
                                </TableCell>
                            </TableRow>
                            <TableRow
                                v-if="
                                    !resetRequests.data ||
                                    resetRequests.data.length === 0
                                "
                            >
                                <TableCell
                                    colspan="5"
                                    class="py-8 text-center text-sm text-slate-400 dark:text-slate-500"
                                >
                                    Tidak ada riwayat pengajuan reset password.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>

                <div
                    v-if="resetRequests.links && resetRequests.links.length > 3"
                    class="flex items-center justify-between border-t border-slate-200 bg-slate-50/50 p-4 px-6 dark:border-slate-800 dark:bg-slate-900/20"
                >
                    <span
                        class="hidden text-[13px] font-medium text-slate-500 sm:block"
                        >Paginasi Halaman</span
                    >
                    <div class="flex items-center gap-1">
                        <template
                            v-for="(link, index) in resetRequests.links"
                            :key="index"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-md px-3 py-1.5 text-[13px] font-semibold transition-colors"
                                :class="
                                    link.active
                                        ? 'border border-slate-200 bg-white text-slate-900 shadow-sm dark:border-slate-800 dark:bg-[#070814] dark:text-white'
                                        : 'dark:hover:bg-slate-855 text-slate-600 hover:bg-slate-200/50 dark:text-slate-400'
                                "
                            >
                                <span v-html="link.label"></span>
                            </Link>
                            <span
                                v-else
                                class="cursor-not-allowed rounded-md px-3 py-1.5 text-[13px] font-semibold text-slate-300 transition-colors dark:text-slate-600"
                            >
                                <span v-html="link.label"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
