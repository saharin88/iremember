<script setup>
import {inject} from 'vue'
import moment from "moment";

const params = inject('params')

defineProps({
  person: {}
})

</script>

<template>
  <div class="h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden">
    <img :src="person.photo" :alt="person.full_name" class="sm:h-80 w-full object-cover object-center">
    <div class="p-6">
      <h2 class="tracking-widest title-font font-medium text-gray-500 mb-1">
        <router-link :to="{ name: 'person', params: {slug: person.slug}}" class="text-gray-900">
          {{ person.full_name }}
        </router-link>
      </h2>
      <p v-if="params.sort === 'birth' || params.sort === 'death'" class="leading-relaxed mb-1 text-gray-500">
        <em>{{ person[params.sort] ? moment(person[params.sort]).format('LL') : ''}}</em>
      </p>
      <router-link v-if="person.unit" :to="{name: 'people.filtered.by.unit', params: {slug: person.unit.slug}}"
                   class=" text-xs text-gray-500">
        {{ person.unit.name }}
      </router-link>
    </div>
  </div>
</template>
