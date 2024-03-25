import {computed, ref} from 'vue'

export default function useLocation() {

    const location = ref({})
    const isLoading = ref(false)

    const getLocation = async (slug, params) => {
        if (isLoading.value) return;
        isLoading.value = true
        return axios.get(`/api/v1/location/${slug}`, {params})
            .then(response => {
                location.value = response.data.data;
            })
            .finally(() => {
                isLoading.value = false
            })
    }

    const fullLocationName = computed(() => {
        const path = [location.value.name];
        let parent = location.value.ancestors;
        while (parent) {
            path.unshift(parent.name);
            parent = parent.ancestors;
        }
        return path.reverse().join(', ');
    })

    return {
        location,
        getLocation,
        isLoading,
        fullLocationName
    }

}
