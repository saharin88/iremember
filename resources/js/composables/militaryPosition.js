import {ref} from 'vue'

export default function useMilitaryPosition() {

    const militaryPosition = ref([])
    const isLoading = ref(false)

    const getMilitaryPosition = async (id) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/military-position/${id}`)
            .then(response => {
                militaryPosition.value = response.data.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        militaryPosition,
        getMilitaryPosition,
        isLoading
    }

}
