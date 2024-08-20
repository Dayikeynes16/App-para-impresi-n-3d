import { defineStore } from "pinia";
import { ref, computed } from "vue";
import axios from "axios";

export const useLoginStore = defineStore("login", () => {
    const authUser = ref({});
    const permissions = ref([]);
    const is_auth = ref(false);
    const roles = ref([])

    const setUser = async () => {
        await axios
            .get("/get_user")
            .then((data) => {
                authUser.value = data.data.data;

                permissions.value = [];
                roles.value = [];
                data.data.data.roles.name

                data.data.data.roles.forEach((element) => {
                    element.permissions.forEach((element) => {
                        permissions.value.push(element.name);
                    });
                    roles.value.push(element.name)
                });
            })
            .catch((error) => {
            });
    };

    const isAutenticated = () => {
        axios.get("/auth").then(({ data }) => {
            if (data === true) {
                is_auth.value = true;
            } else {
                is_auth.value = false;
            }
        });
    };

    const getUserData = computed(() => authUser.value);
    const getPermissions = computed(() => permissions.value);

    return {
        setUser,
        getUserData,
        getPermissions,
        isAutenticated,
        is_auth,
        permissions,
        roles
    };
});
