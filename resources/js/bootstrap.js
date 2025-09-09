import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.axios.defaults.withCredentials = true;
window.axios.defaults.baseURL = window.location.origin;