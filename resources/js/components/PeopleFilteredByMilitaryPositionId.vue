<script setup>
import {onMounted, provide, ref, inject} from "vue";
import {useRoute} from "vue-router";
import usePeople from "@/composables/people.js";
import useMilitaryPosition from "@/composables/militaryPosition.js";
import SearchTools from "@/components/SearchTools.vue";
import {TailwindPagination} from 'laravel-vue-pagination';
import Loading from "@/components/Loading.vue";
import PeopleItem from "@/components/PeopleItem.vue";

const route = useRoute()
const {militaryPosition, getMilitaryPosition} = useMilitaryPosition()
const {people, getPeople, isLoading} = usePeople()
const currentPageTitle = inject('currentPageTitle')
const countPeople = inject('countPeople')
const params = ref({
    page: null,
    limit: null,
    sort: null,
    order: null,
    load: ['unit'],
    ['filter[full_name]']: null,
    ['filter[military_position_id]']: null,
})
provide('params', params)

const getPeopleFilteredByMilitaryPositionId = async () => {
    params.value['filter[military_position_id]'] = route.params.militaryPositionId
    return getPeople(params.value)
}
provide('getPeople', getPeopleFilteredByMilitaryPositionId)

onMounted(() => {
    getMilitaryPosition(route.params.militaryPositionId).then(() => {
        currentPageTitle.value = militaryPosition.value.name
        getPeopleFilteredByMilitaryPositionId().then(() => {
            countPeople.value = people.value.meta.total
        })
    })
})
</script>

<template>

    <SearchTools/>

    <Loading :isLoading="isLoading">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-4">
            <PeopleItem v-for="person in people.data" :key="person.id" :person="person" class="col-span-1"/>
        </div>

        <div class="justify-center items-center w-full flex">
            <TailwindPagination
                :data="people"
                :limit="5"
                @pagination-change-page="page=> {  params.page = page; getPeopleFilteredByMilitaryPositionId() }"
                class="mt-4"
            />
        </div>

    </Loading>

</template>
