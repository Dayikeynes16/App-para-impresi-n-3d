import axios from "axios";

const axiosInstance =
    axios.create(/*{
  withCredentials: true,
  baseURL: import.meta.env.VITE_BASE_URL
}*/);
const token = document
    .querySelector("meta[name='csrf-token']")
    .getAttribute("value");

axiosInstance.interceptors.request.use(
    (config) => {
        config.headers = {
            ...config.headers,
            Accept: "application/json",
            headers: { "X-CSRF-TOKEN": token },
            //   'Authorization': localStorage.getItem('userToken') ? `Bearer ${localStorage.getItem('userToken')}` : null
        };

        console.log("axios new");

        return config;
    },
    (error) => {
        console.log(error.response);

        return Promise.reject(error);
    }
);

// axiosInstance.interceptors.response.use(
//     (response) => {
//         return response;
//     },
//     (error) => {
//         console.log(error.response);

//         if (
//             error.response.status === 401 &&
//             error.response.data.message == "Unauthenticated."
//         ) {
//             localStorage.removeItem("userToken");
//         }

//         return Promise.reject(error);
//     }
// );

export default axiosInstance;
