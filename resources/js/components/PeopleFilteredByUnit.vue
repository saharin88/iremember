<script setup>
import {onMounted, provide, inject, ref} from 'vue';
import {useRoute} from 'vue-router';
import useUnit from "@/composables/unit";
import useUnitPeople from "@/composables/unitPeople";
import {TailwindPagination} from "laravel-vue-pagination";
import SearchTools from "@/components/SearchTools.vue";
import Loading from "@/components/Loading.vue";
import moment from "moment";
import PeopleItem from "@/components/PeopleItem.vue";

const route = useRoute()
const {unit, getUnit} = useUnit()
const {unitPeople, getUnitPeople, isLoading} = useUnitPeople()
const currentPageTitle = inject('currentPageTitle')
const countPeople = inject('countPeople')
const params = ref({
    page: null,
    limit: null,
    sort: null,
    order: null,
    load: ['unit'],
    ['filter[full_name]']: null,
})
provide('params', params)

const getPeopleFilteredByUnit = async () => {
    return getUnitPeople(route.params.slug, params.value)
}
provide('getPeople', getPeopleFilteredByUnit)

onMounted(() => {
    getUnit(route.params.slug).then(() => {
        currentPageTitle.value = unit.value.name
    })
    getPeopleFilteredByUnit().then(() => {
        countPeople.value = unitPeople.value.meta.total
    })
})

</script>

<template>

    <SearchTools/>

    <Loading :isLoading="isLoading">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-4">
            <PeopleItem v-for="person in unitPeople.data" :key="person.id" :person="person" class="col-span-1"/>
        </div>

        <div class="justify-center items-center w-full flex">
            <TailwindPagination
                :data="unitPeople"
                :limit="5"
                @pagination-change-page="page => {params.page = page; getPeopleFilteredByUnit()}"
                class="mt-4"
            />
        </div>

    </Loading>
</template>
