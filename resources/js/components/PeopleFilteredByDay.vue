<script setup>
import {inject, onMounted, provide, ref, watch} from "vue";
import {useRoute} from "vue-router";
import usePeople from "@/composables/people.js";
import {TailwindPagination} from "laravel-vue-pagination";
import Loading from "@/components/Loading.vue";
import SearchTools from "@/components/SearchTools.vue";
import moment from "moment";
import {wTrans} from 'laravel-vue-i18n';
import PeopleItem from "@/components/PeopleItem.vue";

const route = useRoute()
const currentPageTitle = inject('currentPageTitle')
const countPeople = inject('countPeople')
const {people, getPeople, isLoading} = usePeople()
const params = ref({
    page: null,
    limit: null,
    sort: null,
    order: null,
    load: ['unit'],
    ['filter[full_name]']: null,
})
provide('params', params)

const getPeopleFilteredByDay = async () => {
    params.value[`filter[${route.params.type}_day]`] = route.params.day
    return getPeople(params.value)
}
provide('getPeople', getPeopleFilteredByDay)

const title = wTrans(`People filtered by day of ${route.params.type} :day`, {
    day: moment(route.params.day, 'MMDD').format('D MMMM')
})

watch(title, (newTitle) => {
    currentPageTitle.value = newTitle
})

onMounted(() => {
    if (route.params.type === 'birth' || route.params.type === 'death') {
        params.value.sort = route.params.type
    }
    currentPageTitle.value = title.value
    getPeopleFilteredByDay().then(() => {
        countPeople.value = people.value.meta.total
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
                @pagination-change-page="page => { params.page = page; getPeopleFilteredByDay()}"
                class="mt-4"
            />
        </div>

    </Loading>

</template>
