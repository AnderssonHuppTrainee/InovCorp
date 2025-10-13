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
        class="flex min-h-screen flex-col bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-[#0a0a0a] dark:via-[#111] dark:to-[#0f0f0f]"
    >
        <!-- Top Navigation -->
        <header
            class="flex w-full items-center justify-between border-b border-gray-200 px-6 py-4 lg:px-12 dark:border-gray-800"
        >
            <h1
                class="text-2xl font-bold tracking-tight text-gray-800 dark:text-white"
            >
                Smart<span class="text-blue-600">Management</span>
            </h1>

            <nav class="flex items-center gap-4">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="dashboard()"
                        class="rounded-md bg-blue-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                    >
                        Ir para o Dashboard
                    </Link>
                </template>

                <template v-else>
                    <Link
                        :href="login()"
                        class="px-5 py-2 text-sm font-medium text-gray-800 hover:text-blue-600 dark:text-gray-200 dark:hover:text-blue-400"
                    >
                        Entrar
                    </Link>
                    <Link
                        :href="register()"
                        class="rounded-md border border-blue-600 px-5 py-2 text-sm font-medium text-blue-600 transition hover:bg-blue-50 dark:border-blue-500 dark:text-blue-400 dark:hover:bg-blue-950"
                    >
                        Criar Conta
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero Section -->
        <main
            class="flex flex-1 flex-col items-center justify-between px-8 py-16 lg:flex-row lg:px-20"
        >
            <!-- Text -->
            <div class="max-w-xl flex-1 space-y-6">
                <h2
                    class="text-4xl leading-tight font-bold text-gray-900 lg:text-5xl dark:text-white"
                >
                    Gestão <span class="text-blue-600">Inteligente</span> para a
                    sua Empresa
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Automatize processos, acompanhe clientes, fornecedores e
                    finanças num só lugar. Uma solução completa para pequenas e
                    médias empresas.
                </p>

                <div class="flex flex-col gap-4 pt-2 sm:flex-row">
                    <Link
                        :href="register()"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 font-medium text-white transition hover:bg-blue-700"
                    >
                        Experimente Gratuitamente
                    </Link>
                    <Link
                        :href="login()"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-gray-800 transition hover:border-blue-500 hover:text-blue-600 dark:border-gray-700 dark:text-gray-200 dark:hover:border-blue-500 dark:hover:text-blue-400"
                    >
                        Acesse sua Conta
                    </Link>
                </div>
            </div>

            <!-- Illustration -->
            <div class="mt-12 flex flex-1 justify-center lg:mt-0">
                <div
                    class="w-full max-w-md rounded-2xl border border-gray-100 bg-white p-8 shadow-xl dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="font-medium text-gray-700 dark:text-gray-300"
                                >Progresso do mês</span
                            >
                            <span
                                class="text-sm text-blue-600 dark:text-blue-400"
                                >82%</span
                            >
                        </div>
                        <div
                            class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                        >
                            <div class="h-2 w-[82%] bg-blue-600"></div>
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

        <!-- Features -->
        <section
            class="border-t border-gray-200 bg-gray-50 px-8 py-16 lg:px-20 dark:border-gray-800 dark:bg-gray-950"
        >
            <h3
                class="mb-10 text-center text-3xl font-bold text-gray-800 dark:text-white"
            >
                Tudo o que sua gestão precisa
            </h3>
            <div
                class="mx-auto grid max-w-6xl gap-8 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-gray-900"
                >
                    <h4 class="mb-2 text-xl font-semibold text-blue-600">
                        Clientes & Fornecedores
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Centralize todas as informações de clientes e
                        fornecedores com histórico de interações.
                    </p>
                </div>
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-gray-900"
                >
                    <h4 class="mb-2 text-xl font-semibold text-blue-600">
                        Propostas & Encomendas
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Crie propostas em segundos e converta facilmente em
                        encomendas com um clique.
                    </p>
                </div>
                <div
                    class="rounded-xl border border-gray-100 bg-white p-6 shadow-md dark:border-gray-800 dark:bg-gray-900"
                >
                    <h4 class="mb-2 text-xl font-semibold text-blue-600">
                        Financeiro & Faturas
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Controle contas, faturas e relatórios financeiros de
                        forma automatizada e segura.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t border-gray-200 py-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400"
        >
            © {{ new Date().getFullYear() }} SmartManagement — Todos os
            direitos reservados.
        </footer>
    </div>
</template>
