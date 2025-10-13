<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="SmartManagement- Gestão Inteligente">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse justify-center overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div class="flex flex-1 flex-col justify-center p-4 lg:p-8">
                    <h1
                        class="mb-4 text-3xl font-bold lg:text-4xl dark:text-[#EDEDEC]"
                    >
                        Gerencia sua empresa com o
                        <span class="text-[#1976D2]">Smart Management</span>
                    </h1>
                    <p class="mb-6 text-lg text-gray-600 dark:text-gray-400">
                        Feito para facilitar a gestão no seu dia a dia. Crie,
                        edite, organize de forma facilitada.
                    </p>

                    <div class="mb-8 flex flex-col gap-4 sm:flex-row">
                        <Link
                            :href="register()"
                            class="inline-flex items-center justify-center rounded-md bg-blue-500 px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-blue-600"
                        >
                            Começar Agora
                        </Link>
                        <Link
                            :href="login()"
                            class="inline-flex items-center justify-center rounded-md border border-[#19140035] px-6 py-3 text-sm font-medium text-[#1b1b18] transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Saiba Mais
                        </Link>
                    </div>
                </div>
                <div class="flex flex-1 items-center justify-center p-4 lg:p-8">
                    <div class="relative w-full max-w-md">
                        <div
                            class="rounded-2xl bg-gradient-to-br from-[#1976D2] to-[#42A5F5] p-8 shadow-xl"
                        >
                            <div
                                class="mb-4 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800"
                            >
                                <div class="mb-3 flex items-center gap-3">
                                    <div
                                        class="h-3 w-3 rounded-full bg-green-500"
                                    ></div>
                                    <div
                                        class="text-sm font-medium dark:text-white"
                                    >
                                        Reunião com equipe
                                    </div>
                                </div>
                                <div class="mb-3 flex items-center gap-3">
                                    <div
                                        class="h-3 w-3 rounded-full bg-yellow-500"
                                    ></div>
                                    <div
                                        class="text-sm font-medium dark:text-white"
                                    >
                                        Relatório mensal
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-3 w-3 rounded-full bg-red-500"
                                    ></div>
                                    <div
                                        class="text-sm font-medium dark:text-white"
                                    >
                                        Entregar projeto
                                    </div>
                                </div>
                            </div>
                            <div
                                class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800"
                            >
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <span
                                        class="text-sm font-medium dark:text-white"
                                        >Progresso do dia</span
                                    >
                                    <span
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                        >75%</span
                                    >
                                </div>
                                <div
                                    class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700"
                                >
                                    <div
                                        class="h-2 rounded-full bg-green-500"
                                        style="width: 75%"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
