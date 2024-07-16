import { defineStore } from 'pinia'
import {ref, computed} from 'vue'
import axios from 'axios';

export const useLoginStore = defineStore('login', () => {

  const user = ref(null)
  const permissions = ref([])

  const setUser = () => {
    axios.get('/get_user')
      .then((data) => {
        user.value = data.data


      permissions.value = []
        data.data.data.roles.forEach(element => {
          element.permissions.forEach(element => {
            permissions.value.push(element.name)
          });
        });

      })
      .catch((error) => {
        console.log(error);
    });
  }

  const getUserData = computed(() => user.value) 
  const getPermissions = computed(() => permissions.value) 


  return {
    setUser,
    getUserData,
    getPermissions
  }
})
