// axios instance
import axios from 'axios'

const instance = axios.create({
  // change this url to your api
  // baseURL: '//localhost:8889/',
  baseURL: '/api/index.php',
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
})

export default instance
