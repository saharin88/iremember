<script setup>
import {computed, inject} from "vue";

const params = inject('params')
const getPeople = inject('getPeople')

const sort = computed({
    get: () => {
        const sort = params.value.sort ?? 'full_name'
        const order = params.value.order ?? 'asc'
        return `${sort} ${order}`

    },
    set: async value => {
        const sort = value.split(' ')
        params.value.sort = sort[0]
        params.value.order = sort[1]
        params.value.page = 1
        getPeople()
    }
})

defineProps({
    options: {
        type: Array,
        default: [
            'full_name asc',
            'full_name desc',
            'birth asc',
            'birth desc',
            'death asc',
            'death desc',
        ]
    }
})

</script>

<template>
    <select
        v-model="sort"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 self-right">
        <option v-for="value in options" :value="value">{{ $t(value) }}</option>
    </select>
</template>
