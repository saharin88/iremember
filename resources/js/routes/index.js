import {createRouter, createWebHistory} from 'vue-router';
import People from '@/components/People.vue'
import Person from '@/components/Person.vue'
import PeopleFilteredByLocation from "@/components/PeopleFilteredByLocation.vue";
import PeopleFilteredByYear from "@/components/PeopleFilteredByYear.vue";
import PeopleFilteredByDay from "@/components/PeopleFilteredByDay.vue";
import PeopleFilteredByAge from "@/components/PeopleFilteredByAge.vue";
import PeopleFilteredByUnit from "@/components/PeopleFilteredByUnit.vue";
import PeopleFilteredByRank from "@/components/PeopleFilteredByRank.vue";
import PeopleFilteredByMilitaryPositionId from "@/components/PeopleFilteredByMilitaryPositionId.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [

        {
            path: '/',
            name: 'people',
            component: People,
        },
        {
            path: '/:slug',
            name: 'person',
            component: Person,
        },
        {
            path: '/unit/:slug',
            name: 'people.filtered.by.unit',
            component: PeopleFilteredByUnit,
        },
        {
            path: '/rank/:rankId',
            name: 'people.filtered.by.rank',
            component: PeopleFilteredByRank,
        },
        {
            path: '/military-position/:militaryPositionId',
            name: 'people.filtered.by.military.position',
            component: PeopleFilteredByMilitaryPositionId,
        },
        {
            path: '/age/:age',
            name: 'filtered.people.by.age',
            component: PeopleFilteredByAge,
        },
        {
            path: '/:type(birth|death|burial|wound)/year/:year',
            name: 'filtered.people.by.year',
            component: PeopleFilteredByYear,
        },
        {
            path: '/:type(birth|death|burial|wound)/day/:day',
            name: 'filtered.people.by.day',
            component: PeopleFilteredByDay,
        },
        {
            path: '/location/:type(birth|death|burial|wound)/:slug',
            name: 'filtered.people.by.location',
            component: PeopleFilteredByLocation,
        },
    ]
})
export default router
