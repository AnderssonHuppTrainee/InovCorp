<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
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
        <!-- Conteúdo central -->
        <main class="flex max-w-2xl flex-1 flex-col items-center justify-center text-center">
            <h1 class="mb-4 text-4xl font-bold">👋 Bem-vindo ao ChatApp</h1>
            <p class="mb-6 text-lg text-gray-600 dark:text-gray-300">
                Um espaço simples e intuitivo para conversar com amigos, criar salas de discussão e trocar mensagens diretas.
            </p>

            <Link v-if="$page.props.auth.user" :href="dashboard()" class="btn rounded-lg px-6 py-3 text-lg btn-primary"> Ir para o Dashboard </Link>
            <Link v-else :href="register()" class="btn rounded-lg px-6 py-3 text-lg btn-primary"> 🚀 Get Started </Link>
        </main>

        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
