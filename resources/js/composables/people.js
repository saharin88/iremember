import {ref} from 'vue'

export default function usePeople() {

    const people = ref([])
    const isLoading = ref(false)

    const getPeople = async (params) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get('/api/v1/people', {params})
            .then(response => {
                people.value = response.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        people,
        getPeople,
        isLoading
    }

}
