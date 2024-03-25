<script setup>
import {onMounted, provide, ref} from "vue";
import {useRoute} from "vue-router";
import usePeople from "@/composables/people.js";
import SearchTools from "@/components/SearchTools.vue";
import {TailwindPagination} from 'laravel-vue-pagination';
import Loading from "@/components/Loading.vue";
import moment from "moment";

const route = useRoute()
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

const getAllPeople = async () => {
    return getPeople(params.value)
}
provide('getPeople', getAllPeople)

onMounted(() => {
    getAllPeople()
})
</script>

<template>

    <SearchTools/>

    <Loading :isLoading="isLoading">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-4">
            <div v-for="person in people.data" class="col-span-1">
                <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
                    <img :src="person.photo" :alt="person.full_name"
                         class="sm:h-80 w-full object-cover object-center">
                    <div class="p-6">
                        <h2 class="tracking-widest title-font font-medium text-gray-500 mb-1">
                            <router-link :to="{ name: 'person', params: {slug: person.slug}}"
                                         class="text-gray-900">
                                {{ person.full_name }}
                            </router-link>
                        </h2>
                        <p v-if="params.sort === 'birth' || params.sort === 'death'" class="leading-relaxed mb-1 text-gray-500">
                            <em>{{ person[params.sort] ? moment(person[params.sort]).format('LL') : ''}}</em>
                        </p>
                        <router-link v-if="person.unit"
                                     :to="{name: 'people.filtered.by.unit', params: {slug: person.unit.slug}}"
                                     class=" text-xs text-gray-500">{{ person.unit.name }}
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <div class="justify-center items-center w-full flex">
            <TailwindPagination
                :data="people"
                :limit="5"
                @pagination-change-page="page=> {  params.page = page; getAllPeople() }"
                class="mt-4"
            />
        </div>

    </Loading>

</template>
