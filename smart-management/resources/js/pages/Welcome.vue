<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="SmartManagement - Gestão Inteligente">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="flex min-h-screen flex-col bg-gradient-to-b from-[#FAFAFA] to-[#EDEDED] text-[#1B1B1B] dark:from-[#0A0A0A] dark:to-[#121212] dark:text-[#EDEDED]"
    >
        <header
            class="flex w-full items-center justify-between border-b border-gray-200 px-6 py-4 lg:px-12 dark:border-gray-800"
        >
            <h1 class="flex items-center text-2xl font-bold tracking-tight">
                Smart
                <span class="ml-2 rounded bg-black px-2 py-1 text-white">
                    Management
                </span>
            </h1>

            <nav class="flex items-center gap-4">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="dashboard()"
                        class="rounded-md bg-gray-800 px-5 py-2 text-sm font-medium text-white transition hover:bg-gray-700"
                    >
                        Dashboard
                    </Link>
                </template>

                <template v-else>
                    <Link
                        :href="login()"
                        class="px-5 py-2 text-sm font-medium text-gray-800 hover:text-gray-600 dark:text-gray-200 dark:hover:text-gray-400"
                    >
                        Entrar
                    </Link>
                    <Link
                        :href="register()"
                        class="rounded-md border border-gray-800 px-5 py-2 text-sm font-medium text-gray-800 transition hover:bg-gray-900 hover:text-white dark:border-gray-300 dark:text-gray-200 dark:hover:bg-gray-100 dark:hover:text-black"
                    >
                        Criar Conta
                    </Link>
                </template>
            </nav>
        </header>

        <main
            class="flex flex-1 flex-col items-center justify-between px-8 py-16 lg:flex-row lg:px-20"
        >
            <div class="max-w-xl flex-1 space-y-6">
                <h2 class="text-4xl leading-tight font-bold lg:text-5xl">
                    Gestão <span class="text-blue-600">Inteligente</span> e
                    Minimalista
                </h2>
                <p class="text-lg text-gray-700 dark:text-gray-400">
                    Organize, monitore e cresça sua empresa com uma interface
                    moderna e funcional. Foco total na eficiência e clareza dos
                    dados.
                </p>

                <div class="flex flex-col gap-4 pt-2 sm:flex-row">
                    <Link
                        :href="register()"
                        class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-6 py-3 font-medium text-white transition hover:bg-gray-800"
                    >
                        Começar Agora
                    </Link>
                    <Link
                        :href="login()"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-400 px-6 py-3 text-gray-800 transition hover:border-gray-600 hover:text-gray-600 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-500 dark:hover:text-gray-300"
                    >
                        Acessar Conta
                    </Link>
                </div>
            </div>

            <div class="mt-12 flex flex-1 justify-center lg:mt-0">
                <div
                    class="w-full max-w-md rounded-2xl border border-gray-100 bg-white p-8 shadow-xl dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="font-medium text-gray-700 dark:text-gray-300"
                                >Atividades de Hoje</span
                            >
                            <span class="text-sm text-gray-500">75%</span>
                        </div>
                        <div
                            class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                        >
                            <div
                                class="h-2 w-[75%] bg-blue-600 dark:bg-blue-500"
                            ></div>
                        </div>

                        <ul class="mt-6 space-y-3">
                            <li class="flex items-center gap-3">
                                <div
                                    class="h-3 w-3 rounded-full bg-green-500"
                                ></div>
                                <span
                                    class="text-sm text-gray-700 dark:text-gray-300"
                                    >Reunião com Clientes</span
                                >
                            </li>
                            <li class="flex items-center gap-3">
                                <div
                                    class="h-3 w-3 rounded-full bg-yellow-500"
                                ></div>
                                <span
                                    class="text-sm text-gray-700 dark:text-gray-300"
                                    >Emissão de Faturas</span
                                >
                            </li>
                            <li class="flex items-center gap-3">
                                <div
                                    class="h-3 w-3 rounded-full bg-red-500"
                                ></div>
                                <span
                                    class="text-sm text-gray-700 dark:text-gray-300"
                                    >Ordens de Trabalho Pendentes</span
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>

        <section
            class="border-t border-gray-200 bg-gray-50 px-8 py-16 lg:px-20 dark:border-gray-800 dark:bg-[#0F0F0F]"
        >
            <h3 class="mb-10 text-center text-3xl font-bold">
                Soluções para sua gestão diária
            </h3>
            <div
                class="mx-auto grid max-w-6xl gap-8 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-[#1A1A1A]"
                >
                    <h4
                        class="mb-2 text-lg font-semibold text-gray-800 dark:text-gray-200"
                    >
                        Gestão de Clientes
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Centralize cadastros, histórico e comunicações com
                        clientes em um só lugar.
                    </p>
                </div>
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-[#1A1A1A]"
                >
                    <h4
                        class="mb-2 text-lg font-semibold text-gray-800 dark:text-gray-200"
                    >
                        Controle Financeiro
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Monitore entradas, saídas, faturas e relatórios de forma
                        prática e precisa.
                    </p>
                </div>
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-[#1A1A1A]"
                >
                    <h4
                        class="mb-2 text-lg font-semibold text-gray-800 dark:text-gray-200"
                    >
                        Produtividade
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Visualize tarefas, progresso e metas em tempo real, com
                        painéis intuitivos.
                    </p>
                </div>
            </div>
        </section>

        <footer
            class="border-t border-gray-200 py-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400"
        >
            © {{ new Date().getFullYear() }} SmartManagement — Todos os
            direitos reservados.
        </footer>
    </div>
</template>
