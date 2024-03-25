<script setup>
const props = defineProps({
    location: {
        type: Object
    },
    type: {
        type: String
    },
})
</script>

<template>
    <router-link v-if="location" :to="{name: 'filtered.people.by.location', params: {slug: location.slug, type}}"
                 class="text-gray-500 ml-2">
        {{ location.name }}
    </router-link>
</template>

