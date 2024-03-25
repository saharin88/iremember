import {ref} from "vue";

export default function useLocationsPeople() {

    const locationPeople = ref([])
    const isLoading = ref(false)

    const getLocationPeople = async (slug, relation, params) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/location/${slug}/people/${relation}`, {params})
            .then(response => {
                locationPeople.value = response.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    return {
        locationPeople,
        getLocationPeople,
        isLoading
    }

}
