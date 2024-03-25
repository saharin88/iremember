<script setup>
import {computed, watch, inject} from "vue";
import debounce from "lodash.debounce";

const params = inject('params')
const getPeople = inject('getPeople')

let fullName = computed({
    get: () => params.value['filter[full_name]'] ?? '',
    set: value => {
        params.value['filter[full_name]'] = value === '' ? null : value
        params.value.page = 1
    }
})
watch(fullName, debounce(() => {
    if (fullName.value.length < 3 && fullName.value.length !== 0) {
        return
    }
    getPeople()
}, 500))
</script>


<template>
    <div class="relative">
        <input
            v-model="fullName"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 pr-10 w-full"
            min="3"
            max="255"
            :placeholder="$t('Search by full name')">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="absolute top-3 right-3 h-5 w-5 text-gray-500 cursor-pointer" @click="fullName = ''">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </div>
</template>
