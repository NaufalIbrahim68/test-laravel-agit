import axios from 'axios'

const client = axios.create({ baseURL: '/api' })

export function createPlanning(data) {
    return client.post('/planning', data)
}

export function getHistory(page = 1, perPage = 10) {
    return client.get('/planning', { params: { page, per_page: perPage } })
}

export function getDetail(id) {
    return client.get(`/planning/${id}`)
}
