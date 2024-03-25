<script setup>
import {computed, inject, watch} from "vue";

const params = inject('params')
const getPeople = inject('getPeople')
const limit = computed({
    get: () => params.value.limit ?? 10,
    set: value => {
        params.value.limit = value
        params.value.page = 1
        getPeople()
    }
})
</script>

<template>
    <select
        v-model="limit"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 self-right">
        <option v-for="value in [5, 10, 20, 50, 100, 500]" :value="value">{{ value }}</option>
    </select>
</template>

