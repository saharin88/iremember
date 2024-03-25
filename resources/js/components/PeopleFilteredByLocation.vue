<script setup>
import {onMounted, inject, ref, provide, computed, watch} from "vue";
import {useRoute} from "vue-router";
import useLocation from "@/composables/location.js";
import useLocationsPeople from "@/composables/locationPeople.js";
import {TailwindPagination} from "laravel-vue-pagination";
import Loading from "@/components/Loading.vue";
import SearchTools from "@/components/SearchTools.vue";
import {wTrans} from "laravel-vue-i18n";
import PeopleItem from "@/components/PeopleItem.vue";

const route = useRoute()
const {location, getLocation, fullLocationName} = useLocation()
const {locationPeople, getLocationPeople, isLoading} = useLocationsPeople()
const currentPageTitle = inject('currentPageTitle')
const countPeople= inject('countPeople')
const params = ref({
    page: null,
    limit: null,
    sort: null,
    order: null,
    load: ['unit'],
    ['filter[full_name]']: null,
})
provide('params', params)

const getPeople = async () => {
    return getLocationPeople(route.params.slug, route.params.type, params.value)
}
provide('getPeople', getPeople)

const title = computed(() => wTrans(`People filtered by place of ${route.params.type} :location`, {location: fullLocationName.value}).value)

watch(title, (newTitle) => {
    currentPageTitle.value = newTitle
})

onMounted(() => {
    getPeople().then(() => {
        countPeople.value = locationPeople.value.meta.total
    })
    getLocation(route.params.slug, {load: ['ancestors']}).then(() => {
        currentPageTitle.value = title.value
    })
})
</script>

<template>

    <SearchTools/>

    <Loading :isLoading="isLoading">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-4">
            <PeopleItem v-for="person in locationPeople.data" :key="person.id" :person="person" class="col-span-1"/>
        </div>

        <div class="justify-center items-center w-full flex">
            <TailwindPagination
                :data="locationPeople"
                :limit="5"
                @pagination-change-page="page => { params.page = page; getPeople()}"
                class="mt-4"
            />
        </div>

    </Loading>

</template>
