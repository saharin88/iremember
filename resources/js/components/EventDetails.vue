<script setup>
import {computed} from 'vue';
import moment from "moment";
import "moment/dist/locale/uk"
import LocationLink from "@/components/LocationLink.vue";
import {trans, transChoice} from 'laravel-vue-i18n';

const props = defineProps({
    person: {
        type: Object
    },
    type: {
        type: String
    },
})
const date = computed(() => props.person[props.type] ? moment(props.person[props.type], 'YYYY-MM-DD') : null)
const location = computed(() => props.person[`${props.type}Location`])
const label = computed(() => {
    const dateLabel = date.value ? 'Date' : '';
    const locationLabel = location.value ? (date.value ? ' and place' : 'Place') : '';
    return trans(`${dateLabel}${locationLabel} ${props.type}`);
});
const locations = computed(() => {
    const locations = []
    if (location.value) {
        locations.push(location.value)
    } else {
        return []
    }
    let parent = location.value.ancestors
    while (parent) {
        locations.push(parent)
        parent = parent.ancestors
    }
    return locations
})

const age = computed(() => {
    if (props.type === 'death' && date.value && props.person.birth) {
        return date.value.diff(props.person.birth, 'year', false)
    }
    return null
})

</script>

<template>
    <p v-if="date || location" class="mb-2">
        <span class="font-bold">{{ label }}:</span>
        <router-link v-if="date"
                     :to="{name: 'filtered.people.by.day', params: {day: date.format('MMDD'), type: props.type}}"
                     class="text-gray-500 ml-2">
            {{ date.format('D MMMM') }}
        </router-link>
        <router-link v-if="date"
                     :to="{name: 'filtered.people.by.year', params: {year: date.format('YYYY'), type: props.type}}"
                     class="text-gray-500 ml-2">
            {{ date.format('YYYY р.') }}
        </router-link>
        <router-link v-if="age" :to="{name: 'filtered.people.by.age', params: {age}}" class="text-gray-500 ml-2">
            ({{ transChoice('Years old', age) }})
        </router-link>
        <LocationLink v-for="location in locations" :location="location" :type="type" :key="location.id"/>
    </p>
</template>
