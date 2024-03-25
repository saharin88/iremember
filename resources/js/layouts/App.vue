<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-14">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <router-link :to="{ name: 'people', query: {} }"
                                         class="text-xl font-bold leading-5 tracking-tight text-gray-950">
                                Я пам'ятаю
                            </router-link>
                        </div>

                    </div>

                    <div class="flex items-center">
                        <div class="flex">
                            <a href="https://github.com/saharin88/iremember" target="_blank" class="text-gray-500 hover:text-gray-900 flex items-center">
                                GitHub
                                <svg width="13.5" height="13.5" aria-hidden="true" viewBox="0 0 24 24" class="ml-1 pt-1"><path fill="currentColor" d="M21 13v10h-21v-19h12v2h-10v15h17v-8h2zm3-12h-10.988l4.035 4-6.977 7.07 2.828 2.828 6.977-7.07 4.125 4.172v-11z"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="currentPageTitle" class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ currentPageTitle }}
                    <small v-if="countPeople > 0" class="text-gray-500 text-sm font-light rounded-full px-2 py-1">{{ countPeople }}</small>
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <router-view></router-view>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import {ref, provide, watch} from 'vue';
import {useRoute} from 'vue-router'

const route = useRoute()
const currentPageTitle = ref('')
const countPeople = ref(0)

watch(currentPageTitle, (newTitle) => {
    document.title = newTitle
})

watch(route, () => {
    currentPageTitle.value = ''
    countPeople.value = 0
})

provide('currentPageTitle', currentPageTitle)
provide('countPeople', countPeople)
</script>
