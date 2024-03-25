<script setup>
import {onMounted, inject, ref} from 'vue';
import {useRoute, useRouter} from 'vue-router'
import usePerson from "@/composables/person.js";
import EventDetails from "@/components/EventDetails.vue";
import Loading from "@/components/Loading.vue";

const route = useRoute()
const router = useRouter()
const {person, getPerson, isLoading} = usePerson()
const currentPageTitle = inject('currentPageTitle')
const params = ref({
    load: [
        'unit',
        'rank',
        'militaryPosition',
        'birthLocation.ancestors',
        'deathLocation.ancestors',
        'burialLocation.ancestors',
        'woundLocation.ancestors'
    ]
})

onMounted(() => {
    getPerson(route.params.slug, params.value).then(() => {
        currentPageTitle.value = person.value.full_name
    })
})
</script>
<template>

    <Loading :isLoading="isLoading">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12">

            <div class="col-span-12 sm:col-span-5 md:col-span-4 lg:col-span-3 text-center mb-5">
                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                    <img :src="person.photo" :alt="person.full_name" class="w-full object-cover object-center">
                </div>
            </div>

            <div class="col-span-12 sm:col-span-7 md:col-span-8 lg:col-span-9 md:ps-6">

                <EventDetails :person="person" type="birth"/>

                <EventDetails :person="person" type="death"/>

                <EventDetails :person="person" type="burial"/>

                <EventDetails :person="person" type="wound"/>

                <p v-if="person.rank" class="mb-2">
                    <span class="font-bold">{{ $t('Rank') }}:</span>
                    <router-link :to="{name: 'people.filtered.by.rank', params: {rankId: person.rank.id}}"
                                 class="text-gray-500 ml-2">
                        {{ person.rank.name }}
                    </router-link>
                </p>
                <p v-if="person.militaryPosition" class="mb-2">
                    <span class="font-bold">{{ $t('Military position') }}:</span>
                    <router-link
                        :to="{name: 'people.filtered.by.military.position', params: {militaryPositionId: person.militaryPosition.id}}"
                        class="text-gray-500 ml-2">
                        {{ person.militaryPosition.name }}
                    </router-link>
                </p>
                <p v-if="person.unit" class="mb-2">
                    <span class="font-bold">{{ $t('Unit') }}:</span>
                    <router-link :to="{name: 'people.filtered.by.unit', params: {slug: person.unit.slug}}"
                                 class="text-gray-500 ml-2">
                        {{ person.unit.name }}
                    </router-link>
                </p>
                <p v-if="person.death_details" class="mb-2"><span class="font-bold">{{ $t('Death details') }}:</span>
                    {{ person.death_details }}</p>
            </div>

        </div>

        <div v-if="person.obituary" class="mb-5">
            <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-4">{{ $t('Obituary') }}</h3>
            <div v-html="person.obituary" class="space-y-4"/>
        </div>

    </Loading>

</template>
