import Axios from "axios"
import { OK, UNPROCESSABLE_ENTITY} from '../util'

const state = {
  user: null,
  apiStatus: null,
  loginErrorMessages: null,
  registerErrorMessages: null
}

const getters = {
  check: state => !!state.user,
  username: state => state.user ? state.user.name : ''
}

const mutations = {
  setUser (state, user) {
    state.user = user
  },

  setApiStatus (state, apiStatus) {
    state.apiStatus = apiStatus
  },

  setLoginErrorMessages (state, messages) {
    state.loginErrorMessages = messages
  },

  setRegisterErrorMessages (state, messages) {
    state.registerErrorMessages = messages
  }
}

const actions = {
  async register (context, data) {
    const response = await Axios.post('/api/register', data)
    context.commit('setUser', response.data)
  },

  async login (context, data) {
    context.commit('setApiStatus', null)
    const response = await Axios.post('/api/login', data).catch(err => err.response || err)

    if (response.status === OK) {
      context.commit('setApiStatus', true)
      context.commit('setUser', response.data)
      return false
    }

    context.commit('setApiStatus', false)
    if (response.status === UNPROCESSABLE_ENTITY) {
      context.commit('setLoginErrorMessages', response.data.errors)
    } else {
      context.commit('error/setCode', response.status, {root: true})
    }
  },

  async logout (context) {
    await Axios.post('/api/logout')
    context.commit('setUser', null)
  },

  async currentUser (context) {
    const response = await Axios.get('/api/user')
    const user = response.data || null
    context.commit('setUser', user)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
