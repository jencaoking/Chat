import axios from './axios'

export const login = async (email, password) => {
  const response = await axios.post('/auth/login', { email, password })
  if (response.success) {
    localStorage.setItem('token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
  }
  return response
}

export const register = async (username, email, password) => {
  const response = await axios.post('/auth/register', { username, email, password })
  if (response.success) {
    localStorage.setItem('token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
  }
  return response
}

export const logout = async () => {
  await axios.post('/auth/logout')
  localStorage.removeItem('token')
  localStorage.removeItem('user')
}

export const getUser = async () => {
  const response = await axios.get('/auth/user')
  if (response.success) {
    localStorage.setItem('user', JSON.stringify(response.user))
  }
  return response
}

export const isLoggedIn = () => {
  return !!localStorage.getItem('token')
}

export const getUserFromStorage = () => {
  const user = localStorage.getItem('user')
  return user ? JSON.parse(user) : null
}

export const isAdmin = () => {
  const user = getUserFromStorage()
  return user?.role === 'admin'
}