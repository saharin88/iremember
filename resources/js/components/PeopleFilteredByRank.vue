<script setup>
import {onMounted, provide, ref, inject} from "vue";
import {useRoute} from "vue-router";
import usePeople from "@/composables/people.js";
import useRank from "@/composables/rank.js";
import SearchTools from "@/components/SearchTools.vue";
import {TailwindPagination} from 'laravel-vue-pagination';
import Loading from "@/components/Loading.vue";
import moment from "moment";
import PeopleItem from "@/components/PeopleItem.vue";

const route = useRoute()
const {rank, getRank} = useRank()
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
    ['filter[rank_id]']: null,
})
provide('params', params)

const getPeopleFilteredByRankId = async () => {
    params.value['filter[rank_id]'] = route.params.rankId
    return getPeople(params.value)
}
provide('getPeople', getPeopleFilteredByRankId)

onMounted(() => {
    getRank(route.params.rankId).then(() => {
        currentPageTitle.value = rank.value.name
        getPeopleFilteredByRankId().then(() => {
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
                @pagination-change-page="page=> {  params.page = page; getPeopleFilteredByRankId() }"
                class="mt-4"
            />
        </div>

    </Loading>

</template>
