<script setup>
import FullName from "@/components/SearchTools/FullName.vue";
import Sort from "@/components/SearchTools/Sort.vue";
import Limit from "@/components/SearchTools/Limit.vue";
</script>

<template>
    <div class="flex flex-col sm:flex-row mb-4">
        <FullName class="w-full mb-4 sm:mb-0"/>
        <div class="flex mb-4 sm:mb-0">
            <Sort class="w-full md:w-auto ml-0 sm:ml-3"/>
            <Limit class="ml-3"/>
        </div>
    </div>
</template>
