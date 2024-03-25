import {ref} from 'vue'

export default function usePerson() {
    const person = ref({})
    const isLoading = ref(false)

    const getPerson = async (slug, params) => {
        if (isLoading.value) return
        isLoading.value = true
        return axios.get(`/api/v1/person/${slug}`, {params})
            .then(response => {
                person.value = response.data.data;
            })
            .catch(error => {
                console.log(error);
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        person,
        getPerson,
        isLoading
    }

}
