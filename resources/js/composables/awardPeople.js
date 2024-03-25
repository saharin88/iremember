import {ref} from 'vue'

export default function useAwardPeople() {

    const awardPeople = ref([])
    const isLoading = ref(false)

    const getAwardPeople = async (slug) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/award/${slug}/people`)
            .then(response => {
                awardPeople.value = response.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        awardPeople,
        getAwardPeople,
        isLoading
    }
}
