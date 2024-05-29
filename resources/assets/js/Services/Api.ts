import axios from "axios";

const $http = axios.create({
    headers: {
        common: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    }
});

export default  $http;
