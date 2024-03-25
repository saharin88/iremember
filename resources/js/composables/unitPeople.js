import {ref} from 'vue'

export default function useUnit() {

    const unitPeople = ref([])
    const isLoading = ref(false)

    const getUnitPeople = async (slug, params) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/unit/${slug}/people`, {params})
            .then(response => {
                unitPeople.value = response.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        unitPeople,
        getUnitPeople,
        isLoading
    }

}
